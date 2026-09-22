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
     * Analytics overview (revenue & bookings trends, reports), optionally
     * scoped to a date range and/or operator via GET filters.
     */
    public function index(Request $request)
    {
        $filters = $this->validatedFilters($request);

        $data = $this->buildReportData($filters['from'] ?? null, $filters['to'] ?? null, $filters['operator_id'] ?? null);
        $operators = \App\Models\Operator::orderBy('company_name')->pluck('company_name', 'id');

        return view('admin.reports.index', array_merge($data, ['operators' => $operators, 'filters' => $filters]));
    }

    /**
     * Export revenue-by-operator report as CSV, honouring the same
     * date-range/operator filters as the reports dashboard.
     */
    public function export(Request $request)
    {
        $filters = $this->validatedFilters($request);
        [$from, $to] = $this->normaliseRange($filters['from'] ?? null, $filters['to'] ?? null);

        $rows = DB::table('payments')
            ->join('bookings', 'bookings.id', '=', 'payments.booking_id')
            ->join('routes', 'routes.id', '=', 'bookings.route_id')
            ->join('operators', 'operators.id', '=', 'routes.operator_id')
            ->where('payments.status', 'successful')
            ->whereNull('bookings.deleted_at')
            ->whereNull('routes.deleted_at')
            ->when($from, fn ($q) => $q->where('payments.paid_at', '>=', $from))
            ->when($to, fn ($q) => $q->where('payments.paid_at', '<=', $to))
            ->when($filters['operator_id'] ?? null, fn ($q, $id) => $q->where('routes.operator_id', $id))
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

    /**
     * Validate the from/to/operator_id filters shared by index() and export().
     */
    private function validatedFilters(Request $request): array
    {
        return $request->validate([
            'from'        => ['nullable', 'date'],
            'to'          => ['nullable', 'date'],
            'operator_id' => ['nullable', 'integer', 'exists:operators,id'],
        ]);
    }

    /**
     * Build every dataset the reports dashboard needs, scoped to an optional
     * date range (payments.paid_at / bookings.created_at) and/or operator.
     * Trend charts always show the trailing 12 months regardless of filters.
     */
    private function buildReportData(?string $from, ?string $to, ?int $operatorId): array
    {
        [$from, $to] = $this->normaliseRange($from, $to);

        // Last 12 months trends (always the trailing 12 months)
        $labels = [];
        $revenueData = [];
        $bookingsData = [];
        for ($i = 11; $i >= 0; $i--) {
            $m = Carbon::now()->subMonths($i);
            $labels[] = $m->format('M Y');
            $revenueData[] = (float) Payment::where('status', 'successful')
                ->whereYear('paid_at', $m->year)->whereMonth('paid_at', $m->month)->sum('amount');
            $bookingsData[] = Booking::whereYear('created_at', $m->year)->whereMonth('created_at', $m->month)->count();
        }

        $payments = $this->scopedPaymentsQuery($from, $to, $operatorId);

        $revenueByOperator = (clone $payments)
            ->groupBy(['operators.id', 'operators.company_name'])
            ->select('operators.id', 'operators.company_name', DB::raw('COUNT(payments.id) as tx_count'), DB::raw('COALESCE(SUM(payments.amount), 0) as revenue'))
            ->orderByDesc('revenue')->limit(10)->get();

        $revenueByRoute = (clone $payments)
            ->groupBy(['routes.id', 'routes.origin', 'routes.destination'])
            ->select('routes.id', 'routes.origin', 'routes.destination', DB::raw('COUNT(payments.id) as tx_count'), DB::raw('COALESCE(SUM(payments.amount), 0) as revenue'))
            ->orderByDesc('revenue')->limit(10)->get();

        $paymentMethods = (clone $payments)
            ->selectRaw("COALESCE(NULLIF(payment_method, ''), 'Unknown') as channel, COUNT(*) as total")
            ->groupBy('channel')->orderByDesc('total')->pluck('total', 'channel');

        $bookings = $this->scopedBookingsQuery($from, $to, $operatorId);

        $statusBreakdown = [
            'confirmed' => (clone $bookings)->where('bookings.status', 'confirmed')->count(),
            'pending'   => (clone $bookings)->where('bookings.status', 'pending')->count(),
            'cancelled' => (clone $bookings)->where('bookings.status', 'cancelled')->count(),
        ];

        $totals = [
            'revenue'   => (float) (clone $payments)->sum('payments.amount'),
            'bookings'  => (clone $bookings)->count(),
            'operators' => \App\Models\Operator::count(),
        ];

        return compact('labels', 'revenueData', 'bookingsData', 'revenueByOperator', 'revenueByRoute', 'paymentMethods', 'statusBreakdown', 'totals');
    }

    /**
     * Successful payments joined to booking/route/operator, scoped by range + operator.
     */
    private function scopedPaymentsQuery($from, $to, ?int $operatorId)
    {
        $q = DB::table('payments')
            ->join('bookings', 'bookings.id', '=', 'payments.booking_id')
            ->join('routes', 'routes.id', '=', 'bookings.route_id')
            ->join('operators', 'operators.id', '=', 'routes.operator_id')
            ->where('payments.status', 'successful')
            ->whereNull('bookings.deleted_at')
            ->whereNull('routes.deleted_at');

        if ($from) { $q->where('payments.paid_at', '>=', $from); }
        if ($to)   { $q->where('payments.paid_at', '<=', $to); }
        if ($operatorId) { $q->where('routes.operator_id', $operatorId); }

        return $q;
    }

    /**
     * Bookings joined to route, scoped by range (bookings.created_at) + operator.
     */
    private function scopedBookingsQuery($from, $to, ?int $operatorId)
    {
        $q = DB::table('bookings')
            ->join('routes', 'routes.id', '=', 'bookings.route_id')
            ->whereNull('bookings.deleted_at')
            ->whereNull('routes.deleted_at');

        if ($from) { $q->where('bookings.created_at', '>=', $from); }
        if ($to)   { $q->where('bookings.created_at', '<=', $to); }
        if ($operatorId) { $q->where('routes.operator_id', $operatorId); }

        return $q;
    }

    /**
     * Normalise the from/to dates to day bounds; swap if reversed.
     */
    private function normaliseRange(?string $from, ?string $to): array
    {
        $from = $from ? Carbon::parse($from)->startOfDay() : null;
        $to   = $to   ? Carbon::parse($to)->endOfDay()   : null;

        if ($from && $to && $from->gt($to)) {
            return [$to, $from];
        }

        return [$from, $to];
    }
}