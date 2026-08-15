<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Send departure reminders to confirmed passengers on trips departing soon.
Schedule::command('notifications:departure-reminders')->everyFifteenMinutes()->withoutOverlapping();
