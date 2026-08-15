<?php

namespace App\Services\Messaging;

interface MessagingGatewayInterface
{
    /**
     * Send a message to a recipient via the given channel.
     *
     * @param string $to      Recipient phone number (e.g. "0961234567")
     * @param string $message The message body
     * @param string $channel 'sms' or 'whatsapp'
     *
     * @return array{success: bool, message_id: ?string, message: string, channel: string}
     */
    public function send(string $to, string $message, string $channel = 'sms'): array;
}
