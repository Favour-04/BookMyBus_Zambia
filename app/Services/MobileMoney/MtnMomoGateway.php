<?php

namespace App\Services\MobileMoney;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

/**
 * MTN MoMo Collections API gateway (https://momodeveloper.mtn.com).
 *
 * Works against both the sandbox and production base URLs — the only
 * differences are MOMO_BASE_URL, MOMO_TARGET_ENV and the currency (the
 * sandbox settles in EUR; production Zambia uses ZMW).
 *
 * The MoMo API is asynchronous: requesttopay returns 202 Accepted with an
 * X-Reference-Id that identifies the transaction, and the final status
 * arrives later via callback or by polling the transaction endpoint. The
 * GatewayInterface contract is synchronous, so charge() initiates the
 * request-to-pay and then polls the transaction status until it resolves
 * (SUCCESSFUL/FAILED) or the poll budget is exhausted.
 *
 * The access token is cached (valid ~1 hour) under a key that includes the
 * API user, so switching credentials invalidates old tokens.
 */
class MtnMomoGateway implements GatewayInterface
{
    private string $baseUrl;

    private string $subscriptionKey;

    private string $apiUser;

    private string $apiKey;

    private string $callbackHost;

    private string $targetEnvironment;

    private string $currency;

    /** Poll attempts and delay (seconds) while waiting for wallet approval. */
    private int $pollAttempts = 8;

    private int $pollDelaySeconds = 2;

    public function __construct(?array $config = null)
    {
        $config = $config ?? (array) config('services.momo');

        $this->baseUrl = rtrim((string) ($config['base_url'] ?? ''), '/');
        $this->subscriptionKey = (string) ($config['subscription_key'] ?? '');
        $this->apiUser = (string) ($config['api_user'] ?? '');
        $this->apiKey = (string) ($config['api_key'] ?? '');
        $this->callbackHost = (string) ($config['callback_host'] ?? '');
        $this->targetEnvironment = (string) ($config['target_environment'] ?? 'sandbox');
        $this->currency = (string) ($config['currency'] ?? 'EUR');
    }

    /** Convenience check used by PaymentService::resolveGateway(). */
    public static function isConfigured(): bool
    {
        $config = (array) config('services.momo');

        return ! empty($config['subscription_key'])
            && ! empty($config['api_user'])
            && ! empty($config['api_key']);
    }

    /**
     * Zambia MTN numbers: 096/076 local, 26096/26076 international.
     */
    public function validatePhoneNumber(string $phoneNumber): bool
    {
        $digits = $this->toInternationalMsisdn($phoneNumber);

        return (bool) preg_match('/^260(96|76|75|78)\d{7}$/', $digits);
    }

    public function getProviderName(): string
    {
        return 'MTN MoMo';
    }

    public function getProviderLabel(): string
    {
        return 'mtn_money';
    }

