<?php

namespace App\Notifications\Channels;

use App\Models\NotificationLog;
use App\Services\Messaging\MessagingGatewayInterface;
use Illuminate\Notifications\Notification;

class SmsChannel
{
    public function __construct(protected MessagingGatewayInterface $gateway)
    {
    }

    public function send($notifiable, Notification $notification): void
    {
        if (! $notifiable->phone_number) {
            return;
        }

        $message = (string) $notification->toSms($notifiable);

        $result = $this->gateway->send($notifiable->phone_number, $message, 'sms');

        NotificationLog::create([
            'notifiable_type' => $notifiable->getMorphClass(),
            'notifiable_id' => $notifiable->getKey(),
            'channel' => 'sms',
            'notification_type' => get_class($notification),
            'subject' => mb_substr($message, 0, 120),
            'body' => $message,
            'status' => $result['success'] ? 'sent' : 'failed',
            'gateway_message_id' => $result['message_id'] ?? null,
            'sent_at' => now(),
        ]);
    }
}
