<?php

namespace App\Services\MobileMoney;

interface GatewayInterface
{
    /**
     * Process a mobile money payment.
     *
     * @param string $phoneNumber  The customer's phone number (e.g. "0961234567")
     * @param float  $amount       The amount to charge in ZMW
     * @param string $reference    The booking reference ID for the transaction description
     *
     * @return array{
     *   success: bool,
     *   transaction_reference: string|null,
     *   message: string,
     *   gateway_response: array,
     * }
     */
    public function charge(string $phoneNumber, float $amount, string $reference): array;

    /**
     * Get the display name of this gateway (e.g. "MTN MoMo", "Airtel Money").
     */
    public function getProviderName(): string;

    /**
     * Validate that the phone number matches this provider's prefix pattern.
     */
    public function validatePhoneNumber(string $phoneNumber): bool;
}