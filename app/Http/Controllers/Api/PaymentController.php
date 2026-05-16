<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    /**
     * Initiate a payment for a pending booking.
     * In production this would call the MTN/Airtel API.
     */
    public function initiate(Request $request): JsonResponse
    {
        $data = $request->validate([
            'booking_id'     => 'required|exists:bookings,id',
            'payment_method' => 'required|in:mtn_money,airtel_money,zanaco,card',
            'phone_number'   => 'required_if:payment_method,mtn_money,airtel_money|string|nullable',
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

        $payment = Payment::create([
            'booking_id'     => $booking->id,
            'amount'         => $booking->route->fare,
            'currency'       => 'ZMW',
            'payment_method' => $data['payment_method'],
            'status'         => 'pending',
        ]);

        // TODO: Call real MTN/Airtel API here and return their payment URL or prompt
        // For now we return the payment record so the frontend can poll or await callback

        return response()->json([
            'message'    => 'Payment initiated. Awaiting confirmation from ' . $data['payment_method'] . '.',
            'payment_id' => $payment->id,
            'amount'     => $payment->amount,
            'currency'   => $payment->currency,
        ], 201);
    }

    /**
     * Callback endpoint hit by the payment gateway after transaction.
     * Confirms or fails the payment and issues a ticket on success.
     */
    public function callback(Request $request): JsonResponse
    {
        $data = $request->validate([
            'payment_id'            => 'required|exists:payments,id',
            'transaction_reference' => 'required|string',
            'status'                => 'required|in:successful,failed',
            'gateway_response'      => 'nullable|array',
        ]);

        $payment = Payment::with('booking')->findOrFail($data['payment_id']);

        if ($payment->status !== 'pending') {
            return response()->json(['message' => 'Payment already processed.'], 409);
        }

        DB::transaction(function () use ($payment, $data) {
            if ($data['status'] === 'successful') {
                $payment->markSuccessful(
                    $data['transaction_reference'],
                    $data['gateway_response'] ?? []
                );

                // Issue digital ticket
                Ticket::create([
                    'booking_id' => $payment->booking->id,
                    'user_id'    => $payment->booking->user_id,
                ]);
            } else {
                $payment->markFailed($data['gateway_response'] ?? []);
            }
        });

        return response()->json([
            'message' => $data['status'] === 'successful'
                ? 'Payment confirmed. Ticket issued.'
                : 'Payment failed.',
        ]);
    }

    /**
     * Get payment status for a booking.
     */
    public function status(Request $request, int $bookingId): JsonResponse
    {
        $booking = Booking::where('id', $bookingId)
            ->where('user_id', $request->user()->id)
            ->with('payment')
            ->firstOrFail();

        return response()->json([
            'booking_status' => $booking->status,
            'payment'        => $booking->payment,
        ]);
    }
}
