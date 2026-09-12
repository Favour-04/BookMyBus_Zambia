<?php

namespace App\Http\Controllers;

use App\Models\Route;
use Illuminate\Http\Request;
use Stevebauman\Location\Facades\Location;

class LandingController extends Controller
{
    /**
     * Display the landing page with popular routes.
     */
    public function index()
    {
        //$userPosition = Location::get(request()->ip()); this is the actual code to be in the code base
        $userPosition = Location::get('165.56.66.198'); // this is for testing purposes only, will need to be removed
        $detectedCity = $userPosition ? $userPosition->cityName  : null;

        if ($detectedCity) {
            $cityExistsInDB = Route::where('is_active', true)
                ->where('origin', 'like', '%' . $detectedCity . '%')
                ->exists();
            if (!$cityExistsInDB) {
                $detectedCity = null;
            }
        }

        if ($detectedCity) {
            $routes = Route::where('is_active', true)
                ->where('origin', 'like', '%' . $detectedCity . '%')
                ->orderBy('fare', 'asc')
                ->take(20)
                ->get()
                ->shuffle()
                ->take(4);

            if ($routes->count() < 4) {
                $needed = 4 - $routes->count();
                $moreRoutes = Route::where('is_active', true)
                    ->whereNotIn('id', $routes->pluck('id'))
                    ->take(20)
                    ->get()
                    ->shuffle()
                    ->take($needed);

                foreach ($moreRoutes as $route) {
                    $routes->push($route);
                }
            }
        } else {
            $routes = collect();
        }

        if ($routes->isEmpty()) {
            $routes = Route::where('is_active', true)
                ->take(20)
                ->get()
                ->shuffle()
                ->take(4);
        }
        // $routes = Route::where('is_active', true)
        //     ->take(20)
        //     ->get()
        //     ->shuffle()
        //     ->take(4);

        return view('landing_search', compact('routes', 'detectedCity'));
    }

    /*
     * Search for trips based on origin and destination.
     */
    public function search(Request $request)
    {
        $validated = $request->validate([
            'origin' => 'nullable|string',
            'destination' => 'nullable|string',
            'travel_date' => 'nullable|date',
            'passengers' => 'nullable|integer|min:1',
            'min_price' => 'nullable|numeric|min:0',
            'max_price' => 'nullable|numeric|min:0',
            'time_of_day' => 'nullable|array',
            'time_of_day.*' => 'in:dawn,morning,afternoon,night',
            'operators' => 'nullable|array',
            'operators.*' => 'integer|exists:operators,id',
        ]);

        // Base match on route/date only — used to populate the operator checklist
        // so toggling a filter doesn't make other operators disappear from the list.
        $baseQuery = Route::search($request->origin, $request->destination, $request->travel_date);

        $allOperators = (clone $baseQuery)
            ->with('operator')
            ->get()
            ->pluck('operator')
            ->filter()
            ->unique('id')
            ->values();

        $trips = $baseQuery
            ->with(['bus', 'operator'])
            ->priceBetween($request->min_price, $request->max_price)
            ->departureTimeOfDay($request->time_of_day ?? [])
            ->when($request->filled('operators'), fn($q) => $q->whereIn('operator_id', $request->operators))
            ->orderBy('departure_time')
            ->get();

        return view('search_results', [
            'trips' => $trips,
            'allOperators' => $allOperators,
            'request' => $request,
        ]);
    }
}
