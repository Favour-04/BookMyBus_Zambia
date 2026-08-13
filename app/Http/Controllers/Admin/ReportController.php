<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Analytics overview (revenue & bookings trends, reports).
     */
    public function index()
    {
        // Last 12 months trends
        $labels = [];
        $revenueData = [];
        $bookingsData = [];
        for ($i = 11; $i >= 0; $i--) {
            $m = Carbon::now()->subMonths($i);
            $labels[]       = $m->format('M Y');
            $revenueData[]  = (float) Payment::where('status', 'successful')
                ->whereYear('paid_at', $m->year)->whereMonth('paid_at', $m->month)->sum('amount');
            $bookingsData[] = Booking::whereYear('created_at', $m->year)->whereMonth('created_at', $m->month)->count();
        }

        // Revenue by operator (successful payments)
        $revenueByOperator = DB::table('payments')
            ->join('bookings', 'bookings.id', '=', 'payments.booking_id')
            ->join('routes', 'routes.id', '=', 'bookings.route_id')
            ->join('operators', 'operators.id', '=', 'routes.operator_id')
            ->where('payments.status', 'successful')
            ->whereNull('bookings.deleted_at')
            ->whereNull('routes.deleted_at')
            ->groupBy(['operators.id', 'operators.company_name'])
            ->select(
                'operators.id',
                'operators.company_name',
                DB::raw('COUNT(payments.id) as tx_count'),
                DB::raw('COALESCE(SUM(payments.amount), 0) as revenue')
            )
            ->orderByDesc('revenue')
            ->limit(10)
            ->get();

        // Revenue by route
        $revenueByRoute = DB::table('payments')
            ->join('bookings', 'bookings.id', '=', 'payments.booking_id')
            ->join('routes', 'routes.id', '=', 'bookings.route_id')
            ->where('payments.status', 'successful')
            ->whereNull('bookings.deleted_at')
            ->whereNull('routes.deleted_at')
            ->groupBy(['routes.id', 'routes.origin', 'routes.destination'])
            ->select(
                'routes.id',
                'routes.origin',
                'routes.destination',
                DB::raw('COUNT(payments.id) as tx_count'),
                DB::raw('COALESCE(SUM(payments.amount), 0) as revenue')
            )
            ->orderByDesc('revenue')
            ->limit(10)
            ->get();

        // Payment method split
        $paymentMethods = Payment::where('status', 'successful')
            ->selectRaw("COALESCE(NULLIF(payment_method, ''), 'Unknown') as channel, COUNT(*) as total")
            ->groupBy('channel')
            ->orderByDesc('total')
            ->pluck('total', 'channel');

        $statusBreakdown = [
            'confirmed' => Booking::where('status', 'confirmed')->count(),
            'pending'   => Booking::where('status', 'pending')->count(),
            'cancelled' => Booking::where('status', 'cancelled')->count(),
        ];

        $totals = [
            'revenue'  => Payment::where('status', 'successful')->sum('amount'),
            'bookings' => Booking::count(),
            'operators'=> \App\Models\Operator::count(),
        ];

        return view('admin.reports.index', compact(
            'labels',
            'revenueData',
            'bookingsData',
            'revenueByOperator',
            'revenueByRoute',
            'paymentMethods',
            'statusBreakdown',
            'totals'
        ));
    }

    /**
     * Export revenue-by-operator report as CSV.
     */
    public function export()
    {
        $rows = DB::table('payments')
            ->join('bookings', 'bookings.id', '=', 'payments.booking_id')
            ->join('routes', 'routes.id', '=', 'bookings.route_id')
            ->join('operators', 'operators.id', '=', 'routes.operator_id')
            ->where('payments.status', 'successful')
            ->whereNull('bookings.deleted_at')
            ->whereNull('routes.deleted_at')
            ->groupBy(['operators.id', 'operators.company_name'])
            ->select(
                'operators.company_name',
                DB::raw('COUNT(payments.id) as tx_count'),
                DB::raw('COALESCE(SUM(payments.amount), 0) as revenue')
            )
            ->orderByDesc('revenue')
            ->get();

        $filename = 'revenue-by-operator-' . now()->format('Y-m-d') . '.csv';

        $handle = fopen('php://output', 'w');
        ob_start();
        fputcsv($handle, ['Operator', 'Successful Transactions', 'Revenue (ZMW)']);
        foreach ($rows as $row) {
            fputcsv($handle, [$row->company_name, $row->tx_count, number_format((float) $row->revenue, 2, '.', '')]);
        }
        $csv = ob_get_clean();
        fclose($handle);

        return response($csv, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}