<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Payment;
use App\Services\MobileMoney\SimulatedGateway;

class PaymentService
{
    /**
     * Process a mobile money payment for a booking.
     *
     * @param Booking $booking       The booking to process payment for
     * @param string  $provider      The payment provider key ('mtn' or 'airtel')
     * @param string  $phoneNumber   The customer's phone number
     *
     * @return array{
     *   success: bool,
     *   payment: Payment|null,
     *   message: string,
     * }
     */
    public function processMobileMoney(Booking $booking, string $provider, string $phoneNumber): array
    {
        // Normalize phone number: strip non-digits
        $phoneDigits = preg_replace('/[^0-9]/', '', $phoneNumber);

        // Create the gateway instance for the selected provider
        try {
            $gateway = SimulatedGateway::forProvider($provider);
        } catch (\InvalidArgumentException $e) {
            return [
                'success' => false,
                'payment' => null,
                'message' => $e->getMessage(),
            ];
        }

        // Validate phone number format for this provider
        if (!$gateway->validatePhoneNumber($phoneDigits)) {
            return [
                'success' => false,
                'payment' => null,
                'message' => "Invalid phone number for {$gateway->getProviderName()}. " .
                             "Please enter a valid {$gateway->getProviderName()} number.",
            ];
        }

        // Save the phone number to the booking
        $booking->update(['phone_number' => $phoneDigits]);

        // Create a pending payment record
        $payment = Payment::create([
            'booking_id'          => $booking->id,
            'amount'              => $booking->amount,
            'currency'            => 'ZMW',
            'payment_method'      => $gateway->getProviderLabel(),
            'status'              => 'pending',
            'transaction_reference' => null,
            'gateway_response'    => null,
            'paid_at'             => null,
        ]);

        // Call the gateway to process the charge
        $result = $gateway->charge($phoneDigits, (float) $booking->amount, $booking->reference_id);

        if ($result['success']) {
            // Mark payment as successful — this also confirms the booking
            $payment->markSuccessful(
                $result['transaction_reference'],
                $result['gateway_response']
            );

            return [
                'success' => true,
                'payment' => $payment->fresh(),
                'message' => $result['message'],
            ];
        }

        // Mark payment as failed
        $payment->markFailed($result['gateway_response']);

        return [
            'success' => false,
            'payment' => $payment->fresh(),
            'message' => $result['message'],
        ];
    }
}