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
use Illuminate\Pagination\LengthAwarePaginator;
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
            'passengers.*.passenger_name' => ['required', 'string', 'max:255', 'regex:/^[A-Za-z\s\'\-]+$/'],
            'passengers.*.id_number' => ['required', 'string', 'max:50', 'regex:/^[0-9]{6}\/[0-9]{2}\/[0-9]{1}$/'],
            'passengers.*.phone' => ['required', 'string', 'max:20', 'regex:/^(\+260|0)[0-9]{9}$/'],
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
        $booking = Booking::with(['route.bus', 'route.operator', 'promoCode', 'ticket'])
            ->where('id', $bookingId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // A booking that's already paid has a real, stored ticket QR waiting
        // on the success page — send the traveler there instead of re-showing
        // the checkout screen (whose QR is only a reference_id preview, not
        // the issued ticket).
        if ($booking->isConfirmed()) {
            return redirect()->route('booking.success', $booking->id);
        }

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
            'phone_number' => $booking->passenger_phone,
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
     * Release a pending seat hold when a traveler backs out of the payment
     * page to reselect their seat(s). Deliberately separate from cancel():
     * this only ever touches a booking that's still 'pending' (never paid),
     * skips the refund/notification machinery meant for a real cancellation,
     * and always sends the traveler back to seat selection rather than a
     * bookings list. For a multi-seat purchase, every sibling booking
     * sharing the group_reference is released together, not just the one
     * whose id happened to be in the URL.
     */
    public function releaseHold(Booking $booking)
    {
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        $routeId = $booking->route_id;

        if ($booking->status === 'pending') {
            $group = $booking->group_reference
                ? Booking::where('group_reference', $booking->group_reference)
                    ->where('user_id', Auth::id())
                    ->where('status', 'pending')
                    ->get()
                : collect([$booking]);

            $group->each(fn (Booking $b) => $b->cancel());
        }

        return redirect()->route('booking.seats', $routeId)
            ->with('status', 'Your seat selection was released. Please choose your seat(s) again.');
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
     * Number of trips (grouped bookings) shown per page on the My Bookings
     * dashboard for signed-in travelers.
     */
    private const TRIPS_PER_PAGE = 10;

    /**
     * "My Bookings" page.
     *
     * Signed-in travelers see their own booking history immediately — no
     * search required — scoped to Auth::id() and grouped into one card per
     * purchase via Booking::groupIntoTrips(). Guests (not logged in) see
     * only the reference-ID search form below; $trips is null for them so
     * the view knows to skip the dashboard section entirely.
     */
    public function customerLookupView()
    {
        $trips = Auth::check() ? $this->paginatedTripsForCurrentUser() : null;

        return view('booking_lookup', [
            'trips' => $trips,
            'foundBooking' => null,
            'groupBookings' => null,
            'error' => null,
        ]);
    }

    /**
     * Look up a single booking by its exact reference ID.
     *
     * Deliberately available to guests and, unlike the dashboard above,
     * deliberately NOT scoped to Auth::id(). The reference ID itself (a
     * random 6-character code — roughly 2 billion combinations) is treated
     * as the credential here, the same way a paper ticket or airline PNR
     * works: whoever has the code can view that booking. This is what lets
     * a guest retrieve a ticket without an account, e.g. a passenger whose
     * seat was booked under someone else's login.
     *
     * Phone number is deliberately not offered as a search field: a phone
     * number is far easier to guess or enumerate than a random reference
     * code, so allowing it here would make it easy to pull up a stranger's
     * booking. For the same reason this only ever does an exact match —
     * no partial/LIKE matching — so a search can't be used to incrementally
     * narrow down a valid code either. The route this action sits behind
     * is also rate-limited (see routes/web.php) as a further guard against
     * brute-forcing reference IDs.
     */
    public function customerLookup(Request $request)
    {
        $validated = $request->validate([
            'reference_id' => 'required|string|max:50',
        ]);

        $referenceId = strtoupper(trim($validated['reference_id']));

        $foundBooking = Booking::with(['route.bus', 'route.operator', 'ticket'])
            ->where('reference_id', $referenceId)
            ->first();

        $trips = Auth::check() ? $this->paginatedTripsForCurrentUser() : null;

        if (!$foundBooking) {
            return view('booking_lookup', [
                'trips' => $trips,
                'foundBooking' => null,
                'groupBookings' => null,
                'error' => 'No booking found with that reference ID. Please check and try again.',
            ]);
        }

        // Multi-seat purchase: show every seat in the party, not just the
        // one the reference ID happened to belong to. Unscoped by design,
        // matching the lookup above.
        $groupBookings = $foundBooking->group_reference
            ? Booking::with('ticket')
                ->where('group_reference', $foundBooking->group_reference)
                ->orderBy('seat_number')
                ->get()
            : collect([$foundBooking]);

        return view('booking_lookup', [
            'trips' => $trips,
            'foundBooking' => $foundBooking,
            'groupBookings' => $groupBookings,
            'error' => null,
        ]);
    }

    /**
     * Build the paginated, grouped booking history for the signed-in user,
     * for the My Bookings dashboard.
     */
    private function paginatedTripsForCurrentUser(): LengthAwarePaginator
    {
        $bookings = Auth::user()->bookings()
            ->with('route', 'ticket')
            ->latest()
            ->get();

        $trips = Booking::groupIntoTrips($bookings);

        $page = (int) request('page', 1);

        return new LengthAwarePaginator(
            $trips->forPage($page, self::TRIPS_PER_PAGE)->values(),
            $trips->count(),
            self::TRIPS_PER_PAGE,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );
    }
}