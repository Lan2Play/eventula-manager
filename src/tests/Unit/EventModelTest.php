<?php

namespace Tests\Unit;

use App\Event;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Unit tests for the Event model — status constants, query scopes.
 */
class EventModelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedMinimumSettings();
        // Reset booted models so Event::boot() re-runs with current (no) auth context.
        Event::clearBootedModels();
    }

    // -------------------------------------------------------------------------
    // Constants
    // -------------------------------------------------------------------------

    #[Test]
    public function status_constants_are_defined(): void
    {
        $this->assertSame('PUBLISHED',     Event::STATUS_PUBLISHED);
        $this->assertSame('PRIVATE',       Event::STATUS_PRIVATE);
        $this->assertSame('REGISTEREDONLY', Event::STATUS_REGISTEREDONLY);
    }

    // -------------------------------------------------------------------------
    // scopeNextUpcoming
    // -------------------------------------------------------------------------

    #[Test]
    public function scope_next_upcoming_returns_future_events(): void
    {
        $future = Event::factory()->future()->create();

        $result = Event::withoutGlobalScopes()->nextUpcoming(5)->get();

        $this->assertTrue($result->contains($future));
    }

    #[Test]
    public function scope_next_upcoming_excludes_ended_events(): void
    {
        $past = Event::factory()->past()->create();

        $result = Event::withoutGlobalScopes()->nextUpcoming(5)->get();

        $this->assertFalse($result->contains($past));
    }

    #[Test]
    public function scope_next_upcoming_respects_limit(): void
    {
        Event::factory()->count(5)->future()->create();

        $result = Event::withoutGlobalScopes()->nextUpcoming(2)->get();

        $this->assertCount(2, $result);
    }

    // -------------------------------------------------------------------------
    // scopeCurrent
    // -------------------------------------------------------------------------

    #[Test]
    public function scope_current_returns_ongoing_event(): void
    {
        $ongoing = Event::factory()->ongoing()->create();

        $result = Event::withoutGlobalScopes()->current()->get();

        $this->assertTrue($result->contains($ongoing));
    }

    #[Test]
    public function scope_current_excludes_future_events(): void
    {
        $future = Event::factory()->future()->create();

        $result = Event::withoutGlobalScopes()->current()->get();

        $this->assertFalse($result->contains($future));
    }

    #[Test]
    public function scope_current_excludes_past_events(): void
    {
        $past = Event::factory()->past()->create();

        $result = Event::withoutGlobalScopes()->current()->get();

        $this->assertFalse($result->contains($past));
    }

    // -------------------------------------------------------------------------
    // Global scope — unauthenticated user sees only PUBLISHED and PRIVATE events
    // -------------------------------------------------------------------------

    #[Test]
    public function unauthenticated_users_cannot_see_draft_events(): void
    {
        $draft = Event::factory()->draft()->create();

        // Event::all() applies the boot() global scope (no auth = filter drafts out)
        $result = Event::all();

        $this->assertFalse($result->contains($draft));
    }

    #[Test]
    public function unauthenticated_users_can_see_published_events(): void
    {
        $published = Event::factory()->create(['status' => Event::STATUS_PUBLISHED]);

        $result = Event::all();

        $this->assertTrue($result->contains($published));
    }
}
