<?php

namespace App\Http\Controllers\Auth\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Show the admin login form.
     */
    public function showLoginForm()
    {
        return view('auth.admin-login');
    }

    /**
     * Handle admin login.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::guard('admin')->attempt(
            $request->only('email', 'password'),
            $request->filled('remember')
        )) {
            $user = Auth::guard('admin')->user();

            // Only allow users with admin role
            if (!$user->isAdmin()) {
                Auth::guard('admin')->logout();
                throw ValidationException::withMessages([
                    'email' => 'You do not have admin access.',
                ]);
            }

            $request->session()->regenerate();
            return redirect()->intended(route('admin.dashboard'));
        }

        throw ValidationException::withMessages([
            'email' => 'Invalid email or password.',
        ]);
    }

    /**
     * Logout the admin.
     */
    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();

        $request->session()->flush();
        $request->session()->invalidate();
        $request->session()->regenerate(true);

        return redirect(route('admin.login'));
    }
}