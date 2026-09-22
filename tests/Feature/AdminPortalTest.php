<?php

namespace Tests\Feature;

use App\Models\AdminAuditLog;
use App\Models\Booking;
use App\Models\Bus;
use App\Models\Operator;
use App\Models\Payment;
use App\Models\Route;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Regression coverage for the Admin Portal (controllers under
 * app/Http/Controllers/Admin, routes prefixed /admin, views under
 * resources/views/admin), written while auditing the portal end to end.
 * Several tests pin down fixes for bugs found during that audit so they
 * can't silently regress.
 */
class AdminPortalTest extends TestCase
{
    use RefreshDatabase;

    private function makeAdmin(array $overrides = []): User
    {
        return User::create(array_merge([
            'full_name' => 'Admin User',
            'email' => 'admin+' . uniqid() . '@example.com',
            'phone_number' => '0977' . random_int(100000, 999999),
            'password' => bcrypt('password123'),
            'role' => 'admin',
            'is_active' => true,
        ], $overrides));
    }

    private function makeTraveler(array $overrides = []): User
    {
        return User::create(array_merge([
            'full_name' => 'Traveler User',
            'email' => 'traveler+' . uniqid() . '@example.com',
            'phone_number' => '0966' . random_int(100000, 999999),
            'password' => bcrypt('password123'),
            'role' => 'traveler',
            'is_active' => true,
        ], $overrides));
    }

