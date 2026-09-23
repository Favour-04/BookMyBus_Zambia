<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Bus;
use App\Models\Operator;
use App\Models\Route;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QrCheckInTest extends TestCase
{
    use RefreshDatabase;

    private Operator $operator;

    private function setUpTrip(string $email = 'operator@example.com'): void
    {
        static $instance = 0;
        $instance++;

        $this->operator = Operator::create([
            'company_name' => 'Test Operator',
            'email' => $email,
            'phone_number' => '097700000' . $instance,
            'password' => bcrypt('password123'),
            'is_verified' => true,
            'verified_at' => now(),
        ]);

        $bus = Bus::create([
            'operator_id' => $this->operator->id,
            'registration_number' => 'BAZ ' . str_pad((string) $instance, 4, '0', STR_PAD_LEFT),
            'seat_capacity' => 49,
            'bus_class' => 'economy',
            'is_active' => true,
        ]);

        $this->route = Route::create([
            'operator_id' => $this->operator->id,
            'bus_id' => $bus->id,
            'origin' => 'Lusaka',
            'destination' => 'Ndola',
            'travel_date' => now()->addDays(3)->toDateString(),
            'departure_time' => '08:00',
            'arrival_time' => '12:00',
            'fare' => 250.00,
            'is_active' => true,
        ]);
    }

    private function createConfirmedBookingWithTicket(int $seat = 1): Booking
    {
        $user = User::create([
            'full_name' => 'Test Traveler',
            'email' => 'traveler' . $seat . '@example.com',
            'phone_number' => '097700000' . $seat,
            'password' => bcrypt('password123'),
            'role' => 'traveler',
        ]);

        $booking = Booking::create([
            'user_id' => $user->id,
            'route_id' => $this->route->id,
            'seat_number' => $seat,
            'passenger_name' => 'Test Traveler ' . $seat,
            'passenger_id_number' => '123456789' . $seat,
            'passenger_phone' => '096123456' . $seat,
            'amount' => 250.00,
            'base_fare' => 250.00,
            'service_fee_total' => 0,
            'discount_amount' => 0,
            'status' => 'confirmed',
        ]);

        Ticket::create([
            'booking_id' => $booking->id,
            'user_id' => $user->id,
        ]);

        return $booking->fresh();
    }

    private function postQr(string $qrCode)
    {
        return $this->actingAs($this->operator, 'operator')
            ->post(route('operator.passengers.checkin-qr'), ['qr_code' => $qrCode]);
    }

    public function test_qr_checkin_marks_booking_boarded_and_ticket_used(): void
    {
        $this->setUpTrip();
        $booking = $this->createConfirmedBookingWithTicket();
        $ticket = $booking->ticket;

        $response = $this->postQr($ticket->qr_code);

        $response->assertRedirect();
        $response->assertSessionHas('status');

        $this->assertNotNull($booking->fresh()->boarded_at);
        $this->assertSame('used', $ticket->fresh()->status);
        $this->assertNotNull($ticket->fresh()->used_at);
    }

    public function test_qr_checkin_rejects_unknown_code(): void
    {
        $this->setUpTrip();

        $response = $this->postQr('BMZ-QR-DOES-NOT-EXIST');

        $response->assertRedirect();
        $response->assertSessionHasErrors('qr');
    }

    public function test_qr_checkin_rejects_ticket_from_other_operator(): void
    {
        // First operator owns the trip/booking...
        $this->setUpTrip('owner@example.com');
        $booking = $this->createConfirmedBookingWithTicket();

        // ...second operator tries to check it in.
        $this->setUpTrip('intruder@example.com');

        $response = $this->postQr($booking->ticket->qr_code);

        $response->assertRedirect();
        $response->assertSessionHasErrors('qr');
        $this->assertNull($booking->fresh()->boarded_at);
    }

    public function test_qr_checkin_rejects_unconfirmed_booking(): void
    {
        $this->setUpTrip();
        $booking = $this->createConfirmedBookingWithTicket();
        $booking->update(['status' => 'pending']);

        $response = $this->postQr($booking->ticket->qr_code);

        $response->assertRedirect();
        $response->assertSessionHasErrors('qr');
        $this->assertNull($booking->fresh()->boarded_at);
        $this->assertSame('issued', $booking->ticket->fresh()->status);
    }

    public function test_qr_checkin_is_idempotent_for_used_ticket(): void
    {
        $this->setUpTrip();
        $booking = $this->createConfirmedBookingWithTicket();
        $ticket = $booking->ticket;

        $this->postQr($ticket->qr_code);

        // Second presentation of the same ticket must be refused.
        $response = $this->postQr($ticket->qr_code);

        $response->assertRedirect();
        $response->assertSessionHasErrors('qr');
    }

    public function test_manual_board_marks_ticket_used(): void
    {
        $this->setUpTrip();
        $booking = $this->createConfirmedBookingWithTicket();

        $this->actingAs($this->operator, 'operator')
            ->patch(route('operator.bookings.board', $booking->id));

        $this->assertNotNull($booking->fresh()->boarded_at);
        $this->assertSame('used', $booking->ticket->fresh()->status);
    }

    public function test_undo_board_reactivates_ticket(): void
    {
        $this->setUpTrip();
        $booking = $this->createConfirmedBookingWithTicket();

        $this->actingAs($this->operator, 'operator')
            ->patch(route('operator.bookings.board', $booking->id));
        $this->actingAs($this->operator, 'operator')
            ->patch(route('operator.bookings.undo-board', $booking->id));

        $this->assertNull($booking->fresh()->boarded_at);
        $this->assertSame('issued', $booking->ticket->fresh()->status);
        $this->assertNull($booking->ticket->fresh()->used_at);
    }
}
