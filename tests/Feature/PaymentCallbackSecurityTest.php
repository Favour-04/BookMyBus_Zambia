<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Bus;
use App\Models\Operator;
use App\Models\Payment;
use App\Models\Route;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentCallbackSecurityTest extends TestCase
{
    use RefreshDatabase;

    private const SECRET = 'testing-callback-secret';

    private function createPendingBookingWithPayment(): array
    {
        $operator = Operator::create([
            'company_name' => 'Test Operator',
            'email' => 'operator@example.com',
            'phone_number' => '0977000001',
            'password' => bcrypt('password123'),
            'is_verified' => true,
            'verified_at' => now(),
        ]);

        $bus = Bus::create([
            'operator_id' => $operator->id,
            'registration_number' => 'BAZ 1234',
            'seat_capacity' => 49,
            'bus_class' => 'economy',
            'is_active' => true,
        ]);

        $route = Route::create([
            'operator_id' => $operator->id,
            'bus_id' => $bus->id,
            'origin' => 'Lusaka',
            'destination' => 'Ndola',
            'travel_date' => now()->addDays(3)->toDateString(),
            'departure_time' => '08:00',
            'arrival_time' => '12:00',
            'fare' => 250.00,
            'is_active' => true,
        ]);

        $user = User::create([
            'full_name' => 'Test Traveler',
            'email' => 'traveler@example.com',
            'phone_number' => '0977000000',
            'password' => bcrypt('password123'),
            'role' => 'traveler',
        ]);

        $booking = Booking::create([
            'user_id' => $user->id,
            'route_id' => $route->id,
            'seat_number' => 1,
            'passenger_name' => 'Test Traveler',
            'passenger_id_number' => '1234567890',
            'passenger_phone' => '0961234567',
            'amount' => 250.00,
            'base_fare' => 250.00,
            'service_fee_total' => 0,
            'discount_amount' => 0,
            'status' => 'pending',
        ]);

        $payment = Payment::create([
            'booking_id' => $booking->id,
            'amount' => $booking->amount,
            'currency' => 'ZMW',
            'payment_method' => 'mtn_money',
            'status' => 'pending',
        ]);

        return [$booking, $payment];
    }

    public function test_callback_without_signature_is_rejected(): void
    {
        [, $payment] = $this->createPendingBookingWithPayment();

        $response = $this->postJson('/api/payments/callback', [
            'payment_id' => $payment->id,
            'transaction_reference' => 'TXN-123',
            'status' => 'successful',
        ]);

        $response->assertStatus(401);
        $this->assertSame('pending', $payment->fresh()->status);
    }

    public function test_callback_with_invalid_signature_is_rejected(): void
    {
        [$booking, $payment] = $this->createPendingBookingWithPayment();

        $data = [
            'payment_id' => $payment->id,
            'transaction_reference' => 'TXN-123',
            'status' => 'successful',
        ];

        // Signed with the wrong secret — must be rejected.
        $body = json_encode($data);

        $response = $this->postJson('/api/payments/callback', $data, [
            'X-Gateway-Signature' => hash_hmac('sha256', $body, 'attacker-secret'),
        ]);

        $response->assertStatus(401);
        $this->assertSame('pending', $payment->fresh()->status);
        $this->assertSame('pending', $booking->fresh()->status);
        $this->assertNull(Ticket::where('booking_id', $booking->id)->first());
    }

    public function test_callback_with_valid_signature_confirms_payment_and_issues_ticket(): void
    {
        [$booking, $payment] = $this->createPendingBookingWithPayment();

        $data = [
            'payment_id' => $payment->id,
            'transaction_reference' => 'TXN-123',
            'status' => 'successful',
        ];

        $body = json_encode($data);

        $response = $this->postJson('/api/payments/callback', $data, [
            'X-Gateway-Signature' => hash_hmac('sha256', $body, self::SECRET),
        ]);

        $response->assertStatus(200);
        $this->assertSame('successful', $payment->fresh()->status);
        $this->assertSame('confirmed', $booking->fresh()->status);
        $this->assertNotNull(Ticket::where('booking_id', $booking->id)->first());
    }

    public function test_successful_callback_is_idempotent(): void
    {
        [$booking, $payment] = $this->createPendingBookingWithPayment();

        $data = [
            'payment_id' => $payment->id,
            'transaction_reference' => 'TXN-123',
            'status' => 'successful',
        ];

        $body = json_encode($data);
        $headers = ['X-Gateway-Signature' => hash_hmac('sha256', $body, self::SECRET)];

        $this->postJson('/api/payments/callback', $data, $headers);
        $this->postJson('/api/payments/callback', $data, $headers);

        $this->assertSame('successful', $payment->fresh()->status);
        $this->assertCount(1, Ticket::where('booking_id', $booking->id)->get());
    }

    public function test_callback_when_config_missing_returns_503(): void
    {
        [, $payment] = $this->createPendingBookingWithPayment();

        config(['services.payment_callback.secret' => null]);

        $response = $this->postJson('/api/payments/callback', [
            'payment_id' => $payment->id,
            'transaction_reference' => 'TXN-123',
            'status' => 'successful',
        ]);

        $response->assertStatus(503);
        $this->assertSame('pending', $payment->fresh()->status);
    }
}
