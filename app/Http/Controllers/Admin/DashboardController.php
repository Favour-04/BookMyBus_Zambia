<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Operator;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard with system-wide statistics.
     */
    public function index()
    {
        $stats = [
            'total_users' => User::where('role', 'traveler')->count(),
            'total_operators' => Operator::count(),
            'pending_verifications' => Operator::where('is_verified', false)->count(),
            'total_bookings' => Booking::count(),
            'confirmed_bookings' => Booking::where('status', 'confirmed')->count(),
            'total_revenue' => Booking::where('status', 'confirmed')->sum('amount'),
        ];

        $recent_operators = Operator::latest()->take(5)->get();
        $recent_bookings = Booking::with(['route', 'user'])
            ->latest()
            ->take(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recent_operators', 'recent_bookings'));
    }
}