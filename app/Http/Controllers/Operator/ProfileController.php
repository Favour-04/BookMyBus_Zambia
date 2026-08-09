<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\Operator;
use App\Models\Route;
use App\Models\Booking;
use App\Models\Bus;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    /**
     * Display the operator's profile.
     */
    public function index()
    {
        $operator = $this->getOperator();
        $operatorId = $operator->id;

        // --- Operational Statistics ---
        $total_trips = Route::where('operator_id', $operatorId)->count();

        $active_buses = Bus::where('operator_id', $operatorId)
            ->where('is_active', true)
            ->count();

        // Average occupancy across all trips
        $routes = Route::with('bus')->where('operator_id', $operatorId)->get();
        $totalCapacity = 0;
        $totalBooked = 0;
        foreach ($routes as $route) {
            $capacity = $route->bus->seat_capacity ?? 40;
            $booked = count($route->bookedSeats() ?? []);
            $totalCapacity += $capacity;
            $totalBooked += $booked;
        }
        $avg_occupancy = $totalCapacity > 0 ? round(($totalBooked / $totalCapacity) * 100) : 0;

        // On-time rate (based on trips that departed as scheduled)
        $total_trips_counted = Route::where('operator_id', $operatorId)
            ->where('travel_date', '>=', Carbon::now()->subDays(30))
            ->count();
        $on_time_trips = Route::where('operator_id', $operatorId)
            ->where('travel_date', '>=', Carbon::now()->subDays(30))
            ->where('is_active', true)
            ->count();
        $on_time_rate = $total_trips_counted > 0 ? round(($on_time_trips / $total_trips_counted) * 100) : 0;

        // Total confirmed bookings (all time)
        $total_bookings = Booking::whereHas('route', function ($query) use ($operatorId) {
            $query->where('operator_id', $operatorId);
        })->where('status', 'confirmed')->count();

        return view('operator.profile', [
            'operator'       => $operator,
            'total_trips'    => $total_trips,
            'active_buses'   => $active_buses,
            'avg_occupancy'  => $avg_occupancy,
            'on_time_rate'   => $on_time_rate,
            'total_bookings' => $total_bookings,
        ]);
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

    /**
     * Update the operator's business details.
     */
    public function update(Request $request)
    {
        $operator = $this->getOperator();

        $validated = $request->validate([
            'company_name'                => 'required|string|max:255',
            'email'                       => ['required', 'email', Rule::unique('operators', 'email')->ignore($operator->id)],
            'phone_number'                => ['required', 'string', Rule::unique('operators', 'phone_number')->ignore($operator->id)],
            'tpin'                        => 'nullable|string|max:50',
            'address'                     => 'nullable|string|max:255',
            'contact_person_name'         => 'nullable|string|max:255',
            'contact_person_title'        => 'nullable|string|max:255',
            'business_registration_number'=> 'nullable|string|max:100',
            'business_registration_date'  => 'nullable|date',
            'business_type'               => 'nullable|string|max:100',
            'description'                 => 'nullable|string|max:1000',
            'website'                     => 'nullable|url|max:255',
        ]);

        $operator->update($validated);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $request->validate([
                'logo' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            ]);

            // Delete old logo if exists
            if ($operator->logo_path) {
                Storage::disk('public')->delete($operator->logo_path);
            }

            $path = $request->file('logo')->store('operator-logos', 'public');
            $operator->update(['logo_path' => $path]);
        }

        return back()->with('status', 'Your business profile has been updated successfully.');
    }

    /**
     * Update the operator's password.
     */
    public function updatePassword(Request $request)
    {
        $operator = $this->getOperator();

        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, $operator->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'The current password you entered is incorrect.',
            ]);
        }

        $operator->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('status', 'Your password has been updated successfully.');
    }
}