<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, string ...$guards): mixed
    {
        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                return match($guard) {
                    'operator' => redirect(route('operator.dashboard')),
                    'admin'    => redirect(route('admin.dashboard')),
                    default    => redirect(route('home')),
                };
            }
        }

        return $next($request);
    }
}