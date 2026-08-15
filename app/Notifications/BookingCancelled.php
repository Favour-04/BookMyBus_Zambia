<?php

namespace App\Notifications;

use App\Models\Booking;
use App\Notifications\Channels\SmsChannel;
use App\Notifications\Channels\WhatsAppChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingCancelled extends Notification implements ShouldQueue
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
        $refund = $this->booking->refund_amount
            ? " A refund of ZMW " . number_format((float) $this->booking->refund_amount, 2) . " will be processed per the operator's policy."
            : '';

        return (new MailMessage())
            ->subject('Booking cancelled — ' . $route->origin . ' → ' . $route->destination)
            ->greeting('Hi ' . $notifiable->full_name . ',')
            ->line("Your booking (Ref {$this->booking->reference_id}) for {$route->origin} → {$route->destination} has been cancelled." . $refund)
            ->line('If you did not request this, please contact support.');
    }

    public function toSms($notifiable): string
    {
        $route = $this->booking->route;

        return "BookMyBus: Booking {$this->booking->reference_id} ({$route->origin} to {$route->destination}) has been cancelled.";
    }

    public function toWhatsApp($notifiable): string
    {
        return "BookMyBus: Your booking {$this->booking->reference_id} has been cancelled. Contact us if this was unexpected.";
    }
}
