<?php

namespace App\Http\Controllers;

use App\Models\Route;
use App\Models\Booking;
use App\Models\Operator;
use App\Models\PromoCode;
use App\Services\FareCalculationService;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        $searchBackUrl = route('trips.search', [
            'origin' => $route->origin,
            'destination' => $route->destination,
            'travel_date' => $route->travel_date->format('Y-m-d'),
            'passengers' => request()->query('passengers', 1),
        ]);

        return view('seat_selection', compact('route', 'bookedSeats', 'searchBackUrl'));
    }

    /**
     * Store a new booking.
     */
    public function store(Request $request, FareCalculationService $fareService)
    {
        $validated = $request->validate([
            'route_id' => 'required|exists:routes,id',
            'seat_number' => 'required|integer|min:1',
            'passenger_name' => 'required|string|max:255',
            'id_number' => 'required|string|max:50',
            'phone' => 'required|string|max:20',
        ]);

        $route = Route::with('bus')->findOrFail($validated['route_id']);

        if ($validated['seat_number'] > $route->bus->seat_capacity) {
            return back()
                ->withErrors(['seat_number' => 'Please select a valid seat.'])
                ->withInput();
        }

        if (in_array($validated['seat_number'], $route->bookedSeats(), true)) {
            return back()
                ->withErrors(['seat_number' => 'This seat was just taken. Please choose another seat.'])
                ->withInput();
        }

        // Calculate the fare using FareCalculationService
        $fareResult = $fareService->calculate($route);

        $booking = Booking::create([
            'user_id' => Auth::id(),
            'route_id' => $validated['route_id'],
            'seat_number' => $validated['seat_number'],
            'passenger_name' => $validated['passenger_name'],
            'passenger_id_number' => $validated['id_number'],
            'passenger_phone' => $validated['phone'],
            'amount' => $fareResult['total'],
            'base_fare' => $fareResult['base_fare'],
            'service_fee_total' => $fareResult['service_fee_total'],
            'discount_amount' => $fareResult['discount'],
            'promo_code_id' => $fareResult['promo_code_id'],
            'status' => 'pending',
        ]);

        return redirect()->route('payment.ticket', $booking->id);
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
        
        // Get fare breakdown from booking
        $baseFare = $booking->base_fare ?? $booking->amount;
        $serviceFeeTotal = $booking->service_fee_total ?? 0;
        $discountAmount = $booking->discount_amount ?? 0;
        $totalFare = $booking->amount;

        return view('payment_ticket', [
            'booking' => $booking,
            'passenger_name' => $booking->passenger_name,
            'seat_number' => $booking->seat_number,
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

        // Process the payment
        $result = $paymentService->processMobileMoney(
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
        $booking->load(['route.bus', 'route.operator']);
        
        return view('history_page', compact('booking'));
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
            $query = Booking::with(['route.bus', 'route.operator']);

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