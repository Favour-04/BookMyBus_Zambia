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
        
        // Get already booked seats for this route
        $bookedSeats = Booking::where('route_id', $id)
            ->where('status', 'pending')
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
        $booking = Booking::with('route')->findOrFail($bookingId);
        
        return view('payment_ticket', [
            'booking' => $booking,
            'seat_number' => $booking->seat_number,
            'total_fare' => $booking->amount,
            'origin' => $booking->route->origin,
            'destination' => $booking->route->destination,
            'departure_time' => $booking->route->departure_time,
            'departure_date' => $booking->route->travel_date->format('d M, Y'),
            'booking_id' => $booking->reference_id,
        ]);
    }
}