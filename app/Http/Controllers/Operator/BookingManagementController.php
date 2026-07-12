<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Operator;
use App\Models\Route;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BookingManagementController extends Controller
{
    /**
     * Display all bookings for the operator (across all trips).
     */
    public function index(Request $request)
    {
        $operator = $this->getOperator();

        $query = Booking::with(['route.bus', 'route.operator'])
            ->whereHas('route', function ($q) use ($operator) {
                $q->where('operator_id', $operator->id);
            });

        // Apply status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Apply payment filter
        if ($request->filled('payment')) {
            if ($request->payment === 'paid') {
                $query->where('status', 'confirmed');
            } elseif ($request->payment === 'pending') {
                $query->where('status', 'pending');
            } elseif ($request->payment === 'cancelled') {
                $query->where('status', 'cancelled');
            } elseif ($request->payment === 'expired') {
                $query->where('status', 'pending')
                      ->where('held_until', '<', now());
            }
        }

        // Apply date filter
        if ($request->filled('date_filter')) {
            $this->applyDateFilter($query, $request->date_filter);
        }

        // Apply search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference_id', 'like', "%{$search}%")
                  ->orWhere('passenger_name', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%")
                  ->orWhere('seat_number', $search)
                  ->orWhereHas('route', function ($r) use ($search) {
                      $r->where('origin', 'like', "%{$search}%")
                        ->orWhere('destination', 'like', "%{$search}%");
                  });
            });
        }

        // Apply sort
        $sortField = $request->get('sort', 'created_at');
        $sortDir = $request->get('dir', 'desc');
        $allowedSorts = ['created_at', 'seat_number', 'amount', 'status', 'reference_id'];
        if (in_array($sortField, $allowedSorts)) {
            $query->orderBy($sortField, $sortDir === 'asc' ? 'asc' : 'desc');
        }

        $bookings = $query->paginate(25)->withQueryString();

        // Get summary stats
        $stats = $this->getBookingStats($operator);

        $statusStyles = $this->getStatusStyles();

        return view('operator.all_bookings', compact(
            'operator',
            'bookings',
            'stats',
            'statusStyles'
        ));
    }

    /**
     * Display bookings for a specific trip.
     */
    public function tripBookings($tripId)
    {
        $operator = $this->getOperator();

        $route = Route::with(['bus', 'operator'])
            ->where('operator_id', $operator->id)
            ->findOrFail($tripId);

        $bookings = Booking::with(['user'])
            ->where('route_id', $route->id)
            ->orderByRaw("FIELD(status, 'confirmed', 'pending', 'cancelled')")
            ->orderBy('seat_number', 'asc')
            ->get();

        $tripData = $this->formatTripData($route);

        $stats = [
            'total' => $bookings->count(),
            'confirmed' => $bookings->where('status', 'confirmed')->count(),
            'pending' => $bookings->where('status', 'pending')->count(),
            'cancelled' => $bookings->where('status', 'cancelled')->count(),
            'total_revenue' => $bookings->where('status', 'confirmed')->sum('amount'),
        ];

        return view('operator.trip_bookings', compact(
            'operator',
            'route',
            'bookings',
            'tripData',
            'stats'
        ));
    }

    /**
     * Display a single booking detail.
     */
    public function show($bookingId)
    {
        $operator = $this->getOperator();

        $booking = Booking::with(['route.bus', 'route.operator', 'payment', 'user'])
            ->whereHas('route', function ($q) use ($operator) {
                $q->where('operator_id', $operator->id);
            })
            ->findOrFail($bookingId);

        return view('operator.booking_detail', compact('operator', 'booking'));
    }

    /**
     * Customer lookup page — search by reference ID or phone number.
     */
    public function customerLookup(Request $request)
    {
        $booking = null;
        $error = null;

        if ($request->filled('reference_id') || $request->filled('phone_number')) {
            $query = Booking::with(['route.bus', 'route.operator']);

            if ($request->filled('reference_id')) {
                $query->where('reference_id', 'like', '%' . $request->reference_id . '%');
            }

            if ($request->filled('phone_number')) {
                $phone = preg_replace('/[^0-9]/', '', $request->phone_number);
                $query->where('phone_number', 'like', '%' . $phone . '%');
            }

            $booking = $query->first();

            if (!$booking) {
                $error = 'No booking found with the provided details. Please check and try again.';
            }
        }

        return view('booking_lookup', compact('booking', 'error'));
    }

    /**
     * Export bookings as CSV.
     */
    public function export(Request $request)
    {
        $operator = $this->getOperator();

        $query = Booking::with(['route.bus', 'route.operator'])
            ->whereHas('route', function ($q) use ($operator) {
                $q->where('operator_id', $operator->id);
            });

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereHas('route', function ($q) use ($request) {
                $q->where('travel_date', '>=', $request->date_from);
            });
        }

        if ($request->filled('date_to')) {
            $query->whereHas('route', function ($q) use ($request) {
                $q->where('travel_date', '<=', $request->date_to);
            });
        }

        $bookings = $query->orderBy('created_at', 'desc')->get();

        $filename = 'bookings-export-' . now()->format('Y-m-d-His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($bookings) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'Reference ID',
                'Passenger Name',
                'Phone Number',
                'Route',
                'Travel Date',
                'Departure Time',
                'Seat Number',
                'Amount (ZMW)',
                'Status',
                'Booking Date',
            ]);

            foreach ($bookings as $booking) {
                fputcsv($handle, [
                    $booking->reference_id,
                    $booking->passenger_name ?? 'N/A',
                    $booking->phone_number ?? 'N/A',
                    $booking->route->origin . ' → ' . $booking->route->destination,
                    $booking->route->travel_date instanceof \Carbon\Carbon
                        ? $booking->route->travel_date->format('d M Y')
                        : $booking->route->travel_date,
                    $booking->route->departure_time,
                    $booking->seat_number,
                    number_format($booking->amount, 2),
                    ucfirst($booking->status),
                    $booking->created_at->format('d M Y H:i'),
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    // ── Helper Methods ──────────────────────────────────

    private function getOperator()
    {
        if (Auth::guard('operator_api')->check()) {
            $operator = Auth::guard('operator_api')->user();
            if ($operator) {
                session(['operator_id' => $operator->id]);
                return $operator;
            }
        }
        
        if (session('operator_id')) {
            $operator = Operator::find(session('operator_id'));
            if ($operator) {
                return $operator;
            }
        }
        
        // Fallback to operator ID 1
        $operator = Operator::find(1);
        
        if (!$operator) {
            try {
                $operator = Operator::create([
                    'id' => 1,
                    'company_name' => 'Default Operator',
                    'email' => 'default@operator.com',
                    'phone_number' => '0977123456',
                    'password' => bcrypt('password'),
                    'is_verified' => true,
                    'verified_at' => now(),
                    'address' => 'Lusaka, Zambia',
                ]);
            } catch (\Exception $e) {
                $operator = Operator::withTrashed()->find(1);
                if ($operator) {
                    $operator->restore();
                    $operator->update([
                        'is_verified' => true,
                        'verified_at' => now(),
                    ]);
                } else {
                    $operator = Operator::first() ?? Operator::create([
                        'company_name' => 'Fallback Operator',
                        'email' => 'fallback@operator.com',
                        'phone_number' => '0977123456',
                        'password' => bcrypt('password'),
                        'is_verified' => true,
                        'verified_at' => now(),
                    ]);
                }
            }
        }
        
        session(['operator_id' => $operator->id]);
        return $operator;
    }

    private function applyDateFilter($query, $filter)
    {
        switch ($filter) {
            case 'today':
                $query->whereHas('route', function ($q) {
                    $q->where('travel_date', Carbon::today());
                });
                break;
            case 'yesterday':
                $query->whereHas('route', function ($q) {
                    $q->where('travel_date', Carbon::yesterday());
                });
                break;
            case 'tomorrow':
                $query->whereHas('route', function ($q) {
                    $q->where('travel_date', Carbon::tomorrow());
                });
                break;
            case 'this_week':
                $query->whereHas('route', function ($q) {
                    $q->whereBetween('travel_date', [
                        Carbon::now()->startOfWeek(),
                        Carbon::now()->endOfWeek(),
                    ]);
                });
                break;
            case 'this_month':
                $query->whereHas('route', function ($q) {
                    $q->whereMonth('travel_date', Carbon::now()->month)
                      ->whereYear('travel_date', Carbon::now()->year);
                });
                break;
        }
    }

    private function getBookingStats($operator)
    {
        $today = Carbon::today();

        $totalBookings = Booking::whereHas('route', function ($q) use ($operator) {
            $q->where('operator_id', $operator->id);
        })->count();

        $todayBookings = Booking::whereHas('route', function ($q) use ($operator, $today) {
            $q->where('operator_id', $operator->id)
              ->where('travel_date', $today);
        })->count();

        $confirmedBookings = Booking::whereHas('route', function ($q) use ($operator) {
            $q->where('operator_id', $operator->id);
        })->where('status', 'confirmed')->count();

        $pendingBookings = Booking::whereHas('route', function ($q) use ($operator) {
            $q->where('operator_id', $operator->id);
        })->where('status', 'pending')
          ->where('held_until', '>', now())
          ->count();

        $totalRevenue = Booking::whereHas('route', function ($q) use ($operator) {
            $q->where('operator_id', $operator->id);
        })->where('status', 'confirmed')->sum('amount');

        $revenueToday = Booking::whereHas('route', function ($q) use ($operator, $today) {
            $q->where('operator_id', $operator->id)
              ->where('travel_date', $today);
        })->where('status', 'confirmed')->sum('amount');

        return [
            'total_bookings' => $totalBookings,
            'today_bookings' => $todayBookings,
            'confirmed_bookings' => $confirmedBookings,
            'pending_bookings' => $pendingBookings,
            'total_revenue' => $totalRevenue,
            'revenue_today' => $revenueToday,
        ];
    }

    private function getStatusStyles()
    {
        return [
            'confirmed' => 'bg-primary/10 text-primary',
            'pending' => 'bg-tertiary/10 text-tertiary',
            'cancelled' => 'bg-error-container/20 text-error',
            'expired' => 'bg-surface-container-high text-on-surface-variant',
        ];
    }

    private function formatTripData($route)
    {
        $bookedCount = $route->bookings()
            ->where('status', 'confirmed')
            ->count();
        $pendingCount = $route->bookings()
            ->where('status', 'pending')
            ->count();
        $capacity = $route->bus->seat_capacity ?? 49;

        return [
            'id' => 'TRP-' . str_pad($route->id, 4, '0', STR_PAD_LEFT),
            'route_from' => $route->origin,
            'route_to' => $route->destination,
            'date' => Carbon::parse($route->travel_date)->format('d M Y'),
            'departure' => $route->departure_time ? Carbon::parse($route->departure_time)->format('H:i') : '--:--',
            'bus' => $route->bus->registration_number ?? 'N/A',
            'booked' => $bookedCount,
            'pending' => $pendingCount,
            'capacity' => $capacity,
        ];
    }
}