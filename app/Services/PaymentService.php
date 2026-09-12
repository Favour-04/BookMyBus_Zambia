<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\Ticket;
use App\Notifications\TicketIssued;
use App\Services\MobileMoney\GatewayInterface;
use App\Services\MobileMoney\SimulatedGateway;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

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
            $gateway = $this->resolveGateway($provider);
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
            // Confirm the payment and issue the digital ticket atomically.
            // Mirrors Api\PaymentController@callback so the web and API flows
            // stay consistent.
            DB::transaction(function () use ($payment, $booking, $result) {
                // Mark payment as successful — this also confirms the booking
                $payment->markSuccessful(
                    $result['transaction_reference'],
                    $result['gateway_response']
                );

                // Issue a digital ticket (idempotent guard in case the service
                // is ever invoked more than once for the same booking).
                if (! Ticket::where('booking_id', $booking->id)->exists()) {
                    Ticket::create([
                        'booking_id' => $booking->id,
                        'user_id'    => $booking->user_id,
                    ]);
                }
            });

            // Notify the traveler that their ticket has been issued.
            $freshBooking = $booking->fresh();
            if ($freshBooking->ticket && $freshBooking->user) {
                $freshBooking->user->notify(new TicketIssued($freshBooking, $freshBooking->ticket));
            }

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

    /**
     * Process a mobile money payment covering several bookings made together
     * (e.g. a traveler booking 3 seats in one submission, sharing a
     * group_reference). Charges the gateway once for the combined total,
     * ties the Payment record to the first booking, and — on success —
     * confirms and issues a ticket for every booking in the group, notifying
     * each one individually just like the single-booking flow does.
     *
     * @param Collection<int, Booking> $bookings    All bookings in the party (same group_reference)
     * @param string                   $provider    The payment provider key ('mtn' or 'airtel')
     * @param string                   $phoneNumber The customer's phone number
     *
     * @return array{
     *   success: bool,
     *   payment: Payment|null,
     *   message: string,
     * }
     */
    public function processMobileMoneyForGroup(Collection $bookings, string $provider, string $phoneNumber): array
    {
        $primaryBooking = $bookings->first();
        $totalAmount = $bookings->sum('amount');

        // Normalize phone number: strip non-digits
        $phoneDigits = preg_replace('/[^0-9]/', '', $phoneNumber);

        try {
            $gateway = $this->resolveGateway($provider);
        } catch (\InvalidArgumentException $e) {
            return [
                'success' => false,
                'payment' => null,
                'message' => $e->getMessage(),
            ];
        }

        if (!$gateway->validatePhoneNumber($phoneDigits)) {
            return [
                'success' => false,
                'payment' => null,
                'message' => "Invalid phone number for {$gateway->getProviderName()}. " .
                             "Please enter a valid {$gateway->getProviderName()} number.",
            ];
        }

        // Save the phone number against every booking in the party
        $bookings->each(fn (Booking $b) => $b->update(['phone_number' => $phoneDigits]));

        // One Payment record, tied to the first booking, covering the whole group's total
        $payment = Payment::create([
            'booking_id'             => $primaryBooking->id,
            'amount'                 => $totalAmount,
            'currency'               => 'ZMW',
            'payment_method'         => $gateway->getProviderLabel(),
            'status'                 => 'pending',
            'transaction_reference'  => null,
            'gateway_response'       => null,
            'paid_at'                => null,
        ]);

        $result = $gateway->charge($phoneDigits, (float) $totalAmount, $primaryBooking->reference_id);

        if ($result['success']) {
            DB::transaction(function () use ($payment, $bookings, $result) {
                // Confirms $payment->booking (the primary booking only)
                $payment->markSuccessful(
                    $result['transaction_reference'],
                    $result['gateway_response']
                );

                // Confirm every booking in the party and issue each its own ticket
                foreach ($bookings as $b) {
                    if ($b->status !== 'confirmed') {
                        $b->update(['status' => 'confirmed']);
                    }

                    if (! Ticket::where('booking_id', $b->id)->exists()) {
                        Ticket::create([
                            'booking_id' => $b->id,
                            'user_id'    => $b->user_id,
                        ]);
                    }
                }
            });

            // Notify each passenger once their ticket is issued
            foreach ($bookings as $b) {
                $freshBooking = $b->fresh();
                if ($freshBooking->ticket && $freshBooking->user) {
                    $freshBooking->user->notify(new TicketIssued($freshBooking, $freshBooking->ticket));
                }
            }

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

    /**
     * Resolve the payment gateway for the given provider.
     *
     * Extracted as a protected seam so tests can substitute a deterministic
     * gateway without exercising the real SimulatedGateway (which relies on
     * rand + usleep and would make tests slow and flaky).
     */
    protected function resolveGateway(string $provider): GatewayInterface
    {
        return SimulatedGateway::forProvider($provider);
    }
}