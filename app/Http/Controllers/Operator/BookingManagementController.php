<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Operator;
use App\Models\Route;
use App\Services\OperatorAuditService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BookingManagementController extends Controller
{
    /**
     * Display all bookings for the operator (across all trips).
     */
    public function index(Request $request)
    {
        $operator = $this->getOperator();

        $query = Booking::with(['route.bus', 'route.operator'])
            ->whereHas('route', function ($q) use ($operator) {
                $q->where('operator_id', $operator->id);
            });

        // Apply status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Apply payment filter
        if ($request->filled('payment')) {
            if ($request->payment === 'paid') {
                $query->where('status', 'confirmed');
            } elseif ($request->payment === 'pending') {
                $query->where('status', 'pending');
            } elseif ($request->payment === 'cancelled') {
                $query->where('status', 'cancelled');
            } elseif ($request->payment === 'expired') {
                $query->where('status', 'pending')
                      ->where('held_until', '<', now());
            }
        }

        // Apply date filter
        if ($request->filled('date_filter')) {
            $this->applyDateFilter($query, $request->date_filter);
        }

        // Apply search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference_id', 'like', "%{$search}%")
                  ->orWhere('passenger_name', 'like', "%{$search}%")
                  ->orWhere('passenger_phone', 'like', "%{$search}%")
                  ->orWhere('seat_number', $search)
                  ->orWhereHas('route', function ($r) use ($search) {
                      $r->where('origin', 'like', "%{$search}%")
                        ->orWhere('destination', 'like', "%{$search}%");
                  });
            });
        }

        // Apply sort
        $sortField = $request->get('sort', 'created_at');
        $sortDir = $request->get('dir', 'desc');
        $allowedSorts = ['created_at', 'seat_number', 'amount', 'status', 'reference_id'];
        if (in_array($sortField, $allowedSorts)) {
            $query->orderBy($sortField, $sortDir === 'asc' ? 'asc' : 'desc');
        }

        $bookings = $query->paginate(25)->withQueryString();

        // Get summary stats
        $stats = $this->getBookingStats($operator);

        $statusStyles = $this->getStatusStyles();

        return view('operator.all_bookings', compact(
            'operator',
            'bookings',
            'stats',
            'statusStyles'
        ));
    }

    /**
     * Display bookings for a specific trip.
     */
    public function tripBookings($tripId)
    {
        $operator = $this->getOperator();

        $route = Route::with(['bus', 'operator'])
            ->where('operator_id', $operator->id)
            ->findOrFail($tripId);

        $bookings = Booking::with(['user'])
            ->where('route_id', $route->id)
            ->orderByRaw("CASE status WHEN 'confirmed' THEN 0 WHEN 'pending' THEN 1 WHEN 'cancelled' THEN 2 ELSE 3 END")
            ->orderBy('seat_number', 'asc')
            ->get();

        $tripData = $this->formatTripData($route);

        $stats = [
            'total' => $bookings->count(),
            'confirmed' => $bookings->where('status', 'confirmed')->count(),
            'pending' => $bookings->where('status', 'pending')->count(),
            'cancelled' => $bookings->where('status', 'cancelled')->count(),
            'total_revenue' => $bookings->where('status', 'confirmed')->sum('amount'),
        ];

        return view('operator.trip_bookings', compact(
            'operator',
            'route',
            'bookings',
            'tripData',
            'stats'
        ));
    }

    /**
     * Display a single booking detail.
     */
    public function show($bookingId)
    {
        $operator = $this->getOperator();

        $booking = Booking::with(['route.bus', 'route.operator', 'payment', 'user'])
            ->whereHas('route', function ($q) use ($operator) {
                $q->where('operator_id', $operator->id);
            })
            ->findOrFail($bookingId);

        OperatorAuditService::viewed($booking, "Viewed booking {$booking->reference_id}");

        return view('operator.booking_detail', compact('operator', 'booking'));
    }

    /**
     * Show the form for editing a booking.
     */
    public function edit($bookingId)
    {
        $operator = $this->getOperator();

        $booking = Booking::with(['route.bus', 'route.operator', 'payment'])
            ->whereHas('route', function ($q) use ($operator) {
                $q->where('operator_id', $operator->id);
            })
            ->findOrFail($bookingId);

        // Get available seats for seat change (exclude current booking's seat)
        $availableSeats = $booking->route->availableSeats();
        $currentSeat = (int) $booking->seat_number;
        if (!in_array($currentSeat, $availableSeats)) {
            $availableSeats[] = $currentSeat;
            sort($availableSeats);
        }

        return view('operator.booking_edit', compact('operator', 'booking', 'availableSeats'));
    }

    /**
     * Update the specified booking.
     */
    public function update(Request $request, $bookingId)
    {
        $operator = $this->getOperator();

        $booking = Booking::with(['route.bus', 'route.operator'])
            ->whereHas('route', function ($q) use ($operator) {
                $q->where('operator_id', $operator->id);
            })
            ->findOrFail($bookingId);

        $validated = $request->validate([
            'passenger_name' => 'required|string|max:255',
            'passenger_phone'   => 'required|string|max:20',
            'passenger_id_number' => 'nullable|string|max:50',
            'seat_number'    => [
                'required',
                'integer',
                'min:1',
                'max:' . ($booking->route->bus->seat_capacity ?? 100),
                function ($attribute, $value, $fail) use ($booking) {
                    // bookings has a DB-level unique(route_id, seat_number) that
                    // isn't scoped by status, so a cancelled booking still
                    // occupies its seat slot — check against any booking on
                    // this route (not just pending/confirmed) to match that
                    // constraint and avoid a 500 on save.
                    $existing = Booking::where('route_id', $booking->route_id)
                        ->where('seat_number', $value)
                        ->where('id', '!=', $booking->id)
                        ->exists();
                    if ($existing) {
                        $fail('This seat is already taken by another booking on this trip.');
                    }
                },
            ],
            'amount' => 'required|numeric|min:0',
        ]);

        $original = $booking->getOriginal();
        $booking->update([
            'passenger_name' => $validated['passenger_name'],
            'passenger_phone' => $validated['passenger_phone'],
            'passenger_id_number' => $validated['passenger_id_number'] ?? null,
            'seat_number'    => $validated['seat_number'],
            'amount'         => $validated['amount'],
        ]);

        OperatorAuditService::updated($booking, $original, "Updated booking {$booking->reference_id}");

        return redirect()
            ->route('operator.bookings.show', $booking->id)
            ->with('success', "Booking {$booking->reference_id} has been updated successfully.");
    }
    /**
     * Mark a booking as boarded (passenger checked in).
     */
    public function markBoarded($bookingId)
    {
        $operator = $this->getOperator();

        $booking = Booking::with(['route'])
            ->whereHas('route', function ($q) use ($operator) {
                $q->where('operator_id', $operator->id);
            })
            ->findOrFail($bookingId);

        if ($booking->status !== 'confirmed') {
            return back()->withErrors([
                'boarded' => 'Only confirmed bookings can be marked as boarded.'
            ]);
        }

        if ($booking->isBoarded()) {
            return back()->withErrors([
                'boarded' => 'This passenger is already marked as boarded.'
            ]);
        }

        $booking->markBoarded($operator->company_name ?? 'Operator');

        OperatorAuditService::log('booking.marked_boarded', "Marked booking {$booking->reference_id} as boarded", $booking);

        return back()->with('success', "Passenger {$booking->passenger_name} marked as boarded.");
    }

    /**
     * Undo boarded status for a booking.
     */
    public function undoBoarded($bookingId)
    {
        $operator = $this->getOperator();

        $booking = Booking::with(['route'])
            ->whereHas('route', function ($q) use ($operator) {
                $q->where('operator_id', $operator->id);
            })
            ->findOrFail($bookingId);

        if (!$booking->isBoarded()) {
            return back()->withErrors([
                'boarded' => 'This passenger is not marked as boarded.'
            ]);
        }

        $booking->undoBoarded();

        OperatorAuditService::log('booking.undo_boarded', "Undid boarded status for booking {$booking->reference_id}", $booking);

        return back()->with('success', "Boarded status removed for {$booking->passenger_name}.");
    }

    /**
     * Add or update notes on a booking.
     */
    public function updateNotes(Request $request, $bookingId)
    {
        $operator = $this->getOperator();

        $booking = Booking::with(['route'])
            ->whereHas('route', function ($q) use ($operator) {
                $q->where('operator_id', $operator->id);
            })
            ->findOrFail($bookingId);

        $validated = $request->validate([
            'notes' => 'nullable|string|max:2000',
        ]);

        $booking->update(['notes' => $validated['notes'] ?? null]);

        OperatorAuditService::log('booking.notes_updated', "Updated notes for booking {$booking->reference_id}", $booking);

        return back()->with('success', "Notes updated for booking {$booking->reference_id}.");
    }

    /**
     * Bulk actions on bookings (cancel, mark boarded).
     */
    public function bulkAction(Request $request)
    {
        $operator = $this->getOperator();

        $validated = $request->validate([
            'action' => 'required|in:cancel,board',
            'booking_ids' => 'required|array|min:1',
            'booking_ids.*' => 'integer|exists:bookings,id',
        ]);

        $bookings = Booking::with(['route'])
            ->whereHas('route', function ($q) use ($operator) {
                $q->where('operator_id', $operator->id);
            })
            ->whereIn('id', $validated['booking_ids'])
            ->get();

        $count = 0;
        $errors = [];

        foreach ($bookings as $booking) {
            try {
                if ($validated['action'] === 'cancel') {
                    if ($booking->isConfirmed()) {
                        $errors[] = "Booking {$booking->reference_id}: Cannot cancel confirmed booking.";
                        continue;
                    }
                    if ($booking->status === 'cancelled') {
                        continue;
                    }
                    $booking->cancel();
                    $count++;
                } elseif ($validated['action'] === 'board') {
                    if ($booking->status !== 'confirmed') {
                        $errors[] = "Booking {$booking->reference_id}: Only confirmed bookings can be boarded.";
                        continue;
                    }
                    if ($booking->isBoarded()) {
                        continue;
                    }
                    $booking->markBoarded($operator->company_name ?? 'Operator');
                    $count++;
                }
            } catch (\Exception $e) {
                $errors[] = "Booking {$booking->reference_id}: {$e->getMessage()}";
            }
        }

        $actionLabel = $validated['action'] === 'cancel' ? 'cancelled' : 'boarded';
        $message = "{$count} booking(s) {$actionLabel} successfully.";

        OperatorAuditService::log('booking.bulk_action', "Bulk {$actionLabel}: {$count} booking(s) affected", null, null, ['action' => $validated['action'], 'booking_ids' => $validated['booking_ids'], 'count' => $count]);

        if (!empty($errors)) {
            return back()->with('warning', $message)->withErrors(['bulk' => implode(' ', $errors)]);
        }

        return back()->with('success', $message);
    }

    /**
     * Print receipt for a booking.
     */
    public function printReceipt($bookingId)
    {
        $operator = $this->getOperator();

        $booking = Booking::with(['route.bus', 'route.operator', 'payment', 'user'])
            ->whereHas('route', function ($q) use ($operator) {
                $q->where('operator_id', $operator->id);
            })
            ->findOrFail($bookingId);

        return view('operator.booking_receipt', compact('operator', 'booking'));
    }

    /**
     * Cancel a single booking (for pending bookings only).
     */
    public function cancelBooking($bookingId)
    {
        $operator = $this->getOperator();

        $booking = Booking::with(['route'])
            ->whereHas('route', function ($q) use ($operator) {
                $q->where('operator_id', $operator->id);
            })
            ->findOrFail($bookingId);

        if ($booking->isConfirmed()) {
            return back()->withErrors([
                'cancel' => 'Cannot cancel a confirmed booking. Please process a refund instead.'
            ]);
        }

        if ($booking->status === 'cancelled') {
            return back()->withErrors([
                'cancel' => 'This booking is already cancelled.'
            ]);
        }

        $booking->cancel();

        OperatorAuditService::log('booking.cancelled', "Cancelled booking {$booking->reference_id}", $booking, $booking->getOriginal());

        return back()->with('success', "Booking {$booking->reference_id} has been cancelled successfully.");
    }

    /**
     * Process refund for a confirmed booking.
     * Shows refund preview and processes on confirmation.
     */
    public function processRefund($bookingId)
    {
        $operator = $this->getOperator();

        $booking = Booking::with(['route', 'cancellationRule'])
            ->whereHas('route', function ($q) use ($operator) {
                $q->where('operator_id', $operator->id);
            })
            ->findOrFail($bookingId);

        if ($booking->status !== 'confirmed') {
            return back()->withErrors([
                'refund' => 'Only confirmed bookings can be refunded.'
            ]);
        }

        if ($booking->isBoarded()) {
            return back()->withErrors([
                'refund' => 'Cannot refund a booking where the passenger has already boarded.'
            ]);
        }

        $refundService = new \App\Services\RefundCalculationService();
        $refundInfo = $refundService->calculateRefund($booking);

        // Update booking with cancellation info
        $booking->update([
            'status' => 'cancelled',
            'cancellation_rule_id' => $refundInfo['rule_id'],
            'refund_amount' => $refundInfo['refund_amount'],
            'cancelled_at' => now(),
        ]);

        OperatorAuditService::log('booking.refunded', 
            "Processed refund for {$booking->reference_id}: {$refundInfo['refund_percentage']}% (ZMW {$refundInfo['refund_amount']}) - {$refundInfo['rule_name']}", 
            $booking
        );

        return back()->with('success', "Refund processed: {$refundInfo['message']}");
    }

    /**
     * Export bookings as CSV.
     */
    public function export(Request $request)
    {
        $operator = $this->getOperator();

        $query = Booking::with(['route.bus', 'route.operator'])
            ->whereHas('route', function ($q) use ($operator) {
                $q->where('operator_id', $operator->id);
            });

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereHas('route', function ($q) use ($request) {
                $q->where('travel_date', '>=', $request->date_from);
            });
        }

        if ($request->filled('date_to')) {
            $query->whereHas('route', function ($q) use ($request) {
                $q->where('travel_date', '<=', $request->date_to);
            });
        }

        $bookings = $query->orderBy('created_at', 'desc')->get();

        $filename = 'bookings-export-' . now()->format('Y-m-d-His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($bookings) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'Reference ID',
                'Passenger Name',
                'Phone Number',
                'Route',
                'Travel Date',
                'Departure Time',
                'Seat Number',
                'Amount (ZMW)',
                'Status',
                'Booking Date',
            ]);

            foreach ($bookings as $booking) {
                fputcsv($handle, [
                    $booking->reference_id,
                    $booking->passenger_name ?? 'N/A',
                    $booking->passenger_phone ?? 'N/A',
                    $booking->route->origin . ' → ' . $booking->route->destination,
                    $booking->route->travel_date instanceof \Carbon\Carbon
                        ? $booking->route->travel_date->format('d M Y')
                        : $booking->route->travel_date,
                    $booking->route->departure_time,
                    $booking->seat_number,
                    number_format($booking->amount, 2),
                    ucfirst($booking->status),
                    $booking->created_at->format('d M Y H:i'),
                ]);
            }

            fclose($handle);
        };

        OperatorAuditService::log('booking.exported', 'Exported bookings to CSV');

        return response()->stream($callback, 200, $headers);
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

    private function applyDateFilter($query, $filter)
    {
        switch ($filter) {
            case 'today':
                $query->whereHas('route', function ($q) {
                    $q->where('travel_date', Carbon::today());
                });
                break;
            case 'yesterday':
                $query->whereHas('route', function ($q) {
                    $q->where('travel_date', Carbon::yesterday());
                });
                break;
            case 'tomorrow':
                $query->whereHas('route', function ($q) {
                    $q->where('travel_date', Carbon::tomorrow());
                });
                break;
            case 'this_week':
                $query->whereHas('route', function ($q) {
                    $q->whereBetween('travel_date', [
                        Carbon::now()->startOfWeek(),
                        Carbon::now()->endOfWeek(),
                    ]);
                });
                break;
            case 'this_month':
                $query->whereHas('route', function ($q) {
                    $q->whereMonth('travel_date', Carbon::now()->month)
                      ->whereYear('travel_date', Carbon::now()->year);
                });
                break;
        }
    }

    private function getBookingStats($operator)
    {
        $today = Carbon::today();

        $totalBookings = Booking::whereHas('route', function ($q) use ($operator) {
            $q->where('operator_id', $operator->id);
        })->count();

        $todayBookings = Booking::whereHas('route', function ($q) use ($operator, $today) {
            $q->where('operator_id', $operator->id)
              ->where('travel_date', $today);
        })->count();

        $confirmedBookings = Booking::whereHas('route', function ($q) use ($operator) {
            $q->where('operator_id', $operator->id);
        })->where('status', 'confirmed')->count();

        $pendingBookings = Booking::whereHas('route', function ($q) use ($operator) {
            $q->where('operator_id', $operator->id);
        })->where('status', 'pending')
          ->where('held_until', '>', now())
          ->count();

        $totalRevenue = Booking::whereHas('route', function ($q) use ($operator) {
            $q->where('operator_id', $operator->id);
        })->where('status', 'confirmed')->sum('amount');

        $revenueToday = Booking::whereHas('route', function ($q) use ($operator, $today) {
            $q->where('operator_id', $operator->id)
              ->where('travel_date', $today);
        })->where('status', 'confirmed')->sum('amount');

        return [
            'total_bookings' => $totalBookings,
            'today_bookings' => $todayBookings,
            'confirmed_bookings' => $confirmedBookings,
            'pending_bookings' => $pendingBookings,
            'total_revenue' => $totalRevenue,
            'revenue_today' => $revenueToday,
        ];
    }

    private function getStatusStyles()
    {
        return [
            'confirmed' => 'bg-primary/10 text-primary',
            'pending' => 'bg-tertiary/10 text-tertiary',
            'cancelled' => 'bg-error-container/20 text-error',
            'expired' => 'bg-surface-container-high text-on-surface-variant',
        ];
    }

    private function formatTripData($route)
    {
        $bookedCount = $route->bookings()
            ->where('status', 'confirmed')
            ->count();
        $pendingCount = $route->bookings()
            ->where('status', 'pending')
            ->count();
        $capacity = $route->bus->seat_capacity ?? 49;

        return [
            'id' => 'TRP-' . str_pad($route->id, 4, '0', STR_PAD_LEFT),
            'route_from' => $route->origin,
            'route_to' => $route->destination,
            'date' => Carbon::parse($route->travel_date)->format('d M Y'),
            'departure' => $route->departure_time ? Carbon::parse($route->departure_time)->format('H:i') : '--:--',
            'bus' => $route->bus->registration_number ?? 'N/A',
            'booked' => $bookedCount,
            'pending' => $pendingCount,
            'capacity' => $capacity,
        ];
    }
}