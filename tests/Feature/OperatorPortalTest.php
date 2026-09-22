<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Bus;
use App\Models\Driver;
use App\Models\Operator;
use App\Models\Route;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Regression coverage for the Operator Portal (controllers under
 * app/Http/Controllers/Operator, routes prefixed /operator, views under
 * resources/views/operator), written while auditing the portal end to end.
 * Several tests pin down fixes for bugs found during that audit so they
 * can't silently regress.
 */
class OperatorPortalTest extends TestCase
{
    use RefreshDatabase;

    private function makeOperator(array $overrides = []): Operator
    {
        return Operator::create(array_merge([
            'company_name' => 'Test Operator',
            'email' => 'operator+' . uniqid() . '@example.com',
            'phone_number' => '09770000' . random_int(10, 99),
            'password' => bcrypt('password123'),
            'is_verified' => true,
            'verified_at' => now(),
        ], $overrides));
    }

    private function makeBus(Operator $operator, array $overrides = []): Bus
    {
        return Bus::create(array_merge([
            'operator_id' => $operator->id,
            'registration_number' => 'BAZ-' . strtoupper(uniqid()),
            'seat_capacity' => 49,
            'bus_class' => 'economy',
            'is_active' => true,
        ], $overrides));
    }

    /**
     * Under the test suite's sqlite (:memory:) connection, Eloquent's plain
     * 'date' cast serializes for storage using the full 'Y-m-d H:i:s'
     * format (Laravel only trims to a bare date with an explicit
     * 'date:Y-m-d' cast), and sqlite stores that string byte-for-byte. The
     * app's real connection is Postgres, where a DATE column normalizes
     * that same value down to a bare 'Y-m-d' on write — which is what
     * every `where('travel_date', $bareDateString)` filter in the
     * controllers assumes. Left alone, this mismatch would make sqlite
     * fixtures invisible to those exact-match filters in a way real
     * Postgres never is, producing false failures unrelated to the bug
     * under test. This re-normalizes the stored value after Eloquent
     * creates it, so date-equality queries behave the same as production.
     */
    private function makeRoute(Operator $operator, Bus $bus, array $overrides = []): Route
    {
        $travelDate = $overrides['travel_date'] ?? now()->addDays(3)->toDateString();

        $route = Route::create(array_merge([
            'operator_id' => $operator->id,
            'bus_id' => $bus->id,
            'origin' => 'Lusaka',
            'destination' => 'Ndola',
            'travel_date' => $travelDate,
            'departure_time' => '08:00',
            'arrival_time' => '12:00',
            'fare' => 250.00,
            'is_active' => true,
        ], $overrides));

        \Illuminate\Support\Facades\DB::table('routes')
            ->where('id', $route->id)
            ->update(['travel_date' => $travelDate]);

        return $route->fresh();
    }

    private function makeBooking(Route $route, array $overrides = []): Booking
    {
        return Booking::create(array_merge([
            'route_id' => $route->id,
            'seat_number' => 1,
            'passenger_name' => 'Test Passenger',
            'passenger_phone' => '0977000000',
            'amount' => $route->fare,
            'status' => 'confirmed',
        ], $overrides));
    }

    // ─────────────────────────────────────────────────────────────
    // Broad smoke test: nothing in the core nav should 500.
    // ─────────────────────────────────────────────────────────────

    public function test_core_operator_pages_render_without_error(): void
    {
        $operator = $this->makeOperator();
        $bus = $this->makeBus($operator);
        $driver = Driver::create([
            'operator_id' => $operator->id,
            'full_name' => 'John Driver',
            'phone_number' => '0977111111',
            'license_number' => 'DL-' . uniqid(),
            'is_active' => true,
        ]);
        $route = $this->makeRoute($operator, $bus, ['driver_id' => $driver->id]);
        $this->makeBooking($route);

        $this->actingAs($operator, 'operator');

        $pages = [
            'operator.dashboard',
            'operator.trips.index',
            'operator.trips.calendar',
            'operator.bookings.index',
            'operator.buses.index',
            'operator.drivers.index',
            'operator.revenue',
            'operator.fare-rules.index',
            'operator.promo-codes.index',
            'operator.route-templates.index',
            'operator.customers.index',
            'operator.audit-log.index',
            'operator.passengers.index',
            'operator.profile',
        ];

        foreach ($pages as $routeName) {
            $response = $this->get(route($routeName));
            $this->assertLessThan(
                500,
                $response->getStatusCode(),
                "GET {$routeName} returned a {$response->getStatusCode()} — see exception in response."
            );
        }
    }

