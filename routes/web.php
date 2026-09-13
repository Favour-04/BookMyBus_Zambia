<?php

use App\Http\Controllers\Admin\PanelController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Authentication routes (shared by travelers and admins)
|--------------------------------------------------------------------------
*/
Route::get('/login', [PanelController::class, 'showLogin'])->name('login');
Route::post('/login', [PanelController::class, 'login'])->name('login.store');

Route::get('/register', [PanelController::class, 'showRegister'])->name('register');
Route::post('/register', [PanelController::class, 'register'])->name('register.store');

Route::post('/logout', [PanelController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| Admin panel
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    Route::get('/', [PanelController::class, 'index'])->name('dashboard');
    Route::get('/search', [PanelController::class, 'searchResults'])->name('search.results');

    Route::get('/booking/{route}', [PanelController::class, 'booking'])->name('booking');
    Route::post('/booking/{route}', [PanelController::class, 'holdSeat'])->name('booking.hold');

    Route::get('/payment/{booking}', [PanelController::class, 'payment'])->name('payment');
    Route::post('/payment/{booking}', [PanelController::class, 'processPayment'])->name('payment.process');

    Route::get('/profile', [PanelController::class, 'profile'])->name('profile');
    Route::put('/profile', [PanelController::class, 'updateProfile'])->name('profile.update');

    Route::get('/support', [PanelController::class, 'support'])->name('support');
    Route::post('/support', [PanelController::class, 'submitSupport'])->name('support.submit');
});
