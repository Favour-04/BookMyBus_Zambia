<?php

namespace App\Http\Controllers\Auth\Operator;

use App\Http\Controllers\Controller;
use App\Models\OperatorAuditLog;
use App\Services\OperatorAuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Show the operator login form.
     */
    public function showLoginForm()
    {
        return view('auth.operator-login');
    }

    /**
     * Handle operator login.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::guard('operator')->attempt(
            $request->only('email', 'password'),
            $request->filled('remember')
        )) {
            $operator = Auth::guard('operator')->user();

            // Block unverified operators
            if (!$operator->is_verified) {
                Auth::guard('operator')->logout();
                throw ValidationException::withMessages([
                    'email' => 'Your account is pending admin verification. Please check back later or contact support.',
                ]);
            }

            $request->session()->regenerate();

            OperatorAuditService::log('login', 'Operator logged in', $operator);

            return redirect()->intended(route('operator.dashboard'));
        }

        throw ValidationException::withMessages([
            'email' => 'Invalid email or password.',
        ]);
    }

    /**
     * Logout the operator.
     */
    public function logout(Request $request)
{
    Auth::guard('web')->logout();
    Auth::guard('operator')->logout();
    //Auth::guard('admin')->logout();

    $request->session()->flush();          // ← clear all session data first
    $request->session()->invalidate();     // ← invalidate current session
    $request->session()->regenerate(true); // ← force generate a brand new session ID

    try {
        OperatorAuditService::log('logout', 'Operator logged out');
    } catch (\Exception $e) {
        // Logging can fail here because session is already destroyed
    }

    return redirect(route('operator.login'));
}
}
