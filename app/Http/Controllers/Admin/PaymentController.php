<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Operator;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * List all payment transactions across the platform.
     */
    public function index(Request $request)
    {
        $query = Payment::with(['booking.route.operator', 'booking.user']);

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        if ($request->filled('method')) {
            $query->where('payment_method', $request->method);
        }
        if ($request->filled('operator_id')) {
            $query->whereHas('booking.route', fn ($q) => $q->where('operator_id', $request->operator_id));
        }
        if ($request->filled('from')) {
            $query->whereDate('paid_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('paid_at', '<=', $request->to);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('transaction_reference', 'like', "%{$s}%")
                    ->orWhereHas('booking', fn ($b) => $b->where('reference_id', 'like', "%{$s}%"));
            });
        }

        $payments = $query->orderByDesc('paid_at')->paginate(25)->withQueryString();

        $stats = [
            'successful' => Payment::where('status', 'successful')->count(),
            'failed'     => Payment::where('status', 'failed')->count(),
            'pending'    => Payment::where('status', 'pending')->count(),
            'revenue'    => Payment::where('status', 'successful')->sum('amount'),
        ];

        $methods   = Payment::select('payment_method')->whereNotNull('payment_method')->distinct()->pluck('payment_method');
        $operators = Operator::orderBy('company_name')->get(['id', 'company_name']);

        return view('admin.payments.index', compact('payments', 'stats', 'methods', 'operators'));
    }
}