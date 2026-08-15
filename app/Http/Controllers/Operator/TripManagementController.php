<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Bus;
use App\Models\Driver;
use App\Models\Operator;
use App\Models\Route;
use App\Models\Ticket;
use App\Notifications\TripStatusChanged;
use App\Services\OperatorAuditService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class TripManagementController extends Controller
{
    /**
     * Display the trip calendar view.
     */
    public function calendar(Request $request)
    {
        $operator = $this->getOperator();

        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        $currentDate = Carbon::createFromDate($year, $month, 1);
        $startOfMonth = $currentDate->copy()->startOfMonth();
        $endOfMonth = $currentDate->copy()->endOfMonth();

        $prevMonth = $currentDate->copy()->subMonth();
        $nextMonth = $currentDate->copy()->addMonth();

        $routes = Route::with(['bus'])
            ->where('operator_id', $operator->id)
            ->whereBetween('travel_date', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
            ->orderBy('travel_date', 'asc')
            ->orderBy('departure_time', 'asc')
            ->get();

        $tripsByDate = [];
        foreach ($routes as $route) {
            $dateKey = $route->travel_date instanceof Carbon
                ? $route->travel_date->format('Y-m-d')
                : Carbon::parse($route->travel_date)->format('Y-m-d');

            $statusInfo = $this->determineTripStatus($route);

            $bookedCount = $route->bookings()
                ->where('status', 'confirmed')
                ->count();

            if (!isset($tripsByDate[$dateKey])) {
                $tripsByDate[$dateKey] = [];
            }

            $tripsByDate[$dateKey][] = [
                'id' => $route->id,
                'display_id' => 'TRP-' . str_pad($route->id, 4, '0', STR_PAD_LEFT),
                'origin' => $route->origin,
                'destination' => $route->destination,
                'departure' => $route->departure_time ? Carbon::parse($route->departure_time)->format('H:i') : '--:--',
                'bus' => $route->bus->registration_number ?? 'N/A',
                'booked' => $bookedCount,
                'capacity' => $route->bus->seat_capacity ?? 49,
                'status' => $statusInfo['label'],
                'status_type' => $statusInfo['type'],
                'fare' => $route->fare,
                'is_active' => $route->is_active,
            ];
        }

        $calendar = [];
        $daysInMonth = $startOfMonth->daysInMonth;
        $firstDayOfWeek = $startOfMonth->dayOfWeek;
        $firstDayOfWeek = $firstDayOfWeek === 0 ? 6 : $firstDayOfWeek - 1;

        $dayCounter = 1;
        $week = [];

        for ($i = 0; $i < $firstDayOfWeek; $i++) {
            $week[] = null;
        }

        while ($dayCounter <= $daysInMonth) {
            $dateString = sprintf('%s-%s-%s', $year, str_pad($month, 2, '0', STR_PAD_LEFT), str_pad($dayCounter, 2, '0', STR_PAD_LEFT));
            $isToday = $dateString === now()->format('Y-m-d');

            $week[] = [
                'day' => $dayCounter,
                'date' => $dateString,
                'is_today' => $isToday,
                'trips' => $tripsByDate[$dateString] ?? [],
                'trip_count' => count($tripsByDate[$dateString] ?? []),
                'total_booked' => array_sum(array_column($tripsByDate[$dateString] ?? [], 'booked')),
            ];

            if (count($week) === 7) {
                $calendar[] = $week;
                $week = [];
            }

            $dayCounter++;
        }

        if (!empty($week)) {
            while (count($week) < 7) {
                $week[] = null;
            }
            $calendar[] = $week;
        }

        $totalTrips = $routes->count();
        $totalBookings = 0;
        $totalRevenue = 0;
        foreach ($routes as $route) {
            $totalBookings += $route->bookings()->where('status', 'confirmed')->count();
            $totalRevenue += $route->bookings()->where('status', 'confirmed')->sum('amount');
        }

        return view('operator.trip_calendar', compact(
            'operator', 'currentDate', 'month', 'year', 'prevMonth', 'nextMonth',
            'calendar', 'totalTrips', 'totalBookings', 'totalRevenue',
        ));
    }

    /**
     * Display the trip management dashboard
     */
    public function index(Request $request)
    {
        $operator = $this->getOperator();
        $trips = $this->getOperatorTrips($operator, $request);
        $buses = $this->getOperatorBuses($operator);
        $routes = $this->getOperatorRoutes($operator);
        $status_styles = $this->getStatusStyles();
        $stats = $this->getTripStats($operator);

        return view('operator.manage_trips', compact('operator', 'trips', 'buses', 'routes', 'status_styles', 'stats'));
    }

    private function getOperator()
    {
        $operator = Auth::guard('operator')->user();
        if ($operator) {
            session(['operator_id' => $operator->id]);
            return $operator;
        }
        return Operator::find(session('operator_id'));
    }

    private function getOperatorTrips($operator, $request)
    {
        $query = Route::with(['bus', 'operator', 'bookings' => function ($q) {
            $q->where('status', '!=', 'cancelled');
        }])
            ->where('operator_id', $operator->id)
            ->where('travel_date', '>=', Carbon::today()->subDays(7))
            ->orderBy('travel_date', 'asc')
            ->orderBy('departure_time', 'asc');

        if ($request->filled('status')) {
            $status = $request->status;
            $query->where(function ($q) use ($status) {
                $this->applyTripStatusFilter($q, $status);
            });
        }

        if ($request->filled('date_filter')) {
            $this->applyDateFilter($query, $request->date_filter);
        }

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
        return $routes->map(function ($route) {
            return $this->formatTripData($route);
        });
    }

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
        }
    }

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
                $query->whereBetween('travel_date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                break;
            case 'this_month':
                $query->whereMonth('travel_date', Carbon::now()->month)->whereYear('travel_date', Carbon::now()->year);
                break;
            case 'next_week':
                $query->whereBetween('travel_date', [Carbon::now()->addWeek()->startOfWeek(), Carbon::now()->addWeek()->endOfWeek()]);
                break;
        }
    }

    private function formatTripData($route)
    {
        $status = $this->determineTripStatus($route);
        $bookedCount = $route->bookings()->where('status', 'confirmed')->count();
        $pendingCount = $route->bookings()->where('status', 'pending')->count();
        $capacity = $route->bus->seat_capacity ?? 49;
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

    private function determineTripStatus($route)
    {
        $now = Carbon::now();
        $travelDate = $route->travel_date;
        if (strpos($travelDate, ' ') !== false) {
            $travelDate = explode(' ', $travelDate)[0];
        }
        $travelDateCarbon = Carbon::parse($travelDate);
        $departureTime = $route->departure_time;
        if (strpos($departureTime, ' ') !== false) {
            $departureTime = explode(' ', $departureTime)[1];
        }
        $departureDateTime = Carbon::parse($travelDate . ' ' . $departureTime);
        $confirmedCount = $route->bookings()->where('status', 'confirmed')->count();
        $pendingCount = $route->bookings()->where('status', 'pending')->count();

        if (!$route->is_active) {
            return ['label' => 'Cancelled', 'type' => 'cancelled'];
        }

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

        if ($departureDateTime->isToday()) {
            $minutesSinceDeparture = $now->diffInMinutes($departureDateTime);
            if ($minutesSinceDeparture <= 15) {
                return ['label' => 'Departing', 'type' => 'on_route'];
            }
            if ($minutesSinceDeparture <= 120) {
                return ['label' => $confirmedCount > 0 ? 'On Route' : 'On Route (Empty)', 'type' => 'on_route'];
            }
            if ($minutesSinceDeparture > 120) {
                if ($route->arrival_time) {
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

        if ($departureDateTime->isPast()) {
            if ($route->arrival_time) {
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

        return ['label' => 'Scheduled', 'type' => 'scheduled'];
    }

    private function getOperatorBuses($operator)
    {
        return Bus::where('operator_id', $operator->id)
            ->where('is_active', true)
            ->get()
            ->map(function ($bus) {
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

    private function getTripStats($operator)
    {
        $today = Carbon::today();
        $now = Carbon::now();
        $todayTrips = Route::where('operator_id', $operator->id)
            ->where('travel_date', $today)->where('is_active', true)->get();
        $upcomingTrips = Route::where('operator_id', $operator->id)
            ->where('travel_date', '>=', $today)->where('travel_date', '<=', $today->copy()->addDays(7))
            ->where('is_active', true)->count();
        $todayBookings = Booking::whereHas('route', function ($q) use ($operator, $today) {
            $q->where('operator_id', $operator->id)->where('travel_date', $today);
        })->where('status', 'confirmed')->count();
        $todayRevenue = Booking::whereHas('route', function ($q) use ($operator, $today) {
            $q->where('operator_id', $operator->id)->where('travel_date', $today);
        })->where('status', 'confirmed')->sum('amount');
        $totalCapacity = 0; $totalOccupancy = 0;
        foreach ($todayTrips as $trip) {
            $capacity = $trip->bus->seat_capacity ?? 49;
            $booked = $trip->bookings()->where('status', 'confirmed')->count();
            $totalCapacity += $capacity; $totalOccupancy += $booked;
        }
        $avgOccupancy = $totalCapacity > 0 ? round(($totalOccupancy / $totalCapacity) * 100) : 0;
        $pendingBookings = Booking::whereHas('route', function ($q) use ($operator) {
            $q->where('operator_id', $operator->id);
        })->where('status', 'pending')->where('held_until', '>', $now)->count();
        $cancelledToday = Booking::whereHas('route', function ($q) use ($operator, $today) {
            $q->where('operator_id', $operator->id)->where('travel_date', $today);
        })->where('status', 'cancelled')->count();

        return [
            'total_trips_today' => $todayTrips->count(), 'upcoming_trips' => $upcomingTrips,
            'today_bookings' => $todayBookings, 'today_revenue' => $todayRevenue,
            'avg_occupancy' => $avgOccupancy, 'pending_bookings' => $pendingBookings,
            'cancelled_today' => $cancelledToday,
            'active_trips' => $todayTrips->filter(fn($t) => $this->determineTripStatus($t)['type'] === 'on_route')->count(),
        ];
    }

    public function seatMap($tripId)
    {
        $operator = $this->getOperator();
        if (!$operator) return redirect()->route('operator.login')->with('error', 'Please log in to access this page.');
        $route = Route::with(['bus', 'operator', 'bookings' => function ($q) { $q->whereIn('status', ['confirmed', 'pending']); }, 'bookings.user'])
            ->where('operator_id', $operator->id)->findOrFail($tripId);
        $bookedSeats = $route->bookings->pluck('seat_number')->toArray();
        $pendingSeats = $route->bookings->where('status', 'pending')->pluck('seat_number')->toArray();
        $passengerMap = [];
        foreach ($route->bookings as $booking) {
            if ($booking->status === 'confirmed') {
                $passengerMap[$booking->seat_number] = ['name' => $booking->user->full_name ?? 'Passenger', 'status' => 'confirmed', 'booking_id' => $booking->id];
            } elseif ($booking->status === 'pending') {
                $passengerMap[$booking->seat_number] = ['name' => 'Hold - ' . ($booking->user->full_name ?? 'Pending'), 'status' => 'pending', 'booking_id' => $booking->id];
            }
        }
        $capacity = $route->bus->seat_capacity ?? 49;
        $seats = $this->generateSeatMap($capacity, $bookedSeats, $pendingSeats, $passengerMap);
        $trip = $this->formatTripData($route);
        return view('operator.seat_map', compact('route', 'seats', 'bookedSeats', 'pendingSeats', 'passengerMap', 'trip', 'capacity'));
    }

    private function generateSeatMap($capacity, $bookedSeats, $pendingSeats = [], $passengerMap = [])
    {
        $seats = []; $columns = 4; $rows = ceil($capacity / $columns); $seatNumber = 1;
        for ($row = 0; $row < $rows; $row++) {
            $rowSeats = [];
            for ($col = 0; $col < $columns; $col++) {
                if ($seatNumber <= $capacity) {
                    $status = 'available';
                    if (in_array($seatNumber, $bookedSeats)) $status = 'booked';
                    if (in_array($seatNumber, $pendingSeats)) $status = 'pending';
                    $rowSeats[] = ['number' => $seatNumber, 'status' => $status, 'row' => $row + 1, 'col' => $col + 1, 'passenger' => $passengerMap[$seatNumber] ?? null];
                }
                $seatNumber++;
            }
            if (!empty($rowSeats)) $seats[] = $rowSeats;
        }
        return $seats;
    }

    public function store(Request $request)
    {
        $operator = $this->getOperator();
        if (!$operator) return redirect()->route('operator.login')->with('error', 'Please log in to create trips.');
        $validated = $request->validate([
            'origin' => 'required|string|max:255', 'destination' => 'required|string|max:255|different:origin',
            'travel_date' => 'required|date|after_or_equal:today', 'departure_time' => 'required|date_format:H:i',
            'arrival_time' => 'nullable|date_format:H:i', 'bus_id' => 'required|exists:buses,id',
            'driver_id' => 'nullable|exists:drivers,id',
            'fare' => 'required|numeric|min:0', 'distance_km' => 'nullable|numeric|min:0', 'notes' => 'nullable|string|max:500',
        ]);
        $bus = Bus::where('id', $validated['bus_id'])->where('operator_id', $operator->id)->first();
        if (!$bus) return back()->withErrors(['bus_id' => 'The selected bus does not belong to your fleet.']);
        $existingRoute = Route::where('bus_id', $bus->id)->where('travel_date', $validated['travel_date'])
            ->where('departure_time', $validated['departure_time'])->where('is_active', true)->exists();
        if ($existingRoute) return back()->withErrors(['bus_id' => 'This bus is already scheduled for the selected date and time.'])->withInput();
        $overlappingRoute = Route::where('bus_id', $bus->id)->where('travel_date', $validated['travel_date'])
            ->where('is_active', true)->where(function ($q) use ($validated) {
                $time = $validated['departure_time'];
                $q->whereBetween('departure_time', [Carbon::parse($time)->subHours(2)->format('H:i'), Carbon::parse($time)->addHours(2)->format('H:i')]);
            })->exists();
        if ($overlappingRoute) return back()->withErrors(['departure_time' => 'This bus has another trip within 2 hours of the selected departure time.'])->withInput();

        try {
            DB::beginTransaction();
            // Verify driver belongs to this operator if provided
            if ($request->filled('driver_id')) {
                $driver = Driver::where('id', $validated['driver_id'])->where('operator_id', $operator->id)->first();
                if (!$driver) return back()->withErrors(['driver_id' => 'The selected driver does not belong to your fleet.']);
            }

            $route = Route::create([
                'operator_id' => $operator->id, 'bus_id' => $validated['bus_id'], 'origin' => $validated['origin'],
                'destination' => $validated['destination'], 'travel_date' => $validated['travel_date'],
                'departure_time' => $validated['departure_time'], 'arrival_time' => $validated['arrival_time'] ?? null,
                'driver_id' => $validated['driver_id'] ?? null,
                'fare' => $validated['fare'], 'distance_km' => $validated['distance_km'] ?? 0, 'is_active' => true,
            ]);
            DB::commit();
            $tripId = 'TRP-' . str_pad($route->id, 4, '0', STR_PAD_LEFT);
            OperatorAuditService::created($route, "Created trip {$tripId}: {$route->origin} → {$route->destination}");

            return redirect()->route('operator.trips.index')->with('success', "Trip {$tripId} created successfully! Seat map is now available for booking.");
        } catch (\Exception $e) {
            DB::rollBack(); Log::error('Trip creation failed: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to create trip. Please try again.'])->withInput();
        }
    }

    public function update(Request $request, $tripId)
    {
        $operator = $this->getOperator();
        if (!$operator) return redirect()->route('operator.login')->with('error', 'Please log in to update trips.');
        $route = Route::where('operator_id', $operator->id)->findOrFail($tripId);
        $hasConfirmedBookings = $route->bookings()->where('status', 'confirmed')->exists();
        $validated = $request->validate([
            'fare' => 'nullable|numeric|min:0', 'departure_time' => 'nullable|date_format:H:i',
            'travel_date' => 'nullable|date|after_or_equal:today', 'bus_id' => 'nullable|exists:buses,id',
            'arrival_time' => 'nullable|date_format:H:i', 'is_active' => 'nullable|boolean',
        ]);
        if ($request->filled('bus_id')) {
            $bus = Bus::where('id', $validated['bus_id'])->where('operator_id', $operator->id)->first();
            if (!$bus) return back()->withErrors(['bus_id' => 'The selected bus does not belong to your fleet.']);
            if ($request->filled('travel_date') && $request->filled('departure_time')) {
                $conflict = Route::where('bus_id', $validated['bus_id'])->where('travel_date', $validated['travel_date'])
                    ->where('departure_time', $validated['departure_time'])->where('id', '!=', $tripId)->where('is_active', true)->exists();
                if ($conflict) return back()->withErrors(['bus_id' => 'The selected bus is already scheduled for this date and time.']);
            }
        }
        if ($request->filled('travel_date') && $request->filled('departure_time')) {
            $conflict = Route::where('bus_id', $route->bus_id)->where('travel_date', $validated['travel_date'])
                ->where('departure_time', $validated['departure_time'])->where('id', '!=', $tripId)->where('is_active', true)->exists();
            if ($conflict) return back()->withErrors(['departure_time' => 'This bus is already scheduled for the selected date and time.']);
        }
        try {
            DB::beginTransaction();
            $route->update($validated);
            DB::commit();
            OperatorAuditService::updated($route, $route->getOriginal(), "Updated trip TRP-" . str_pad($route->id, 4, '0', STR_PAD_LEFT));

            return redirect()->route('operator.trips.index')->with('success', 'Trip updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack(); Log::error('Trip update failed: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to update trip. Please try again.'])->withInput();
        }
    }

    public function cancel($tripId)
    {
        $operator = $this->getOperator();
        if (!$operator) return redirect()->route('operator.login')->with('error', 'Please log in to cancel trips.');
        $route = Route::where('operator_id', $operator->id)->findOrFail($tripId);
        $confirmedBookings = $route->bookings()->where('status', 'confirmed')->get();
        if ($confirmedBookings->count() > 0) {
            return back()->withErrors(['trip' => "Cannot cancel trip with {$confirmedBookings->count()} confirmed bookings. Please notify passengers first or process refunds."]);
        }
        try {
            DB::beginTransaction();
            $cancelledPending = $route->bookings()->where('status', 'pending')->update(['status' => 'cancelled']);
            $route->update(['is_active' => false]);
            $route->delete();
            DB::commit();
            $tripId = 'TRP-' . str_pad($route->id, 4, '0', STR_PAD_LEFT);
            $message = "Trip {$tripId} cancelled successfully.";
            if ($cancelledPending > 0) $message .= " {$cancelledPending} pending booking(s) were automatically cancelled.";
            $tripLabel = 'TRP-' . str_pad($route->id, 4, '0', STR_PAD_LEFT);
            OperatorAuditService::deleted($route, "Cancelled trip {$tripLabel}");

            return redirect()->route('operator.trips.index')->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack(); Log::error('Trip cancellation failed: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to cancel trip. Please try again.']);
        }
    }

    public function occupancy($tripId)
    {
        $operator = $this->getOperator();
        if (!$operator) return response()->json(['error' => 'Unauthorized'], 401);
        $route = Route::with(['bookings' => function ($q) { $q->whereIn('status', ['confirmed', 'pending']); }])
            ->where('operator_id', $operator->id)->findOrFail($tripId);
        $confirmedSeats = $route->bookings->where('status', 'confirmed')->pluck('seat_number')->toArray();
        $pendingSeats = $route->bookings->where('status', 'pending')->pluck('seat_number')->toArray();
        $capacity = $route->bus->seat_capacity ?? 49;
        $seatMap = $this->generateDetailedSeatMap($capacity, $confirmedSeats, $pendingSeats);
        return response()->json([
            'trip_id' => 'TRP-' . str_pad($route->id, 4, '0', STR_PAD_LEFT),
            'route' => $route->origin . ' → ' . $route->destination, 'date' => $route->travel_date,
            'departure' => $route->departure_time, 'capacity' => $capacity,
            'confirmed' => count($confirmedSeats), 'pending' => count($pendingSeats),
            'available' => $capacity - count($confirmedSeats) - count($pendingSeats),
            'confirmed_seats' => $confirmedSeats, 'pending_seats' => $pendingSeats,
            'occupancy_percentage' => round((count($confirmedSeats) / $capacity) * 100),
            'seat_map' => $seatMap, 'status' => $this->determineTripStatus($route),
        ]);
    }

    private function generateDetailedSeatMap($capacity, $confirmedSeats, $pendingSeats = [])
    {
        $seatMap = []; $columns = 4; $rows = ceil($capacity / $columns); $seatNumber = 1;
        for ($row = 0; $row < $rows; $row++) {
            $rowData = ['row' => $row + 1, 'seats' => []];
            for ($col = 0; $col < $columns; $col++) {
                if ($seatNumber <= $capacity) {
                    $status = 'available';
                    if (in_array($seatNumber, $confirmedSeats)) $status = 'confirmed';
                    elseif (in_array($seatNumber, $pendingSeats)) $status = 'pending';
                    $rowData['seats'][] = ['number' => $seatNumber, 'status' => $status];
                }
                $seatNumber++;
            }
            $seatMap[] = $rowData;
        }
        return $seatMap;
    }

    public function export(Request $request)
    {
        $operator = $this->getOperator();
        if (!$operator) return redirect()->route('operator.login')->with('error', 'Please log in to export data.');
        $trips = $this->getOperatorTrips($operator, $request);
        $filename = 'trips_export_' . Carbon::now()->format('Y-m-d_His') . '.csv';
        $headers = ['Content-Type' => 'text/csv', 'Content-Disposition' => "attachment; filename=\"$filename\"", 'Pragma' => 'no-cache', 'Expires' => '0'];
        $callback = function () use ($trips) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['Trip ID', 'Route', 'Date', 'Departure', 'Arrival', 'Bus', 'Class', 'Booked', 'Pending', 'Capacity', 'Occupancy %', 'Fare (ZMW)', 'Status', 'Has Bookings']);
            foreach ($trips as $trip) {
                fputcsv($handle, [$trip['id'], $trip['route_from'] . ' → ' . $trip['route_to'], $trip['date'], $trip['departure'], $trip['arrival'] ?? '--:--', $trip['bus'], ucfirst($trip['class']), $trip['booked'], $trip['pending'] ?? 0, $trip['capacity'], $trip['occupancy_percentage'] . '%', number_format($trip['fare'], 2), $trip['status'], $trip['has_bookings'] ? 'Yes' : 'No']);
            }
            fclose($handle);
        };
        OperatorAuditService::log('trip.exported', 'Exported trips to CSV');

        return response()->stream($callback, 200, $headers);
    }

    public function upcoming(Request $request)
    {
        $operator = $this->getOperator();
        if (!$operator) return response()->json(['error' => 'Unauthorized'], 401);
        $limit = $request->input('limit', 10);
        $days = $request->input('days', 7);
        $trips = Route::with(['bus', 'bookings' => function ($q) { $q->where('status', 'confirmed'); }])
            ->where('operator_id', $operator->id)->where('travel_date', '>=', Carbon::today())
            ->where('travel_date', '<=', Carbon::today()->addDays($days))->where('is_active', true)
            ->orderBy('travel_date', 'asc')->orderBy('departure_time', 'asc')->limit($limit)->get()
            ->map(fn($route) => $this->formatTripData($route));
        return response()->json(['success' => true, 'data' => $trips, 'meta' => ['total' => $trips->count(), 'limit' => $limit, 'days' => $days]]);
    }

    public function show($tripId)
    {
        $operator = $this->getOperator();
        if (!$operator) return response()->json(['error' => 'Unauthorized'], 401);
        $route = Route::with(['bus', 'operator', 'bookings' => function ($q) { $q->with('user'); }])
            ->where('operator_id', $operator->id)->findOrFail($tripId);
        $tripData = $this->formatTripData($route);
        $bookings = $route->bookings->map(fn($b) => ['id' => $b->id, 'reference' => $b->reference_id, 'seat' => $b->seat_number, 'status' => $b->status, 'passenger' => $b->user->full_name ?? 'Guest', 'amount' => $b->amount, 'created_at' => $b->created_at->format('Y-m-d H:i')]);
        return response()->json(['success' => true, 'data' => ['trip' => $tripData, 'bookings' => $bookings, 'summary' => ['total_bookings' => $bookings->count(), 'confirmed' => $bookings->where('status', 'confirmed')->count(), 'pending' => $bookings->where('status', 'pending')->count(), 'cancelled' => $bookings->where('status', 'cancelled')->count(), 'revenue' => $bookings->where('status', 'confirmed')->sum('amount')]]]);
    }

    public function stats()
    {
        $operator = $this->getOperator();
        if (!$operator) return response()->json(['error' => 'Unauthorized'], 401);
        $stats = $this->getTripStats($operator);
        $weeklyTrend = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $bookings = Booking::whereHas('route', fn($q) => $q->where('operator_id', $operator->id)->where('travel_date', $date))->where('status', 'confirmed')->count();
            $weeklyTrend[] = ['date' => $date->format('D'), 'bookings' => $bookings];
        }
        return response()->json(['success' => true, 'data' => ['stats' => $stats, 'weekly_trend' => $weeklyTrend]]);
    }

    public function updateStatus(Request $request, $tripId)
    {
        $operator = $this->getOperator();
        if (!$operator) return response()->json(['error' => 'Unauthorized'], 401);
        $request->validate(['status' => 'required|in:scheduled,on_route,delayed,completed,cancelled']);
        $route = Route::where('operator_id', $operator->id)->findOrFail($tripId);
        switch ($request->status) {
            case 'cancelled':
                if ($route->bookings()->where('status', 'confirmed')->exists()) return response()->json(['error' => 'Cannot cancel trip with confirmed bookings.'], 422);
                $route->update(['is_active' => false]);
                $route->bookings()->where('status', 'pending')->update(['status' => 'cancelled']);
                break;
            case 'completed':
                $route->bookings()->where('status', 'confirmed')->update(['status' => 'completed']);
                break;
        }
        OperatorAuditService::log('trip.status_updated', "Updated trip status to {$request->status} for TRP-" . str_pad($route->id, 4, '0', STR_PAD_LEFT), $route);

        return response()->json(['success' => true, 'message' => 'Trip status updated successfully.', 'data' => $this->formatTripData($route->fresh())]);
    }

    /**
     * Mark a trip as delayed.
     */
    public function markDelayed(Request $request, $tripId)
    {
        $operator = $this->getOperator();
        $route = Route::where('operator_id', $operator->id)->findOrFail($tripId);
        $validated = $request->validate([
            'delay_minutes' => 'required|integer|min:1|max:1440',
            'delay_reason' => 'nullable|string|max:500',
        ]);
        $route->update([
            'delayed_at' => now(),
            'delay_minutes' => $validated['delay_minutes'],
            'delay_reason' => $validated['delay_reason'] ?? null,
        ]);
        OperatorAuditService::log('trip.delayed', "Trip TRP-" . str_pad($route->id, 4, '0', STR_PAD_LEFT) . " delayed by {$validated['delay_minutes']}mins", $route);
        Notification::send($route->confirmedPassengers(), new TripStatusChanged($route, 'delayed'));

        return back()->with('success', 'Trip marked as delayed. Passengers will be notified.');
    }

    /**
     * Mark a trip as departed.
     */
    public function markDeparted($tripId)
    {
        $operator = $this->getOperator();
        $route = Route::where('operator_id', $operator->id)->findOrFail($tripId);
        $route->update(['departed_at' => now()]);
        OperatorAuditService::log('trip.departed', "Trip TRP-" . str_pad($route->id, 4, '0', STR_PAD_LEFT) . " marked as departed", $route);
        Notification::send($route->confirmedPassengers(), new TripStatusChanged($route, 'departed'));

        return back()->with('success', 'Trip marked as departed.');
    }

    /**
     * Mark a trip as arrived.
     */
    public function markArrived($tripId)
    {
        $operator = $this->getOperator();
        $route = Route::where('operator_id', $operator->id)->findOrFail($tripId);
        $route->update(['arrived_at' => now()]);
        OperatorAuditService::log('trip.arrived', "Trip TRP-" . str_pad($route->id, 4, '0', STR_PAD_LEFT) . " marked as arrived", $route);
        Notification::send($route->confirmedPassengers(), new TripStatusChanged($route, 'arrived'));

        return back()->with('success', 'Trip marked as arrived.');
    }

    /**
     * Assign a driver to a trip.
     */
    public function assignDriver(Request $request, $tripId)
    {
        $operator = $this->getOperator();
        $route = Route::where('operator_id', $operator->id)->findOrFail($tripId);
        $request->validate(['driver_id' => 'nullable|exists:drivers,id']);
        if ($request->filled('driver_id')) {
            $driver = Driver::where('operator_id', $operator->id)->find($request->driver_id);
            if (!$driver) return back()->withErrors(['driver_id' => 'Driver not found in your fleet.']);
        }
        $route->update(['driver_id' => $request->driver_id]);
        $driverName = $route->driver?->full_name ?? 'Unassigned';
        OperatorAuditService::log('trip.driver_assigned', "Driver {$driverName} assigned to TRP-" . str_pad($route->id, 4, '0', STR_PAD_LEFT), $route);
        return back()->with('success', "Driver assigned to trip successfully.");
    }

    /**
     * Return trip JSON data for the edit drawer.
     */
    public function tripJson($tripId)
    {
        $operator = $this->getOperator();
        $route = Route::with(['bus', 'driver', 'operator'])->where('operator_id', $operator->id)->findOrFail($tripId);
        $availableDrivers = Driver::where('operator_id', $operator->id)->active()->get(['id', 'full_name']);
        return response()->json([
            'success' => true,
            'trip' => [
                'id' => $route->id,
                'origin' => $route->origin,
                'destination' => $route->destination,
                'travel_date' => $route->travel_date instanceof \Carbon\Carbon ? $route->travel_date->format('Y-m-d') : $route->travel_date,
                'departure_time' => $route->departure_time,
                'arrival_time' => $route->arrival_time,
                'fare' => $route->fare,
                'bus_id' => $route->bus_id,
                'driver_id' => $route->driver_id,
                'is_active' => $route->is_active,
                'bus' => $route->bus ? ['id' => $route->bus->id, 'registration_number' => $route->bus->registration_number] : null,
                'driver' => $route->driver ? ['id' => $route->driver->id, 'full_name' => $route->driver->full_name] : null,
            ],
            'available_drivers' => $availableDrivers,
        ]);
    }

    /**
     * Cancel a trip with notification to passengers (bypasses the confirmed bookings check).
     */
    public function cancelWithNotification($tripId)
    {
        $operator = $this->getOperator();
        $route = Route::where('operator_id', $operator->id)->findOrFail($tripId);
        $passengers = $route->confirmedPassengers();
        $confirmedBookings = $route->bookings()->where('status', 'confirmed')->get();
        $pendingCount = $route->bookings()->where('status', 'pending')->count();

        try {
            DB::beginTransaction();
            // Cancel all pending bookings
            $route->bookings()->where('status', 'pending')->update(['status' => 'cancelled', 'cancelled_at' => now()]);
            // Cancel all confirmed bookings
            $route->bookings()->where('status', 'confirmed')->update(['status' => 'cancelled', 'cancelled_at' => now()]);
            $route->update(['is_active' => false]);
            DB::commit();

            $tripLabel = 'TRP-' . str_pad($route->id, 4, '0', STR_PAD_LEFT);
            $message = "Trip {$tripLabel} cancelled. ";
            $message .= "{$confirmedBookings->count()} confirmed and {$pendingCount} pending booking(s) were cancelled.";
            OperatorAuditService::log('trip.cancelled_with_notification', $message, $route);

            Notification::send($passengers, new TripStatusChanged($route, 'cancelled'));

            return redirect()->route('operator.trips.index')->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Trip cancellation with notification failed: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to cancel trip. Please try again.']);
        }
    }

    /**
     * Printable schedule view for a given date range.
     */
    public function printableSchedule(Request $request)
    {
        $operator = $this->getOperator();
        $dateFrom = $request->input('date_from', Carbon::today()->toDateString());
        $dateTo = $request->input('date_to', Carbon::today()->addDays(7)->toDateString());

        $trips = Route::with(['bus', 'driver'])
            ->where('operator_id', $operator->id)
            ->whereBetween('travel_date', [$dateFrom, $dateTo])
            ->where('is_active', true)
            ->orderBy('travel_date', 'asc')
            ->orderBy('departure_time', 'asc')
            ->get()
            ->map(fn($r) => $this->formatTripData($r));

        $stats = [
            'total' => $trips->count(),
            'total_booked' => $trips->sum('booked'),
            'total_capacity' => $trips->sum('capacity'),
            'total_revenue' => $trips->sum(fn($t) => $t['booked'] * $t['fare']),
        ];

        return view('operator.printable_schedule', compact('operator', 'trips', 'stats', 'dateFrom', 'dateTo'));
    }
}
