<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
        'role' => \App\Http\Middleware\RoleMiddleware::class,
        // The app's own RedirectIfAuthenticated (not the framework default)
        // sends an already-logged-in admin/operator back to their own
        // dashboard instead of Laravel's generic 'dashboard'/'home' fallback.
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
    ]);

        // Without this, an unauthenticated hit on any auth:admin / auth:operator
        // route falls back to the framework default of route('login') — the
        // traveler login page — regardless of which guard rejected the
        // request. Since guards 'web' and 'admin' share the same `users`
        // table, an admin sent there can even log in successfully under the
        // 'web' guard, then bounce straight back to the same wrong page
        // (auth:admin still rejects them, guest still blocks /login once
        // they're authenticated as a traveler). Route guests to the login
        // page that matches the portal they were trying to reach.
        $middleware->redirectGuestsTo(function ($request) {
            if ($request->is('admin', 'admin/*')) {
                return route('admin.login');
            }
            if ($request->is('operator', 'operator/*')) {
                return route('operator.login');
            }
            return route('login');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // A stale/expired CSRF token (e.g. a login form left open past the
        // session lifetime) used to dead-end on Laravel's bare "Page Expired"
        // screen. Send the user back to the form they were on instead, with
        // a fresh token and a message explaining what happened.
        // (TokenMismatchException is converted to a 419 HttpException by
        // Handler::prepareException() before render callbacks run, so it
        // must be caught here by status code rather than exception class.)
        $exceptions->render(function (HttpExceptionInterface $e, $request) {
            if ($e->getStatusCode() === 419) {
                return redirect()->back()
                    ->withInput($request->except('password'))
                    ->withErrors(['email' => 'Your session expired. Please try signing in again.']);
            }
        });
    })->create();
