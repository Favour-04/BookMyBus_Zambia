<?php

namespace App\Services\MobileMoney;

class SimulatedGateway implements GatewayInterface
{
    /**
     * The provider prefix patterns and their display names.
     */
    protected array $providers = [
        'mtn' => [
            'prefixes' => ['096', '076'],
            'name' => 'MTN MoMo',
            'label' => 'mtn_money',
        ],
        'airtel' => [
            'prefixes' => ['097', '077'],
            'name' => 'Airtel Money',
            'label' => 'airtel_money',
        ],
    ];

    /**
     * The provider key to use (mtn or airtel).
     */
    protected string $providerKey;

    public function __construct(string $providerKey = 'mtn')
    {
        $this->providerKey = $providerKey;
    }

    /**
     * Simulate charging a mobile money wallet.
     *
     * In local dev: realistic behavior — 1-3s latency and a ~10% random
     * decline for testing edge cases. Outside local (staging/production):
     * deterministic success with no delay, so real users are never randomly
     * declined by a coin flip while Airtel integration is pending.
     */
    public function charge(string $phoneNumber, float $amount, string $reference): array
    {
        $isLocal = app()->environment('local');

        if ($isLocal) {
            // Simulate network latency (1-3 seconds)
            usleep(rand(1_000_000, 3_000_000));
        }

        // Validate the phone number for this provider
        if (! $this->validatePhoneNumber($phoneNumber)) {
            return [
                'success' => false,
                'transaction_reference' => null,
                'message' => "Invalid phone number for {$this->getProviderName()}. Please use a valid number.",
                'gateway_response' => [
                    'provider' => $this->getProviderName(),
                    'phone' => $phoneNumber,
                    'error_code' => 'INVALID_PHONE',
                    'reference' => $reference,
                    'timestamp' => now()->toIso8601String(),
                ],
            ];
        }

        // Random ~10% decline only in local dev; otherwise always succeed.
        $isSuccess = ! $isLocal || rand(1, 100) <= 90;

        if ($isSuccess) {
            $txnRef = 'TXN-'.strtoupper(substr(md5(uniqid()), 0, 12));

            return [
                'success' => true,
                'transaction_reference' => $txnRef,
                'message' => "Payment of ZMW {$amount} via {$this->getProviderName()} was successful.",
                'gateway_response' => [
                    'provider' => $this->getProviderName(),
                    'phone' => $phoneNumber,
                    'amount' => $amount,
                    'currency' => 'ZMW',
                    'transaction_id' => $txnRef,
                    'reference' => $reference,
                    'status' => 'completed',
                    'timestamp' => now()->toIso8601String(),
                    'simulated' => true,
                ],
            ];
        }

        return [
            'success' => false,
            'transaction_reference' => null,
            'message' => "{$this->getProviderName()} payment declined. Please try again or use a different payment method.",
            'gateway_response' => [
                'provider' => $this->getProviderName(),
                'phone' => $phoneNumber,
                'amount' => $amount,
                'currency' => 'ZMW',
                'reference' => $reference,
                'status' => 'failed',
                'error_code' => 'TRANSACTION_DECLINED',
                'error_detail' => 'Insufficient funds or transaction declined by provider.',
                'timestamp' => now()->toIso8601String(),
                'simulated' => true,
            ],
        ];
    }

    public function getProviderName(): string
    {
        return $this->providers[$this->providerKey]['name'] ?? 'Unknown';
    }

    public function getProviderLabel(): string
    {
        return $this->providers[$this->providerKey]['label'] ?? $this->providerKey;
    }

    public function validatePhoneNumber(string $phoneNumber): bool
    {
        // Normalize: strip everything except digits
        $digits = preg_replace('/[^0-9]/', '', $phoneNumber);

        // Must be exactly 10 digits for Zambian mobile numbers
        if (strlen($digits) !== 10) {
            return false;
        }

        // Check that the prefix matches this provider
        foreach ($this->providers[$this->providerKey]['prefixes'] as $prefix) {
            if (str_starts_with($digits, $prefix)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Create the appropriate gateway instance based on the provider key.
     */
    public static function forProvider(string $providerKey): self
    {
        if (! in_array($providerKey, ['mtn', 'airtel'])) {
            throw new \InvalidArgumentException("Unsupported payment provider: {$providerKey}");
        }

        return new self($providerKey);
    }
}
