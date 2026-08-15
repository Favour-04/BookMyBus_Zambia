<?php

namespace App\Notifications;

use App\Models\Route;
use App\Notifications\Channels\SmsChannel;
use App\Notifications\Channels\WhatsAppChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TripStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Route $route, public string $status)
    {
    }

    public function via($notifiable): array
    {
        return ['mail', SmsChannel::class, WhatsAppChannel::class];
    }

    public function toMail($notifiable): MailMessage
    {
        $route = $this->route;

        return (new MailMessage())
            ->subject('Trip update: ' . $route->origin . ' → ' . $route->destination)
            ->greeting('Hi ' . $notifiable->full_name . ',')
            ->line($this->describe())
            ->line('Route: ' . $route->origin . ' → ' . $route->destination)
            ->line('Scheduled: ' . $route->travel_date->format('d M Y') . ' at ' . $route->departure_time)
            ->line('Thank you for travelling with BookMyBus Zambia.');
    }

    public function toSms($notifiable): string
    {
        $route = $this->route;

        return "BookMyBus update ({$route->origin} to {$route->destination}, "
            . "{$route->travel_date->format('d M')} {$route->departure_time}): {$this->describe()}";
    }

    public function toWhatsApp($notifiable): string
    {
        $route = $this->route;

        return "🚌 BookMyBus Trip Update\nRoute: {$route->origin} → {$route->destination}\n"
            . "Scheduled: {$route->travel_date->format('d M Y')} at {$route->departure_time}\n\n"
            . $this->describe();
    }

    protected function describe(): string
    {
        $route = $this->route;

        return match ($this->status) {
            'delayed'   => "Your trip is delayed by {$route->delay_minutes} minute(s)" . ($route->delay_reason ? " ({$route->delay_reason})." : "."),
            'departed'  => "Your trip has departed from {$route->origin}.",
            'arrived'   => "Your trip has arrived at {$route->destination}.",
            'cancelled' => "Your trip has been CANCELLED. A refund (if applicable) will be processed per the operator's policy.",
            default     => "Your trip status is now {$this->status}.",
        };
    }
}
