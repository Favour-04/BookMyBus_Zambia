<?php

namespace App\Services\Messaging;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AfricasTalkingGateway implements MessagingGatewayInterface
{
    /**
     * Send via Africa's Talking. Only used when MESSAGING_DRIVER=africas_talking.
     * Requires AT_USERNAME and AT_API_KEY in .env.
     */
    public function send(string $to, string $message, string $channel = 'sms'): array
    {
        $username = config('messaging.at_username');
        $apiKey = config('messaging.at_api_key');

        if (! $username || ! $apiKey) {
            Log::error("[Messaging] Africa's Talking credentials missing for {$channel}.");

            return [
                'success' => false,
                'message_id' => null,
                'message' => "Africa's Talking credentials not configured.",
                'channel' => $channel,
            ];
        }

        try {
            $endpoint = $channel === 'whatsapp'
                ? config('messaging.at_whatsapp_endpoint')
                : config('messaging.at_sms_endpoint');

            $response = Http::withToken($apiKey)->asForm()->post($endpoint, array_filter([
                'username' => $username,
                'to' => $this->normalizePhone($to),
                'message' => $message,
                'from' => $channel === 'whatsapp' ? null : config('messaging.at_sender_id'),
            ], fn ($value) => ! is_null($value)));

            if ($response->successful()) {
                $body = $response->json();

                return [
                    'success' => true,
                    'message_id' => $body['SMSMessageData']['Recipients'][0]['messageId']
                        ?? ($body['messageId'] ?? null),
                    'message' => "Message sent via Africa's Talking.",
                    'channel' => $channel,
                ];
            }

            return [
                'success' => false,
                'message_id' => null,
                'message' => "Africa's Talking error: " . $response->body(),
                'channel' => $channel,
            ];
        } catch (\Throwable $e) {
            Log::error("[Messaging] Africa's Talking {$channel} send failed: " . $e->getMessage());

            return [
                'success' => false,
                'message_id' => null,
                'message' => 'Gateway exception: ' . $e->getMessage(),
                'channel' => $channel,
            ];
        }
    }

    /**
     * Convert a local Zambian number (0967...) to international (260967...).
     */
    protected function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/[^0-9]/', '', $phone);

        if (str_starts_with($digits, '0')) {
            return '260' . substr($digits, 1);
        }

        return $digits;
    }
}
