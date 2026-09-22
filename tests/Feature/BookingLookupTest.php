<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Bus;
use App\Models\Operator;
use App\Models\Route;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingLookupTest extends TestCase
{
    use RefreshDatabase;

    private function createBooking(): Booking
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
            'status' => 'confirmed',
            'reference_id' => 'BMZ' . strtoupper(substr(uniqid(), -6)),
        ]);
    }

    public function test_booking_lookup_by_reference_id(): void
    {
        $booking = $this->createBooking();

        $response = $this->actingAs($booking->user)->post('/my-booking/lookup', [
            'reference_id' => $booking->reference_id,
        ]);

        $response->assertStatus(200);
        $response->assertSee($booking->reference_id);
    }

    /**
     * Phone-number lookup was deliberately removed (see
     * BookingController::customerLookup's docblock): a phone number is far
     * easier to guess/enumerate than a random reference code, so it would
     * let a searcher pull up a stranger's booking. reference_id is now the
     * only accepted field, so a phone-only submission must fail validation
     * rather than silently ignore the field and search anyway.
     */
    public function test_booking_lookup_by_phone_number_alone_is_rejected(): void
    {
        $booking = $this->createBooking();

        $response = $this->actingAs($booking->user)->post('/my-booking/lookup', [
            'phone_number' => '0977000000',
        ]);

        $response->assertSessionHasErrors('reference_id');
    }

    public function test_booking_lookup_returns_error_when_not_found(): void
    {
        $user = User::create([
            'full_name' => 'Test Traveler',
            'email' => 'traveler@example.com',
            'phone_number' => '0977000000',
            'password' => bcrypt('password123'),
            'role' => 'traveler',
        ]);

        $response = $this->actingAs($user)->post('/my-booking/lookup', [
            'reference_id' => 'BMZ-NOTREAL',
        ]);

        $response->assertStatus(200);
        $response->assertSee('No booking found');
    }

    /**
     * The reference-ID match is exact, not a partial/LIKE match (also part
     * of the same hardening) — a substring of a real reference code must
     * not find the booking.
     */
    public function test_booking_lookup_does_not_partial_match_reference_id(): void
    {
        $booking = $this->createBooking();
        $partial = substr($booking->reference_id, 0, 5);

        $response = $this->actingAs($booking->user)->post('/my-booking/lookup', [
            'reference_id' => $partial,
        ]);

        $response->assertStatus(200);
        $response->assertSee('No booking found');
    }
}