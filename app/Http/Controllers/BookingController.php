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
        if(!$route->is_active){
            abort(404, 'This route is no longer available.');
        }

        // $bookedSeats = $route->bookedSeats();
        // Get already booked seats for this route (excludes cancelled seats)
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
    public function store(Request $request)
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

        $booking = Booking::create([
            'user_id' => Auth::id(),
            'route_id' => $validated['route_id'],
            'seat_number' => $validated['seat_number'],
            'passenger_name' => $validated['passenger_name'],
            'passenger_id_number' => $validated['id_number'],
            'passenger_phone' => $validated['phone'],
            'amount' => $route->fare,
            'status' => 'pending',
        ]);

        return redirect()->route('payment.ticket', $booking->id);
    }

    /**
     * Display the payment ticket page.
     */
    public function paymentTicket($bookingId)
    {
        $booking = Booking::with(['route.bus', 'route.operator'])
            ->where('id', $bookingId)
            ->where('user_id', Auth::id())
            ->firstOrFail();
        
        return view('payment_ticket', [
            'booking' => $booking,
            'passenger_name' => $booking->passenger_name,
            'seat_number' => $booking->seat_number,
            'total_fare' => $booking->amount,
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
