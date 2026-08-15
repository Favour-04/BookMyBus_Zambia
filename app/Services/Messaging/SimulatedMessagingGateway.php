<?php

namespace App\Services\Messaging;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SimulatedMessagingGateway implements MessagingGatewayInterface
{
    /**
     * Log the message and return a simulated success result.
     * No real SMS/WhatsApp is sent — safe for development and tests.
     */
    public function send(string $to, string $message, string $channel = 'sms'): array
    {
        $messageId = 'SIM-' . strtoupper(Str::random(10));

        Log::info("[Messaging][simulated][{$channel}] to={$to} id={$messageId}", [
            'to' => $to,
            'channel' => $channel,
            'message' => $message,
            'message_id' => $messageId,
        ]);

        return [
            'success' => true,
            'message_id' => $messageId,
            'message' => "Simulated {$channel} message logged.",
            'channel' => $channel,
        ];
    }
}
