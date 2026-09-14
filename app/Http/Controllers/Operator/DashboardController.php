<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Route;
use App\Models\Booking;
use App\Models\Operator;
use App\Models\Bus;
use App\Models\Driver;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Operator\TripManagementController;

class DashboardController extends Controller
{
    public function index()
    {
        $operator = $this->getOperator();
        abort_unless($operator, 403, 'Operator session expired. Please log in again.');
        $operatorId = $operator->id;

        $routes = $this->getOperatorRoutes($operator);
        $buses = $this->getOperatorBuses($operator);
        $drivers = $this->getOperatorDrivers($operator);

        $today = Carbon::today()->toDateString();
        $lastMonth = Carbon::today()->subMonth()->toDateString();

        // ===== Total Bookings (confirmed) =====
        $total_bookings = Booking::whereHas('route', function ($query) use ($operatorId) {
            $query->where('operator_id', $operatorId);
        })
            ->where('status', 'confirmed')
            ->count();

        // ===== Bookings Trend (vs last month) =====
        $currentMonthBookings = Booking::whereHas('route', function ($query) use ($operatorId) {
            $query->where('operator_id', $operatorId);
        })
            ->where('status', 'confirmed')
            ->where('created_at', '>=', Carbon::now()->startOfMonth())
            ->count();

        $lastMonthBookings = Booking::whereHas('route', function ($query) use ($operatorId) {
            $query->where('operator_id', $operatorId);
        })
            ->where('status', 'confirmed')
            ->whereBetween('created_at', [
                Carbon::now()->subMonth()->startOfMonth(),
                Carbon::now()->subMonth()->endOfMonth()
            ])
            ->count();

        if ($lastMonthBookings > 0) {
            $bookingsTrendValue = (($currentMonthBookings - $lastMonthBookings) / $lastMonthBookings) * 100;
            $bookings_trend = ($bookingsTrendValue >= 0 ? '+' : '') . number_format($bookingsTrendValue, 1) . '%';
        } else {
            $bookings_trend = $currentMonthBookings > 0 ? '+100%' : '0%';
        }

        // ===== Revenue =====
        $revenue = Booking::whereHas('route', function ($query) use ($operatorId) {
            $query->where('operator_id', $operatorId);
        })
            ->where('status', 'confirmed')
            ->sum('amount');

        // ===== Revenue Trend (vs last month) =====
        $currentMonthRevenue = Booking::whereHas('route', function ($query) use ($operatorId) {
            $query->where('operator_id', $operatorId);
        })
            ->where('status', 'confirmed')
            ->where('created_at', '>=', Carbon::now()->startOfMonth())
            ->sum('amount');

        $lastMonthRevenue = Booking::whereHas('route', function ($query) use ($operatorId) {
            $query->where('operator_id', $operatorId);
        })
            ->where('status', 'confirmed')
            ->whereBetween('created_at', [
                Carbon::now()->subMonth()->startOfMonth(),
                Carbon::now()->subMonth()->endOfMonth()
            ])
            ->sum('amount');

        if ($lastMonthRevenue > 0) {
            $revenueTrendValue = (($currentMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100;
            $revenue_trend = ($revenueTrendValue >= 0 ? '+' : '') . number_format($revenueTrendValue, 1) . '%';
        } else {
            $revenue_trend = $currentMonthRevenue > 0 ? '+100%' : '0%';
        }

        $revenue_average = 'ZMW' . number_format(($revenue > 0 ? ($revenue / 30) : 0) / 1000, 1) . 'k';

        // ===== Today's Bookings =====
        $today_bookings = Booking::whereHas('route', function ($query) use ($operatorId) {
            $query->where('operator_id', $operatorId);
        })
            ->where('status', 'confirmed')
            ->whereDate('created_at', $today)
            ->count();

        // ===== Pending Bookings (awaiting payment) =====
        $pending_bookings = Booking::whereHas('route', function ($query) use ($operatorId) {
            $query->where('operator_id', $operatorId);
        })
            ->where('status', 'pending')
            ->where('held_until', '>', now())
            ->count();

        // ===== Cancelled Bookings (this month) =====
        $cancelled_bookings = Booking::whereHas('route', function ($query) use ($operatorId) {
            $query->where('operator_id', $operatorId);
        })
            ->where('status', 'cancelled')
            ->where('created_at', '>=', Carbon::now()->startOfMonth())
            ->count();

        // ===== Active Trips =====
        $active_trips_count = Route::where('operator_id', $operatorId)
            ->whereDate('travel_date', $today)
            ->where('is_active', true)
            ->count();

        $total_trips_today = Route::where('operator_id', $operatorId)
            ->whereDate('travel_date', $today)
            ->count();

        // ===== Fleet Status =====
        $fleet_raw = Route::with('bus')
            ->where('operator_id', $operatorId)
            ->whereDate('travel_date', $today)
            ->get();

        $totalCapacityCalculated = 0;
        $totalSeatsBookedCalculated = 0;

        $fleet_status = $fleet_raw->map(function ($route) use (&$totalCapacityCalculated, &$totalSeatsBookedCalculated) {
            $busCapacity = $route->bus->seat_capacity;

            $bookedSeats = count($route->bookedSeats() ?? 10);

            $totalCapacityCalculated += $busCapacity;
            $totalSeatsBookedCalculated += $bookedSeats;

            $percentage = min(round(($bookedSeats / max($busCapacity, 1)) * 100), 100);

            $status = $route->is_active ? 'ACTIVE' : 'INACTIVE';
            $badge_class = $route->is_active ? 'bg-seat-available/10 text-seat-available' : 'bg-tertiary-container/10 text-tertiary';
            $bar_class = 'bg-primary';

            return [
                'plate' => $route->bus->registration_number ?? 'none',
                'route' => "{$route->origin} -> {$route->destination}",
                'status' => $status,
                'progress' => $percentage,
                'progress_label' => "{$percentage}% Seats Used",
                'badge_class' => $badge_class,
                'bar_class' => $bar_class
            ];
        })->toArray();

        $avg_occupancy = $totalCapacityCalculated > 0 ? round(($totalSeatsBookedCalculated / $totalCapacityCalculated) * 100) : 0;

        // ===== Upcoming Trips =====
        $upcoming_trips = Route::with('bus')
            ->where('operator_id', $operatorId)
            ->whereDate('travel_date', '>=', $today)
            ->orderBy('travel_date', 'asc')
            ->orderBy('departure_time', 'asc')
            ->take(5)
            ->get()
            ->map(function ($route) {
                $capacity = $route->bus->seat_capacity ?? 40;
                $booked = count($route->bookedSeats());
                $bookingPercentage = min(round(($booked / max($capacity, 1)) * 100), 100);

                return [
                    'id' => '#BMZ-' . $route->id,
                    'route_from' => $route->origin,
                    'route_to' => $route->destination,
                    'class' => ucfirst($route->bus->bus_class ?? 'none'),
                    'departure' => Carbon::parse($route->departure_time)->format('H:i'),
                    'booked' => $booked,
                    'capacity' => $capacity,
                    'occupancy_percentage' => $bookingPercentage,
                    'status' => $route->is_active ? 'On Time' : 'Suspended',
                    'status_type' => $route->is_active ? 'on_time' : 'delayed'
                ];
            })->toArray();

        // ===== Recent Bookings (latest 5) =====
        $recent_bookings = Booking::with('route', 'user')
            ->whereHas('route', function ($query) use ($operatorId) {
                $query->where('operator_id', $operatorId);
            })
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($booking) {
                return [
                    'reference' => $booking->reference_id,
                    'passenger' => $booking->passenger_name ?? $booking->user->full_name ?? 'N/A',
                    'route' => $booking->route ? "{$booking->route->origin} → {$booking->route->destination}" : 'N/A',
                    'seat' => $booking->seat_number,
                    'amount' => $booking->amount,
                    'status' => $booking->status,
                    'created_at' => $booking->created_at->diffForHumans(),
                ];
            })->toArray();

        // ===== Top Routes by Bookings =====
        $top_routes = Route::withCount(['bookings' => function ($query) {
            $query->where('status', 'confirmed');
        }])
            ->where('operator_id', $operatorId)
            ->orderBy('bookings_count', 'desc')
            ->take(5)
            ->get()
            ->map(function ($route) {
                return [
                    'route' => "{$route->origin} → {$route->destination}",
                    'bookings_count' => $route->bookings_count,
                    'fare' => $route->fare,
                ];
            })->toArray();

        $operator = Operator::find($operatorId);

        // Alerts section (Section 5 of the bento grid) expects a static
        // 3-card list in the exact shape below. Placeholder actions are '#'
        // until the underlying trips/bookings actions exist.
        $alerts = [
            [
                'bg_class'     => 'bg-tertiary/5 border-tertiary/20',
                'icon'         => 'warning',
                'icon_bg'      => 'bg-tertiary/10',
                'title'        => 'Low Seat Inventory',
                'title_class'  => 'text-tertiary',
                'message'      => 'One or more upcoming trips are almost fully booked. Consider adding capacity or extra departures.',
                'btn_class'    => 'text-tertiary',
                'action_label' => 'Review trips',
                'action_icon'  => 'arrow_forward',
            ],
            [
                'bg_class'     => 'bg-secondary/5 border-secondary/20',
                'icon'         => 'payments',
                'icon_bg'      => 'bg-secondary/10',
                'title'        => 'Pending Payments',
                'title_class'  => 'text-secondary',
                'message'      => 'Some bookings are still awaiting payment. Follow up before the hold expires to avoid losing the seats.',
                'btn_class'    => 'text-secondary',
                'action_label' => 'Review bookings',
                'action_icon'  => 'arrow_forward',
            ],
            [
                'bg_class'     => 'bg-primary/5 border-primary/20',
                'icon'         => 'directions_bus',
                'icon_bg'      => 'bg-primary/10',
                'title'        => 'Upcoming Departures',
                'title_class'  => 'text-primary',
                'message'      => 'Trips scheduled for today and tomorrow require driver and vehicle assignment confirmation.',
                'btn_class'    => 'text-primary',
                'action_label' => 'Open schedule',
                'action_icon'  => 'arrow_forward',
            ],
        ];

        return view('operator.dashboard', compact(
            'total_bookings', 'bookings_trend',
            'revenue', 'revenue_trend', 'revenue_average',
            'today_bookings', 'pending_bookings', 'cancelled_bookings',
            'active_trips_count', 'total_trips_today', 'avg_occupancy',
            'fleet_status', 'upcoming_trips', 'recent_bookings', 'top_routes',
            'operator', 'routes', 'buses', 'drivers',
            'alerts'
        ));
    }

    private function getOperator()
    {
        $operator = Auth::guard('operator')->user();
        if ($operator) {
            session(['operator_id' => $operator->id]);
            return $operator;
        }
        if (session('operator_id')) {
            $operator = Operator::find(session('operator_id'));
            if ($operator) return $operator;
        }
        abort(403, 'Operator session expired. Please log in again.');
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

    private function getOperatorDrivers($operator)
    {
        return Driver::where('operator_id', $operator->id)
            ->where('is_active', true)
            ->get()
            ->map(function ($driver) {
                return [
                    'id' => $driver->id,
                    'full_name' => $driver->full_name,
                    'phone_number' => $driver->phone_number,
                    'license_number' => $driver->license_number,
                ];
            });
    }
}