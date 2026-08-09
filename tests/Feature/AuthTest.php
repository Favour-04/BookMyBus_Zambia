<?php

namespace Tests\Feature;

use App\Models\Operator;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_traveler_can_register(): void
    {
        $response = $this->post('/register', [
            'full_name' => 'Test Traveler',
            'email' => 'traveler@example.com',
            'phone_number' => '0977000000',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('home'));
        $this->assertDatabaseHas('users', [
            'email' => 'traveler@example.com',
            'role' => 'traveler',
        ]);
    }

    public function test_traveler_can_login(): void
    {
        $user = User::create([
            'full_name' => 'Test Traveler',
            'email' => 'traveler@example.com',
            'phone_number' => '0977000000',
            'password' => bcrypt('password123'),
            'role' => 'traveler',
        ]);

        $response = $this->post('/login', [
            'email' => 'traveler@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('home'));
        $this->assertAuthenticated();
    }

    public function test_operator_can_login(): void
    {
        $operator = Operator::create([
            'company_name' => 'Test Operator',
            'email' => 'operator@example.com',
            'phone_number' => '0977000001',
            'password' => bcrypt('password123'),
            'is_verified' => true,
            'verified_at' => now(),
        ]);

        $response = $this->post('/operator/login', [
            'email' => 'operator@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('operator.dashboard'));
        $this->assertAuthenticated('operator');
    }

    public function test_unverified_operator_cannot_login(): void
    {
        $operator = Operator::create([
            'company_name' => 'Unverified Operator',
            'email' => 'unverified@example.com',
            'phone_number' => '0977000002',
            'password' => bcrypt('password123'),
            'is_verified' => false,
        ]);

        $response = $this->post('/operator/login', [
            'email' => 'unverified@example.com',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest('operator');
    }

    public function test_admin_can_login(): void
    {
        $admin = User::create([
            'full_name' => 'Admin User',
            'email' => 'admin@example.com',
            'phone_number' => '0977000003',
            'password' => bcrypt('password123'),
            'role' => 'admin',
        ]);

        $response = $this->post('/admin/login', [
            'email' => 'admin@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticated('admin');
    }

    public function test_non_admin_cannot_login_to_admin_panel(): void
    {
        $user = User::create([
            'full_name' => 'Regular User',
            'email' => 'regular@example.com',
            'phone_number' => '0977000004',
            'password' => bcrypt('password123'),
            'role' => 'traveler',
        ]);

        $response = $this->post('/admin/login', [
            'email' => 'regular@example.com',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest('admin');
    }
}