<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Operator;
use App\Services\AdminAuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OperatorController extends Controller
{
    /**
     * List all operators with verification filtering and search.
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        $search = trim((string) $request->get('search'));

        $query = Operator::withCount(['buses', 'routes'])
            ->withCount(['routes as active_routes_count' => function ($q) {
                $q->where('is_active', true);
            }]);

        if ($status === 'verified') {
            $query->where('is_verified', true);
        } elseif ($status === 'pending') {
            $query->where('is_verified', false);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('tpin', 'like', "%{$search}%")
                    ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }

        $operators = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        $counts = [
            'all'      => Operator::count(),
            'verified' => Operator::where('is_verified', true)->count(),
            'pending'  => Operator::where('is_verified', false)->count(),
        ];
        $fleetTotal = Operator::withCount('buses')->get()->sum('buses_count');

        return view('admin.operators.index', compact('operators', 'status', 'search', 'counts', 'fleetTotal'));
    }

    /**
     * Show a single operator's profile, documents and activity.
     */
    public function show($id)
    {
        $operator = Operator::with(['verifiedBy'])
            ->withCount(['buses', 'routes'])
            ->withCount(['routes as active_routes_count' => fn ($q) => $q->where('is_active', true)])
            ->findOrFail($id);

        $stats = $this->operatorStats($operator->id);

        $recentBookings = Booking::with(['route'])
            ->whereHas('route', fn ($q) => $q->where('operator_id', $operator->id))
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('admin.operators.show', compact('operator', 'stats', 'recentBookings'));
    }

    /**
     * Approve & verify an operator.
     */
    public function verify(Request $request, $id)
    {
        $operator = Operator::findOrFail($id);

        if ($operator->is_verified) {
            return back()->with('warning', "{$operator->company_name} is already verified.");
        }

        $operator->update([
            'is_verified' => true,
            'verified_at' => now(),
            'verified_by' => Auth::guard('admin')->id(),
        ]);

        AdminAuditService::log(
            'operator.verified',
            "Verified and approved {$operator->company_name}",
            $operator,
            null,
            ['is_verified' => true, 'verified_at' => now()->toDateTimeString(), 'verified_by' => Auth::guard('admin')->id()],
            $request
        );

        return back()->with('success', "{$operator->company_name} has been verified and approved.");
    }

    /**
     * Suspend / unverify an operator.
     */
    public function suspend(Request $request, $id)
    {
        $operator = Operator::findOrFail($id);

        if (!$operator->is_verified) {
            return back()->with('warning', "{$operator->company_name} is not verified yet.");
        }

        $operator->update([
            'is_verified' => false,
            'verified_at' => null,
            'verified_by' => null,
        ]);

        AdminAuditService::log(
            'operator.suspended',
            "Suspended {$operator->company_name}",
            $operator,
            ['is_verified' => true],
            ['is_verified' => false, 'verified_at' => null, 'verified_by' => null],
            $request
        );

        return back()->with('success', "{$operator->company_name} has been suspended.");
    }

    /**
     * Soft delete an operator.
     */
    public function destroy(Request $request, $id)
    {
        $operator = Operator::findOrFail($id);
        $name     = $operator->company_name;

        AdminAuditService::log(
            'operator.deleted',
            "Removed {$name}",
            $operator,
            $operator->only(['is_verified', 'email', 'company_name']),
            ['deleted_at' => now()->toDateTimeString()],
            $request
        );

        $operator->delete();

        return redirect()->route('admin.operators.index')
            ->with('success', "{$name} has been removed.");
    }

    /**
     * Aggregate booking/usage stats for an operator.
     */
    private function operatorStats(int $operatorId): array
    {
        $scoped = fn ($q) => $q->where('operator_id', $operatorId);
        $booked = fn ($q) => $q->whereHas('route', $scoped);

        $confirmed = Booking::whereHas('route', $scoped)->where('status', 'confirmed');

        return [
            'total_bookings' => Booking::whereHas('route', $scoped)->count(),
            'confirmed'      => (clone $confirmed)->count(),
            'pending'        => Booking::whereHas('route', $scoped)->where('status', 'pending')->count(),
            'cancelled'      => Booking::whereHas('route', $scoped)->where('status', 'cancelled')->count(),
            'revenue'        => (clone $confirmed)->sum('amount'),
        ];
    }
}