<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\BusController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\RouteController;
use App\Http\Controllers\Api\TicketController;
use Illuminate\Support\Facades\Route;

/*
 BookMyBus Zambia — API Routes

Guards:
auth:sanctum          → Traveler (User model)
auth:operator_api     → Operator (Operator model)  *custom guard*
admin                 → Traveler with role = admin

*/

//Public Routes (no auth required)

Route::prefix('auth')->group(function () {
    // Traveler auth
    Route::post('/register',          [AuthController::class, 'register']);
    Route::post('/login',             [AuthController::class, 'login']);

    // Operator auth
    Route::post('/operator/register', [AuthController::class, 'registerOperator']);
    Route::post('/operator/login',    [AuthController::class, 'loginOperator']);
});

// Public route search (guests can browse without logging in)
Route::get('/routes/search',          [RouteController::class, 'search']);
Route::get('/routes/{id}',            [RouteController::class, 'show']);

// Payment gateway callback (called by MTN/Airtel, not the user)
Route::post('/payments/callback',     [PaymentController::class, 'callback']);


// Authenticated Traveler Routes

Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/auth/logout',       [AuthController::class, 'logout']);
    Route::get('/auth/me',            [AuthController::class, 'me']);

    // Bookings
    Route::get('/bookings',           [BookingController::class, 'index']);
    Route::post('/bookings/hold',     [BookingController::class, 'hold']);
    Route::get('/bookings/{ref}',     [BookingController::class, 'show']);
    Route::patch('/bookings/{id}/cancel', [BookingController::class, 'cancel']);

    // Payments
    Route::post('/payments/initiate', [PaymentController::class, 'initiate']);
    Route::get('/payments/status/{bookingId}', [PaymentController::class, 'status']);

    // Tickets
    Route::get('/tickets',            [TicketController::class, 'index']);
    Route::get('/tickets/{qrCode}',   [TicketController::class, 'show']);
});


//  Operator Routes
// Uses a custom 'operator' guard — see config/auth.php

Route::middleware('auth:operator_api')->prefix('operator')->group(function () {
    // Fleet management
    Route::get('/buses',              [BusController::class, 'index']);
    Route::post('/buses',             [BusController::class, 'store']);
    Route::patch('/buses/{id}',       [BusController::class, 'update']);
    Route::delete('/buses/{id}',      [BusController::class, 'destroy']);

    // Route management
    Route::get('/routes',             [RouteController::class, 'operatorRoutes']);
    Route::post('/routes',            [RouteController::class, 'store']);
    Route::patch('/routes/{id}',      [RouteController::class, 'update']);
    Route::delete('/routes/{id}',     [RouteController::class, 'destroy']);

    // Passenger manifest
    Route::get('/routes/{routeId}/manifest', [BookingController::class, 'routeManifest']);

    // Ticket verification at station
    Route::post('/tickets/verify',    [TicketController::class, 'verify']);
});


// Admin Routes

Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard',                      [AdminController::class, 'dashboard']);
    Route::get('/users',                          [AdminController::class, 'users']);
    Route::get('/operators',                      [AdminController::class, 'operators']);
    Route::post('/operators/{id}/verify',         [AdminController::class, 'verifyOperator']);
    Route::post('/operators/{id}/suspend',        [AdminController::class, 'suspendOperator']);
    Route::get('/bookings',                       [AdminController::class, 'bookings']);
});

// System Reports (publicly accessible via web route, but API endpoint for data)
Route::get('/system-reports',                   [AdminController::class, 'systemReports']);
