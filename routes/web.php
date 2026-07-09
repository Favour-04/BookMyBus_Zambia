<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\Operator\DashboardController;
use App\Http\Controllers\Operator\TripManagementController;
use App\Http\Controllers\Operator\BookingManagementController;

Route::get('/', [LandingController::class, 'index']);
Route::get('/search', [LandingController::class, 'search'])->name('trips.search');
Route::get('/booking/{route}/seats', [BookingController::class, 'showSeats'])->name('booking.seats');
Route::post('/booking/store', [BookingController::class, 'store'])->name('bookings.store');
Route::get('/payment/ticket/{booking}', [BookingController::class, 'paymentTicket'])->name('payment.ticket');
Route::post('/payment/process/{booking}', [BookingController::class, 'processPayment'])->name('payment.process');
Route::get('/booking/success/{booking}', [BookingController::class, 'success'])->name('booking.success');
Route::get('/operator', [DashboardController::class, 'index'])->name('operator.dashboard');
// Route::get('/operator/trips', [TripManagementController::class, 'index']);

Route::get('/my-booking', [BookingController::class, 'customerLookupView'])->name('booking.lookup');
Route::post('/my-booking/lookup', [BookingController::class, 'customerLookup'])->name('booking.lookup.search');

Route::prefix('operator')->name('operator.')->group(function () {
    
    // All your routes - the controller handles authentication/fallback
    Route::get('/trips', [TripManagementController::class, 'index'])->name('trips.index');
    Route::get('/bookings', [BookingManagementController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/export', [BookingManagementController::class, 'export'])->name('bookings.export');
    Route::get('/bookings/{booking}', [BookingManagementController::class, 'show'])->name('bookings.show');
    Route::get('/trips/export', [TripManagementController::class, 'export'])->name('trips.export');
    Route::post('/trips', [TripManagementController::class, 'store'])->name('trips.store');
    Route::get('/trips/{trip}/seat-map', [TripManagementController::class, 'seatMap'])->name('trips.seat-map');
    Route::get('/trips/{trip}/occupancy', [TripManagementController::class, 'occupancy'])->name('trips.occupancy');
    Route::get('/trips/{trip}/bookings', [BookingManagementController::class, 'tripBookings'])->name('trips.bookings');
    Route::get('/trips/{trip}', [TripManagementController::class, 'show'])->name('trips.show');
    Route::put('/trips/{trip}', [TripManagementController::class, 'update'])->name('trips.update');
    Route::delete('/trips/{trip}', [TripManagementController::class, 'cancel'])->name('trips.cancel');
    Route::patch('/trips/{trip}/status', [TripManagementController::class, 'updateStatus'])->name('trips.update-status');
    Route::get('/trips/stats', [TripManagementController::class, 'stats'])->name('trips.stats');
    Route::get('/trips/upcoming', [TripManagementController::class, 'upcoming'])->name('trips.upcoming');
});

