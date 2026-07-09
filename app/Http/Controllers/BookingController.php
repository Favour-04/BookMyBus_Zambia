<?php

namespace App\Http\Controllers;

use App\Models\Route;
use App\Models\Booking;
use App\Models\Operator;
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
        
        // Get already booked seats for this route (excludes cancelled seats)
        $bookedSeats = Booking::where('route_id', $id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->pluck('seat_number')
            ->toArray();
        
        return view('seat_selection', compact('route', 'bookedSeats'));
    }

    /**
     * Store a new booking.
     */
    public function store(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'route_id' => 'required|exists:routes,id',
            'seat_number' => 'required|integer|min:1',
        ]);

        // Get the route to access fare
        $route = Route::findOrFail($validated['route_id']);

        // Create the booking
        $booking = Booking::create([
            'user_id' => null, // nullable for now
            'route_id' => $validated['route_id'],
            'seat_number' => $validated['seat_number'],
            'amount' => $route->fare,
            'status' => 'pending',
        ]);

        // Redirect to payment page
        return redirect()->route('payment.ticket', $booking->id);
    }

    /**
     * Display the payment ticket page.
     */
    public function paymentTicket($bookingId)
    {
        $booking = Booking::with(['route.bus', 'route.operator'])->findOrFail($bookingId);
        
        return view('payment_ticket', [
            'booking' => $booking,
            'seat_number' => $booking->seat_number,
            'total_fare' => $booking->amount,
            'origin' => $booking->route->origin,
            'destination' => $booking->route->destination,
            'departure_time' => $booking->route->departure_time,
            'departure_date' => $booking->route->travel_date->format('d M, Y'),
            'booking_id' => $booking->reference_id,
            'expired' => $booking->isExpired(),
            'held_until' => $booking->held_until,
            'passenger_name' => $booking->passenger_name ?? null,
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
     * Display the success page (digital ticket).
     */
    public function success(Booking $booking)
    {
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
                $query->where('phone_number', 'like', '%' . $phone . '%');
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
