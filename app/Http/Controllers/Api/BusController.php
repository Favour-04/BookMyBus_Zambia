<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bus;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class BusController extends Controller
{
    // List all buses for the authenticated operator.
     
    public function index(Request $request): JsonResponse
    {
        $buses = Bus::where('operator_id', auth()->guard('operator_api')->id())
            ->withCount('routes')
            ->get();

        return response()->json(['buses' => $buses]);
    }

    //Add a new bus to the operator's fleet.
     
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'registration_number' => 'required|string|unique:buses,registration_number',
            'model'               => 'nullable|string',
            'seat_capacity'       => 'required|integer|min:1|max:100',
            'bus_class'           => 'required|in:economy,business,luxury',
            'amenities'           => 'nullable|array',
            'amenities.*'         => 'string',
        ]);

        $bus = Bus::create(array_merge($data, [
            'operator_id' => auth()->guard('operator_api')->id(),
        ]));

        return response()->json([
            'message' => 'Bus added to fleet successfully.',
            'bus'     => $bus,
        ], 201);
    }

    // Update bus details.
    public function update(Request $request, int $id): JsonResponse
    {
        $bus = Bus::where('id', $id)
            ->where('operator_id', auth()->guard('operator_api')->id())
            ->firstOrFail();

        $data = $request->validate([
            'model'         => 'sometimes|string',
            'seat_capacity' => 'sometimes|integer|min:1|max:100',
            'bus_class'     => 'sometimes|in:economy,business,luxury',
            'amenities'     => 'sometimes|array',
            'amenities.*'   => 'string',
            'is_active'     => 'sometimes|boolean',
        ]);

        $bus->update($data);

        return response()->json([
            'message' => 'Bus updated successfully.',
            'bus'     => $bus,
        ]);
    }

    // Remove a bus from the fleet (only if no active routes).
    public function destroy(Request $request, int $id): JsonResponse
    {
        $bus = Bus::where('id', $id)
            ->where('operator_id', auth()->guard('operator_api')->id())
            ->firstOrFail();

        $hasActiveRoutes = $bus->routes()->where('is_active', true)->exists();

        if ($hasActiveRoutes) {
            return response()->json([
                'message' => 'Cannot remove a bus with active routes.',
            ], 422);
        }

        $bus->delete();

        return response()->json(['message' => 'Bus removed from fleet.']);
    }
}
