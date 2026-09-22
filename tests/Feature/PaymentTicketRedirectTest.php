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

/**
 * Regression coverage for the uncommitted change to
 * BookingController::paymentTicket() that redirects already-confirmed
 * bookings to booking.success instead of re-rendering the checkout page.
 */
class PaymentTicketRedirectTest extends TestCase
{
    use RefreshDatabase;

    private function createBooking(string $status = 'pending'): Booking
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
            'passenger_phone' => '0977000000',
            'amount' => 250.00,
            'base_fare' => 250.00,
            'service_fee_total' => 0,
            'discount_amount' => 0,
            'status' => $status,
        ]);
    }

    public function test_pending_booking_still_shows_checkout_page(): void
    {
        $booking = $this->createBooking('pending');

        $response = $this->actingAs($booking->user)->get('/payment/ticket/' . $booking->id);

        $response->assertStatus(200);
        $response->assertViewIs('payment_ticket');
        $response->assertSee('Secure Checkout');
    }

    public function test_confirmed_booking_redirects_to_success_page_instead_of_checkout(): void
    {
        $booking = $this->createBooking('confirmed');

        $response = $this->actingAs($booking->user)->get('/payment/ticket/' . $booking->id);

        $response->assertRedirect(route('booking.success', $booking->id));
    }

    public function test_confirmed_booking_with_issued_ticket_redirects_without_error(): void
    {
        $booking = $this->createBooking('confirmed');
        Ticket::create([
            'booking_id' => $booking->id,
            'user_id' => $booking->user_id,
        ]);

        $response = $this->actingAs($booking->user)->get('/payment/ticket/' . $booking->id);

        $response->assertRedirect(route('booking.success', $booking->id));

        // Follow the redirect: history_page must render cleanly with the
        // eager-loaded ticket relation now pulled in by paymentTicket()'s query.
        $follow = $this->actingAs($booking->user)->get(route('booking.success', $booking->id));
        $follow->assertStatus(200);
        $follow->assertSee('PAYMENT SUCCESSFUL');
    }

    public function test_cancelled_booking_still_shows_checkout_page_not_redirected(): void
    {
        // isConfirmed() is strictly status === 'confirmed', so a cancelled
        // booking must NOT be redirected — it should fall through to the
        // normal (now-stale) checkout view rather than looping to success.
        $booking = $this->createBooking('cancelled');

        $response = $this->actingAs($booking->user)->get('/payment/ticket/' . $booking->id);

        $response->assertStatus(200);
        $response->assertViewIs('payment_ticket');
    }

    public function test_payment_ticket_view_no_longer_references_missing_qr_endpoint(): void
    {
        $booking = $this->createBooking('pending');

        $response = $this->actingAs($booking->user)->get('/payment/ticket/' . $booking->id);

        $response->assertStatus(200);
        $response->assertDontSee('api.qrserver.com');
        $response->assertSee('Generated after payment');
    }

    public function test_other_users_confirmed_booking_is_not_accessible(): void
    {
        $booking = $this->createBooking('confirmed');

        $other = User::create([
            'full_name' => 'Other User',
            'email' => 'other@example.com',
            'phone_number' => '0977999999',
            'password' => bcrypt('password123'),
            'role' => 'traveler',
        ]);

        $response = $this->actingAs($other)->get('/payment/ticket/' . $booking->id);

        $response->assertStatus(404);
    }
}
