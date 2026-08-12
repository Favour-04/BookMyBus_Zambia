<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Operator;
use App\Services\AdminAuditService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    /**
     * List bookings across the whole platform (every operator).
     */
    public function index(Request $request)
    {
        $query = Booking::with(['route.operator', 'route.bus', 'user', 'payment']);

        // Status filter
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Operator filter
        if ($request->filled('operator_id')) {
            $query->whereHas('route', fn ($q) => $q->where('operator_id', $request->operator_id));
        }

        // Search (reference, passenger, phone, seat)
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('reference_id', 'like', "%{$s}%")
                    ->orWhere('passenger_name', 'like', "%{$s}%")
                    ->orWhere('passenger_phone', 'like', "%{$s}%")
                    ->orWhere('seat_number', $s);
            });
        }

        // Travel-date filter (on the route)
        if ($request->filled('date')) {
            $this->applyDateFilter($query, $request->date);
        }

        // Sort (booking columns only)
        $sortField = $request->get('sort', 'created_at');
        $sortDir   = $request->get('dir', 'desc');
        if (in_array($sortField, ['created_at', 'amount', 'status', 'reference_id', 'seat_number'])) {
            $query->orderBy($sortField, $sortDir === 'asc' ? 'asc' : 'desc');
        }

        $bookings = $query->paginate(25)->withQueryString();

        $stats = [
            'total'     => Booking::count(),
            'confirmed' => Booking::where('status', 'confirmed')->count(),
            'pending'   => Booking::where('status', 'pending')->count(),
            'cancelled' => Booking::where('status', 'cancelled')->count(),
            'revenue'   => Booking::where('status', 'confirmed')->sum('amount'),
        ];

        $operators = Operator::orderBy('company_name')->get(['id', 'company_name']);

        return view('admin.bookings.index', compact('bookings', 'stats', 'operators'));
    }

    /**
     * Show a single booking's full detail (system-wide).
     */
    public function show($id)
    {
        $booking = Booking::with([
            'route.operator', 'route.bus', 'route.driver',
            'user', 'payment', 'ticket', 'promoCode', 'cancellationRule',
        ])->findOrFail($id);

        AdminAuditService::log(
            'booking.viewed',
            "Viewed booking {$booking->reference_id}",
            $booking
        );

        return view('admin.bookings.show', compact('booking'));
    }

    private function applyDateFilter($query, string $date): void
    {
        $today = Carbon::today();
        switch ($date) {
            case 'today':
                $query->whereHas('route', fn ($q) => $q->where('travel_date', $today));
                break;
            case 'week':
                $query->whereHas('route', fn ($q) => $q->whereBetween('travel_date', [$today->startOfWeek(), $today->endOfWeek()]));
                break;
            case 'month':
                $query->whereHas('route', fn ($q) => $q->whereMonth('travel_date', now()->month)->whereYear('travel_date', now()->year));
                break;
        }
    }
}