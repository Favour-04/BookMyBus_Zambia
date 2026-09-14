<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Show the traveler login form.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle traveler login.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        // Normalize the email (lowercase + trim) so copy/paste or Caps Lock
        // can't silently break a valid login.
        $email = strtolower(trim($request->input('email')));

        // Pre-fetch for the cross-portal hint below (no password involved).
        $matchingTraveler = \App\Models\User::where('email', $email)->first(['id', 'email', 'role', 'deleted_at']);

        if (Auth::guard('web')->attempt(
            ['email' => $email, 'password' => $request->input('password')],
            $request->filled('remember')
        )) {
            $user = Auth::guard('web')->user();

            // Block suspended accounts
            if (!$user->isActive()) {
                Auth::guard('web')->logout();
                throw ValidationException::withMessages([
                    'email' => 'This account has been suspended. Please contact support.',
                ]);
            }

            $request->session()->regenerate();
            return redirect()->intended(route('home'));
        }

        // If the email belongs to an operator account instead, point the
        // user at the right portal rather than a bare "invalid" message.
        if (! $matchingTraveler) {
            if (\App\Models\Operator::where('email', $email)->exists()) {
                throw ValidationException::withMessages([
                    'email' => 'This email belongs to an operator account. Please sign in on the Operator Portal page instead.',
                ]);
            }
        }

        throw ValidationException::withMessages([
            'email' => 'Invalid email or password.',
        ]);
    }

    /**
     * Logout the traveler.
     */
    public function logout(Request $request)
    {
        Auth::guard('operator')->logout();
        Auth::guard('web')->logout();
        //Auth::guard('admin')->logout();

        $request->session()->flush();          // ← clear all session data first
        $request->session()->invalidate();     // ← invalidate current session
        $request->session()->regenerate(true); // ← force generate a brand new session ID

        return redirect(route('home'));
    }
}
