<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Bus;
use App\Models\Operator;
use App\Models\Payment;
use App\Models\Route;
use App\Models\Ticket;
use App\Models\User;
use App\Services\MobileMoney\MtnMomoGateway;
use App\Services\MobileMoney\SimulatedGateway;
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiPaymentInitiateTest extends TestCase
{
    use RefreshDatabase;

    private function createPendingBooking(): Booking
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

        return Booking::create([
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
    }

    public function test_api_initiate_charges_and_confirms_booking(): void
    {
        // No MoMo credentials in the test env (phpunit.xml blanks them), so
        // PaymentService falls back to the SimulatedGateway, which is
        // deterministic outside 'local'.
        $this->assertFalse(MtnMomoGateway::isConfigured());

        $booking = $this->createPendingBooking();

        $response = $this->actingAs($booking->user, 'sanctum')
            ->postJson('/api/payments/initiate', [
                'booking_id' => $booking->id,
                'payment_method' => 'mtn_money',
                'phone_number' => '0961234567',
            ]);

        $response->assertStatus(201);
        $response->assertJsonStructure(['message', 'payment_id', 'amount', 'currency', 'status', 'booking_status']);

        $this->assertSame('confirmed', $booking->fresh()->status);

        $payment = Payment::where('booking_id', $booking->id)->first();
        $this->assertNotNull($payment);
        $this->assertSame('successful', $payment->status);

        $this->assertNotNull(Ticket::where('booking_id', $booking->id)->first());
    }

    public function test_api_initiate_rejects_unsupported_card_payment(): void
    {
        $booking = $this->createPendingBooking();

        $response = $this->actingAs($booking->user, 'sanctum')
            ->postJson('/api/payments/initiate', [
                'booking_id' => $booking->id,
                'payment_method' => 'card',
            ]);

        $response->assertStatus(501);
        $this->assertSame('pending', $booking->fresh()->status);
        $this->assertNull(Payment::where('booking_id', $booking->id)->first());
    }

    public function test_api_initiate_rejects_duplicate_payment(): void
    {
        $booking = $this->createPendingBooking();

        $payload = [
            'booking_id' => $booking->id,
            'payment_method' => 'mtn_money',
            'phone_number' => '0961234567',
        ];

        $this->actingAs($booking->user, 'sanctum')->postJson('/api/payments/initiate', $payload);

        // First call confirmed the booking, so the second hits the
        // not-in-a-payable-state guard (422) — either way, no 2xx.
        $second = $this->actingAs($booking->user, 'sanctum')
            ->postJson('/api/payments/initiate', $payload);

        $second->assertStatus(422);

        $this->assertSame(1, Payment::where('booking_id', $booking->id)->count());
        $this->assertSame(1, Ticket::where('booking_id', $booking->id)->count());
    }

    public function test_api_initiate_requires_auth(): void
    {
        $booking = $this->createPendingBooking();

        $this->postJson('/api/payments/initiate', [
            'booking_id' => $booking->id,
            'payment_method' => 'mtn_money',
            'phone_number' => '0961234567',
        ])->assertStatus(401);
    }
}
