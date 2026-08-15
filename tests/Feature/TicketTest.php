<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Bus;
use App\Models\Operator;
use App\Models\Route;
use App\Models\Ticket;
use App\Models\User;
use App\Services\MobileMoney\GatewayInterface;
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketTest extends TestCase
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

    public function test_ticket_is_issued_after_successful_payment(): void
    {
        $booking = $this->createPendingBooking();
        $service = new TestablePaymentService();

        $result = $service->processMobileMoney($booking, 'mtn', '0961234567');

        $this->assertTrue($result['success']);
        $this->assertSame('confirmed', $booking->fresh()->status);

        $ticket = Ticket::where('booking_id', $booking->id)->first();
        $this->assertNotNull($ticket, 'A ticket should be issued after successful payment.');
        $this->assertSame('issued', $ticket->status);
        $this->assertSame($booking->user_id, $ticket->user_id);
        $this->assertStringStartsWith('BMZ-QR-', $ticket->qr_code);
    }

    public function test_ticket_issuance_is_idempotent(): void
    {
        $booking = $this->createPendingBooking();
        $service = new TestablePaymentService();

        $service->processMobileMoney($booking, 'mtn', '0961234567');

        // Simulate a second invocation for the same booking (an edge retry) by
        // resetting the booking to pending again.
        $booking->update(['status' => 'pending']);
        $service->processMobileMoney($booking, 'mtn', '0961234567');

        $this->assertCount(1, Ticket::where('booking_id', $booking->id)->get());
    }

    public function test_ticket_page_is_viewable_by_owner(): void
    {
        $booking = $this->createPendingBooking();
        $booking->update(['status' => 'confirmed']);

        $ticket = Ticket::create([
            'booking_id' => $booking->id,
            'user_id' => $booking->user_id,
        ]);

        $response = $this->actingAs($booking->user)->get('/tickets/' . $ticket->qr_code);

        $response->assertStatus(200);
        $response->assertSee($ticket->qr_code, false);
        $response->assertSee($booking->passenger_name);
    }

    public function test_ticket_page_is_not_viewable_by_other_user(): void
    {
        $booking = $this->createPendingBooking();
        $booking->update(['status' => 'confirmed']);

        $ticket = Ticket::create([
            'booking_id' => $booking->id,
            'user_id' => $booking->user_id,
        ]);

        $other = User::create([
            'full_name' => 'Other User',
            'email' => 'other@example.com',
            'phone_number' => '0977999999',
            'password' => bcrypt('password123'),
            'role' => 'traveler',
        ]);

        $response = $this->actingAs($other)->get('/tickets/' . $ticket->qr_code);

        $response->assertStatus(404);
    }
}

/**
 * Always-succeeds fake gateway used to make PaymentService deterministic in tests.
 */
class FakeAlwaysSucceedsGateway implements GatewayInterface
{
    private static int $counter = 0;

    public function charge(string $phoneNumber, float $amount, string $reference): array
    {
        $txnRef = 'TXN-FAKE-' . ++self::$counter;

        return [
            'success' => true,
            'transaction_reference' => $txnRef,
            'message' => "Payment of ZMW {$amount} via MTN MoMo was successful.",
            'gateway_response' => [
                'provider' => 'MTN MoMo',
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

    public function getProviderName(): string
    {
        return 'MTN MoMo';
    }

    public function validatePhoneNumber(string $phoneNumber): bool
    {
        return true;
    }

    /**
     * Required by PaymentService (not part of the interface) to label the
     * payment method stored on the Payment record.
     */
    public function getProviderLabel(): string
    {
        return 'mtn_money';
    }
}

/**
 * PaymentService subclass that swaps in the fake gateway for deterministic tests.
 */
class TestablePaymentService extends PaymentService
{
    protected function resolveGateway(string $provider): GatewayInterface
    {
        return new FakeAlwaysSucceedsGateway();
    }
}