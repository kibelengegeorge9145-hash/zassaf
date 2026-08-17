<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BrandLogoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();

        Storage::fake('public');
    }

    public function test_homepage_shows_uploaded_logo_in_navbar_and_footer(): void
    {
        Storage::disk('public')->put('settings/logo.jpg', 'fake-image-bytes');

        Setting::set('logo_path', 'settings/logo.jpg');

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('class="brand-logo"', false)
            ->assertSee('/storage/settings/logo.jpg', false)
            ->assertDontSee('<span class="brand-mark" aria-hidden="true">Z</span>', false);
    }

    public function test_homepage_falls_back_to_monogram_when_no_logo_is_uploaded(): void
    {
        Setting::set('logo_path', '');

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('<span class="brand-mark" aria-hidden="true">Z</span>', false)
            ->assertDontSee('class="brand-logo"', false);
    }

    public function test_homepage_falls_back_to_monogram_when_logo_file_is_missing(): void
    {
        Setting::set('logo_path', 'settings/missing.jpg');

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('<span class="brand-mark" aria-hidden="true">Z</span>', false)
            ->assertDontSee('class="brand-logo"', false)
            ->assertDontSee('/storage/settings/missing.jpg', false);
    }

    public function test_admin_settings_preview_shows_logo_when_file_exists(): void
    {
        Storage::disk('public')->put('settings/logo.jpg', 'fake-image-bytes');

        Setting::set('logo_path', 'settings/logo.jpg');

        $admin = User::where('email', env('ADMIN_EMAIL', 'admin@zassaf.com'))->first();

        $this->actingAs($admin)->get(route('admin.settings.edit'))
            ->assertOk()
            ->assertSee('/storage/settings/logo.jpg', false);
    }

    public function test_admin_settings_preview_is_hidden_when_logo_file_is_missing(): void
    {
        Setting::set('logo_path', 'settings/missing.jpg');

        $admin = User::where('email', env('ADMIN_EMAIL', 'admin@zassaf.com'))->first();

        $this->actingAs($admin)->get(route('admin.settings.edit'))
            ->assertOk()
            ->assertDontSee('logo-preview', false);
    }
}
