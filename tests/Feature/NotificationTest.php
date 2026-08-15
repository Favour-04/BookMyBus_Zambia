<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Bus;
use App\Models\Operator;
use App\Models\Route;
use App\Models\Ticket;
use App\Models\User;
use App\Notifications\BookingCancelled;
use App\Notifications\DepartureReminder;
use App\Notifications\TicketIssued;
use App\Services\Messaging\SimulatedMessagingGateway;
use App\Services\MobileMoney\GatewayInterface;
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    private function makeBooking(string $status = 'pending', ?string $travelDate = null, string $departureTime = '08:00'): Booking
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
            'travel_date' => $travelDate ?? now()->addDays(3)->toDateString(),
            'departure_time' => $departureTime,
            'arrival_time' => '12:00',
            'fare' => 250.00,
            'is_active' => true,
        ]);

        $user = User::create([
            'full_name' => 'Test Traveler',
            'email' => 'traveler@example.com',
            'phone_number' => '0961234567',
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
            'status' => $status,
        ]);
    }

    public function test_ticket_issued_notification_is_dispatched_after_payment(): void
    {
        Notification::fake();

        $booking = $this->makeBooking('pending');
        $service = new NotificationTestPaymentService();

        $result = $service->processMobileMoney($booking, 'mtn', '0961234567');

        $this->assertTrue($result['success']);
        Notification::assertSentTo($booking->user, TicketIssued::class);
    }

    public function test_ticket_issued_sms_contains_qr_code_and_route(): void
    {
        $booking = $this->makeBooking('confirmed');
        $ticket = Ticket::create(['booking_id' => $booking->id, 'user_id' => $booking->user_id]);

        $sms = (new TicketIssued($booking, $ticket))->toSms($booking->user);

        $this->assertStringContainsString($ticket->qr_code, $sms);
        $this->assertStringContainsString('Lusaka', $sms);
        $this->assertStringContainsString('Ndola', $sms);
        $this->assertStringContainsString((string) $booking->seat_number, $sms);
    }

    public function test_booking_cancelled_notification_is_dispatched_on_cancel(): void
    {
        Notification::fake();

        $booking = $this->makeBooking('pending');

        $response = $this->actingAs($booking->user)->post('/bookings/' . $booking->id . '/cancel');

        $response->assertRedirect();
        Notification::assertSentTo($booking->user, BookingCancelled::class);
    }

    public function test_simulated_messaging_gateway_returns_success_with_message_id(): void
    {
        $gateway = new SimulatedMessagingGateway();
        $result = $gateway->send('0961234567', 'Hello from BookMyBus', 'sms');

        $this->assertTrue($result['success']);
        $this->assertSame('sms', $result['channel']);
        $this->assertNotEmpty($result['message_id']);
    }

    public function test_sms_channel_logs_to_notification_log(): void
    {
        $booking = $this->makeBooking('confirmed');
        $ticket = Ticket::create(['booking_id' => $booking->id, 'user_id' => $booking->user_id]);
        $user = $booking->user;

        // Resolve the channel via the container (uses the SimulatedMessagingGateway binding).
        $channel = app(\App\Notifications\Channels\SmsChannel::class);
        $channel->send($user, new TicketIssued($booking, $ticket));

        $this->assertDatabaseHas('notification_logs', [
            'notifiable_id' => $user->id,
            'channel' => 'sms',
            'notification_type' => TicketIssued::class,
            'status' => 'sent',
        ]);
    }

    public function test_departure_reminder_command_notifies_confirmed_passengers(): void
    {
        Notification::fake();

        // Trip departing ~1 hour from now (date + time together, safe across midnight).
        $departure = now()->addHour();
        $booking = $this->makeBooking('confirmed', $departure->toDateString(), $departure->format('H:i'));

        $this->artisan('notifications:departure-reminders')->assertSuccessful();

        Notification::assertSentTo($booking->user, DepartureReminder::class);
    }

    public function test_departure_reminder_command_skips_far_future_trips(): void
    {
        Notification::fake();

        // Trip departing in ~3 days (outside the 2-hour window).
        $booking = $this->makeBooking('confirmed');

        $this->artisan('notifications:departure-reminders')->assertSuccessful();

        Notification::assertNotSentTo($booking->user, DepartureReminder::class);
    }
}

/**
 * Deterministic, always-succeeds payment gateway for notification tests
 * (avoids the real SimulatedGateway's rand + usleep).
 */
class NotificationTestFakeGateway implements GatewayInterface
{
    public function charge(string $phoneNumber, float $amount, string $reference): array
    {
        $txnRef = 'TXN-NOTIF-' . uniqid();

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

    public function getProviderLabel(): string
    {
        return 'mtn_money';
    }
}

class NotificationTestPaymentService extends PaymentService
{
    protected function resolveGateway(string $provider): GatewayInterface
    {
        return new NotificationTestFakeGateway();
    }
}