    private function makeOperator(array $overrides = []): Operator
    {
        return Operator::create(array_merge([
            'company_name' => 'Test Operator',
            'email' => 'operator+' . uniqid() . '@example.com',
            'phone_number' => '0955' . random_int(100000, 999999),
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
    // Broad smoke test: nothing in the core admin nav should 500.
    // ─────────────────────────────────────────────────────────────

    public function test_core_admin_pages_render_without_error(): void
    {
        $admin = $this->makeAdmin();
        $operator = $this->makeOperator();
        $bus = $this->makeBus($operator);
        $route = $this->makeRoute($operator, $bus);
        $booking = $this->makeBooking($route);
        Payment::create([
            'booking_id' => $booking->id,
            'amount' => $booking->amount,
            'currency' => 'ZMW',
            'payment_method' => 'mtn_money',
            'status' => 'successful',
            'transaction_reference' => 'TXN-' . uniqid(),
            'paid_at' => now(),
        ]);

        $this->actingAs($admin, 'admin');

        $pages = [
            'admin.dashboard',
            'admin.operators.index',
            'admin.operators.create',
            'admin.users.index',
            'admin.users.create',
            'admin.bookings.index',
            'admin.payments.index',
            'admin.trips.index',
            'admin.reports.index',
            'admin.audit-log.index',
            'admin.profile',
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
    // FIXED: bootstrap/app.php aliased 'guest' to the framework's own
    // Illuminate\Auth\Middleware\RedirectIfAuthenticated instead of the
    // app's guard-aware App\Http\Middleware\RedirectIfAuthenticated, and
    // never configured redirectGuestsTo(). Both auth:admin (unauthenticated)
    // and guest:admin (already authenticated) therefore fell back to the
    // framework defaults — route('login') / route('home') — regardless of
    // which portal the request was actually for.
    // ─────────────────────────────────────────────────────────────

    public function test_unauthenticated_admin_request_redirects_to_admin_login_not_traveler_login(): void
    {
        $response = $this->get(route('admin.dashboard'));

        $response->assertRedirect(route('admin.login'));
    }

    public function test_already_authenticated_admin_visiting_admin_login_is_sent_to_admin_dashboard(): void
    {
        $admin = $this->makeAdmin();

        $response = $this->actingAs($admin, 'admin')->get(route('admin.login'));

        $response->assertRedirect(route('admin.dashboard'));
    }

    /**
     * Positive control: the traveler portal's own guest/auth redirects are
     * untouched by the guard-aware change above.
     */
    public function test_unauthenticated_traveler_request_still_redirects_to_traveler_login(): void
    {
        $response = $this->get(route('profile'));

        $response->assertRedirect(route('login'));
    }

    // ─────────────────────────────────────────────────────────────
    // FIXED: UserController::show()/suspend()/activate()/destroy() looked
    // up User::findOrFail($id) with no role filter, even though these
    // routes live under "Traveler / user management" and the index() list
    // is scoped to role=traveler. Any admin could suspend, deactivate,
    // delete or view another admin account by guessing/incrementing an id
    // in these traveler-management endpoints. They're now scoped to
    // role=traveler, matching index().
    // ─────────────────────────────────────────────────────────────

    public function test_admin_cannot_suspend_another_admin_via_the_traveler_management_endpoint(): void
    {
        $admin = $this->makeAdmin();
        $otherAdmin = $this->makeAdmin();

        $response = $this->actingAs($admin, 'admin')
            ->post(route('admin.users.suspend', $otherAdmin->id));

        $response->assertStatus(404);
        $this->assertTrue($otherAdmin->fresh()->isActive());
    }

    public function test_admin_cannot_view_another_admin_via_the_traveler_management_endpoint(): void
    {
        $admin = $this->makeAdmin();
        $otherAdmin = $this->makeAdmin();

        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.users.show', $otherAdmin->id));

        $response->assertStatus(404);
    }

    public function test_admin_cannot_delete_another_admin_via_the_traveler_management_endpoint(): void
    {
        $admin = $this->makeAdmin();
        $otherAdmin = $this->makeAdmin();

        $response = $this->actingAs($admin, 'admin')
            ->delete(route('admin.users.destroy', $otherAdmin->id));

        $response->assertStatus(404);
        $this->assertNotNull(User::withTrashed()->find($otherAdmin->id));
        $this->assertNull(User::withTrashed()->find($otherAdmin->id)->deleted_at);
    }

    /**
     * Positive control: suspending an actual traveler through the same
     * endpoint still works.
     */
    public function test_admin_can_still_suspend_a_traveler(): void
    {
        $admin = $this->makeAdmin();
        $traveler = $this->makeTraveler();

        $response = $this->actingAs($admin, 'admin')
            ->post(route('admin.users.suspend', $traveler->id));

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertFalse($traveler->fresh()->isActive());
    }

    // ─────────────────────────────────────────────────────────────
    // FIXED: UserController::create()/store() existed with full,
    // already-validated logic, but no route ever pointed at them and the
    // 'admin.users.create' view didn't exist — an "Add Account" button was
    // simply missing from the travelers list. Wired up as traveler-only
    // (see store()'s docblock for why it can't also create admins here).
    // ─────────────────────────────────────────────────────────────

    public function test_admin_can_create_a_new_traveler_account(): void
    {
        $admin = $this->makeAdmin();

        $response = $this->actingAs($admin, 'admin')->post(route('admin.users.store'), [
            'full_name' => 'New Traveler',
            'email' => 'new.traveler@example.com',
            'phone_number' => '0977123456',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $user = User::where('email', 'new.traveler@example.com')->first();

        $this->assertNotNull($user);
        $this->assertSame('traveler', $user->role);
        $response->assertRedirect(route('admin.users.show', $user->id));
    }

    // ─────────────────────────────────────────────────────────────
    // Positive control: the admin audit log records admin actions and the
    // detail page renders the before/after state.
    // ─────────────────────────────────────────────────────────────

    public function test_operator_verification_is_recorded_in_the_audit_log(): void
    {
        $admin = $this->makeAdmin();
        $operator = $this->makeOperator(['is_verified' => false, 'verified_at' => null]);

        $this->actingAs($admin, 'admin')->post(route('admin.operators.verify', $operator->id));

        $this->assertTrue($operator->fresh()->is_verified);

        $log = AdminAuditLog::where('event', 'operator.verified')->first();
        $this->assertNotNull($log);
        $this->assertSame($admin->id, $log->admin_id);

        $response = $this->actingAs($admin, 'admin')->get(route('admin.audit-log.show', $log->id));
        $response->assertStatus(200);
    }

    // ─────────────────────────────────────────────────────────────
    // ReportController::data() was fully written (validated from/to/
    // operator_id, returned the same buildReportData() as index() as JSON)
    // but nothing in admin.reports.index called it — no filter UI existed
    // at all. index()/export() now accept those same filters directly via
    // GET, and data() (and its route) has been removed as redundant.
    // ─────────────────────────────────────────────────────────────

    public function test_reports_index_scopes_totals_to_the_selected_operator(): void
    {
        $admin = $this->makeAdmin();

        $operatorA = $this->makeOperator(['company_name' => 'Operator A']);
        $busA = $this->makeBus($operatorA);
        $routeA = $this->makeRoute($operatorA, $busA);
        $bookingA = $this->makeBooking($routeA, ['amount' => 300]);
        Payment::create([
            'booking_id' => $bookingA->id,
            'amount' => 300,
            'currency' => 'ZMW',
            'payment_method' => 'mtn_money',
            'status' => 'successful',
            'transaction_reference' => 'TXN-A-' . uniqid(),
            'paid_at' => now(),
        ]);

        $operatorB = $this->makeOperator(['company_name' => 'Operator B']);
        $busB = $this->makeBus($operatorB);
        $routeB = $this->makeRoute($operatorB, $busB);
        $bookingB = $this->makeBooking($routeB, ['amount' => 700]);
        Payment::create([
            'booking_id' => $bookingB->id,
            'amount' => 700,
            'currency' => 'ZMW',
            'payment_method' => 'airtel_money',
            'status' => 'successful',
            'transaction_reference' => 'TXN-B-' . uniqid(),
            'paid_at' => now(),
        ]);

        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.reports.index', ['operator_id' => $operatorA->id]));

        $response->assertStatus(200);
        $response->assertViewHas('totals', fn ($totals) => (float) $totals['revenue'] === 300.0);
    }

    public function test_reports_export_respects_the_operator_filter(): void
    {
        $admin = $this->makeAdmin();

        $operatorA = $this->makeOperator(['company_name' => 'Operator A']);
        $busA = $this->makeBus($operatorA);
        $routeA = $this->makeRoute($operatorA, $busA);
        $bookingA = $this->makeBooking($routeA, ['amount' => 300]);
        Payment::create([
            'booking_id' => $bookingA->id,
            'amount' => 300,
            'currency' => 'ZMW',
            'payment_method' => 'mtn_money',
            'status' => 'successful',
            'transaction_reference' => 'TXN-A-' . uniqid(),
            'paid_at' => now(),
        ]);

        $operatorB = $this->makeOperator(['company_name' => 'Operator B']);
        $busB = $this->makeBus($operatorB);
        $routeB = $this->makeRoute($operatorB, $busB);
        $bookingB = $this->makeBooking($routeB, ['amount' => 700]);
        Payment::create([
            'booking_id' => $bookingB->id,
            'amount' => 700,
            'currency' => 'ZMW',
            'payment_method' => 'airtel_money',
            'status' => 'successful',
            'transaction_reference' => 'TXN-B-' . uniqid(),
            'paid_at' => now(),
        ]);

        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.reports.export', ['operator_id' => $operatorA->id]));

        $csv = $response->getContent();
        $this->assertStringContainsString('Operator A', $csv);
        $this->assertStringNotContainsString('Operator B', $csv);
    }

    public function test_reports_data_json_endpoint_no_longer_exists(): void
    {
        $admin = $this->makeAdmin();

        $response = $this->actingAs($admin, 'admin')->get('/admin/reports/data');

        $response->assertStatus(404);
    }
}
