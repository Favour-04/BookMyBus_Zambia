<?php

namespace App\Http\Controllers\Auth\Operator;

use App\Http\Controllers\Controller;
use App\Models\Operator;
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

        // Normalize the email (lowercase + trim) so copy/paste or Caps Lock
        // can't silently break a valid login.
        $email = strtolower(trim($request->input('email')));

        // Pre-fetch for the cross-portal hint below (no password involved).
        $matchingOperator = Operator::withoutGlobalScopes()
            ->where('email', $email)
            ->first(['id', 'email', 'is_verified', 'deleted_at']);

        if (Auth::guard('operator')->attempt(
            ['email' => $email, 'password' => $request->input('password')],
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

        // If the email belongs to a traveler/admin account instead, point the
        // user at the right portal rather than a bare "invalid" message.
        if (! $matchingOperator) {
            if (\App\Models\User::where('email', $email)->exists()) {
                throw ValidationException::withMessages([
                    'email' => 'This email belongs to a traveler/admin account. Please sign in on the Traveler Sign In page instead.',
                ]);
            }
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
