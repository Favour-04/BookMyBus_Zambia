<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Bus;
use App\Models\Operator;
use App\Models\Route;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingCancelTest extends TestCase
{
    use RefreshDatabase;

    private function createBooking(string $status = 'pending', ?string $travelDate = null): Booking
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
            'status' => $status,
        ]);
    }

    public function test_pending_future_booking_can_be_cancelled(): void
    {
        $booking = $this->createBooking('pending');

        $response = $this->actingAs($booking->user)->post('/bookings/' . $booking->id . '/cancel');

        $response->assertRedirect();
        $this->assertSame('cancelled', $booking->fresh()->status);
    }

    public function test_confirmed_future_booking_can_be_cancelled(): void
    {
        $booking = $this->createBooking('confirmed');

        $response = $this->actingAs($booking->user)->post('/bookings/' . $booking->id . '/cancel');

        $response->assertRedirect();
        $this->assertSame('cancelled', $booking->fresh()->status);
        $this->assertNotNull($booking->fresh()->cancelled_at);
    }

    public function test_past_booking_cannot_be_cancelled(): void
    {
        $booking = $this->createBooking('confirmed', now()->subDays(2)->toDateString());

        $response = $this->actingAs($booking->user)->post('/bookings/' . $booking->id . '/cancel');

        $response->assertRedirect();
        $response->assertSessionHasErrors('cancel');
        $this->assertSame('confirmed', $booking->fresh()->status);
    }

    public function test_already_cancelled_booking_cannot_be_cancelled_again(): void
    {
        $booking = $this->createBooking('cancelled');

        $response = $this->actingAs($booking->user)->post('/bookings/' . $booking->id . '/cancel');

        $response->assertRedirect();
        $response->assertSessionHasErrors('cancel');
    }

    public function test_other_user_cannot_cancel_booking(): void
    {
        $booking = $this->createBooking('pending');

        $other = User::create([
            'full_name' => 'Other User',
            'email' => 'other@example.com',
            'phone_number' => '0977999999',
            'password' => bcrypt('password123'),
            'role' => 'traveler',
        ]);

        $response = $this->actingAs($other)->post('/bookings/' . $booking->id . '/cancel');

        $response->assertForbidden();
    }

    public function test_profile_booking_history_renders_cancel_button(): void
    {
        $booking = $this->createBooking('pending');

        $response = $this->actingAs($booking->user)->get('/profile');

        $response->assertStatus(200);
        $response->assertSee('Cancel');
        $response->assertSee('bookings/' . $booking->id . '/cancel');
    }
}