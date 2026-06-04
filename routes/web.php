<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\BookingController;

Route::get('/', [LandingController::class, 'index']);
Route::get('/search', [LandingController::class, 'search'])->name('trips.search');
Route::get('/booking/{route}/seats', [BookingController::class, 'showSeats'])->name('booking.seats');
Route::post('/booking/store', [BookingController::class, 'store'])->name('bookings.store');
Route::get('/payment/ticket/{booking}', [BookingController::class, 'paymentTicket'])->name('payment.ticket');
Route::post('/payment/process/{booking}', [BookingController::class, 'processPayment'])->name('payment.process');
Route::get('/booking/success/{booking}', [BookingController::class, 'success'])->name('booking.success');