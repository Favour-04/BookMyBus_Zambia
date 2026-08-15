<?php

namespace Tests\Feature;

use App\Models\AdminAuditLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_profile_page_displays_recent_activity(): void
    {
        $admin = User::create([
            'full_name' => 'Admin User',
            'email' => 'admin@example.com',
            'phone_number' => '0977000003',
            'password' => bcrypt('password123'),
            'role' => 'admin',
        ]);

        AdminAuditLog::create([
            'admin_id' => $admin->id,
            'event' => 'profile.updated',
            'description' => 'Updated own admin profile',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'TestAgent',
        ]);

        $response = $this->actingAs($admin, 'admin')->get(route('admin.profile'));

        $response->assertStatus(200);
        $response->assertSee('Recent Activity');
        $response->assertSee('Updated Admin Profile'); // eventLabel() for profile.updated
        $response->assertSee('Updated own admin profile'); // description
    }

    public function test_admin_profile_page_shows_empty_state_when_no_activity(): void
    {
        $admin = User::create([
            'full_name' => 'Admin User',
            'email' => 'admin@example.com',
            'phone_number' => '0977000003',
            'password' => bcrypt('password123'),
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin, 'admin')->get(route('admin.profile'));

        $response->assertStatus(200);
        $response->assertSee('No recent activity yet.');
    }
}
