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

    public function test_booking_lookup_by_passenger_phone_is_rejected(): void
    {
        // Phone number is deliberately NOT a search field: it's too easy to
        // guess or enumerate (see BookingController::customerLookup). A
        // phone-only submission must fail validation, not leak bookings.
        $booking = $this->createBooking();

        $response = $this->from('/my-booking')->actingAs($booking->user)->post('/my-booking/lookup', [
            'phone_number' => '0977000000',
        ]);

        $response->assertRedirect('/my-booking');
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
            'reference_id' => 'BMZ-NOPE0',
        ]);

        $response->assertStatus(200);
        $response->assertSee('No booking found');
    }
}