<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Route;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class BookingController extends Controller
{
    
     // Hold a seat for 10 minutes pending payment. Traveler only.

    public function hold(Request $request): JsonResponse
    {
        $data = $request->validate([
            'route_id'    => 'required|exists:routes,id',
            'seat_number' => 'required|integer|min:1',
        ]);

        $route = Route::with('bus')->findOrFail($data['route_id']);

        // Check if seat is within bus capacity
        if ($data['seat_number'] > $route->bus->seat_capacity) {
            return response()->json(['message' => 'Invalid seat number for this bus.'], 422);
        }

        // Check if seat is not already taken
        $alreadyBooked = Booking::where('route_id', $data['route_id'])
            ->where('seat_number', $data['seat_number'])
            ->whereIn('status', ['pending', 'confirmed'])
            ->where(function ($q) {
                $q->where('status', 'confirmed')
                  ->orWhere('held_until', '>', now());
            })
            ->exists();

        if ($alreadyBooked) {
            return response()->json(['message' => 'This seat is already taken.'], 409);
        }

        $booking = Booking::create([
            'user_id'     => $request->user()->id,
            'route_id'    => $data['route_id'],
            'seat_number' => $data['seat_number'],
            'status'      => 'pending',
        ]);

        return response()->json([
            'message'      => 'Seat held for 10 minutes. Proceed to payment.',
            'booking'      => $booking,
            'reference_id' => $booking->reference_id,
            'held_until'   => $booking->held_until,
        ], 201);
    }

    //List all bookings for the authenticated traveler.

    public function index(Request $request): JsonResponse
    {
        $bookings = Booking::with(['route.operator', 'route.bus', 'payment', 'ticket'])
            ->where('user_id', $request->user()->id)
            ->orderByDesc('created_at')
            ->get();

        return response()->json(['bookings' => $bookings]);
    }

    // Get a single booking by reference ID.

    public function show(Request $request, string $referenceId): JsonResponse
    {
        $booking = Booking::with(['route.operator', 'route.bus', 'payment', 'ticket'])
            ->where('reference_id', $referenceId)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        return response()->json(['booking' => $booking]);
    }

    //Cancel a booking (traveler cancels their own pending booking).
    public function cancel(Request $request, int $id): JsonResponse
    {
        $booking = Booking::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        if ($booking->status === 'confirmed') {
            return response()->json([
                'message' => 'Confirmed bookings cannot be self-cancelled. Contact support.',
            ], 422);
        }

        $booking->cancel();

        return response()->json(['message' => 'Booking cancelled successfully.']);
    }

    //List bookings for a specific route (operator dashboard).
     
    public function routeManifest(Request $request, int $routeId): JsonResponse
    {
        $route = Route::where('id', $routeId)
            ->where('operator_id', auth()->guard('operator_api')->id())
            ->firstOrFail();    

        $bookings = Booking::with('user')
            ->where('route_id', $routeId)
            ->where('status', 'confirmed')
            ->orderBy('seat_number')
            ->get()
            ->map(fn($b) => [
                'seat_number'  => $b->seat_number,
                'passenger'    => $b->user->full_name,
                'phone'        => $b->user->phone_number,
                'reference_id' => $b->reference_id,
            ]);

        return response()->json([
            'route'    => $route->only(['origin', 'destination', 'travel_date', 'departure_time']),
            'manifest' => $bookings,
            'total'    => $bookings->count(),
        ]);
    }
}