    /**
     * Initiate a request-to-pay and poll until the transaction resolves.
     *
     * {@inheritdoc}
     */
    public function charge(string $phoneNumber, float $amount, string $reference): array
    {
        try {
            $token = $this->getAccessToken();
        } catch (Throwable $e) {
            Log::error('MTN MoMo: token request failed', ['error' => $e->getMessage()]);

            return $this->failure('Could not reach MTN MoMo to start the payment. Please try again.', []);
        }

        $payerMsisdn = $this->toInternationalMsisdn($phoneNumber);

        // The X-Reference-Id we generate here is the transaction's permanent
        // identifier — it becomes our transaction_reference.
        $transactionId = (string) Str::uuid();

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$token,
                'X-Reference-Id' => $transactionId,
                'X-Target-Environment' => $this->targetEnvironment,
                'Ocp-Apim-Subscription-Key' => $this->subscriptionKey,
                'X-Callback-Url' => $this->buildCallbackUrl(),
            ])->post("{$this->baseUrl}/collection/v1_0/requesttopay", [
                'amount' => number_format($amount, 2, '.', ''),
                'currency' => $this->currency,
                'externalId' => $reference,
                'payer' => [
                    'partyIdType' => 'MSISDN',
                    'partyId' => $payerMsisdn,
                ],
                'payerMessage' => "BookMyBus ticket {$reference}",
                'payeeNote' => "BookMyBus ticket {$reference}",
            ]);
        } catch (Throwable $e) {
            Log::error('MTN MoMo: requesttopay failed', ['error' => $e->getMessage()]);

            return $this->failure('Could not submit the payment request to MTN MoMo. Please try again.', []);
        }

        if ($response->status() !== 202) {
            Log::warning('MTN MoMo: requesttopay rejected', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return $this->failure(
                'MTN MoMo rejected the payment request ('.$response->status().'). Please try again or use another payment method.',
                $response->json() ?? ['raw' => $response->body()]
            );
        }

        // 202 Accepted — poll the transaction until it resolves.
        for ($attempt = 0; $attempt < $this->pollAttempts; $attempt++) {
            sleep($this->pollDelaySeconds);

            $status = $this->fetchTransactionStatus($token, $transactionId);

            if ($status === 'SUCCESSFUL') {
                return [
                    'success' => true,
                    'transaction_reference' => $transactionId,
                    'message' => "Payment of {$this->currency} {$amount} via MTN MoMo was successful.",
                    'gateway_response' => [
                        'provider' => 'MTN MoMo',
                        'environment' => $this->targetEnvironment,
                        'transaction_id' => $transactionId,
                        'reference' => $reference,
                        'payer' => $payerMsisdn,
                        'amount' => $amount,
                        'currency' => $this->currency,
                        'status' => 'SUCCESSFUL',
                        'completed_at' => now()->toIso8601String(),
                    ],
                ];
            }

            if ($status === 'FAILED') {
                return $this->failure(
                    'The MTN MoMo payment was rejected or declined. Please try again.',
                    [
                        'provider' => 'MTN MoMo',
                        'environment' => $this->targetEnvironment,
                        'transaction_id' => $transactionId,
                        'reference' => $reference,
                        'status' => 'FAILED',
                    ]
                );
            }

            // PENDING (or unknown) — keep polling.
        }

        return $this->failure(
            'The MTN MoMo payment is still pending approval on your phone. No money was taken yet — please retry or check your approval prompt.',
            [
                'provider' => 'MTN MoMo',
                'environment' => $this->targetEnvironment,
                'transaction_id' => $transactionId,
                'reference' => $reference,
                'status' => 'PENDING',
            ]
        );
    }

    /**
     * Fetch the transaction status via
     * GET /collection/v1_0/requesttopay/{referenceId}. Returns the raw status
     * string (PENDING / SUCCESSFUL / FAILED) or null on transport errors.
     */
    private function fetchTransactionStatus(string $token, string $transactionId): ?string
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$token,
                'X-Target-Environment' => $this->targetEnvironment,
                'Ocp-Apim-Subscription-Key' => $this->subscriptionKey,
            ])->get("{$this->baseUrl}/collection/v1_0/requesttopay/{$transactionId}");
        } catch (Throwable $e) {
            Log::error('MTN MoMo: status poll failed', ['error' => $e->getMessage()]);

            return null;
        }

        if ($response->status() !== 200) {
            return null;
        }

        return strtoupper((string) ($response->json('status') ?? ''));
    }

    /**
     * Get a bearer token, cached for its useful lifetime. MTN tokens last
     * ~1 hour; we cache for 5 minutes to stay well clear of expiry while
     * avoiding a token round-trip on every charge.
     */
    private function getAccessToken(): string
    {
        $cacheKey = 'momo_token_'.md5($this->apiUser.$this->baseUrl);

        $cached = Cache::get($cacheKey);

        if (is_string($cached) && $cached !== '') {
            return $cached;
        }

        // The token endpoint requires a Content-Length header; an empty POST
        // body without one returns HTTP 411 from MTN's WAF, so send "{}".
        $response = Http::withHeaders([
            'Ocp-Apim-Subscription-Key' => $this->subscriptionKey,
            'Content-Type' => 'application/json',
        ])->withBasicAuth($this->apiUser, $this->apiKey)
            ->withBody('{}', 'application/json')
            ->post("{$this->baseUrl}/collection/token/");

        if ($response->status() !== 200) {
            throw new \RuntimeException(
                "MTN MoMo token request returned HTTP {$response->status()}: ".$response->body()
            );
        }

        $token = (string) $response->json('access_token');

        if ($token === '') {
            throw new \RuntimeException('MTN MoMo token response contained no access_token.');
        }

        Cache::put($cacheKey, $token, now()->addMinutes(5));

        return $token;
    }

    /**
     * Normalize a Zambian phone number to MSISDN format (260XXXXXXXXX):
     * "0961234567" → "260961234567", "961234567" → "260961234567",
     * "260961234567" → unchanged. Strips any non-digits first.
     */
    private function toInternationalMsisdn(string $phoneNumber): string
    {
        $digits = preg_replace('/[^0-9]/', '', $phoneNumber) ?? '';

        if (str_starts_with($digits, '260')) {
            return $digits;
        }

        if (str_starts_with($digits, '0')) {
            return '260'.substr($digits, 1);
        }

        // 9-digit local form, e.g. 961234567
        if (strlen($digits) === 9) {
            return '260'.$digits;
        }

        return $digits;
    }

    private function failure(string $message, array $gatewayResponse): array
    {
        return [
            'success' => false,
            'transaction_reference' => null,
            'message' => $message,
            'gateway_response' => $gatewayResponse,
        ];
    }

    /**
     * MTN expects a full URL (scheme + host + path). MOMO_CALLBACK_HOST may be
     * just a hostname (e.g. "example.com" or an ngrok host) — build the full
     * callback endpoint from it, or use it as-is when already a complete URL
     * pointing at our secured callback endpoint.
     */
    private function buildCallbackUrl(): ?string
    {
        if ($this->callbackHost === '') {
            return null;
        }

        if (str_starts_with($this->callbackHost, 'http')) {
            return $this->callbackHost;
        }

        return 'https://'.ltrim($this->callbackHost, '/').'/api/payments/callback';
    }
}
