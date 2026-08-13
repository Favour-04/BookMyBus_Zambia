<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Operator;
use App\Models\Route;
use Illuminate\Http\Request;

class TripController extends Controller
{
    /**
     * List all trips across every operator with their status.
     */
    public function index(Request $request)
    {
        $query = Route::with(['operator', 'bus']);

        if ($request->filled('operator_id')) {
            $query->where('operator_id', $request->operator_id);
        }
        if ($request->filled('date')) {
            $query->whereDate('travel_date', $request->date);
        }

        if ($request->filled('status')) {
            switch ($request->status) {
                case 'delayed':
                    $query->whereNotNull('delayed_at');
                    break;
                case 'departed':
                    $query->whereNotNull('departed_at');
                    break;
                case 'arrived':
                    $query->whereNotNull('arrived_at');
                    break;
                case 'scheduled':
                    $query->whereNull('delayed_at')->whereNull('departed_at');
                    break;
                case 'inactive':
                    $query->where('is_active', false);
                    break;
            }
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('origin', 'like', "%{$s}%")
                    ->orWhere('destination', 'like', "%{$s}%");
            });
        }

        $trips = $query->orderByDesc('travel_date')->orderBy('departure_time')->paginate(20)->withQueryString();

        $stats = [
            'total'   => Route::count(),
            'today'   => Route::whereDate('travel_date', today())->count(),
            'delayed' => Route::whereNotNull('delayed_at')->count(),
            'active'  => Route::where('is_active', true)->count(),
        ];

        $operators = Operator::orderBy('company_name')->get(['id', 'company_name']);

        return view('admin.trips.index', compact('trips', 'stats', 'operators'));
    }
}