<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Route;
use App\Models\Booking;
use App\Models\Operator;
use App\Models\Bus;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Operator\TripManagementController;

class DashboardController extends Controller
{
    public function index()
    {
        $operator = $this->getOperator();
        $operatorId = $operator->id;

        $routes = $this->getOperatorRoutes($operator);
        $buses = $this->getOperatorBuses($operator);

        $today = Carbon::today()->toDateString();

        $total_bookings = Booking::whereHas('route', function ($query) use ($operatorId) {
            $query->where('operator_id', $operatorId);
        })
            ->where('status', 'confirmed')
            ->count();

        $bookings_trend = '12.5%';

        $revenue = Booking::whereHas('route', function ($query) use ($operatorId) {
            $query->where('operator_id', $operatorId);
        })
            ->where('status', 'confirmed')
            ->sum('amount');

        $revenue_trend = "+8.2%";
        $revenue_average = 'ZMW' . number_format(($revenue > 0 ? ($revenue / 30) : 0) / 1000, 1) . 'k';

        $active_trips_count = Route::where('operator_id', $operatorId)
            ->whereDate('travel_date', $today)
            ->where('is_active', true)
            ->count();

        $total_trips_today = Route::where('operator_id', $operatorId)
            ->whereDate('travel_date', $today)
            ->count();

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

        $operator = Operator::find($operatorId);

        return view('operator-dashboard', compact('total_bookings', 'bookings_trend', 'revenue', 'revenue_trend', 'revenue_average', 'active_trips_count', 'total_trips_today', 'avg_occupancy', 'fleet_status', 'upcoming_trips', 'operator', 'routes', 'buses'));
    }
    private function getOperator()
    {
        return Auth::guard('operator_api')->user()
        ?? Operator::find(session('operator_id'));
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
}
