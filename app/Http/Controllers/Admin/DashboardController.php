<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Bus;
use App\Models\Operator;
use App\Models\Payment;
use App\Models\Route;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard with system-wide statistics.
     */
    public function index()
    {
        $totalBookings      = Booking::count();
        $confirmedBookings  = Booking::where('status', 'confirmed')->count();
        $pendingBookings    = Booking::where('status', 'pending')->count();
        $cancelledBookings  = Booking::where('status', 'cancelled')->count();

        $totalRevenue   = Booking::where('status', 'confirmed')->sum('amount');
        $avgBookingValue = $confirmedBookings > 0
            ? Booking::where('status', 'confirmed')->avg('amount')
            : 0;

        $stats = [
            'total_users'           => User::where('role', 'traveler')->count(),
            'total_operators'       => Operator::count(),
            'pending_verifications' => Operator::where('is_verified', false)->count(),
            'total_bookings'        => $totalBookings,
            'confirmed_bookings'    => $confirmedBookings,
            'total_revenue'         => $totalRevenue,
            'total_buses'           => Bus::count(),
            'total_routes'          => Route::count(),
            'avg_booking_value'     => $avgBookingValue,
        ];

        // ---- Month-over-month trends (confirmed bookings / revenue) ----
        $revenueThisMonth = Booking::where('status', 'confirmed')
            ->where('created_at', '>=', Carbon::now()->startOfMonth())->sum('amount');
        $revenueLastMonth = Booking::where('status', 'confirmed')
            ->whereBetween('created_at', [
                Carbon::now()->subMonth()->startOfMonth(),
                Carbon::now()->subMonth()->endOfMonth(),
            ])->sum('amount');

        $bookingsThisMonth = Booking::where('created_at', '>=', Carbon::now()->startOfMonth())->count();
        $bookingsLastMonth = Booking::whereBetween('created_at', [
            Carbon::now()->subMonth()->startOfMonth(),
            Carbon::now()->subMonth()->endOfMonth(),
        ])->count();

        $stats['revenue_trend']  = $this->percentageChange($revenueLastMonth, $revenueThisMonth);
        $stats['bookings_trend'] = $this->percentageChange($bookingsLastMonth, $bookingsThisMonth);
// ---- Booking status breakdown ----
        $status_breakdown = [
            ['label' => 'Confirmed', 'count' => $confirmedBookings, 'color' => '#197b30'],
            ['label' => 'Pending',   'count' => $pendingBookings,   'color' => '#c26400'],
            ['label' => 'Cancelled', 'count' => $cancelledBookings, 'color' => '#ba1a1a'],
        ];

        // ---- Payment channel split (successful payments) ----
        $payment_channels = Payment::where('status', 'successful')
            ->selectRaw('COALESCE(NULLIF(payment_method, \'\'), \'Unknown\') as channel, COUNT(*) as total')
            ->groupBy('channel')
            ->orderByDesc('total')
            ->get();

        // ---- Last 30 days revenue + bookings (for charts) ----
        $chartLabels = [];
        $revenueChartData  = [];
        $bookingsChartData = [];
        for ($i = 29; $i >= 0; $i--) {
            $day = Carbon::today()->subDays($i);
            $chartLabels[]       = $day->format('d M');
            $revenueChartData[]  = (float) Booking::where('status', 'confirmed')
                ->whereDate('created_at', $day->toDateString())->sum('amount');
            $bookingsChartData[] = Booking::whereDate('created_at', $day->toDateString())->count();
        }

        // ---- Top operators by confirmed-booking revenue ----
        $top_operators = DB::table('operators')
            ->leftJoin('buses', 'buses.operator_id', '=', 'operators.id')
            ->join('routes', 'routes.operator_id', '=', 'operators.id')
            ->leftJoin('bookings', function ($join) {
                $join->on('bookings.route_id', '=', 'routes.id')
                     ->where('bookings.status', 'confirmed');
            })
            ->whereNull('routes.deleted_at')
            ->whereNull('bookings.deleted_at')
            ->whereNull('buses.deleted_at')
            ->groupBy(['operators.id', 'operators.company_name', 'operators.email', 'operators.is_verified'])
            ->select(
                'operators.id',
                'operators.company_name',
                'operators.email',
                'operators.is_verified',
                DB::raw('COUNT(DISTINCT buses.id) as bus_count'),
                DB::raw('COALESCE(SUM(bookings.amount), 0) as revenue'),
                DB::raw('COUNT(bookings.id) as total_bookings')
            )
            ->orderByDesc('revenue')
            ->take(5)
            ->get();

        $recent_operators    = Operator::latest()->take(5)->get();
        $recent_verifications = Operator::whereNotNull('verified_at')
            ->orderByDesc('verified_at')
            ->take(5)
            ->get();
        $recent_travelers    = User::where('role', 'traveler')->latest()->take(5)->get();
        $recent_bookings     = Booking::with(['route', 'user'])
            ->latest()
            ->take(10)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'status_breakdown',
            'payment_channels',
            'chartLabels',
            'revenueChartData',
            'bookingsChartData',
            'top_operators',
            'recent_operators',
            'recent_verifications',
            'recent_travelers',
            'recent_bookings'
        ));
    }

    /**
     * Compute the percentage change between two numeric baselines.
     */
    private function percentageChange(float $previous, float $current): string
    {
        if ($previous > 0) {
            $value = (($current - $previous) / $previous) * 100;
            return ($value >= 0 ? '+' : '') . number_format($value, 1) . '%';
        }
        return $current > 0 ? '+100%' : '0%';
    }
}