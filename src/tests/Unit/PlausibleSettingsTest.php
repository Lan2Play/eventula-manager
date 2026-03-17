<?php

namespace Tests\Unit;

use App\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Unit tests for the Plausible Analytics getter/setter methods on Setting.
 *
 * All tests run against an in-memory SQLite database seeded with the minimum
 * required rows (including the plausible_* settings created by the migration).
 */
class PlausibleSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedMinimumSettings();
    }

    // -------------------------------------------------------------------------
    // plausible_enabled toggle
    // -------------------------------------------------------------------------

    #[Test]
    public function plausible_is_disabled_by_default(): void
    {
        $this->assertFalse(Setting::isPlausibleEnabled());
    }

    #[Test]
    public function enable_plausible_persists_true(): void
    {
        Setting::enablePlausible();

        $this->assertTrue(Setting::isPlausibleEnabled());
    }

    #[Test]
    public function disable_plausible_persists_false(): void
    {
        Setting::enablePlausible();
        Setting::disablePlausible();

        $this->assertFalse(Setting::isPlausibleEnabled());
    }

    // -------------------------------------------------------------------------
    // plausible_script_url
    // -------------------------------------------------------------------------

    #[Test]
    public function script_url_is_null_by_default(): void
    {
        $this->assertNull(Setting::getPlausibleScriptUrl());
    }

    #[Test]
    public function set_script_url_persists_value(): void
    {
        Setting::setPlausibleScriptUrl('https://plausible.io/js/pa-ABCDE.js');

        $this->assertSame('https://plausible.io/js/pa-ABCDE.js', Setting::getPlausibleScriptUrl());
    }

    #[Test]
    public function set_script_url_can_be_cleared(): void
    {
        Setting::setPlausibleScriptUrl('https://plausible.io/js/pa-ABCDE.js');
        Setting::setPlausibleScriptUrl(null);

        $this->assertNull(Setting::getPlausibleScriptUrl());
    }

    // -------------------------------------------------------------------------
    // plausible_domain
    // -------------------------------------------------------------------------

    #[Test]
    public function domain_is_null_by_default(): void
    {
        $this->assertNull(Setting::getPlausibleDomain());
    }

    #[Test]
    public function set_domain_persists_value(): void
    {
        Setting::setPlausibleDomain('mylan.example.com');

        $this->assertSame('mylan.example.com', Setting::getPlausibleDomain());
    }

    // -------------------------------------------------------------------------
    // plausible_api_url
    // -------------------------------------------------------------------------

    #[Test]
    public function api_url_defaults_to_plausible_io(): void
    {
        $this->assertSame('https://plausible.io/api/event', Setting::getPlausibleApiUrl());
    }

    #[Test]
    public function set_api_url_persists_self_hosted_url(): void
    {
        Setting::setPlausibleApiUrl('https://plausible.myhost.de/api/event');

        $this->assertSame('https://plausible.myhost.de/api/event', Setting::getPlausibleApiUrl());
    }
}
