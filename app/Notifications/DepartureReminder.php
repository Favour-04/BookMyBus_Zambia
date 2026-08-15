<?php

namespace App\Notifications;

use App\Models\Booking;
use App\Notifications\Channels\SmsChannel;
use App\Notifications\Channels\WhatsAppChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DepartureReminder extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Booking $booking)
    {
    }

    public function via($notifiable): array
    {
        return ['mail', SmsChannel::class, WhatsAppChannel::class];
    }

    public function toMail($notifiable): MailMessage
    {
        $route = $this->booking->route;

        return (new MailMessage())
            ->subject('Departure reminder: ' . $route->origin . ' → ' . $route->destination)
            ->greeting('Hi ' . $notifiable->full_name . ',')
            ->line('This is a reminder that your trip departs soon.')
            ->line('Route: ' . $route->origin . ' → ' . $route->destination)
            ->line('Departure: ' . $route->travel_date->format('d M Y') . ' at ' . $route->departure_time)
            ->line('Seat: ' . $this->booking->seat_number . ' · Ref: ' . $this->booking->reference_id)
            ->line('Please arrive at the boarding point on time.');
    }

    public function toSms($notifiable): string
    {
        $route = $this->booking->route;

        return "BookMyBus reminder: {$route->origin} to {$route->destination} departs "
            . "{$route->travel_date->format('d M')} at {$route->departure_time}. "
            . "Seat {$this->booking->seat_number}. Ref {$this->booking->reference_id}.";
    }

    public function toWhatsApp($notifiable): string
    {
        $route = $this->booking->route;

        return "⏰ BookMyBus Departure Reminder\n{$route->origin} → {$route->destination}\n"
            . "Departs: {$route->travel_date->format('d M Y')} at {$route->departure_time}\n"
            . "Seat: {$this->booking->seat_number} · Ref: {$this->booking->reference_id}";
    }
}
