<?php

namespace App\Console\Commands;

use App\Models\Booking;
use Illuminate\Console\Command;

class ExpirePendingBookings extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'bookings:expire-pending';

    /**
     * The console command description.
     */
    protected $description = 'Flip pending bookings whose 10-minute seat hold has passed to expired';

    /**
     * Execute the console command.
     *
     * This is a data-hygiene sweep, not the source of truth for seat
     * availability — Route::bookedSeats() already excludes pending bookings
     * past their held_until at read time, so seats free up instantly for
     * new searches regardless of how often this command runs. This command
     * just makes the stored status honest, so reports/dashboards/operator
     * views don't keep counting long-abandoned bookings as "pending".
     */
    public function handle(): int
    {
        $count = Booking::where('status', 'pending')
            ->whereNotNull('held_until')
            ->where('held_until', '<', now())
            ->update(['status' => 'expired']);

        $this->info("Expired {$count} stale pending booking(s).");

        return self::SUCCESS;
    }
}
