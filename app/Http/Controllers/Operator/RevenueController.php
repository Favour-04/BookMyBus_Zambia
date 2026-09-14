<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Operator;
use App\Models\Payment;
use App\Models\Route;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RevenueController extends Controller
{
    public function index(Request $request)
    {
        $operator = $this->getOperator();
        $operatorId = $operator->id;

        // Date range filter
        $dateFrom = $request->input('date_from', Carbon::now()->startOfMonth()->toDateString());
        $dateTo = $request->input('date_to', Carbon::now()->toDateString());

        // Period filter (overrides date range if set)
        $period = $request->input('period', 'this_month');
        if ($period && !$request->filled('date_from')) {
            $dateFrom = match ($period) {
                'today'      => Carbon::today()->toDateString(),
                'yesterday'  => Carbon::yesterday()->toDateString(),
                'this_week'  => Carbon::now()->startOfWeek()->toDateString(),
                'this_month' => Carbon::now()->startOfMonth()->toDateString(),
                'last_month' => Carbon::now()->subMonth()->startOfMonth()->toDateString(),
                'this_year'  => Carbon::now()->startOfYear()->toDateString(),
                default      => Carbon::now()->startOfMonth()->toDateString(),
            };
            $dateTo = match ($period) {
                'today'      => Carbon::today()->toDateString(),
                'yesterday'  => Carbon::yesterday()->toDateString(),
                'this_week'  => Carbon::now()->endOfWeek()->toDateString(),
                'this_month' => Carbon::now()->endOfMonth()->toDateString(),
                'last_month' => Carbon::now()->subMonth()->endOfMonth()->toDateString(),
                'this_year'  => Carbon::now()->endOfYear()->toDateString(),
                default      => Carbon::now()->toDateString(),
            };
            $dateTo = Carbon::parse($dateTo)->toDateString();
        }

        // --- KPIs ---

        // Total confirmed revenue (all time)
        $totalRevenue = Booking::whereHas('route', fn($q) => $q->where('operator_id', $operatorId))
            ->where('status', 'confirmed')
            ->sum('amount');

        // Revenue in date range
        $periodRevenue = Booking::whereHas('route', function ($q) use ($operatorId, $dateFrom, $dateTo) {
                $q->where('operator_id', $operatorId)
                  ->whereBetween('travel_date', [$dateFrom, $dateTo]);
            })
            ->where('status', 'confirmed')
            ->sum('amount');

        // Revenue today
        $revenueToday = Booking::whereHas('route', function ($q) use ($operatorId) {
                $q->where('operator_id', $operatorId)
                  ->where('travel_date', Carbon::today());
            })
            ->where('status', 'confirmed')
            ->sum('amount');

        // Pending payments (bookings not yet confirmed)
        $pendingAmount = Booking::whereHas('route', fn($q) => $q->where('operator_id', $operatorId))
            ->where('status', 'pending')
            ->where('held_until', '>', now())
            ->sum('amount');

        // Total confirmed bookings
        $confirmedCount = Booking::whereHas('route', fn($q) => $q->where('operator_id', $operatorId))
            ->where('status', 'confirmed')
            ->count();

        // Cancelled revenue (total amount of cancelled bookings)
        $cancelledAmount = Booking::whereHas('route', fn($q) => $q->where('operator_id', $operatorId))
            ->where('status', 'cancelled')
            ->sum('amount');

        // Number of trips in date range
        $tripsCount = Route::where('operator_id', $operatorId)
            ->whereBetween('travel_date', [$dateFrom, $dateTo])
            ->count();

        $avgPerTrip = $tripsCount > 0 ? $periodRevenue / $tripsCount : 0;

        // --- Revenue by Route (in date range) ---
        $revenueByRoute = Route::where('operator_id', $operatorId)
            ->whereBetween('travel_date', [$dateFrom, $dateTo])
            ->get()
            ->map(function ($route) {
                $revenue = $route->bookings()
                    ->where('status', 'confirmed')
                    ->sum('amount');
                $count = $route->bookings()
                    ->where('status', 'confirmed')
                    ->count();
                return [
                    'route'      => $route->origin . ' → ' . $route->destination,
                    'travel_date' => $route->travel_date instanceof Carbon
                        ? $route->travel_date->format('d M Y')
                        : Carbon::parse($route->travel_date)->format('d M Y'),
                    'revenue'    => $revenue,
                    'bookings'   => $count,
                ];
            })
            ->filter(fn($r) => $r['revenue'] > 0)
            ->sortByDesc('revenue')
            ->values()
            ->take(10);

        $topRoute = $revenueByRoute->first();
        $topRouteName = $topRoute['route'] ?? 'N/A';
        $topRouteRevenue = $topRoute['revenue'] ?? 0;

        // --- Revenue by Payment Method (in date range) ---
        $revenueByMethod = Payment::whereHas('booking.route', function ($q) use ($operatorId, $dateFrom, $dateTo) {
                $q->where('operator_id', $operatorId)
                  ->whereBetween('travel_date', [$dateFrom, $dateTo]);
            })
            ->where('status', 'successful')
            ->selectRaw('payment_method, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('payment_method')
            ->get()
            ->map(function ($item) {
                return [
                    'method' => str_replace('_', ' ', ucfirst($item->payment_method)),
                    'total'  => $item->total,
                    'count'  => $item->count,
                ];
            })
            ->toArray();

        // --- Daily Revenue for Current Month (chart data) ---
        $chartStart = Carbon::parse($dateFrom);
        $chartEnd = Carbon::parse($dateTo);
        $dailyRevenue = collect();
        $current = $chartStart->copy();

        while ($current->lte($chartEnd)) {
            $dayTotal = Booking::whereHas('route', function ($q) use ($operatorId, $current) {
                    $q->where('operator_id', $operatorId)
                      ->where('travel_date', $current->toDateString());
                })
                ->where('status', 'confirmed')
                ->sum('amount');

            $dailyRevenue->push([
                'date'  => $current->format('d M'),
                'total' => $dayTotal,
                'day'   => $current->format('D'),
            ]);

            $current->addDay();
        }

        $chartMax = $dailyRevenue->max('total') ?: 1;

        // --- Recent Transactions ---
        $recentTransactions = Payment::whereHas('booking.route', function ($q) use ($operatorId) {
                $q->where('operator_id', $operatorId);
            })
            ->where('status', 'successful')
            ->with(['booking' => fn($q) => $q->with('route')])
            ->orderBy('paid_at', 'desc')
            ->take(15)
            ->get()
            ->map(function ($payment) {
                return [
                    'id'           => $payment->id,
                    'reference'    => $payment->booking->reference_id ?? 'N/A',
                    'passenger'    => $payment->booking->passenger_name ?? 'N/A',
                    'route'        => $payment->booking->route
                        ? $payment->booking->route->origin . ' → ' . $payment->booking->route->destination
                        : 'N/A',
                    'method'       => str_replace('_', ' ', ucfirst($payment->payment_method)),
                    'amount'       => $payment->amount,
                    'date'         => $payment->paid_at ? $payment->paid_at->format('d M Y H:i') : 'N/A',
                    'booking_id'   => $payment->booking_id,
                ];
            });

        return view('operator.revenue', compact(
            'operator',
            'totalRevenue',
            'periodRevenue',
            'revenueToday',
            'pendingAmount',
            'confirmedCount',
            'cancelledAmount',
            'period',
            'dateFrom',
            'dateTo',
            'tripsCount',
            'avgPerTrip',
            'revenueByRoute',
            'topRouteName',
            'topRouteRevenue',
            'revenueByMethod',
            'dailyRevenue',
            'chartMax',
            'recentTransactions',
        ));
    }

    // ── Helper Methods ──────────────────────────────────

    private function getOperator()
    {
        if (Auth::guard('operator')->check()) {
            $operator = Auth::guard('operator')->user();
            session(['operator_id' => $operator->id]);
            return $operator;
        }
        if (session('operator_id')) {
            $operator = Operator::find(session('operator_id'));
            if ($operator) {
                return $operator;
            }
        }
        abort(403, 'Operator session expired. Please log in again.');
}
}