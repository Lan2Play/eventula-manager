<?php

namespace Tests\Feature\Plausible;

use App\Setting;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Feature tests for the Plausible Analytics section in Admin → Settings → API.
 *
 * Covers:
 *  - The settings page renders Plausible fields with DB values
 *  - Enable / disable toggle persists correctly
 *  - Script URL, Domain, API URL fields save correctly
 *  - Access control: guest and non-admin are rejected
 *  - ENV_OVERRIDE note is shown when PLAUSIBLE_ENABLE is set in environment
 */
class PlausibleAdminSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedMinimumSettings();
    }

    // =========================================================================
    // Access control
    // =========================================================================

    #[Test]
    public function guest_cannot_access_api_settings_page(): void
    {
        $this->get('/admin/settings/api')->assertRedirect('/');
    }

    #[Test]
    public function regular_user_cannot_access_api_settings_page(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/admin/settings/api')
            ->assertRedirect('/');
    }

    #[Test]
    public function admin_can_access_api_settings_page(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->get('/admin/settings/api')
            ->assertOk();
    }

    // =========================================================================
    // Settings page renders Plausible section with current DB values
    // =========================================================================

    #[Test]
    public function settings_page_renders_plausible_fields(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->get('/admin/settings/api')
            ->assertOk()
            ->assertSee('Plausible Analytics')
            ->assertSee('plausible_script_url')
            ->assertSee('plausible_domain')
            ->assertSee('plausible_api_url');
    }

    #[Test]
    public function settings_page_shows_current_script_url(): void
    {
        Setting::setPlausibleScriptUrl('https://plausible.io/js/pa-TEST.js');

        $this->actingAs(User::factory()->admin()->create())
            ->get('/admin/settings/api')
            ->assertSee('https://plausible.io/js/pa-TEST.js');
    }

    #[Test]
    public function settings_page_shows_current_api_url(): void
    {
        Setting::setPlausibleApiUrl('https://plausible.myhost.de/api/event');

        $this->actingAs(User::factory()->admin()->create())
            ->get('/admin/settings/api')
            ->assertSee('https://plausible.myhost.de/api/event');
    }

    // =========================================================================
    // Enable / disable toggle
    // =========================================================================

    #[Test]
    public function admin_can_enable_plausible_via_settings_form(): void
    {
        $this->assertFalse(Setting::isPlausibleEnabled());

        $this->actingAs(User::factory()->admin()->create())
            ->post('/admin/settings/api', [
                'plausible_enabled'    => 'on',
                'plausible_script_url' => 'https://plausible.io/js/pa-TEST.js',
                'plausible_domain'     => '',
                'plausible_api_url'    => 'https://plausible.io/api/event',
            ])
            ->assertRedirect();

        $this->assertTrue(Setting::isPlausibleEnabled());
    }

    #[Test]
    public function admin_can_disable_plausible_via_settings_form(): void
    {
        Setting::enablePlausible();
        $this->assertTrue(Setting::isPlausibleEnabled());

        // Unchecked checkbox sends the hidden 'off' value (see the blade template).
        $this->actingAs(User::factory()->admin()->create())
            ->post('/admin/settings/api', [
                'plausible_enabled'    => 'off',
                'plausible_script_url' => 'https://plausible.io/js/pa-TEST.js',
                'plausible_domain'     => '',
                'plausible_api_url'    => 'https://plausible.io/api/event',
            ])
            ->assertRedirect();

        $this->assertFalse(Setting::isPlausibleEnabled());
    }

    // =========================================================================
    // Saving individual fields
    // =========================================================================

    #[Test]
    public function admin_can_save_script_url(): void
    {
        $url = 'https://plausible.io/js/pa-ABCDE.js';

        $this->actingAs(User::factory()->admin()->create())
            ->post('/admin/settings/api', [
                'plausible_enabled'    => 'off',
                'plausible_script_url' => $url,
                'plausible_domain'     => '',
                'plausible_api_url'    => 'https://plausible.io/api/event',
            ])
            ->assertRedirect();

        $this->assertSame($url, Setting::getPlausibleScriptUrl());
    }

    #[Test]
    public function admin_can_save_domain_override(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->post('/admin/settings/api', [
                'plausible_enabled'    => 'off',
                'plausible_script_url' => '',
                'plausible_domain'     => 'mylan.example.com',
                'plausible_api_url'    => 'https://plausible.io/api/event',
            ])
            ->assertRedirect();

        $this->assertSame('mylan.example.com', Setting::getPlausibleDomain());
    }

    #[Test]
    public function admin_can_save_self_hosted_api_url(): void
    {
        $apiUrl = 'https://plausible.myhost.de/api/event';

        $this->actingAs(User::factory()->admin()->create())
            ->post('/admin/settings/api', [
                'plausible_enabled'    => 'off',
                'plausible_script_url' => '',
                'plausible_domain'     => '',
                'plausible_api_url'    => $apiUrl,
            ])
            ->assertRedirect();

        $this->assertSame($apiUrl, Setting::getPlausibleApiUrl());
    }

    #[Test]
    public function successful_update_flashes_success_message(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->post('/admin/settings/api', [
                'plausible_enabled'    => 'off',
                'plausible_script_url' => '',
                'plausible_domain'     => '',
                'plausible_api_url'    => 'https://plausible.io/api/event',
            ])
            ->assertSessionHas('alert-success');
    }

    // =========================================================================
    // ENV kill-switch note in UI
    // =========================================================================

    #[Test]
    public function env_override_warning_not_shown_when_plausible_enable_not_set(): void
    {
        // PLAUSIBLE_ENABLE is not set in phpunit.xml, so env() returns null.
        $this->actingAs(User::factory()->admin()->create())
            ->get('/admin/settings/api')
            ->assertDontSee('PLAUSIBLE_ENABLE</code> is set in the environment', escape: false);
    }
}
