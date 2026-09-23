<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Ticket;
use App\Notifications\TicketIssued;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    // Initiate a payment for a pending booking via the configured mobile-money
    // gateway (real MTN MoMo when MOMO_* credentials are set, simulated
    // otherwise) — see Services\PaymentService.

    public function initiate(Request $request): JsonResponse
    {
        $data = $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'payment_method' => 'required|in:mtn_money,airtel_money,zanaco,card',
            'phone_number' => 'required_if:payment_method,mtn_money,airtel_money|string|nullable',
        ]);

        $booking = Booking::with('route')
            ->where('id', $data['booking_id'])
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        if ($booking->isExpired()) {
            $booking->update(['status' => 'expired']);

            return response()->json(['message' => 'Seat hold has expired. Please book again.'], 422);
        }

        if ($booking->status !== 'pending') {
            return response()->json(['message' => 'Booking is not in a payable state.'], 422);
        }

        // Prevent duplicate payment records
        if ($booking->payment) {
            return response()->json(['message' => 'Payment already initiated for this booking.'], 409);
        }

        // Bank/card channels have no gateway integration yet — return an
        // honest 501 rather than creating a pending payment that could
        // never resolve.
        $provider = match ($data['payment_method']) {
            'mtn_money' => 'mtn',
            'airtel_money' => 'airtel',
            default => null,
        };

        if ($provider === null) {
            return response()->json([
                'message' => ucfirst(str_replace('_', ' ', $data['payment_method'])).' payments are not yet supported. Please use mobile money.',
            ], 501);
        }

        $result = (new PaymentService)->processMobileMoney(
            $booking,
            $provider,
            (string) ($data['phone_number'] ?? '')
        );

        if (! $result['success'] || ! $result['payment']) {
            return response()->json(['message' => $result['message']], 402);
        }

        $payment = $result['payment'];

        return response()->json([
            'message' => $result['message'],
            'payment_id' => $payment->id,
            'amount' => $payment->amount,
            'currency' => $payment->currency,
            'status' => $payment->status,
            'booking_status' => $booking->fresh()->status,
        ], 201);
    }

    // Callback endpoint hit by the payment gateway after transaction.
    // Confirms or fails the payment and issues a ticket on success.
    //
    // Security: the request must carry an HMAC-SHA256 signature of the raw
    // request body in the X-Gateway-Signature header, keyed with the shared
    // PAYMENT_CALLBACK_SECRET. Without a valid signature the callback is
    // rejected before any database lookup, so a caller who merely knows a
    // payment_id cannot confirm their own payment and receive a free ticket.
    public function callback(Request $request): JsonResponse
    {
        $secret = config('services.payment_callback.secret');

        if (! $secret) {
            // Misconfiguration: never silently accept unsigned callbacks.
            return response()->json(['message' => 'Payment gateway callback is not configured.'], 503);
        }

        $signature = $request->header('X-Gateway-Signature', '');

        $expected = hash_hmac('sha256', $request->getContent(), $secret);

        if (! is_string($signature) || ! hash_equals($expected, $signature)) {
            return response()->json(['message' => 'Invalid or missing gateway signature.'], 401);
        }

        $data = $request->validate([
            'payment_id' => 'required|exists:payments,id',
            'transaction_reference' => 'required|string',
            'status' => 'required|in:successful,failed',
            'gateway_response' => 'nullable|array',
        ]);

        $payment = Payment::with('booking')->findOrFail($data['payment_id']);

        if ($payment->status !== 'pending') {
            return response()->json(['message' => 'Payment already processed.'], 409);
        }

        $ticket = null;

        DB::transaction(function () use ($payment, $data, &$ticket) {
            if ($data['status'] === 'successful') {
                $payment->markSuccessful(
                    $data['transaction_reference'],
                    $data['gateway_response'] ?? []
                );

                // Issue digital ticket
                $ticket = Ticket::create([
                    'booking_id' => $payment->booking->id,
                    'user_id' => $payment->booking->user_id,
                ]);
            } else {
                $payment->markFailed($data['gateway_response'] ?? []);
            }
        });

        // Notify the traveler that their ticket has been issued.
        if ($ticket && $payment->booking->user) {
            $payment->booking->user->notify(new TicketIssued($payment->booking, $ticket));
        }

        return response()->json([
            'message' => $data['status'] === 'successful'
                ? 'Payment confirmed. Ticket issued.'
                : 'Payment failed.',
        ]);
    }

    // Get payment status for a booking.

    public function status(Request $request, int $bookingId): JsonResponse
    {
        $booking = Booking::where('id', $bookingId)
            ->where('user_id', $request->user()->id)
            ->with('payment')
            ->firstOrFail();

        return response()->json([
            'booking_status' => $booking->status,
            'payment' => $booking->payment,
        ]);
    }
}
