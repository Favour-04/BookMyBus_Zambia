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
        'guest' => \Illuminate\Auth\Middleware\RedirectIfAuthenticated::class,
    ]);
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
