<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\CancellationRule;
use App\Models\Operator;
use App\Models\ServiceFee;
use App\Services\FareCalculationService;
use App\Services\OperatorAuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FareRuleController extends Controller
{
    private function getOperator()
    {
        $operator = Auth::guard('operator')->user();
        if ($operator) {
            session(['operator_id' => $operator->id]);
            return $operator;
        }
        return Operator::find(session('operator_id'));
    }

    /**
     * Display the fare rules management page.
     */
    public function index(Request $request, FareCalculationService $fareService)
    {
        $operator = $this->getOperator();
        if (!$operator) {
            return redirect()->route('operator.login')->with('error', 'Please log in to manage fare rules.');
        }

        $cancellationRules = CancellationRule::where('operator_id', $operator->id)
            ->orderBy('hours_before_departure', 'desc')
            ->get();

        $serviceFees = ServiceFee::where('operator_id', $operator->id)->get();

        // Fare preview calculation
        $previewResult = null;
        if ($request->has('preview_fare')) {
            $previewFare = $request->input('preview_fare', 0);
            $previewCode = $request->input('preview_code', '');

            // Create a mock route-like object for preview
            $mockRoute = new \stdClass();
            $mockRoute->fare = $previewFare;
            $mockRoute->operator_id = $operator->id;

            // Use a mock PromoCode lookup
            $promoCode = null;
            if ($previewCode) {
                $promoCode = \App\Models\PromoCode::where('code', $previewCode)
                    ->where('operator_id', $operator->id)
                    ->first();
            }

            // We need to pass a Route model, so let's just calculate manually for preview
            $baseFare = (float) $previewFare;
            $fees = ServiceFee::where('operator_id', $operator->id)->active()->get();
            $feeTotal = 0;
            $feeDetails = [];
            foreach ($fees as $fee) {
                $amount = $fee->calculateFee($baseFare);
                $feeTotal += $amount;
                $feeDetails[] = [
                    'name' => $fee->name,
                    'type' => $fee->fee_type,
                    'value' => $fee->fee_value,
                    'amount' => $amount,
                ];
            }

            $subtotal = $baseFare + $feeTotal;
            $discount = 0;
            $discountMsg = null;

            if ($promoCode && $promoCode->isValid() && $subtotal >= $promoCode->min_booking_amount) {
                $discount = $promoCode->calculateDiscount($subtotal);
                $discountMsg = "Promo {$promoCode->code}: -ZMW {$discount}";
            } elseif ($promoCode && !$promoCode->isValid()) {
                $discountMsg = 'Promo code is expired or inactive.';
            } elseif ($promoCode && $subtotal < $promoCode->min_booking_amount) {
                $discountMsg = 'Min. booking amount not met.';
            } elseif ($previewCode) {
                $discountMsg = 'Promo code not found.';
            }

            $total = round($subtotal - $discount, 2);

            $previewResult = [
                'base_fare' => $baseFare,
                'service_fees' => $feeDetails,
                'service_fee_total' => $feeTotal,
                'discount' => $discount,
                'discount_msg' => $discountMsg,
                'total' => $total,
            ];
        }

        return view('operator.fare_rules', compact(
            'operator',
            'cancellationRules',
            'serviceFees',
            'previewResult',
        ));
    }

    // ─── Cancellation Rules ─────────────────────────────────

    public function storeCancellationRule(Request $request)
    {
        $operator = $this->getOperator();
        if (!$operator) {
            return redirect()->route('operator.login')->with('error', 'Please log in.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'hours_before_departure' => 'required|integer|min:0',
            'refund_percentage' => 'required|numeric|min:0|max:100',
        ]);

        $rule = CancellationRule::create([
            'operator_id' => $operator->id,
            'name' => $validated['name'],
            'hours_before_departure' => $validated['hours_before_departure'],
            'refund_percentage' => $validated['refund_percentage'],
            'is_active' => true,
        ]);

        OperatorAuditService::created($rule, "Created cancellation rule: {$rule->name} ({$rule->hours_before_departure}h, {$rule->refund_percentage}% refund)");

        return redirect()->route('operator.fare-rules.index')
            ->with('success', 'Cancellation rule created successfully.');
    }

    public function updateCancellationRule(Request $request, $id)
    {
        $operator = $this->getOperator();
        $rule = CancellationRule::where('operator_id', $operator->id)->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'hours_before_departure' => 'required|integer|min:0',
            'refund_percentage' => 'required|numeric|min:0|max:100',
            'is_active' => 'boolean',
        ]);

        $rule->update($validated);

        OperatorAuditService::updated($rule, $rule->getOriginal(), "Updated cancellation rule: {$rule->name}");

        return redirect()->route('operator.fare-rules.index')
            ->with('success', 'Cancellation rule updated successfully.');
    }

    public function destroyCancellationRule($id)
    {
        $operator = $this->getOperator();
        $rule = CancellationRule::where('operator_id', $operator->id)->findOrFail($id);
        $rule->delete();

        OperatorAuditService::deleted($rule, "Deleted cancellation rule: {$rule->name}");

        return redirect()->route('operator.fare-rules.index')
            ->with('success', 'Cancellation rule deleted.');
    }

    // ─── Service Fees ──────────────────────────────────────

    public function storeServiceFee(Request $request)
    {
        $operator = $this->getOperator();
        if (!$operator) {
            return redirect()->route('operator.login')->with('error', 'Please log in.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'fee_type' => 'required|in:fixed,percentage',
            'fee_value' => 'required|numeric|min:0',
        ]);

        $fee = ServiceFee::create([
            'operator_id' => $operator->id,
            'name' => $validated['name'],
            'fee_type' => $validated['fee_type'],
            'fee_value' => $validated['fee_value'],
            'is_active' => true,
        ]);

        $typeLabel = $validated['fee_type'] === 'fixed' ? 'ZMW' : '%';
        OperatorAuditService::created($fee, "Created service fee: {$fee->name} ({$typeLabel}{$fee->fee_value})");

        return redirect()->route('operator.fare-rules.index')
            ->with('success', 'Service fee created successfully.');
    }

    public function updateServiceFee(Request $request, $id)
    {
        $operator = $this->getOperator();
        $fee = ServiceFee::where('operator_id', $operator->id)->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'fee_type' => 'required|in:fixed,percentage',
            'fee_value' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $fee->update($validated);

        OperatorAuditService::updated($fee, $fee->getOriginal(), "Updated service fee: {$fee->name}");

        return redirect()->route('operator.fare-rules.index')
            ->with('success', 'Service fee updated successfully.');
    }

    public function destroyServiceFee($id)
    {
        $operator = $this->getOperator();
        $fee = ServiceFee::where('operator_id', $operator->id)->findOrFail($id);
        $fee->delete();

        OperatorAuditService::deleted($fee, "Deleted service fee: {$fee->name}");

        return redirect()->route('operator.fare-rules.index')
            ->with('success', 'Service fee deleted.');
    }
}