<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class RegisterController extends Controller
{
    /**
     * Show the traveler registration form.
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Handle traveler registration.
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'full_name'    => ['required', 'string', 'max:255'],
            'email'        => ['required', 'string', 'email', 'max:255', Rule::unique('users')],
            'phone_number' => ['required', 'string', 'max:20', Rule::unique('users')],
            'password'     => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'full_name'    => $validated['full_name'],
            'email'        => $validated['email'],
            'phone_number' => $validated['phone_number'],
            'password'     => Hash::make($validated['password']),
            'role'         => 'traveler',
        ]);

        Auth::guard('web')->login($user);

        $request->session()->regenerate();

        return redirect()->intended(route('home'));
    }
}