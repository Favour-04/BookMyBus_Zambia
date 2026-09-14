<?php

namespace App\Http\Controllers;

use App\Models\Route;
use Illuminate\Http\Request;
use Stevebauman\Location\Facades\Location;

class LandingController extends Controller
{
    /**
     * Flat, deduped, sorted list of every town/city defined in
     * config/zambia_cities.php. Shared by index() (autocomplete data) and
     * search() (origin/destination validation) so there's a single source
     * of truth for "what counts as a real Zambian city" in this controller.
     */
    protected function allCities()
    {
        return collect(config('zambia_cities'))
            ->flatten()
            ->unique()
            ->sort()
            ->values();
    }

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

        // Flat, deduped list of every town/city defined in config/zambia_cities.php,
        // used to power the From/To autocomplete on the search bar.
        $cities = $this->allCities();

        return view('landing_search', compact('routes', 'detectedCity', 'cities'));
    }

    /*
     * Search for trips based on origin and destination.
     */
    public function search(Request $request)
    {
        $cities = $this->allCities();

        // Case-insensitive membership check: the autocomplete always inserts
        // a city exactly as config/zambia_cities.php spells it, but existing
        // links elsewhere in the app (e.g. popular-route cards built from
        // Route::origin/destination) might not match that casing exactly, so
        // this doesn't require an exact-case Rule::in() match.
        $cityRule = function ($attribute, $value, $fail) use ($cities) {
            $matches = $cities->contains(fn ($city) => strcasecmp($city, $value) === 0);

            if (! $matches) {
                $fail('Please choose a valid Zambian city from the suggestions.');
            }
        };

        $validated = $request->validate([
            'origin' => ['nullable', 'string', $cityRule],
            'destination' => ['nullable', 'string', $cityRule],
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
