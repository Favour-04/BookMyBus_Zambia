<?php

namespace Tests\Feature;

use App\Services\MobileMoney\SimulatedGateway;
use Tests\TestCase;

class SimulatedGatewayDeterminismTest extends TestCase
{
    public function test_gateway_always_succeeds_outside_local_environment(): void
    {
        // Simulate production: no coin-flip declines for real users.
        $this->app->instance('env', 'production');

        $gateway = SimulatedGateway::forProvider('airtel');

        // 20 attempts would virtually certainly hit a ~10% failure rate if
        // the coin flip were still active (probability of all-pass ≈ 0.9^20 ≈ 12%).
        for ($i = 0; $i < 20; $i++) {
            $result = $gateway->charge('0971234567', 100.0, 'BMZ-REF'.$i);

            $this->assertTrue(
                $result['success'],
                "Attempt {$i} failed outside local — simulated gateway must be deterministic in production."
            );
        }
    }

    public function test_invalid_phone_still_fails_in_production(): void
    {
        $this->app->instance('env', 'production');

        $gateway = SimulatedGateway::forProvider('mtn');

        $result = $gateway->charge('0951234567', 100.0, 'BMZ-REF');

        $this->assertFalse($result['success']);
        $this->assertSame('INVALID_PHONE', $result['gateway_response']['error_code'] ?? null);
    }
}
