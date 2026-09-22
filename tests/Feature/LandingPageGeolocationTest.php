<?php

namespace Tests\Feature;

use App\Models\Bus;
use App\Models\Operator;
use App\Models\Route;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Stevebauman\Location\Facades\Location;
use Tests\TestCase;

/**
 * Regression coverage for LandingController::index()'s IP geolocation
 * lookup: it uses the real client IP (request()->ip()) for route
 * suggestions, cached per IP with private/reserved addresses skipped
 * entirely so the homepage doesn't pay for a live third-party HTTP call
 * (or a multi-driver fallback cascade) on every request.
 */
class LandingPageGeolocationTest extends TestCase
{
    use RefreshDatabase;

    private function seedRoute(): void
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

        Route::create([
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
    }

    /**
     * PHPUnit's default test client IP (127.0.0.1) is loopback, a reserved
     * address. detectCityFromIp() must short-circuit before ever calling
     * the geolocation service for it, so no outbound HTTP call is made.
     */
    public function test_landing_page_skips_geolocation_lookup_for_loopback_ip(): void
    {
        $this->seedRoute();
        Http::fake();

        $response = $this->get('/');

        $response->assertStatus(200);
        Http::assertNothingSent();
    }

    /**
     * A public IP's lookup result must be cached: two requests from the same
     * IP should only call the geolocation service once.
     */
    public function test_landing_page_caches_geolocation_result_per_public_ip(): void
    {
        $this->seedRoute();
        Location::shouldReceive('get')->once()->andReturn(false);

        $this->withServerVariables(['REMOTE_ADDR' => '165.56.66.198'])->get('/')->assertStatus(200);
        $this->withServerVariables(['REMOTE_ADDR' => '165.56.66.198'])->get('/')->assertStatus(200);
    }

    public function test_landing_page_falls_back_to_random_routes_when_geolocation_unavailable(): void
    {
        $this->seedRoute();
        Location::shouldReceive('get')->andReturn(false);

        $response = $this->withServerVariables(['REMOTE_ADDR' => '165.56.66.198'])->get('/');

        $response->assertStatus(200);
        $response->assertSee('Ndola');
    }

    public function test_landing_page_uses_detected_city_when_geolocation_resolves(): void
    {
        $this->seedRoute();

        $position = new \Stevebauman\Location\Position();
        $position->cityName = 'Lusaka';
        Location::shouldReceive('get')->andReturn($position);

        $response = $this->withServerVariables(['REMOTE_ADDR' => '165.56.66.198'])->get('/');

        $response->assertStatus(200);
        $response->assertViewHas('detectedCity', 'Lusaka');
    }
}
