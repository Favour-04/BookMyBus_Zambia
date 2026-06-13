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

        if($detectedCity){
            $cityExistsInDB = Route::where('is_active', true)
                -> where('origin', 'ilike', '%' . $detectedCity . '%')
                -> exists();
            if(!$cityExistsInDB){
                $detectedCity = null;
            }
        }

        if($detectedCity){
            $routes = Route::where('is_active', true)
                -> where('origin', 'ilike', '%' . $detectedCity . '%')
                -> orderBy('fare', 'asc')
                -> take(20)
                -> get()
                -> shuffle()
                -> take(4);

            if ($routes->count() < 4){
                $needed = 4 - $routes->count();
                $moreRoutes = Route::where('is_active', true)
                    -> whereNotIn('id', $routes -> pluck('id'))
                    -> take(20)
                    -> get()
                    -> shuffle()
                    -> take($needed);

                foreach($moreRoutes as $route){
                    $routes->push($route);
                }
            }
        }
        else{
            $routes = collect();
        }

        if($routes -> isEmpty()){
            $routes = Route::where('is_active', true)
                -> take(20)
                -> get()
                -> shuffle()
                -> take(4);
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
        $trips = Route::search($request->from, $request->to)
            ->where('is_active', true)
            ->with(['bus', 'operator'])
            ->get();

        return view('search_results', compact('trips', 'request'));
    }
}
