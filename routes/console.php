<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Send departure reminders to confirmed passengers on trips departing soon.
Schedule::command('notifications:departure-reminders')->everyFifteenMinutes()->withoutOverlapping();

// Flip stale pending bookings (10-minute seat hold passed, never paid) to
// expired. Seat availability itself doesn't depend on this running promptly
// — Route::bookedSeats() already excludes expired holds at read time — but
// this keeps booking status accurate for reporting/operator views.
Schedule::command('bookings:expire-pending')->everyFiveMinutes()->withoutOverlapping();
