<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_login_page_is_accessible(): void
    {
        $response = $this->get('/admin/login');

        $response->assertStatus(200)
            ->assertSee('Zassaf')
            ->assertSee('name="email"', false)
            ->assertSee('name="password"', false)
            ->assertSee('name="remember"', false)
            ->assertSee('Forgot password?');
    }

    public function test_unauthenticated_user_is_redirected_to_login(): void
    {
        $this->get('/admin/dashboard')->assertRedirect('/admin/login');
        $this->get('/admin/programs')->assertRedirect('/admin/login');
    }

    public function test_super_admin_can_log_in_and_reach_dashboard(): void
    {
        $response = $this->post('/admin/login', [
            'email' => env('ADMIN_EMAIL', 'admin@zassaf.com'),
            'password' => env('ADMIN_PASSWORD', 'password'),
        ]);

        $response->assertRedirect('/admin');
        $this->assertAuthenticated();

        $this->get('/admin/dashboard')->assertOk();
    }

    public function test_editor_cannot_access_admin_management(): void
    {
        $editor = User::create([
            'name' => 'Staff Editor',
            'email' => 'editor@example.test',
            'password' => Hash::make('editorpass123'),
            'role' => User::ROLE_EDITOR,
            'is_active' => true,
        ]);

        $this->actingAs($editor);

        $this->get('/admin/dashboard')->assertOk();
        $this->get('/admin/programs')->assertOk();
        $this->get('/admin/administrators')->assertForbidden();
    }

    public function test_member_cannot_access_admin_area(): void
    {
        $member = User::create([
            'name' => 'Member User',
            'email' => 'member@example.test',
            'password' => Hash::make('memberpass123'),
            'role' => User::ROLE_MEMBER,
            'is_active' => true,
        ]);

        $this->actingAs($member);

        $this->get('/admin/dashboard')->assertRedirect('/admin/login');
    }

    public function test_logout_ends_the_session(): void
    {
        $this->actingAs(User::where('email', env('ADMIN_EMAIL', 'admin@zassaf.com'))->first());

        $response = $this->post('/admin/logout');

        $response->assertRedirect('/admin/login');
        $this->assertGuest();

        $this->get('/admin/dashboard')->assertRedirect('/admin/login');
    }
}