    // ─────────────────────────────────────────────────────────────
    // FIXED: TripManagementController::store()'s "within 2 hours" overlap
    // check used to build a whereBetween() from Carbon::parse($time)
    // ->subHours(2)->format('H:i') / ->addHours(2)->format('H:i'). For a
    // departure time close to midnight, subHours(2) rolled back into the
    // previous day and the H:i format silently dropped the date, producing
    // a low bound that was *numerically greater* than the high bound (e.g.
    // ['23:00', '03:00']) — a BETWEEN that can never match. The check now
    // compares minutes-since-midnight with wraparound instead.
    // ─────────────────────────────────────────────────────────────

    public function test_overlap_check_catches_conflicts_close_to_midnight(): void
    {
        $operator = $this->makeOperator();
        $bus = $this->makeBus($operator);
        $travelDate = now()->addDays(3)->toDateString();

        // Existing trip at 00:30 on this bus.
        $this->makeRoute($operator, $bus, [
            'departure_time' => '00:30',
            'travel_date' => $travelDate,
        ]);

        $this->actingAs($operator, 'operator');

        // New trip only 30 minutes later on the SAME bus — within the
        // 2-hour buffer, so this must be rejected.
        $response = $this->post(route('operator.trips.store'), [
            'origin' => 'Lusaka',
            'destination' => 'Kitwe',
            'travel_date' => $travelDate,
            'departure_time' => '01:00',
            'bus_id' => $bus->id,
            'fare' => 200,
        ]);

        $response->assertSessionHasErrors('departure_time');
        $this->assertSame(1, Route::where('bus_id', $bus->id)->count());
    }

    /**
     * Positive control for the test above: the same 2-hour conflict check,
     * away from midnight, behaves as intended. This isolates the bug to the
     * time-of-day wraparound rather than the conflict check being broken
     * outright.
     */
    public function test_overlap_check_correctly_blocks_daytime_conflicts(): void
    {
        $operator = $this->makeOperator();
        $bus = $this->makeBus($operator);
        $travelDate = now()->addDays(3)->toDateString();

        $this->makeRoute($operator, $bus, [
            'departure_time' => '10:00',
            'travel_date' => $travelDate,
        ]);

        $this->actingAs($operator, 'operator');

        $response = $this->post(route('operator.trips.store'), [
            'origin' => 'Lusaka',
            'destination' => 'Kitwe',
            'travel_date' => $travelDate,
            'departure_time' => '11:00',
            'bus_id' => $bus->id,
            'fare' => 200,
        ]);

        $response->assertSessionHasErrors('departure_time');
        $this->assertSame(1, Route::where('bus_id', $bus->id)->count());
    }

    // ─────────────────────────────────────────────────────────────
    // FIXED: TripManagementController::update() computed
    // $hasConfirmedBookings but never checked it, so an operator could swap
    // the bus and/or reschedule a trip that already had paying, confirmed
    // passengers with no warning and no block. It's now used to reject bus/
    // date/time changes on such a trip.
    // ─────────────────────────────────────────────────────────────

    public function test_trip_with_confirmed_bookings_cannot_be_reassigned_to_another_bus(): void
    {
        $operator = $this->makeOperator();
        $originalBus = $this->makeBus($operator, ['seat_capacity' => 49]);
        $smallerBus = $this->makeBus($operator, ['seat_capacity' => 14]);
        $route = $this->makeRoute($operator, $originalBus);
        $this->makeBooking($route, ['seat_number' => 30, 'status' => 'confirmed']);

        $this->actingAs($operator, 'operator');

        $response = $this->put(route('operator.trips.update', $route->id), [
            'bus_id' => $smallerBus->id,
        ]);

        $response->assertSessionHasErrors('trip');
        $this->assertSame($originalBus->id, $route->fresh()->bus_id);
    }

