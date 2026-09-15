<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\Bus;
use App\Models\Operator;
use App\Models\Route;
use App\Services\OperatorAuditService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class BusController extends Controller
{
    /**
     * Display a list of buses for the operator.
     */
    public function index(Request $request)
    {
        $operator = $this->getOperator();

        $query = Bus::where('operator_id', $operator->id);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('registration_number', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Class filter
        if ($request->filled('class')) {
            $query->where('bus_class', $request->class);
        }

        $buses = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        // Summary stats
        $totalBuses = Bus::where('operator_id', $operator->id)->count();
        $activeBuses = Bus::where('operator_id', $operator->id)->where('is_active', true)->count();
        $totalCapacity = Bus::where('operator_id', $operator->id)->sum('seat_capacity');
        $maintenanceDue = Bus::where('operator_id', $operator->id)
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNotNull('next_maintenance_date')
                  ->where('next_maintenance_date', '<=', Carbon::now()->addDays(7));
            })->count();

        return view('operator.buses', compact(
            'operator', 'buses', 'totalBuses', 'activeBuses', 'totalCapacity', 'maintenanceDue'
        ));
    }

    /**
     * Store a newly created bus.
     */
    public function store(Request $request)
    {
        $operator = $this->getOperator();

        $validated = $request->validate([
            'registration_number' => [
                'required', 'string', 'max:50',
                Rule::unique('buses')->where('operator_id', $operator->id),
            ],
            'model' => 'nullable|string|max:255',
            'seat_capacity' => 'required|integer|min:1|max:100',
            'bus_class' => 'required|in:economy,business,luxury',
            'amenities' => 'nullable|array',
            'amenities.*' => 'string|in:wifi,ac,usb_charging,entertainment,refreshments,restroom',
            'last_maintenance_date' => 'nullable|date',
            'next_maintenance_date' => 'nullable|date|after_or_equal:last_maintenance_date',
            'mileage_km' => 'nullable|integer|min:0',
            'notes' => 'nullable|string|max:2000',
        ]);

        $bus = Bus::create([
            'operator_id' => $operator->id,
            'registration_number' => strtoupper($validated['registration_number']),
            'model' => $validated['model'] ?? null,
            'seat_capacity' => $validated['seat_capacity'],
            'bus_class' => $validated['bus_class'],
            'amenities' => $validated['amenities'] ?? [],
            'last_maintenance_date' => $validated['last_maintenance_date'] ?? null,
            'next_maintenance_date' => $validated['next_maintenance_date'] ?? null,
            'mileage_km' => $validated['mileage_km'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'is_active' => true,
        ]);

        OperatorAuditService::created($bus, "Added bus {$bus->registration_number} to fleet");

        return redirect()->route('operator.buses.index')
            ->with('success', "Bus {$bus->registration_number} added to your fleet successfully.");
    }

    /**
     * Display a single bus with its upcoming trips.
     */
    public function show($busId)
    {
        $operator = $this->getOperator();

        $bus = Bus::where('operator_id', $operator->id)->findOrFail($busId);

        // Upcoming trips assigned to this bus
        $upcomingTrips = Route::with(['bookings' => function ($q) {
                $q->where('status', 'confirmed');
            }])
            ->where('bus_id', $bus->id)
            ->where('travel_date', '>=', Carbon::today())
            ->where('is_active', true)
            ->orderBy('travel_date', 'asc')
            ->orderBy('departure_time', 'asc')
            ->take(10)
            ->get()
            ->map(function ($route) {
                return [
                    'id' => 'TRP-' . str_pad($route->id, 4, '0', STR_PAD_LEFT),
                    'route_id' => $route->id,
                    'origin' => $route->origin,
                    'destination' => $route->destination,
                    'date' => $route->travel_date instanceof Carbon
                        ? $route->travel_date->format('d M Y')
                        : Carbon::parse($route->travel_date)->format('d M Y'),
                    'departure' => $route->departure_time,
                    'bookings' => $route->bookings->count(),
                    'fare' => $route->fare,
                ];
            });

        // Trip history (past trips)
        $tripHistory = Route::with(['bookings' => function ($q) {
                $q->where('status', 'confirmed');
            }])
            ->where('bus_id', $bus->id)
            ->where('travel_date', '<', Carbon::today())
            ->orderBy('travel_date', 'desc')
            ->take(10)
            ->get()
            ->map(function ($route) {
                return [
                    'id' => 'TRP-' . str_pad($route->id, 4, '0', STR_PAD_LEFT),
                    'origin' => $route->origin,
                    'destination' => $route->destination,
                    'date' => $route->travel_date instanceof Carbon
                        ? $route->travel_date->format('d M Y')
                        : Carbon::parse($route->travel_date)->format('d M Y'),
                    'bookings' => $route->bookings->count(),
                ];
            });

        $stats = [
            'total_trips' => Route::where('bus_id', $bus->id)->count(),
            'upcoming_trips' => Route::where('bus_id', $bus->id)
                ->where('travel_date', '>=', Carbon::today())
                ->where('is_active', true)->count(),
            'total_revenue' => $bus->routes()
                ->whereHas('bookings', fn($q) => $q->where('status', 'confirmed'))
                ->withSum(['bookings as revenue_sum' => fn($q) => $q->where('status', 'confirmed')], 'amount')
                ->get()->sum('revenue_sum'),
        ];

        OperatorAuditService::viewed($bus, "Viewed bus {$bus->registration_number}");

        return view('operator.bus_detail', compact('operator', 'bus', 'upcomingTrips', 'tripHistory', 'stats'));
    }

    /**
     * Update the specified bus.
     */
    public function update(Request $request, $busId)
    {
        $operator = $this->getOperator();

        $bus = Bus::where('operator_id', $operator->id)->findOrFail($busId);

        $validated = $request->validate([
            'registration_number' => [
                'required', 'string', 'max:50',
                Rule::unique('buses')->where('operator_id', $operator->id)->ignore($bus->id),
            ],
            'model' => 'nullable|string|max:255',
            'seat_capacity' => 'required|integer|min:1|max:100',
            'bus_class' => 'required|in:economy,business,luxury',
            'amenities' => 'nullable|array',
            'amenities.*' => 'string|in:wifi,ac,usb_charging,entertainment,refreshments,restroom',
            'is_active' => 'boolean',
            'last_maintenance_date' => 'nullable|date',
            'next_maintenance_date' => 'nullable|date|after_or_equal:last_maintenance_date',
            'mileage_km' => 'nullable|integer|min:0',
            'notes' => 'nullable|string|max:2000',
        ]);

        $original = $bus->getOriginal();
        $bus->update([
            'registration_number' => strtoupper($validated['registration_number']),
            'model' => $validated['model'] ?? null,
            'seat_capacity' => $validated['seat_capacity'],
            'bus_class' => $validated['bus_class'],
            'amenities' => $validated['amenities'] ?? [],
            'is_active' => $validated['is_active'] ?? $bus->is_active,
            'last_maintenance_date' => $validated['last_maintenance_date'] ?? null,
            'next_maintenance_date' => $validated['next_maintenance_date'] ?? null,
            'mileage_km' => $validated['mileage_km'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        OperatorAuditService::updated($bus, $original, "Updated bus {$bus->registration_number}");

        return redirect()->route('operator.buses.show', $bus->id)
            ->with('success', "Bus {$bus->registration_number} updated successfully.");
    }

    /**
     * Toggle bus active status (soft delete alternative).
     */
    public function toggleStatus($busId)
    {
        $operator = $this->getOperator();

        $bus = Bus::where('operator_id', $operator->id)->findOrFail($busId);

        // Check if bus has upcoming trips before deactivating
        if ($bus->is_active) {
            $hasUpcoming = Route::where('bus_id', $bus->id)
                ->where('travel_date', '>=', Carbon::today())
                ->where('is_active', true)
                ->exists();
            if ($hasUpcoming) {
                return back()->withErrors([
                    'status' => 'Cannot deactivate bus with upcoming scheduled trips. Please reassign or cancel them first.'
                ]);
            }
        }

        $bus->update(['is_active' => !$bus->is_active]);

        $status = $bus->is_active ? 'activated' : 'deactivated';
        OperatorAuditService::log('bus.status_updated', "{$status} bus {$bus->registration_number}", $bus);

        return back()->with('success', "Bus {$bus->registration_number} has been {$status}.");
    }

    /**
     * Remove the specified bus (soft delete).
     */
    public function destroy($busId)
    {
        $operator = $this->getOperator();

        $bus = Bus::where('operator_id', $operator->id)->findOrFail($busId);

        $hasUpcoming = Route::where('bus_id', $bus->id)
            ->where('travel_date', '>=', Carbon::today())
            ->where('is_active', true)
            ->exists();

        if ($hasUpcoming) {
            return back()->withErrors([
                'delete' => 'Cannot delete bus with upcoming scheduled trips. Deactivate it instead.'
            ]);
        }

        $regNumber = $bus->registration_number;
        OperatorAuditService::deleted($bus, "Removed bus {$regNumber} from fleet");
        $bus->delete();

        return redirect()->route('operator.buses.index')
            ->with('success', "Bus {$regNumber} has been removed from your fleet.");
    }

    private function getOperator()
    {
        if (Auth::guard('operator')->check()) {
            $operator = Auth::guard('operator')->user();
            session(['operator_id' => $operator->id]);
            return $operator;
        }
        if (session('operator_id')) {
            $operator = Operator::find(session('operator_id'));
            if ($operator) return $operator;
        }
        abort(403, 'Operator session expired. Please log in again.');
    }
}