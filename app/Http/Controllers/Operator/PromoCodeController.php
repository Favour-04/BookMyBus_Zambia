<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\Operator;
use App\Models\PromoCode;
use App\Services\OperatorAuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PromoCodeController extends Controller
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
     * Display promo codes management page.
     */
    public function index()
    {
        $operator = $this->getOperator();
        if (!$operator) {
            return redirect()->route('operator.login')->with('error', 'Please log in.');
        }

        $promoCodes = PromoCode::where('operator_id', $operator->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('operator.promo_codes', compact('operator', 'promoCodes'));
    }

    /**
     * Store a new promo code.
     */
    public function store(Request $request)
    {
        $operator = $this->getOperator();
        if (!$operator) {
            return redirect()->route('operator.login')->with('error', 'Please log in.');
        }

        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:promo_codes,code',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'min_booking_amount' => 'nullable|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'valid_from' => 'required|date',
            'valid_until' => 'required|date|after:valid_from',
        ]);

        $promoCode = PromoCode::create([
            'operator_id' => $operator->id,
            'code' => strtoupper($validated['code']),
            'discount_type' => $validated['discount_type'],
            'discount_value' => $validated['discount_value'],
            'min_booking_amount' => $validated['min_booking_amount'] ?? 0,
            'max_discount_amount' => $validated['max_discount_amount'] ?? null,
            'usage_limit' => $validated['usage_limit'] ?? null,
            'valid_from' => $validated['valid_from'],
            'valid_until' => $validated['valid_until'],
            'is_active' => true,
        ]);

        OperatorAuditService::created($promoCode, "Created promo code: {$promoCode->code}");

        return redirect()->route('operator.promo-codes.index')
            ->with('success', "Promo code {$promoCode->code} created successfully.");
    }

    /**
     * Update a promo code.
     */
    public function update(Request $request, $id)
    {
        $operator = $this->getOperator();
        $promoCode = PromoCode::where('operator_id', $operator->id)->findOrFail($id);

        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:promo_codes,code,' . $id,
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'min_booking_amount' => 'nullable|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'valid_from' => 'required|date',
            'valid_until' => 'required|date|after:valid_from',
            'is_active' => 'boolean',
        ]);

        $validated['code'] = strtoupper($validated['code']);
        $promoCode->update($validated);

        OperatorAuditService::updated($promoCode, $promoCode->getOriginal(), "Updated promo code: {$promoCode->code}");

        return redirect()->route('operator.promo-codes.index')
            ->with('success', "Promo code {$promoCode->code} updated successfully.");
    }

    /**
     * Delete a promo code.
     */
    public function destroy($id)
    {
        $operator = $this->getOperator();
        $promoCode = PromoCode::where('operator_id', $operator->id)->findOrFail($id);
        $code = $promoCode->code;
        $promoCode->delete();

        OperatorAuditService::deleted($promoCode, "Deleted promo code: {$code}");

        return redirect()->route('operator.promo-codes.index')
            ->with('success', "Promo code {$code} deleted.");
    }
}