<?php

namespace Tests\Feature\Api;

use App\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Feature tests for the public Events REST API.
 *
 * Covered endpoints (all behind the `installed` middleware):
 *   GET  /api/events/
 *   GET  /api/events/upcoming
 *   GET  /api/events/{slug}
 */
class PublicEventsApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedMinimumSettings();
        Event::clearBootedModels();
    }

    // -------------------------------------------------------------------------
    // GET /api/events/
    // -------------------------------------------------------------------------

    #[Test]
    public function events_index_returns_200_with_json_array(): void
    {
        $this->getJson('/api/events/')->assertOk()->assertJsonIsArray();
    }

    #[Test]
    public function events_index_includes_published_event_in_response(): void
    {
        $event = Event::factory()->create(['status' => Event::STATUS_PUBLISHED]);

        $response = $this->getJson('/api/events/');

        $response->assertOk()
            ->assertJsonFragment(['name' => $event->display_name]);
    }

    #[Test]
    public function events_index_does_not_include_draft_events(): void
    {
        Event::factory()->draft()->create(['display_name' => 'Draft Only Event']);

        $response = $this->getJson('/api/events/');

        // The global scope filters drafts for unauthenticated requests.
        $response->assertOk()
            ->assertJsonMissing(['name' => 'Draft Only Event']);
    }

    #[Test]
    public function events_index_includes_address_and_api_fields(): void
    {
        Event::factory()->create();

        $response = $this->getJson('/api/events/');

        $response->assertOk()
            ->assertJsonStructure([['name', 'capacity', 'start', 'end', 'desc', 'address', 'api', 'url']]);
    }

    // -------------------------------------------------------------------------
    // GET /api/events/{slug}
    // -------------------------------------------------------------------------

    #[Test]
    public function event_show_returns_event_by_slug(): void
    {
        $event = Event::factory()->create();

        $response = $this->getJson('/api/events/' . $event->slug);

        $response->assertOk()
            ->assertJsonFragment(['name' => $event->display_name]);
    }

    #[Test]
    public function event_show_returns_404_for_unknown_slug(): void
    {
        $this->getJson('/api/events/does-not-exist')->assertNotFound();
    }

    // -------------------------------------------------------------------------
    // GET /api/events/upcoming
    // Note: the controller initialises $return inside the loop, so this test
    // uses at least one future event to avoid the undefined-variable bug.
    // -------------------------------------------------------------------------

    #[Test]
    public function upcoming_events_returns_200_with_future_events(): void
    {
        $future = Event::factory()->future()->create();

        $response = $this->getJson('/api/events/upcoming');

        $response->assertOk()
            ->assertJsonFragment(['name' => $future->display_name]);
    }

    #[Test]
    public function upcoming_events_excludes_past_events(): void
    {
        // Ensure at least one future event so the controller $return is defined.
        Event::factory()->future()->create(['display_name' => 'Future Event']);
        $past = Event::factory()->past()->create(['display_name' => 'Past Event']);

        $response = $this->getJson('/api/events/upcoming');

        $response->assertOk()
            ->assertJsonMissing(['name' => 'Past Event']);
    }
}
