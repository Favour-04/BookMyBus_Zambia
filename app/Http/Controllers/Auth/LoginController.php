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

        if (Auth::guard('web')->attempt(
            $request->only('email', 'password'),
            $request->filled('remember')
        )) {
            $request->session()->regenerate();
            return redirect()->intended(route('home'));
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
