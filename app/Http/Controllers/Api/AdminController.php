<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Operator;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AdminController extends Controller
{
    /*
      System overview dashboard stats.
     */
    public function dashboard(): JsonResponse
    {
        return response()->json([
            'total_users'              => User::where('role', 'traveler')->count(),
            'total_operators'          => Operator::count(),
            'pending_operator_approvals' => Operator::where('is_verified', false)->count(),
            'total_bookings'           => Booking::count(),
            'confirmed_bookings'       => Booking::where('status', 'confirmed')->count(),
            'total_revenue_zmw'        => Payment::where('status', 'successful')->sum('amount'),
        ]);
    }

    /*
      List of all operators (whether verified and pending).
     */
    public function operators(Request $request): JsonResponse
    {
        $status    = $request->query('status', 'all');
        $query     = Operator::withCount(['buses', 'routes']);

        if ($status === 'pending') {
            $query->where('is_verified', false);
        } elseif ($status === 'verified') {
            $query->where('is_verified', true);
        }

        return response()->json(['operators' => $query->get()]);
    }

    /*
     Function for approving and verifying a bus operator.
     */
    public function verifyOperator(Request $request, int $id): JsonResponse
    {
        $operator = Operator::findOrFail($id);

        if ($operator->is_verified) {
            return response()->json(['message' => 'Operator is already verified.'], 409);
        }

        $operator->update([
            'is_verified' => true,
            'verified_at' => now(),
            'verified_by' => $request->user()->id,
        ]);

        return response()->json([
            'message'  => 'Operator verified successfully.',
            'operator' => $operator,
        ]);
    }

    /*
     Suspend / unverify an operator.
     */
    public function suspendOperator(int $id): JsonResponse
    {
        $operator = Operator::findOrFail($id);

        $operator->update([
            'is_verified' => false,
            'verified_at' => null,
            'verified_by' => null,
        ]);

        return response()->json(['message' => 'Operator suspended successfully.']);
    }


     // List all travelers.

    public function users(): JsonResponse
    {
        $users = User::where('role', 'traveler')
            ->withCount('bookings')
            ->orderByDesc('created_at')
            ->get();

        return response()->json(['users' => $users]);
    }

    //List all bookings system-wide.

    public function bookings(Request $request): JsonResponse
    {
        $status   = $request->query('status');
        $query    = Booking::with(['user', 'route.operator', 'payment'])
                           ->orderByDesc('created_at');

        if ($status) {
            $query->where('status', $status);
        }

        return response()->json(['bookings' => $query->paginate(50)]);
    }

    /*
      System reports (chart data) for admin dashboard.
     */
    public function systemReports(Request $request): JsonResponse
    {
        $start = $request->query('from');
        $end   = $request->query('to');

        $query = Booking::query();

        if ($start) {
            $query->whereDate('created_at', '>=', $start);
        }
        if ($end) {
            $query->whereDate('created_at', '<=', $end);
        }

        $totalBookings = $query->count();
        $totalRevenue  = $query->whereHas('payment', function ($q) {
            $q->where('status', 'successful');
        })->join('payments', 'bookings.id', '=', 'payments.booking_id')
          ->sum('payments.amount');

        // Monthly revenue + bookings for last 12 months
        $months = [];
        $revenueData = [];
        $bookingData = [];
        $current = now()->startOfMonth();

        for ($i = 11; $i >= 0; $i--) {
            $m = $current->copy()->subMonths($i);
            $months[] = $m->format('M');

            $revenueData[] = (clone $query)
                ->whereYear('bookings.created_at', $m->year)
                ->whereMonth('bookings.created_at', $m->month)
                ->whereHas('payment', fn($q) => $q->where('status', 'successful'))
                ->join('payments', 'bookings.id', '=', 'payments.booking_id')
                ->sum('payments.amount');

            $bookingData[] = (clone $query)
                ->whereYear('bookings.created_at', $m->year)
                ->whereMonth('bookings.created_at', $m->month)
                ->count();
        }

        // Popular routes
        $popularRoutes = Booking::selectRaw('route_id, COUNT(*) as bookings')
            ->groupBy('route_id')
            ->orderByDesc('bookings')
            ->limit(5)
            ->get()
            ->map(function ($item) use ($totalBookings) {
                $route = \App\Models\Route::find($item->route_id);
                return [
                    'route'      => $route ? $route->origin . ' → ' . $route->destination : 'Route #' . $item->route_id,
                    'bookings'   => $item->bookings,
                    'percentage' => min(100, round(($item->bookings / max(1, $totalBookings)) * 100)),
                ];
            });

        // Top operators by revenue
        $topOperators = Operator::select('operators.id', 'operators.name')
            ->join('routes', 'routes.operator_id', '=', 'operators.id')
            ->join('bookings', 'bookings.route_id', '=', 'routes.id')
            ->join('payments', 'payments.booking_id', '=', 'bookings.id')
            ->where('payments.status', 'successful')
            ->groupBy('operators.id', 'operators.name')
            ->selectRaw('SUM(payments.amount) as revenue, COUNT(bookings.id) as bookings')
            ->orderByDesc('revenue')
            ->limit(5)
            ->get()
            ->map(fn($item) => [
                'name'     => $item->name,
                'bookings' => $item->bookings,
                'revenue'  => (int) $item->revenue,
            ]);

        return response()->json([
            'totalUsers'        => User::where('role', 'traveler')->count(),
            'userGrowth'        => (clone $query)->whereYear('created_at', now()->year)->count(),
            'totalOperators'    => Operator::count(),
            'operatorGrowth'    => Operator::where('created_at', '>=', now()->subMonths(1))->count(),
            'totalBookings'     => $totalBookings,
            'bookingGrowth'     => (clone $query)->where('created_at', '>=', now()->subMonths(1))->count(),
            'totalRevenue'      => (int) $totalRevenue,
            'revenueGrowth'     => 0,
            'revenueData'       => $revenueData,
            'bookingData'       => $bookingData,
            'popularRoutes'     => $popularRoutes,
            'topOperators'      => $topOperators,
        ]);
    }
}
