<?php

namespace Tests\Feature\UserStories;

use App\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * User Story 1.B — As a Guest I want to browse the public pages.
 *
 * Verifies that all publicly accessible pages respond with 200 OK
 * without requiring an authenticated session.
 */
class GuestBrowsingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedMinimumSettings();
        Event::clearBootedModels();
    }

    // -------------------------------------------------------------------------
    // Public pages (US 1.B)
    // -------------------------------------------------------------------------

    #[Test]
    public function guest_can_view_home_page(): void
    {
        $this->get('/')->assertOk();
    }

    #[Test]
    public function guest_can_view_news_listing(): void
    {
        $this->get('/news')->assertOk();
    }

    #[Test]
    public function guest_can_view_events_listing(): void
    {
        $this->get('/events')->assertOk();
    }

    #[Test]
    public function guest_can_view_gallery(): void
    {
        $this->get('/gallery')->assertOk();
    }

    #[Test]
    public function guest_can_view_help_page(): void
    {
        $this->get('/help')->assertOk();
    }

    #[Test]
    public function guest_can_view_about_page(): void
    {
        $this->get('/about')->assertOk();
    }

    #[Test]
    public function guest_can_view_terms_page(): void
    {
        $this->get('/terms')->assertOk();
    }

    #[Test]
    public function guest_can_view_legal_notice_page(): void
    {
        $this->get('/legalnotice')->assertOk();
    }

    #[Test]
    public function guest_can_view_login_page(): void
    {
        $this->get('/login')->assertOk();
    }

    // -------------------------------------------------------------------------
    // Protected pages redirect unauthenticated visitors (US 1.B negative)
    // -------------------------------------------------------------------------

    #[Test]
    public function guest_is_redirected_away_from_account_page(): void
    {
        $this->get('/account')->assertRedirect();
    }

    #[Test]
    public function guest_is_redirected_away_from_admin_area(): void
    {
        // Admin middleware redirects non-admin (and guests) to '/'
        $this->get('/admin')->assertRedirect('/');
    }
}
