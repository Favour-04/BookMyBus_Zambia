<?php

namespace App\Http\Controllers;

use App\Models\Route;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    /**
     * Display the landing page with popular routes.
     */
    public function index()
    {
        $routes = Route::where('is_active', true)
            ->take(4)
            ->get();

        return view('landing_search', compact('routes'));
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