    /**
     * Positive control: fields that don't affect an already-booked
     * passenger's trip (like fare) can still be edited even with confirmed
     * bookings on the trip.
     */
    public function test_trip_with_confirmed_bookings_can_still_have_its_fare_updated(): void
    {
        $operator = $this->makeOperator();
        $bus = $this->makeBus($operator);
        $route = $this->makeRoute($operator, $bus, ['fare' => 250]);
        $this->makeBooking($route, ['seat_number' => 1, 'status' => 'confirmed']);

        $this->actingAs($operator, 'operator');

        $response = $this->put(route('operator.trips.update', $route->id), [
            'fare' => 300,
        ]);

        $response->assertSessionDoesntHaveErrors();
        $this->assertEquals(300, $route->fresh()->fare);
    }

    // ─────────────────────────────────────────────────────────────
    // FIXED: RouteTemplateController::createBulkTrips() used to reject only
    // an EXACT departure-time match on the same bus/date, unlike
    // TripManagementController::store() which also rejects anything within
    // a 2-hour window. It now uses the same 2-hour buffer check.
    // ─────────────────────────────────────────────────────────────

    public function test_bulk_trip_creation_enforces_the_same_two_hour_buffer_as_single_creation(): void
    {
        $operator = $this->makeOperator();
        $bus = $this->makeBus($operator);

        $nextMonday = now()->next(\Carbon\Carbon::MONDAY);
        $this->makeRoute($operator, $bus, [
            'departure_time' => '08:00',
            'travel_date' => $nextMonday->toDateString(),
        ]);

        $template = \App\Models\RouteTemplate::create([
            'operator_id' => $operator->id,
            'name' => 'Lusaka-Ndola Express',
            'origin' => 'Lusaka',
            'destination' => 'Ndola',
            'distance_km' => 320,
            'base_fare' => 250,
            'is_active' => true,
        ]);

        $this->actingAs($operator, 'operator');

        // 08:30 is only 30 minutes after the 08:00 trip already on this bus
        // — within the 2-hour buffer, so no trip should be created for that
        // Monday (it's the only day selected).
        $response = $this->post(route('operator.route-templates.create-bulk-trips', $template->id), [
            'departure_time' => '08:30',
            'bus_id' => $bus->id,
            'days_of_week' => [$nextMonday->dayOfWeek],
            'weeks' => 1,
            'start_date' => $nextMonday->toDateString(),
        ]);

        $response->assertSessionHas('bulk_errors');
        $this->assertSame(
            1,
            Route::where('bus_id', $bus->id)
                ->whereDate('travel_date', $nextMonday->toDateString())
                ->count()
        );
    }

    // ─────────────────────────────────────────────────────────────
    // FIXED: BookingManagementController::update()'s seat-availability
    // check used to only look at bookings whose status is
    // pending/confirmed, so a seat number belonging to a CANCELLED booking
    // on the same route passed validation as "available". But bookings has
    // a DB-level unique(route_id, seat_number) index that isn't filtered by
    // status, so the update then threw a raw QueryException (an uncaught
    // 500) instead of a friendly validation error. The check now matches
    // the DB constraint's scope (any booking on the route, any status).
    // ─────────────────────────────────────────────────────────────

    public function test_reassigning_a_booking_to_a_cancelled_bookings_seat_is_rejected_with_a_validation_error(): void
    {
        $operator = $this->makeOperator();
        $bus = $this->makeBus($operator, ['seat_capacity' => 10]);
        $route = $this->makeRoute($operator, $bus);

        // Seat 5 is "taken" by a cancelled booking — still occupies the
        // (route_id, seat_number) unique index row.
        $this->makeBooking($route, ['seat_number' => 5, 'status' => 'cancelled']);

        // The booking we'll try to move onto seat 5.
        $booking = $this->makeBooking($route, ['seat_number' => 6, 'status' => 'confirmed']);

        $this->actingAs($operator, 'operator');

        $response = $this->put(route('operator.bookings.update', $booking->id), [
            'passenger_name' => $booking->passenger_name,
            'passenger_phone' => $booking->passenger_phone,
            'seat_number' => 5,
            'amount' => $booking->amount,
        ]);

        $response->assertSessionHasErrors('seat_number');
        $this->assertSame(6, $booking->fresh()->seat_number);
    }

