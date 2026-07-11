<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    /**
     * Display the traveler's profile, including booking history.
     */
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::guard('web')->user();

        $bookings = $user->bookings()
            ->with('route')
            ->latest()
            ->take(20)
            ->get();

        return view('profile', [
            'user'     => $user,
            'bookings' => $bookings,
        ]);
    }

    /**
     * Update the traveler's basic account details.
     */
    public function update(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::guard('web')->user();

        $validated = $request->validate([
            'full_name'           => 'required|string|max:255',
            'email'               => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'phone_number'        => ['required', 'string', Rule::unique('users', 'phone_number')->ignore($user->id)],
            'preferred_language'  => 'nullable|string|in:en,ny,be',
        ]);

        $user->update($validated);

        return back()->with('status', 'Your profile has been updated successfully.');
    }

    /**
     * Update the traveler's password.
     */
    public function updatePassword(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::guard('web')->user();

        $request->validate([
            'current_password'      => 'required',
            'password'              => 'required|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'The current password you entered is incorrect.',
            ]);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('status', 'Your password has been updated successfully.');
    }
}
