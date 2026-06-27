<?php

namespace App\Http\Controllers;

use App\Models\Route;
use App\Models\Booking;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    /**
     * Display seat selection for a specific route.
     */
    public function showSeats($id)
    {
        $route = Route::with(['bus', 'operator'])->findOrFail($id);

        $bookedSeats = $route->bookedSeats();

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
            'user_id' => null,
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
        $booking = Booking::with('route')->findOrFail($bookingId);
        
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
        ]);
    }

    /*
     * Process the payment for a booking.
     * Updates status from 'pending' to 'confirmed'.
     */
    public function processPayment(Booking $booking)
    {
        // Update booking status from pending to confirmed
        $booking->update([
            'status' => 'confirmed',
        ]);

        // Redirect to success page
        return redirect()->route('booking.success', $booking->id);
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
}