<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Operator;
use App\Models\Route;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PassengerListController extends Controller
{
    /**
     * Display a paginated, filterable list of all passengers across trips.
     */
    public function index(Request $request)
    {
        $operator = $this->getOperator();

        $query = Booking::with(['route.bus', 'route.operator'])
            ->whereHas('route', function ($q) use ($operator) {
                $q->where('operator_id', $operator->id);
            });

        // Filter by trip
        if ($request->filled('trip_id')) {
            $query->where('route_id', $request->trip_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by boarding status
        if ($request->filled('boarded')) {
            if ($request->boarded === 'yes') {
                $query->whereNotNull('boarded_at');
            } elseif ($request->boarded === 'no') {
                $query->whereNull('boarded_at');
            }
        }

        // Filter by date range
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

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('passenger_name', 'like', "%{$search}%")
                  ->orWhere('passenger_phone', 'like', "%{$search}%")
                  ->orWhere('reference_id', 'like', "%{$search}%")
                  ->orWhere('seat_number', $search);
            });
        }

        // Sort
        $sortField = $request->get('sort', 'created_at');
        $sortDir = $request->get('dir', 'desc');
        $allowedSorts = ['created_at', 'seat_number', 'passenger_name', 'status', 'reference_id'];
        if (in_array($sortField, $allowedSorts)) {
            $query->orderBy($sortField, $sortDir === 'asc' ? 'asc' : 'desc');
        }

        $passengers = $query->paginate(30)->withQueryString();

        // Get all operator trips for the filter dropdown
        $trips = Route::where('operator_id', $operator->id)
            ->where('travel_date', '>=', Carbon::today()->subDays(7))
            ->orderBy('travel_date', 'desc')
            ->get()
            ->map(function ($route) {
                return [
                    'id' => $route->id,
                    'label' => "{$route->origin} → {$route->destination} - " . Carbon::parse($route->travel_date)->format('d M Y') . " ({$route->departure_time})",
                ];
            });

        // Summary stats
        $stats = $this->getPassengerStats($operator);

        $statusStyles = [
            'confirmed' => 'bg-primary/10 text-primary',
            'pending' => 'bg-tertiary/10 text-tertiary',
            'cancelled' => 'bg-error-container/20 text-error',
            'expired' => 'bg-surface-container-high text-on-surface-variant',
        ];

        return view('operator.passenger_list', compact(
            'operator',
            'passengers',
            'trips',
            'stats',
            'statusStyles'
        ));
    }

    /**
     * Print-friendly manifest for a specific trip.
     */
    public function manifest($routeId)
    {
        $operator = $this->getOperator();

        $route = Route::with(['bus', 'operator'])
            ->where('operator_id', $operator->id)
            ->findOrFail($routeId);

        $bookings = Booking::where('route_id', $route->id)
            ->orderByRaw("FIELD(status, 'confirmed', 'pending', 'cancelled')")
            ->orderBy('seat_number', 'asc')
            ->get();

        $stats = [
            'total' => $bookings->count(),
            'confirmed' => $bookings->where('status', 'confirmed')->count(),
            'pending' => $bookings->where('status', 'pending')->count(),
            'cancelled' => $bookings->where('status', 'cancelled')->count(),
            'boarded' => $bookings->whereNotNull('boarded_at')->count(),
            'total_revenue' => $bookings->where('status', 'confirmed')->sum('amount'),
        ];

        return view('operator.passenger_manifest', compact(
            'operator',
            'route',
            'bookings',
            'stats'
        ));
    }

    /**
     * Export passenger list as CSV for a specific trip.
     */
    public function export($routeId)
    {
        $operator = $this->getOperator();

        $route = Route::with('bus')
            ->where('operator_id', $operator->id)
            ->findOrFail($routeId);

        $bookings = Booking::where('route_id', $route->id)
            ->orderBy('seat_number', 'asc')
            ->get();

        $filename = 'passenger-manifest-' . preg_replace('/[^a-z0-9]/i', '-', $route->origin . '-' . $route->destination)
            . '-' . Carbon::parse($route->travel_date)->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($bookings, $route) {
            $handle = fopen('php://output', 'w');

            // Header row
            fputcsv($handle, [
                'Seat #', 'Passenger Name', 'Phone Number', 'ID Number',
                'Reference', 'Status', 'Boarded', 'Amount (ZMW)'
            ]);

            foreach ($bookings as $booking) {
                fputcsv($handle, [
                    $booking->seat_number,
                    $booking->passenger_name ?? 'N/A',
                    $booking->passenger_phone ?? '—',
                    $booking->passenger_id_number ?? '—',
                    $booking->reference_id,
                    ucfirst($booking->status),
                    $booking->isBoarded() ? 'Yes' : 'No',
                    number_format($booking->amount, 2),
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Bulk check-in (mark as boarded) for selected passengers.
     */
    public function bulkCheckin(Request $request)
    {
        $operator = $this->getOperator();

        $request->validate([
            'booking_ids' => 'required|array',
            'booking_ids.*' => 'exists:bookings,id',
        ]);

        $count = 0;
        foreach ($request->booking_ids as $bookingId) {
            $booking = Booking::whereHas('route', function ($q) use ($operator) {
                $q->where('operator_id', $operator->id);
            })->find($bookingId);

            if ($booking && $booking->status === 'confirmed' && !$booking->isBoarded()) {
                $booking->markBoarded('operator');
                $count++;
            }
        }

        return back()->with('status', "{$count} passenger(s) checked in successfully.");
    }

    private function getPassengerStats($operator)
    {
        $operatorId = $operator->id;
        $today = Carbon::today();

        $total = Booking::whereHas('route', fn($q) => $q->where('operator_id', $operatorId))->count();
        $todayCount = Booking::whereHas('route', fn($q) => $q->where('operator_id', $operatorId)->where('travel_date', $today))->count();
        $boarded = Booking::whereHas('route', fn($q) => $q->where('operator_id', $operatorId))->whereNotNull('boarded_at')->count();
        $notBoarded = Booking::whereHas('route', fn($q) => $q->where('operator_id', $operatorId))
            ->where('status', 'confirmed')
            ->whereNull('boarded_at')
            ->count();

        return compact('total', 'todayCount', 'boarded', 'notBoarded');
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
}