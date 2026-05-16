<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Route;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class RouteController extends Controller
{
    /**
     * Search routes by origin, destination, and date.
     * Accessible by guests and travelers.
     */
    public function search(Request $request): JsonResponse
    {
        $data = $request->validate([
            'origin'      => 'required|string',
            'destination' => 'required|string',
            'travel_date' => 'required|date|after_or_equal:today',
        ]);

        $routes = Route::with(['operator', 'bus'])
            ->where('origin', 'ilike', '%' . $data['origin'] . '%')
            ->where('destination', 'ilike', '%' . $data['destination'] . '%')
            ->where('travel_date', $data['travel_date'])
            ->where('is_active', true)
            ->orderBy('fare')
            ->get()
            ->map(function ($route) {
                return [
                    'id'              => $route->id,
                    'operator'        => $route->operator->company_name,
                    'bus_class'       => $route->bus->bus_class,
                    'amenities'       => $route->bus->amenities,
                    'origin'          => $route->origin,
                    'destination'     => $route->destination,
                    'departure_time'  => $route->departure_time,
                    'arrival_time'    => $route->arrival_time,
                    'fare'            => $route->fare,
                    'available_seats' => $route->availableSeatsCount(),
                ];
            });

        return response()->json([
            'results' => $routes,
            'count'   => $routes->count(),
        ]);
    }

    /**
     * Get full route details including seat map.
     * Used when a traveler clicks on a route to select a seat.
     */
    public function show(int $id): JsonResponse
    {
        $route = Route::with(['operator', 'bus'])->findOrFail($id);

        $totalSeats  = range(1, $route->bus->seat_capacity);
        $bookedSeats = $route->bookedSeats();

        $seatMap = array_map(fn($seat) => [
            'seat_number' => $seat,
            'status'      => in_array($seat, $bookedSeats) ? 'booked' : 'available',
        ], $totalSeats);

        return response()->json([
            'route'    => $route,
            'seat_map' => $seatMap,
        ]);
    }

    /**
     * List all routes for a specific operator (operator dashboard).
     */
    public function operatorRoutes(Request $request): JsonResponse
    {
        $routes = Route::with('bus')
            ->where('operator_id', $request->user()->id)
            ->orderByDesc('travel_date')
            ->get();

        return response()->json(['routes' => $routes]);
    }

    /**
     * Create a new route (operator only).
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'bus_id'         => 'required|exists:buses,id',
            'origin'         => 'required|string',
            'destination'    => 'required|string',
            'departure_time' => 'required|date_format:H:i',
            'arrival_time'   => 'nullable|date_format:H:i',
            'fare'           => 'required|numeric|min:0',
            'travel_date'    => 'required|date|after_or_equal:today',
        ]);

        $route = Route::create(array_merge($data, [
            'operator_id' => $request->user()->id,
        ]));

        return response()->json([
            'message' => 'Route created successfully.',
            'route'   => $route->load('bus'),
        ], 201);
    }

    /**
     * Update fare or times for a route (operator only).
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $route = Route::where('id', $id)
            ->where('operator_id', $request->user()->id)
            ->firstOrFail();

        $data = $request->validate([
            'fare'           => 'sometimes|numeric|min:0',
            'departure_time' => 'sometimes|date_format:H:i',
            'arrival_time'   => 'sometimes|date_format:H:i',
            'is_active'      => 'sometimes|boolean',
        ]);

        $route->update($data);

        return response()->json([
            'message' => 'Route updated successfully.',
            'route'   => $route,
        ]);
    }

    /**
     * Delete a route (operator only, if no confirmed bookings).
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $route = Route::where('id', $id)
            ->where('operator_id', $request->user()->id)
            ->firstOrFail();

        $hasConfirmedBookings = $route->bookings()
            ->where('status', 'confirmed')
            ->exists();

        if ($hasConfirmedBookings) {
            return response()->json([
                'message' => 'Cannot delete a route with confirmed bookings.',
            ], 422);
        }

        $route->delete();

        return response()->json(['message' => 'Route deleted successfully.']);
    }
}
