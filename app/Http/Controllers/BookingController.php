<?php

namespace App\Http\Controllers;

use App\Models\Route;
use App\Models\Booking;
use App\Models\Operator;
use App\Models\PromoCode;
use App\Models\Ticket;
use App\Notifications\BookingCancelled;
use App\Services\FareCalculationService;
use App\Services\PaymentService;
use App\Services\RefundCalculationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class BookingController extends Controller
{
    /**
     * Display seat selection for a specific route.
     */
    public function showSeats($id)
    {
        $route = Route::with(['bus', 'operator'])->findOrFail($id);
        if(!$route->is_active){
            abort(404, 'This route is no longer available.');
        }

        $bookedSeats = Booking::where('route_id', $id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->pluck('seat_number')
            ->toArray();

        $availableRemaining = $route->bus->seat_capacity - count($bookedSeats);
        $passengers = max(1, min((int) request()->query('passengers', 1), max($availableRemaining, 1)));

        $searchBackUrl = route('trips.search', [
            'origin' => $route->origin,
            'destination' => $route->destination,
            'travel_date' => $route->travel_date->format('Y-m-d'),
            'passengers' => $passengers,
        ]);

        return view('seat_selection', compact('route', 'bookedSeats', 'searchBackUrl', 'passengers'));
    }

    /**
     * Store a new booking.
     */
    public function store(Request $request, FareCalculationService $fareService)
    {
        $validated = $request->validate([
            'route_id' => 'required|exists:routes,id',
            'passengers' => 'required|array|min:1',
            'passengers.*.seat_number' => 'required|integer|min:1',
            'passengers.*.passenger_name' => 'required|string|max:255',
            'passengers.*.id_number' => 'required|string|max:50',
            'passengers.*.phone' => 'required|string|max:20',
        ]);

        $route = Route::with('bus')->findOrFail($validated['route_id']);
        $seatNumbers = array_column($validated['passengers'], 'seat_number');

        if (count($seatNumbers) !== count(array_unique($seatNumbers))) {
            return back()
                ->withErrors(['seat_numbers' => 'Each passenger must be assigned a different seat.'])
                ->withInput();
        }

        foreach ($seatNumbers as $seatNumber) {
            if ($seatNumber > $route->bus->seat_capacity) {
                return back()
                    ->withErrors(['seat_numbers' => 'Please select valid seats.'])
                    ->withInput();
            }
        }

        if (array_intersect($seatNumbers, $route->bookedSeats())) {
            return back()
                ->withErrors(['seat_numbers' => 'One of the selected seats was just taken. Please choose again.'])
                ->withInput();
        }

        // Fare is the same per seat for a given route/promo, so this only needs to run once.
        $fareResult = $fareService->calculate($route);

        // Multiple seats in one submission share a group_reference so the payment
        // step (paymentTicket / processPayment) can treat them as a single transaction.
        $groupReference = count($validated['passengers']) > 1
            ? 'GRP-' . strtoupper(Str::random(8))
            : null;

        $bookings = collect();

        DB::transaction(function () use ($validated, $fareResult, $groupReference, &$bookings) {
            foreach ($validated['passengers'] as $passenger) {
                $bookings->push(Booking::create([
                    'user_id' => Auth::id(),
                    'route_id' => $validated['route_id'],
                    'seat_number' => $passenger['seat_number'],
                    'passenger_name' => $passenger['passenger_name'],
                    'passenger_id_number' => $passenger['id_number'],
                    'passenger_phone' => $passenger['phone'],
                    'amount' => $fareResult['total'],
                    'base_fare' => $fareResult['base_fare'],
                    'service_fee_total' => $fareResult['service_fee_total'],
                    'discount_amount' => $fareResult['discount'],
                    'promo_code_id' => $fareResult['promo_code_id'],
                    'group_reference' => $groupReference,
                    'status' => 'pending',
                ]));
            }
        });

        return redirect()->route('payment.ticket', $bookings->first()->id);
    }

    /**
     * Display the payment ticket page.
     */
    public function paymentTicket($bookingId)
    {
        $booking = Booking::with(['route.bus', 'route.operator', 'promoCode'])
            ->where('id', $bookingId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // For a multi-seat purchase, pull in the sibling bookings sharing this
        // group_reference so the payment page can show/charge for the whole party.
        $groupBookings = $booking->group_reference
            ? Booking::where('group_reference', $booking->group_reference)
                ->where('user_id', Auth::id())
                ->orderBy('seat_number')
                ->get()
            : collect([$booking]);

        // Get fare breakdown across the whole group
        $baseFare = $groupBookings->sum(fn (Booking $b) => $b->base_fare ?? $b->amount);
        $serviceFeeTotal = $groupBookings->sum('service_fee_total');
        $discountAmount = $groupBookings->sum('discount_amount');
        $totalFare = $groupBookings->sum('amount');

        return view('payment_ticket', [
            'booking' => $booking,
            'group_bookings' => $groupBookings,
            'passenger_name' => $booking->passenger_name,
            'seat_number' => $booking->seat_number,
            'seat_numbers' => $groupBookings->pluck('seat_number')->all(),
            'total_fare' => $totalFare,
            'base_fare' => $baseFare,
            'service_fee_total' => $serviceFeeTotal,
            'discount_amount' => $discountAmount,
            'applied_promo' => $booking->promoCode ? $booking->promoCode->code : null,
            'origin' => $booking->route->origin,
            'destination' => $booking->route->destination,
            'departure_time' => $booking->route->departure_time,
            'departure_date' => $booking->route->travel_date->format('d M, Y'),
            'booking_id' => $booking->reference_id,
            'expired' => $booking->isExpired(),
            'held_until' => $booking->held_until,
            'origin_code' => strtoupper(substr($booking->route->origin, 0, 3)),
            'destination_code' => strtoupper(substr($booking->route->destination, 0, 3)),
            'class_type' => 'Premium Class',
        ]);
    }

    /**
     * Process the payment for a booking via mobile money.
     * Validates input, calls the payment gateway, and creates a Payment record.
     */
    public function processPayment(Request $request, Booking $booking, PaymentService $paymentService)
    {
        // Validate the request
        $validated = $request->validate([
            'payment_provider' => 'required|in:mtn,airtel',
            'phone_number'     => [
                'required',
                'string',
                'regex:/^0[0-9]{9}$/',
            ],
        ]);

        if($booking->user_id !== Auth::id()){
            abort(403);
        }
        // Check if booking is already confirmed
        if ($booking->isConfirmed()) {
            return redirect()->route('booking.success', $booking->id)
                ->with('info', 'This booking is already confirmed.');
        }

        // Check if booking has expired
        if ($booking->isExpired()) {
            return redirect()->back()
                ->withErrors(['expired' => 'This booking reservation has expired. Please select your seat again.'])
                ->withInput();
        }

        // Pull in any sibling bookings from the same multi-seat purchase so we
        // charge (and confirm/ticket) the full group as one transaction.
        $groupBookings = $booking->group_reference
            ? Booking::where('group_reference', $booking->group_reference)
                ->where('user_id', Auth::id())
                ->get()
            : collect([$booking]);

        // A lone booking uses the original single-booking flow untouched;
        // a multi-seat group uses the dedicated group method so every seat
        // gets confirmed, ticketed, and notified — not just the primary one.
        $result = $groupBookings->count() > 1
            ? $paymentService->processMobileMoneyForGroup(
                $groupBookings,
                $validated['payment_provider'],
                $validated['phone_number']
            )
            : $paymentService->processMobileMoney(
                $booking,
                $validated['payment_provider'],
                $validated['phone_number']
            );

        if ($result['success']) {
            return redirect()->route('booking.success', $booking->id)
                ->with('success', $result['message']);
        }

        // Payment failed — redirect back with error
        return redirect()->back()
            ->withErrors(['payment' => $result['message']])
            ->withInput();
    }

    /**
     * Validate a promo code via AJAX.
     */
    public function validatePromoCode(Request $request, FareCalculationService $fareService)
    {
        $request->validate([
            'code' => 'required|string|max:20',
            'route_id' => 'required|exists:routes,id',
        ]);

        $route = Route::findOrFail($request->route_id);
        $operatorId = $route->operator_id;
        $baseFare = (float) $route->fare;

        // Calculate subtotal with service fees
        $subtotal = $baseFare;
        $fees = \App\Models\ServiceFee::where('operator_id', $operatorId)->active()->get();
        foreach ($fees as $fee) {
            $subtotal += $fee->calculateFee($baseFare);
        }

        $result = $fareService->validatePromoCode($request->code, $operatorId, $subtotal);

        return response()->json($result);
    }

    /**
     * Display the success page (digital ticket).
     */
    public function success(Booking $booking)
    {
        if($booking->user_id !== Auth::id()){
            abort(403);
        }
    
        // Load relationships for the ticket display
        $booking->load(['route.bus', 'route.operator', 'ticket']);

        // Multi-seat purchases: bring in the rest of the party so the success
        // page can list every ticket, not just the one that was paid through.
        $groupBookings = $booking->group_reference
            ? Booking::with(['ticket'])
                ->where('group_reference', $booking->group_reference)
                ->where('user_id', Auth::id())
                ->orderBy('seat_number')
                ->get()
            : collect([$booking]);

        return view('history_page', compact('booking', 'groupBookings'));
    }

    /**
     * Display the traveler's digital ticket by QR code.
     * Lets a traveler re-open their ticket after booking.
     */
    public function showTicket(string $qrCode)
    {
        $ticket = Ticket::with(['booking.route.bus', 'booking.route.operator'])
            ->where('qr_code', $qrCode)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $booking = $ticket->booking;

        return view('ticket', compact('ticket', 'booking'));
    }

    /**
     * Cancel a traveler's booking.
     *
     * Pending (unpaid) bookings are simply released. Confirmed bookings are
     * cancelled with a refund calculated via RefundCalculationService (per the
     * operator's cancellation rules). Past trips (departure already passed)
     * and already-cancelled bookings cannot be cancelled.
     */
    public function cancel(Booking $booking, RefundCalculationService $refundService)
    {
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        $booking->load('route');

        if ($booking->status === 'cancelled') {
            return back()->withErrors(['cancel' => 'This booking has already been cancelled.'])
                ->with('active_tab', 'bookings');
        }

        // Past trips (departure already passed) cannot be cancelled.
        if ($route = $booking->route) {
            $departure = Carbon::parse($route->travel_date)->setTimeFromTimeString((string) $route->departure_time);
            if (now()->isAfter($departure)) {
                return back()->withErrors(['cancel' => 'This trip has already departed and can no longer be cancelled.'])
                    ->with('active_tab', 'bookings');
            }
        }

        // Confirmed bookings are cancelled with a refund calculation; pending
        // (unpaid) bookings are simply released.
        if ($booking->status === 'confirmed') {
            $refund = $refundService->processCancellation($booking);
            $booking->user?->notify(new BookingCancelled($booking));

            return back()->with('status', 'Booking cancelled. ' . $refund['message'])
                ->with('active_tab', 'bookings');
        }

        $booking->cancel();
        $booking->user?->notify(new BookingCancelled($booking));

        return back()->with('status', 'Booking cancelled successfully.')
            ->with('active_tab', 'bookings');
    }

    /**
     * Display the customer booking lookup form.
     */
    public function customerLookupView()
    {
        return view('booking_lookup', ['booking' => null, 'error' => null]);
    }

    /**
     * Process customer booking lookup by reference ID or phone number.
     */
    public function customerLookup(Request $request)
    {
        $request->validate([
            'reference_id' => 'nullable|string|max:50',
            'phone_number' => 'nullable|string|max:20',
        ]);

        $booking = null;
        $error = null;

        if ($request->filled('reference_id') || $request->filled('phone_number')) {
            $query = Booking::with(['route.bus', 'route.operator', 'ticket']);

            if ($request->filled('reference_id')) {
                $query->where('reference_id', 'like', '%' . $request->reference_id . '%');
            }

            if ($request->filled('phone_number')) {
                $phone = preg_replace('/[^0-9]/', '', $request->phone_number);
                $query->where('passenger_phone', 'like', '%' . $phone . '%');
            }

            $booking = $query->first();

            if (!$booking) {
                $error = 'No booking found with the provided details. Please check and try again.';
            }
        } else {
            $error = 'Please provide a Booking Reference ID or Phone Number.';
        }

        return view('booking_lookup', compact('booking', 'error'));
    }
}