<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\Operator\DashboardController;
use App\Http\Controllers\Operator\TripManagementController;
use App\Http\Controllers\Operator\BookingManagementController;

Route::get('/', [LandingController::class, 'index'])->name('home');
Route::get('/search', [LandingController::class, 'search'])->name('trips.search');

// ============================================
// TRAVELER AUTH ROUTES (Guest Only)
// ============================================

Route::middleware('guest')->group(function () {

    //Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
   // Route::post('/login', [LoginController::class, 'login']);

    // Registration (controllers to be implemented)
    // Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    // Route::post('/register', [RegisterController::class, 'register']);

    // Password Reset (controllers to be implemented)
    // Route::get('/forgot-password', [PasswordResetController::class, 'showLinkRequestForm'])->name('password.request');
    // Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLinkEmail'])->name('password.email');
    // Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
    // Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.update');
});

// ============================================
// OPERATOR AUTH ROUTES (Operator Guest Only)
// ============================================

Route::middleware('guest:operator')->group(function () {
   // Route::get('/operator/login', [OperatorLoginController::class, 'showLoginForm'])->name('operator.login');
   // Route::post('/operator/login', [OperatorLoginController::class, 'login']);
});

// ============================================
// TRAVELER AUTHENTICATED ROUTES
// ============================================

Route::middleware('auth')->group(function () {

    //Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
//
    // Booking
    Route::get('/booking/{route}/seats', [BookingController::class, 'showSeats'])->name('booking.seats');
    Route::post('/booking/store', [BookingController::class, 'store'])->name('bookings.store');
    Route::get('/payment/ticket/{booking}', [BookingController::class, 'paymentTicket'])->name('payment.ticket');
    Route::post('/payment/process/{booking}', [BookingController::class, 'processPayment'])->name('payment.process');
    Route::get('/booking/success/{booking}', [BookingController::class, 'success'])->name('booking.success');

    // Booking History & Lookup
   // Route::get('/my-booking', [BookingController::class, 'customerLookupView'])->name('booking.lookup');
   // Route::post('/my-booking/lookup', [BookingController::class, 'customerLookup'])->name('booking.lookup.search');

    // Profile
   // Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
  //  Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
  //  Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
});

// ============================================
// OPERATOR ROUTES (Requires Operator Auth)
// ============================================

Route::prefix('operator')->name('operator.')->middleware('auth:operator')->group(function () {

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Trip Management
    Route::get('/trips', [TripManagementController::class, 'index'])->name('trips.index');
    Route::get('/bookings', [BookingManagementController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/export', [BookingManagementController::class, 'export'])->name('bookings.export');
    Route::get('/bookings/{booking}', [BookingManagementController::class, 'show'])->name('bookings.show');
    Route::get('/trips/export', [TripManagementController::class, 'export'])->name('trips.export');
    Route::get('/trips/stats', [TripManagementController::class, 'stats'])->name('trips.stats');
    Route::get('/trips/upcoming', [TripManagementController::class, 'upcoming'])->name('trips.upcoming');
    Route::post('/trips', [TripManagementController::class, 'store'])->name('trips.store');

    // Wildcard {trip} routes (must come after static routes)
    Route::get('/trips/{trip}/seat-map', [TripManagementController::class, 'seatMap'])->name('trips.seat-map');
    Route::get('/trips/{trip}/occupancy', [TripManagementController::class, 'occupancy'])->name('trips.occupancy');
    Route::get('/trips/{trip}/bookings', [BookingManagementController::class, 'tripBookings'])->name('trips.bookings');
    Route::get('/trips/{trip}', [TripManagementController::class, 'show'])->name('trips.show');
    Route::put('/trips/{trip}', [TripManagementController::class, 'update'])->name('trips.update');
    Route::delete('/trips/{trip}', [TripManagementController::class, 'cancel'])->name('trips.cancel');
    Route::patch('/trips/{trip}/status', [TripManagementController::class, 'updateStatus'])->name('trips.update-status');

    // Profile
 //   Route::get('/profile', [OperatorProfileController::class, 'index'])->name('profile');
  //  Route::put('/profile', [OperatorProfileController::class, 'update'])->name('profile.update');
   // Route::put('/profile/password', [OperatorProfileController::class, 'updatePassword'])->name('profile.password');

    // Logout
   // Route::post('/logout', [OperatorLoginController::class, 'logout'])->name('logout');
});

// ============================================
// ADMIN ROUTES
// ============================================

// Route::prefix('admin')->name('admin.')->middleware('auth:admin')->group(function () {
//     Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
//     Route::post('/logout', [App\Http\Controllers\Auth\Admin\LoginController::class, 'logout'])->name('logout');
//     Route::get('/operators', [App\Http\Controllers\Admin\OperatorManagementController::class, 'index'])->name('operators.index');
//     Route::post('/operators/{id}/verify', [App\Http\Controllers\Admin\OperatorManagementController::class, 'verify'])->name('operators.verify');
//     Route::get('/users', [App\Http\Controllers\Admin\UserManagementController::class, 'index'])->name('users.index');
//     Route::get('/bookings', [App\Http\Controllers\Admin\BookingManagementController::class, 'index'])->name('bookings.index');
// });

// Admin Audit Logs Page (standalone view)
Route::get('/audit-logs', function () {
    return view('admin.audits_log');
});

// Admin System Reports Page
Route::get('/system-reports', function () {
    return view('admin.systems_report');
});

// ============================================
// FALLBACK ROUTE
// ============================================

// Route::fallback(fn() => view('errors.404'));
