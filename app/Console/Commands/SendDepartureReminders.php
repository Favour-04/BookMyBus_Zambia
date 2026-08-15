<?php

namespace App\Console\Commands;

use App\Models\NotificationLog;
use App\Models\Route;
use App\Models\User;
use App\Notifications\DepartureReminder;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendDepartureReminders extends Command
{
    protected $signature = 'notifications:departure-reminders';

    protected $description = 'Send departure reminders to confirmed passengers on trips departing within 2 hours.';

    public function handle(): int
    {
        $windowStart = Carbon::now();
        $windowEnd = Carbon::now()->addHours(2);

        $routes = Route::where('is_active', true)->whereNull('departed_at')->get();

        $sent = 0;

        foreach ($routes as $route) {
            $departure = Carbon::parse($route->travel_date)->setTimeFromTimeString((string) $route->departure_time);

            if (! $departure->between($windowStart, $windowEnd)) {
                continue;
            }

            $bookings = $route->bookings()->where('status', 'confirmed')->with('user', 'ticket')->get();

            foreach ($bookings as $booking) {
                if (! $booking->user) {
                    continue;
                }

                // De-duplicate: only one departure reminder per traveler per 6 hours.
                $alreadyReminded = NotificationLog::where('notifiable_type', User::class)
                    ->where('notifiable_id', $booking->user->id)
                    ->where('notification_type', DepartureReminder::class)
                    ->where('created_at', '>=', Carbon::now()->subHours(6))
                    ->exists();

                if ($alreadyReminded) {
                    continue;
                }

                $booking->user->notify(new DepartureReminder($booking));
                $sent++;
            }
        }

        $this->info("Sent {$sent} departure reminder(s).");

        return self::SUCCESS;
    }
}
