<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Bus;
use App\Models\Operator;
use App\Models\Route;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TripManagementController extends Controller
{
    /**
     * Display the trip management dashboard
     */
    public function index(Request $request)
    {
        // Get the authenticated operator
        $operator = $this->getOperator();

        // Get all trips for this operator with filters
        $trips = $this->getOperatorTrips($operator, $request);

        // Get buses for the dropdown and display
        $buses = $this->getOperatorBuses($operator);

        // Get routes for the dropdown
        $routes = $this->getOperatorRoutes($operator);

        // Get status styles
        $status_styles = $this->getStatusStyles();

        // Get summary statistics
        $stats = $this->getTripStats($operator);

        return view('manage_trips', compact(
            'operator',
            'trips',
            'buses',
            'routes',
            'status_styles',
            'stats'
        ));
    }

    /**
     * Get authenticated operator (works for both API and web)
     */
    private function getOperator()
    {
        return Auth::guard('operator')->user() ?? Operator::find(session('operator_id'));
    }

    /**
     * Get all trips for an operator with filters
     */
    private function getOperatorTrips($operator, $request)
    {
        $query = Route::with(['bus', 'operator', 'bookings' => function ($q) {
            $q->where('status', '!=', 'cancelled');
        }])
            ->where('operator_id', $operator->id)
            ->where('travel_date', '>=', Carbon::today()->subDays(7))
            ->orderBy('travel_date', 'asc')
            ->orderBy('departure_time', 'asc');

        // Apply status filter based on trip status logic
        if ($request->filled('status')) {
            $status = $request->status;
            $query->where(function ($q) use ($status) {
                $this->applyTripStatusFilter($q, $status);
            });
        }

        // Apply date filter
        if ($request->filled('date_filter')) {
            $this->applyDateFilter($query, $request->date_filter);
        }

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('origin', 'like', "%{$search}%")
                  ->orWhere('destination', 'like', "%{$search}%")
                  ->orWhereHas('bus', function ($bus) use ($search) {
                      $bus->where('registration_number', 'like', "%{$search}%");
                  });
            });
        }

        $routes = $query->get();

        // Transform routes into trip format for the view
        return $routes->map(function ($route) {
            return $this->formatTripData($route);
        });
    }

    /**
     * Apply trip status filter to query
     */
    private function applyTripStatusFilter($query, $status)
    {
        $now = Carbon::now();

        switch ($status) {
            case 'scheduled':
                $query->where(function ($q) use ($now) {
                    $q->where('travel_date', '>', $now->toDateString())
                      ->orWhere(function ($sub) use ($now) {
                          $sub->where('travel_date', $now->toDateString())
                              ->where('departure_time', '>', $now->format('H:i:s'));
                      });
                });
                break;

            case 'on_route':
                $query->where(function ($q) use ($now) {
                    $q->where('travel_date', $now->toDateString())
                      ->where('departure_time', '<=', $now->format('H:i:s'))
                      ->where('departure_time', '>', $now->subHours(2)->format('H:i:s'))
                      ->whereHas('bookings', function ($b) {
                          $b->where('status', 'confirmed');
                      });
                });
                break;

            case 'delayed':
                $query->where(function ($q) use ($now) {
                    $q->where('travel_date', $now->toDateString())
                      ->where('departure_time', '<', $now->subHours(2)->format('H:i:s'))
                      ->whereHas('bookings', function ($b) {
                          $b->where('status', 'confirmed');
                      });
                });
                break;

            case 'completed':
                $query->where(function ($q) use ($now) {
                    $q->where('travel_date', '<', $now->toDateString())
                      ->orWhere(function ($sub) use ($now) {
                          $sub->where('travel_date', $now->toDateString())
                              ->where('departure_time', '<', $now->subHours(2)->format('H:i:s'));
                      });
                })->whereHas('bookings', function ($b) {
                    $b->where('status', 'confirmed');
                });
                break;

            case 'cancelled':
                $query->where('is_active', false);
                break;

            default:
                // No filter applied
                break;
        }
    }

    /**
     * Apply date filter to query
     */
    private function applyDateFilter($query, $filter)
    {
        switch ($filter) {
            case 'today':
                $query->where('travel_date', Carbon::today());
                break;
            case 'yesterday':
                $query->where('travel_date', Carbon::yesterday());
                break;
            case 'tomorrow':
                $query->where('travel_date', Carbon::tomorrow());
                break;
            case 'this_week':
                $query->whereBetween('travel_date', [
                    Carbon::now()->startOfWeek(),
                    Carbon::now()->endOfWeek()
                ]);
                break;
            case 'this_month':
                $query->whereMonth('travel_date', Carbon::now()->month)
                      ->whereYear('travel_date', Carbon::now()->year);
                break;
            case 'next_week':
                $query->whereBetween('travel_date', [
                    Carbon::now()->addWeek()->startOfWeek(),
                    Carbon::now()->addWeek()->endOfWeek()
                ]);
                break;
            default:
                // No filter applied
                break;
        }
    }

    /**
     * Format route data into trip format
     */
    private function formatTripData($route)
    {
        // Determine trip status
        $status = $this->determineTripStatus($route);

        // Get confirmed bookings count
        $bookedCount = $route->bookings()
            ->where('status', 'confirmed')
            ->count();

        // Get pending bookings count
        $pendingCount = $route->bookings()
            ->where('status', 'pending')
            ->count();

        $capacity = $route->bus->seat_capacity ?? 49;

        // Calculate occupancy percentage
        $occupancyPercentage = $capacity > 0 ? round(($bookedCount / $capacity) * 100) : 0;

        return [
            'id' => 'TRP-' . str_pad($route->id, 4, '0', STR_PAD_LEFT),
            'route_id' => $route->id,
            'route_from' => $route->origin,
            'route_to' => $route->destination,
            'date' => Carbon::parse($route->travel_date)->format('d M Y'),
            'date_raw' => $route->travel_date,
            'departure' => Carbon::parse($route->departure_time)->format('H:i'),
            'departure_raw' => $route->departure_time,
            'arrival' => $route->arrival_time ? Carbon::parse($route->arrival_time)->format('H:i') : '--:--',
            'bus' => $route->bus->registration_number ?? 'N/A',
            'bus_id' => $route->bus_id,
            'class' => $route->bus->bus_class ?? 'economy',
            'booked' => $bookedCount,
            'pending' => $pendingCount,
            'capacity' => $capacity,
            'fare' => $route->fare,
            'status' => $status['label'],
            'status_type' => $status['type'],
            'occupancy_percentage' => $occupancyPercentage,
            'is_active' => $route->is_active,
            'has_bookings' => $bookedCount > 0 || $pendingCount > 0,
        ];
    }

    /**
     * Determine trip status with comprehensive logic
     */
    private function determineTripStatus($route)
{
    $now = Carbon::now();

    // FIX: Handle travel_date properly
    $travelDate = $route->travel_date;

    // If travel_date is already a datetime, extract just the date part
    if (strpos($travelDate, ' ') !== false) {
        $travelDate = explode(' ', $travelDate)[0];
    }

    // Parse the date
    $travelDateCarbon = Carbon::parse($travelDate);

    // Parse departure time
    $departureTime = $route->departure_time;

    // If departure_time is already full datetime, extract just the time
    if (strpos($departureTime, ' ') !== false) {
        $departureTime = explode(' ', $departureTime)[1];
    }

    // Combine date and time safely
    $departureDateTime = Carbon::parse($travelDate . ' ' . $departureTime);

    // Get booking counts
    $confirmedCount = $route->bookings()->where('status', 'confirmed')->count();
    $pendingCount = $route->bookings()->where('status', 'pending')->count();
    $cancelledCount = $route->bookings()->where('status', 'cancelled')->count();
    $totalBookings = $confirmedCount + $pendingCount + $cancelledCount;

    // Check if route is inactive (cancelled)
    if (!$route->is_active) {
        return ['label' => 'Cancelled', 'type' => 'cancelled'];
    }

    // Check if trip is in the future
    if ($departureDateTime->isFuture()) {
        if ($departureDateTime->isToday()) {
            $hoursUntilDeparture = $now->diffInHours($departureDateTime);

            if ($hoursUntilDeparture <= 2 && $confirmedCount > 0) {
                return ['label' => 'Boarding Soon', 'type' => 'scheduled'];
            }

            if ($hoursUntilDeparture <= 6 && $confirmedCount > 0) {
                return ['label' => 'Scheduled', 'type' => 'scheduled'];
            }
        }

        if ($pendingCount > 0 && $departureDateTime->isToday()) {
            return ['label' => 'Awaiting Payment', 'type' => 'scheduled'];
        }

        return ['label' => 'Scheduled', 'type' => 'scheduled'];
    }

    // Trip is today or in the past
    if ($departureDateTime->isToday()) {
        $minutesSinceDeparture = $now->diffInMinutes($departureDateTime);

        if ($minutesSinceDeparture <= 15) {
            return ['label' => 'Departing', 'type' => 'on_route'];
        }

        if ($minutesSinceDeparture <= 120) {
            if ($confirmedCount > 0) {
                return ['label' => 'On Route', 'type' => 'on_route'];
            }
            return ['label' => 'On Route (Empty)', 'type' => 'on_route'];
        }

        if ($minutesSinceDeparture > 120) {
            if ($route->arrival_time) {
                // FIX: Handle arrival time safely
                $arrivalTime = $route->arrival_time;
                if (strpos($arrivalTime, ' ') !== false) {
                    $arrivalTime = explode(' ', $arrivalTime)[1];
                }
                $arrivalDateTime = Carbon::parse($travelDate . ' ' . $arrivalTime);

                if ($arrivalDateTime->isPast()) {
                    return ['label' => 'Completed', 'type' => 'completed'];
                }
            }

            if ($confirmedCount === 0 && $pendingCount === 0) {
                return ['label' => 'No Show', 'type' => 'cancelled'];
            }

            return ['label' => 'Completed', 'type' => 'completed'];
        }
    }

    // Trip was in the past
    if ($departureDateTime->isPast()) {
        if ($route->arrival_time) {
            // FIX: Handle arrival time safely
            $arrivalTime = $route->arrival_time;
            if (strpos($arrivalTime, ' ') !== false) {
                $arrivalTime = explode(' ', $arrivalTime)[1];
            }
            $arrivalDateTime = Carbon::parse($travelDate . ' ' . $arrivalTime);

            if ($arrivalDateTime->isPast()) {
                return ['label' => 'Completed', 'type' => 'completed'];
            }

            if ($arrivalDateTime->isFuture()) {
                return ['label' => 'On Route', 'type' => 'on_route'];
            }
        }

        if ($departureDateTime->isYesterday() || $departureDateTime->isBefore(Carbon::yesterday())) {
            return ['label' => 'Completed', 'type' => 'completed'];
        }

        return ['label' => 'On Route', 'type' => 'on_route'];
    }

    // Default fallback
    return ['label' => 'Scheduled', 'type' => 'scheduled'];
}


    /**
     * Get operator's buses with detailed info
     */
    private function getOperatorBuses($operator)
    {
        return Bus::where('operator_id', $operator->id)
            ->where('is_active', true)
            ->get()
            ->map(function ($bus) {
                // Get today's trips for this bus
                $todayTrips = Route::where('bus_id', $bus->id)
                    ->where('travel_date', Carbon::today())
                    ->where('is_active', true)
                    ->count();

                return [
                    'id' => $bus->id,
                    'plate' => $bus->registration_number,
                    'model' => $bus->model ?? 'Bus',
                    'capacity' => $bus->seat_capacity,
                    'class' => $bus->bus_class ?? 'economy',
                    'amenities' => $bus->amenities ?? [],
                    'today_trips' => $todayTrips,
                    'is_active' => $bus->is_active,
                ];
            });
    }

    /**
     * Get operator's routes for dropdown
     */
    private function getOperatorRoutes($operator)
    {
        return Route::where('operator_id', $operator->id)
            ->where('travel_date', '>=', Carbon::today())
            ->where('is_active', true)
            ->get()
            ->unique(function ($route) {
                return $route->origin . '|' . $route->destination;
            })
            ->map(function ($route) {
                return [
                    'from' => $route->origin,
                    'to' => $route->destination,
                    'id' => $route->id,
                    'display' => $route->origin . ' → ' . $route->destination,
                ];
            })
            ->values();
    }

    /**
     * Get status styles for badges
     */
    private function getStatusStyles()
    {
        return [
            'scheduled' => 'bg-primary/10 text-primary',
            'on_route' => 'bg-secondary-container/20 text-secondary',
            'delayed' => 'bg-tertiary-container/10 text-tertiary',
            'completed' => 'bg-surface-container-high text-on-surface-variant',
            'cancelled' => 'bg-error-container/20 text-error',
            'boarding_soon' => 'bg-secondary/10 text-secondary',
            'departing' => 'bg-primary/20 text-primary',
            'awaiting_payment' => 'bg-tertiary/10 text-tertiary',
            'no_show' => 'bg-error/10 text-error',
            'on_route_empty' => 'bg-surface-container-high text-on-surface-variant',
        ];
    }

    /**
     * Get trip statistics for dashboard
     */
    private function getTripStats($operator)
    {
        $today = Carbon::today();
        $now = Carbon::now();

        // Today's trips
        $todayTrips = Route::where('operator_id', $operator->id)
            ->where('travel_date', $today)
            ->where('is_active', true)
            ->get();

        // Upcoming trips (next 7 days)
        $upcomingTrips = Route::where('operator_id', $operator->id)
            ->where('travel_date', '>=', $today)
            ->where('travel_date', '<=', $today->copy()->addDays(7))
            ->where('is_active', true)
            ->count();

        // Total bookings today
        $todayBookings = Booking::whereHas('route', function ($q) use ($operator, $today) {
            $q->where('operator_id', $operator->id)
              ->where('travel_date', $today);
        })->where('status', 'confirmed')->count();

        // Revenue today
        $todayRevenue = Booking::whereHas('route', function ($q) use ($operator, $today) {
            $q->where('operator_id', $operator->id)
              ->where('travel_date', $today);
        })->where('status', 'confirmed')->sum('amount');

        // Average occupancy for today's trips
        $totalCapacity = 0;
        $totalOccupancy = 0;
        foreach ($todayTrips as $trip) {
            $capacity = $trip->bus->seat_capacity ?? 49;
            $booked = $trip->bookings()->where('status', 'confirmed')->count();
            $totalCapacity += $capacity;
            $totalOccupancy += $booked;
        }
        $avgOccupancy = $totalCapacity > 0 ? round(($totalOccupancy / $totalCapacity) * 100) : 0;

        // Pending bookings that need attention
        $pendingBookings = Booking::whereHas('route', function ($q) use ($operator) {
            $q->where('operator_id', $operator->id);
        })->where('status', 'pending')
          ->where('held_until', '>', $now)
          ->count();

        // Cancelled bookings today
        $cancelledToday = Booking::whereHas('route', function ($q) use ($operator, $today) {
            $q->where('operator_id', $operator->id)
              ->where('travel_date', $today);
        })->where('status', 'cancelled')->count();

        return [
            'total_trips_today' => $todayTrips->count(),
            'upcoming_trips' => $upcomingTrips,
            'today_bookings' => $todayBookings,
            'today_revenue' => $todayRevenue,
            'avg_occupancy' => $avgOccupancy,
            'pending_bookings' => $pendingBookings,
            'cancelled_today' => $cancelledToday,
            'active_trips' => $todayTrips->filter(function ($trip) {
                return $this->determineTripStatus($trip)['type'] === 'on_route';
            })->count(),
        ];
    }

    /**
     * Show seat map for a specific trip
     */
    public function seatMap($tripId)
    {
        $operator = $this->getOperator();

        if (!$operator) {
            return redirect()->route('operator.login')
                ->with('error', 'Please log in to access this page.');
        }

        $route = Route::with(['bus', 'operator', 'bookings' => function ($q) {
            $q->whereIn('status', ['confirmed', 'pending']);
        }, 'bookings.user'])
            ->where('operator_id', $operator->id)
            ->findOrFail($tripId);

        // Get all booked seats (confirmed + pending)
        $bookedSeats = $route->bookings->pluck('seat_number')->toArray();

        // Get pending seats (for hold display)
        $pendingSeats = $route->bookings
            ->where('status', 'pending')
            ->pluck('seat_number')
            ->toArray();

        // Get passenger names for booked seats
        $passengerMap = [];
        foreach ($route->bookings as $booking) {
            if ($booking->status === 'confirmed') {
                $passengerMap[$booking->seat_number] = [
                    'name' => $booking->user->full_name ?? 'Passenger',
                    'status' => 'confirmed',
                    'booking_id' => $booking->id,
                ];
            } elseif ($booking->status === 'pending') {
                $passengerMap[$booking->seat_number] = [
                    'name' => 'Hold - ' . ($booking->user->full_name ?? 'Pending'),
                    'status' => 'pending',
                    'booking_id' => $booking->id,
                ];
            }
        }

        // Generate seat map
        $capacity = $route->bus->seat_capacity ?? 49;
        $seats = $this->generateSeatMap($capacity, $bookedSeats, $pendingSeats, $passengerMap);

        // Get trip data
        $trip = $this->formatTripData($route);

        return view('operator.seat_map', compact(
            'route',
            'seats',
            'bookedSeats',
            'pendingSeats',
            'passengerMap',
            'trip',
            'capacity'
        ));
    }

    /**
     * Generate seat map array
     */
    private function generateSeatMap($capacity, $bookedSeats, $pendingSeats = [], $passengerMap = [])
    {
        $seats = [];
        $columns = 4;
        $rows = ceil($capacity / $columns);
        $seatNumber = 1;

        for ($row = 0; $row < $rows; $row++) {
            $rowSeats = [];
            for ($col = 0; $col < $columns; $col++) {
                if ($seatNumber <= $capacity) {
                    $status = 'available';
                    if (in_array($seatNumber, $bookedSeats)) {
                        $status = 'booked';
                    }
                    if (in_array($seatNumber, $pendingSeats)) {
                        $status = 'pending';
                    }

                    $rowSeats[] = [
                        'number' => $seatNumber,
                        'status' => $status,
                        'row' => $row + 1,
                        'col' => $col + 1,
                        'passenger' => $passengerMap[$seatNumber] ?? null,
                    ];
                }
                $seatNumber++;
            }
            if (!empty($rowSeats)) {
                $seats[] = $rowSeats;
            }
        }

        return $seats;
    }

    /**
     * Store a new trip
     */
    public function store(Request $request)
    {
        $operator = $this->getOperator();

        if (!$operator) {
            return redirect()->route('operator.login')
                ->with('error', 'Please log in to create trips.');
        }

        $validated = $request->validate([
            'origin' => 'required|string|max:255',
            'destination' => 'required|string|max:255|different:origin',
            'travel_date' => 'required|date|after_or_equal:today',
            'departure_time' => 'required|date_format:H:i',
            'arrival_time' => 'nullable|date_format:H:i',
            'bus_id' => 'required|exists:buses,id',
            'fare' => 'required|numeric|min:0',
            'distance_km' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        // Verify bus belongs to operator
        $bus = Bus::where('id', $validated['bus_id'])
            ->where('operator_id', $operator->id)
            ->first();

        if (!$bus) {
            return back()->withErrors([
                'bus_id' => 'The selected bus does not belong to your fleet.'
            ]);
        }

        // Check if bus is already scheduled for this time
        $existingRoute = Route::where('bus_id', $bus->id)
            ->where('travel_date', $validated['travel_date'])
            ->where('departure_time', $validated['departure_time'])
            ->where('is_active', true)
            ->exists();

        if ($existingRoute) {
            return back()->withErrors([
                'bus_id' => 'This bus is already scheduled for the selected date and time.'
            ])->withInput();
        }

        // Check if bus has overlapping trip (within 2 hours)
        $overlappingRoute = Route::where('bus_id', $bus->id)
            ->where('travel_date', $validated['travel_date'])
            ->where('is_active', true)
            ->where(function ($q) use ($validated) {
                $time = $validated['departure_time'];
                $q->whereBetween('departure_time', [
                    Carbon::parse($time)->subHours(2)->format('H:i'),
                    Carbon::parse($time)->addHours(2)->format('H:i'),
                ]);
            })
            ->exists();

        if ($overlappingRoute) {
            return back()->withErrors([
                'departure_time' => 'This bus has another trip within 2 hours of the selected departure time.'
            ])->withInput();
        }

        // Create the route/trip
        try {
            abort_if($operator->id !== Auth::guard('operator_api')->id(), 403);
            DB::beginTransaction();
            $route = Route::create([
                'operator_id' => $operator->id,
                'bus_id' => $validated['bus_id'],
                'origin' => $validated['origin'],
                'destination' => $validated['destination'],
                'travel_date' => $validated['travel_date'],
                'departure_time' => $validated['departure_time'],
                'arrival_time' => $validated['arrival_time'] ?? null,
                'fare' => $validated['fare'],
                'distance_km' => $validated['distance_km'] ?? 0,
                'is_active' => true,
            ]);

            DB::commit();

            $tripId = 'TRP-' . str_pad($route->id, 4, '0', STR_PAD_LEFT);

            return redirect()->route('operator.trips.index')
                ->with('success', "Trip {$tripId} created successfully! Seat map is now available for booking.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Trip creation failed: ' . $e->getMessage());

            return back()->withErrors([
                'error' => 'Failed to create trip. Please try again.'
            ])->withInput();
        }
    }

    /**
     * Update an existing trip
     */
    public function update(Request $request, $tripId)
    {
        $operator = $this->getOperator();

        if (!$operator) {
            return redirect()->route('operator.login')
                ->with('error', 'Please log in to update trips.');
        }

        $route = Route::where('operator_id', $operator->id)
            ->findOrFail($tripId);

        // Check if trip has confirmed bookings
        $hasConfirmedBookings = $route->bookings()
            ->where('status', 'confirmed')
            ->exists();

        $validated = $request->validate([
            'fare' => 'nullable|numeric|min:0',
            'departure_time' => 'nullable|date_format:H:i',
            'travel_date' => 'nullable|date|after_or_equal:today',
            'bus_id' => 'nullable|exists:buses,id',
            'arrival_time' => 'nullable|date_format:H:i',
            'is_active' => 'nullable|boolean',
        ]);

        // If changing bus, verify it belongs to operator
        if ($request->filled('bus_id')) {
            $bus = Bus::where('id', $validated['bus_id'])
                ->where('operator_id', $operator->id)
                ->first();

            if (!$bus) {
                return back()->withErrors([
                    'bus_id' => 'The selected bus does not belong to your fleet.'
                ]);
            }

            // Check for conflicts with new bus
            if ($request->filled('travel_date') && $request->filled('departure_time')) {
                $conflict = Route::where('bus_id', $validated['bus_id'])
                    ->where('travel_date', $validated['travel_date'])
                    ->where('departure_time', $validated['departure_time'])
                    ->where('id', '!=', $tripId)
                    ->where('is_active', true)
                    ->exists();

                if ($conflict) {
                    return back()->withErrors([
                        'bus_id' => 'The selected bus is already scheduled for this date and time.'
                    ]);
                }
            }
        }

        // If changing date/time, check for conflicts
        if ($request->filled('travel_date') && $request->filled('departure_time')) {
            $conflict = Route::where('bus_id', $route->bus_id)
                ->where('travel_date', $validated['travel_date'])
                ->where('departure_time', $validated['departure_time'])
                ->where('id', '!=', $tripId)
                ->where('is_active', true)
                ->exists();

            if ($conflict) {
                return back()->withErrors([
                    'departure_time' => 'This bus is already scheduled for the selected date and time.'
                ]);
            }
        }

        // If fare is being reduced, check if it affects confirmed bookings
        if ($request->filled('fare') && $hasConfirmedBookings) {
            $oldFare = $route->fare;
            $newFare = $validated['fare'];

            // Log fare change for audit
            Log::info('Fare change for trip', [
                'trip_id' => $tripId,
                'old_fare' => $oldFare,
                'new_fare' => $newFare,
                'operator_id' => $operator->id,
                'has_bookings' => $hasConfirmedBookings,
            ]);
        }

        try {
            DB::beginTransaction();

            $route->update($validated);

            DB::commit();

            return redirect()->route('operator.trips.index')
                ->with('success', 'Trip updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Trip update failed: ' . $e->getMessage());

            return back()->withErrors([
                'error' => 'Failed to update trip. Please try again.'
            ])->withInput();
        }
    }

    /**
     * Cancel a trip
     */
    public function cancel($tripId)
    {
        $operator = $this->getOperator();

        if (!$operator) {
            return redirect()->route('operator.login')
                ->with('error', 'Please log in to cancel trips.');
        }

        $route = Route::where('operator_id', $operator->id)
            ->findOrFail($tripId);

        // Check if trip has confirmed bookings
        $confirmedBookings = $route->bookings()
            ->where('status', 'confirmed')
            ->get();

        if ($confirmedBookings->count() > 0) {
            // Instead of blocking, we can allow cancellation with notification
            // But we need to handle refunds or notifications
            return back()->withErrors([
                'trip' => "Cannot cancel trip with {$confirmedBookings->count()} confirmed bookings. Please notify passengers first or process refunds."
            ]);
        }

        try {
            DB::beginTransaction();

            // Cancel all pending bookings
            $cancelledPending = $route->bookings()
                ->where('status', 'pending')
                ->update(['status' => 'cancelled']);

            // Mark route as inactive and soft delete
            $route->update([
                'is_active' => false,
            ]);
            $route->delete();

            DB::commit();

            $tripId = 'TRP-' . str_pad($route->id, 4, '0', STR_PAD_LEFT);

            $message = "Trip {$tripId} cancelled successfully.";
            if ($cancelledPending > 0) {
                $message .= " {$cancelledPending} pending booking(s) were automatically cancelled.";
            }

            return redirect()->route('operator.trips.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Trip cancellation failed: ' . $e->getMessage());

            return back()->withErrors([
                'error' => 'Failed to cancel trip. Please try again.'
            ]);
        }
    }

    /**
     * Get seat occupancy for a specific trip (API)
     */
    public function occupancy($tripId)
    {
        $operator = $this->getOperator();

        if (!$operator) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $route = Route::with(['bookings' => function ($q) {
            $q->whereIn('status', ['confirmed', 'pending']);
        }])->where('operator_id', $operator->id)
          ->findOrFail($tripId);

        $confirmedSeats = $route->bookings
            ->where('status', 'confirmed')
            ->pluck('seat_number')
            ->toArray();

        $pendingSeats = $route->bookings
            ->where('status', 'pending')
            ->pluck('seat_number')
            ->toArray();

        $capacity = $route->bus->seat_capacity ?? 49;

        // Generate detailed seat map
        $seatMap = $this->generateDetailedSeatMap($capacity, $confirmedSeats, $pendingSeats);

        return response()->json([
            'trip_id' => 'TRP-' . str_pad($route->id, 4, '0', STR_PAD_LEFT),
            'route' => $route->origin . ' → ' . $route->destination,
            'date' => $route->travel_date,
            'departure' => $route->departure_time,
            'capacity' => $capacity,
            'confirmed' => count($confirmedSeats),
            'pending' => count($pendingSeats),
            'available' => $capacity - count($confirmedSeats) - count($pendingSeats),
            'confirmed_seats' => $confirmedSeats,
            'pending_seats' => $pendingSeats,
            'occupancy_percentage' => round((count($confirmedSeats) / $capacity) * 100),
            'seat_map' => $seatMap,
            'status' => $this->determineTripStatus($route),
        ]);
    }

    /**
     * Generate detailed seat map for API
     */
    private function generateDetailedSeatMap($capacity, $confirmedSeats, $pendingSeats = [])
    {
        $seatMap = [];
        $columns = 4;
        $rows = ceil($capacity / $columns);
        $seatNumber = 1;

        for ($row = 0; $row < $rows; $row++) {
            $rowData = ['row' => $row + 1, 'seats' => []];
            for ($col = 0; $col < $columns; $col++) {
                if ($seatNumber <= $capacity) {
                    $status = 'available';
                    if (in_array($seatNumber, $confirmedSeats)) {
                        $status = 'confirmed';
                    } elseif (in_array($seatNumber, $pendingSeats)) {
                        $status = 'pending';
                    }
                    $rowData['seats'][] = [
                        'number' => $seatNumber,
                        'status' => $status,
                    ];
                }
                $seatNumber++;
            }
            $seatMap[] = $rowData;
        }

        return $seatMap;
    }

    /**
     * Export trips data (CSV)
     */
    public function export(Request $request)
    {
        $operator = $this->getOperator();

        if (!$operator) {
            return redirect()->route('operator.login')
                ->with('error', 'Please log in to export data.');
        }

        $trips = $this->getOperatorTrips($operator, $request);

        $filename = 'trips_export_' . Carbon::now()->format('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ];

        $callback = function () use ($trips) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM for Excel compatibility
            fwrite($handle, "\xEF\xBB\xBF");

            // Headers
            fputcsv($handle, [
                'Trip ID',
                'Route',
                'Date',
                'Departure',
                'Arrival',
                'Bus',
                'Class',
                'Booked',
                'Pending',
                'Capacity',
                'Occupancy %',
                'Fare (ZMW)',
                'Status',
                'Has Bookings',
            ]);

            // Data
            foreach ($trips as $trip) {
                fputcsv($handle, [
                    $trip['id'],
                    $trip['route_from'] . ' → ' . $trip['route_to'],
                    $trip['date'],
                    $trip['departure'],
                    $trip['arrival'] ?? '--:--',
                    $trip['bus'],
                    ucfirst($trip['class']),
                    $trip['booked'],
                    $trip['pending'] ?? 0,
                    $trip['capacity'],
                    $trip['occupancy_percentage'] . '%',
                    number_format($trip['fare'], 2),
                    $trip['status'],
                    $trip['has_bookings'] ? 'Yes' : 'No',
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get upcoming trips (API endpoint)
     */
    public function upcoming(Request $request)
    {
        $operator = $this->getOperator();

        if (!$operator) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $limit = $request->input('limit', 10);
        $days = $request->input('days', 7);

        $trips = Route::with(['bus', 'bookings' => function ($q) {
            $q->where('status', 'confirmed');
        }])
            ->where('operator_id', $operator->id)
            ->where('travel_date', '>=', Carbon::today())
            ->where('travel_date', '<=', Carbon::today()->addDays($days))
            ->where('is_active', true)
            ->orderBy('travel_date', 'asc')
            ->orderBy('departure_time', 'asc')
            ->limit($limit)
            ->get()
            ->map(function ($route) {
                return $this->formatTripData($route);
            });

        return response()->json([
            'success' => true,
            'data' => $trips,
            'meta' => [
                'total' => $trips->count(),
                'limit' => $limit,
                'days' => $days,
            ]
        ]);
    }

    /**
     * Get trip details (API endpoint)
     */
    public function show($tripId)
    {
        $operator = $this->getOperator();

        if (!$operator) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $route = Route::with(['bus', 'operator', 'bookings' => function ($q) {
            $q->with('user');
        }])
            ->where('operator_id', $operator->id)
            ->findOrFail($tripId);

        $tripData = $this->formatTripData($route);

        // Get detailed booking info
        $bookings = $route->bookings->map(function ($booking) {
            return [
                'id' => $booking->id,
                'reference' => $booking->reference_id,
                'seat' => $booking->seat_number,
                'status' => $booking->status,
                'passenger' => $booking->user->full_name ?? 'Guest',
                'amount' => $booking->amount,
                'created_at' => $booking->created_at->format('Y-m-d H:i'),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'trip' => $tripData,
                'bookings' => $bookings,
                'summary' => [
                    'total_bookings' => $bookings->count(),
                    'confirmed' => $bookings->where('status', 'confirmed')->count(),
                    'pending' => $bookings->where('status', 'pending')->count(),
                    'cancelled' => $bookings->where('status', 'cancelled')->count(),
                    'revenue' => $bookings->where('status', 'confirmed')->sum('amount'),
                ]
            ]
        ]);
    }

    /**
     * Get trip statistics (API endpoint)
     */
    public function stats()
    {
        $operator = $this->getOperator();

        if (!$operator) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $stats = $this->getTripStats($operator);

        // Get weekly trend
        $weeklyTrend = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $bookings = Booking::whereHas('route', function ($q) use ($operator, $date) {
                $q->where('operator_id', $operator->id)
                  ->where('travel_date', $date);
            })->where('status', 'confirmed')->count();

            $weeklyTrend[] = [
                'date' => $date->format('D'),
                'bookings' => $bookings,
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'stats' => $stats,
                'weekly_trend' => $weeklyTrend,
            ]
        ]);
    }

    /**
     * Update trip status (API endpoint)
     */
    public function updateStatus(Request $request, $tripId)
    {
        $operator = $this->getOperator();

        if (!$operator) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'status' => 'required|in:scheduled,on_route,delayed,completed,cancelled',
        ]);

        $route = Route::where('operator_id', $operator->id)
            ->findOrFail($tripId);

        // Map status to route updates
        switch ($request->status) {
            case 'cancelled':
                if ($route->bookings()->where('status', 'confirmed')->exists()) {
                    return response()->json([
                        'error' => 'Cannot cancel trip with confirmed bookings.'
                    ], 422);
                }
                $route->update(['is_active' => false]);
                $route->bookings()->where('status', 'pending')->update(['status' => 'cancelled']);
                break;

            case 'completed':
                // Mark all confirmed bookings as completed
                $route->bookings()
                    ->where('status', 'confirmed')
                    ->update(['status' => 'completed']);
                break;

            default:
                // Status change doesn't require DB updates
                // It's calculated based on date/time
                break;
        }

        return response()->json([
            'success' => true,
            'message' => 'Trip status updated successfully.',
            'data' => $this->formatTripData($route->fresh()),
        ]);
    }
}
