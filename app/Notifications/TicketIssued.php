<?php

namespace App\Notifications;

use App\Models\Booking;
use App\Models\Ticket;
use App\Notifications\Channels\SmsChannel;
use App\Notifications\Channels\WhatsAppChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketIssued extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Booking $booking, public Ticket $ticket)
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
            ->subject('Your BookMyBus Zambia Ticket — ' . $route->origin . ' → ' . $route->destination)
            ->greeting('Hi ' . $notifiable->full_name . ',')
            ->line('Your booking is confirmed. Here are your trip details:')
            ->line('Route: ' . $route->origin . ' → ' . $route->destination)
            ->line('Date: ' . $route->travel_date->format('d M Y') . ' at ' . $route->departure_time)
            ->line('Seat: ' . $this->booking->seat_number . ' · Ref: ' . $this->booking->reference_id)
            ->line('Boarding QR code: ' . $this->ticket->qr_code)
            ->action('View Ticket Online', route('tickets.show', $this->ticket->qr_code))
            ->line('Present the QR code at boarding. Safe travels!');
    }

    public function toSms($notifiable): string
    {
        $route = $this->booking->route;

        return "BookMyBus: Ticket confirmed. {$route->origin} to {$route->destination}, "
            . $route->travel_date->format('d M') . " at {$route->departure_time}, "
            . "Seat {$this->booking->seat_number}. Ref {$this->booking->reference_id}. "
            . "QR {$this->ticket->qr_code}. Show at boarding.";
    }

    public function toWhatsApp($notifiable): string
    {
        $route = $this->booking->route;

        return "🚌 BookMyBus Zambia — Ticket Confirmed!\n\n"
            . "Route: {$route->origin} → {$route->destination}\n"
            . "Date: {$route->travel_date->format('d M Y')} at {$route->departure_time}\n"
            . "Seat: {$this->booking->seat_number}\n"
            . "Ref: {$this->booking->reference_id}\n"
            . "Boarding QR: {$this->ticket->qr_code}\n\n"
            . "Show this code at boarding. Safe travels!";
    }
}