    /**
     * Positive control: moving to a genuinely free seat still works.
     */
    public function test_reassigning_a_booking_to_a_free_seat_succeeds(): void
    {
        $operator = $this->makeOperator();
        $bus = $this->makeBus($operator, ['seat_capacity' => 10]);
        $route = $this->makeRoute($operator, $bus);
        $booking = $this->makeBooking($route, ['seat_number' => 6, 'status' => 'confirmed']);

        $this->actingAs($operator, 'operator');

        $response = $this->put(route('operator.bookings.update', $booking->id), [
            'passenger_name' => $booking->passenger_name,
            'passenger_phone' => $booking->passenger_phone,
            'seat_number' => 7,
            'amount' => $booking->amount,
        ]);

        $response->assertSessionDoesntHaveErrors();
        $this->assertSame(7, $booking->fresh()->seat_number);
    }

    // ─────────────────────────────────────────────────────────────
    // Positive control: tenant isolation. An operator must not be able to
    // reach another operator's trip by guessing/incrementing an ID.
    // ─────────────────────────────────────────────────────────────

    public function test_operator_cannot_view_another_operators_trip(): void
    {
        $operatorA = $this->makeOperator();
        $busA = $this->makeBus($operatorA);
        $routeA = $this->makeRoute($operatorA, $busA);

        $operatorB = $this->makeOperator();

        $this->actingAs($operatorB, 'operator');

        $response = $this->get(route('operator.trips.show', $routeA->id));

        $response->assertStatus(404);
    }

    // ─────────────────────────────────────────────────────────────
    // FIXED: promo_codes.code used to be globally unique (DB-level
    // unique('code')), so two different operators could never both run a
    // "SAVE10" code. The constraint is now unique(operator_id, code).
    // ─────────────────────────────────────────────────────────────

    public function test_different_operators_can_use_the_same_promo_code(): void
    {
        $operatorA = $this->makeOperator();
        $operatorB = $this->makeOperator();

        $this->actingAs($operatorA, 'operator');
        $responseA = $this->post(route('operator.promo-codes.store'), [
            'code' => 'SAVE10',
            'discount_type' => 'percentage',
            'discount_value' => 10,
            'valid_from' => now()->toDateString(),
            'valid_until' => now()->addMonth()->toDateString(),
        ]);
        $responseA->assertSessionDoesntHaveErrors();

        $this->actingAs($operatorB, 'operator');
        $responseB = $this->post(route('operator.promo-codes.store'), [
            'code' => 'SAVE10',
            'discount_type' => 'fixed',
            'discount_value' => 20,
            'valid_from' => now()->toDateString(),
            'valid_until' => now()->addMonth()->toDateString(),
        ]);
        $responseB->assertSessionDoesntHaveErrors();

        $this->assertSame(2, \App\Models\PromoCode::where('code', 'SAVE10')->count());
    }

    /**
     * Positive control: the same operator still can't create two promo
     * codes with the same code.
     */
    public function test_same_operator_cannot_reuse_a_promo_code(): void
    {
        $operator = $this->makeOperator();
        $this->actingAs($operator, 'operator');

        \App\Models\PromoCode::create([
            'operator_id' => $operator->id,
            'code' => 'SAVE10',
            'discount_type' => 'percentage',
            'discount_value' => 10,
            'valid_from' => now(),
            'valid_until' => now()->addMonth(),
            'is_active' => true,
        ]);

        $response = $this->post(route('operator.promo-codes.store'), [
            'code' => 'SAVE10',
            'discount_type' => 'fixed',
            'discount_value' => 5,
            'valid_from' => now()->toDateString(),
            'valid_until' => now()->addMonth()->toDateString(),
        ]);

        $response->assertSessionHasErrors('code');
        $this->assertSame(1, \App\Models\PromoCode::where('operator_id', $operator->id)->count());
    }
}
