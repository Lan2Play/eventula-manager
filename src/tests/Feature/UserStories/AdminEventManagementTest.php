<?php

namespace Tests\Feature\UserStories;

use App\Event;
use App\EventVenue;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * User Story 4.A — As an Admin I want to manage events.
 *
 * GET  /admin/events               — list events
 * POST /admin/events               — create event
 * GET  /admin/events/{event}       — view event detail
 */
class AdminEventManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedMinimumSettings();
        Event::clearBootedModels();
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function validEventPayload(int $venueId): array
    {
        return [
            'event_name'  => 'Test LAN Party',
            'desc_short'  => 'Short description for the event.',
            'desc_long'   => 'Longer description with all the exciting details.',
            'start_date'  => now()->addMonth()->format('m/d/Y'),
            'start_time'  => '10:00',
            'end_date'    => now()->addMonth()->addDays(2)->format('m/d/Y'),
            'end_time'    => '18:00',
            'capacity'    => 50,
            'venue'       => $venueId,
        ];
    }

    // -------------------------------------------------------------------------
    // GET /admin/events — list
    // -------------------------------------------------------------------------

    #[Test]
    public function admin_can_view_events_index(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get('/admin/events')
            ->assertOk();
    }

    #[Test]
    public function non_admin_is_redirected_from_admin_events(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/admin/events')
            ->assertRedirect('/');
    }

    #[Test]
    public function guest_is_redirected_from_admin_events(): void
    {
        $this->get('/admin/events')->assertRedirect('/');
    }

    // -------------------------------------------------------------------------
    // POST /admin/events — create
    // -------------------------------------------------------------------------

    #[Test]
    public function admin_can_create_event_with_valid_data(): void
    {
        $admin = User::factory()->admin()->create();
        $venue = EventVenue::factory()->create();

        $this->actingAs($admin)
            ->post('/admin/events', $this->validEventPayload($venue->id))
            ->assertRedirect();

        $this->assertDatabaseHas('events', ['display_name' => 'Test LAN Party']);
    }

    #[Test]
    public function event_creation_requires_event_name(): void
    {
        $admin = User::factory()->admin()->create();
        $venue = EventVenue::factory()->create();
        $payload = $this->validEventPayload($venue->id);
        unset($payload['event_name']);

        $this->actingAs($admin)
            ->post('/admin/events', $payload)
            ->assertRedirect(); // redirected back with validation errors

        $this->assertDatabaseCount('events', 0);
    }

    #[Test]
    public function event_creation_requires_capacity_to_be_integer(): void
    {
        $admin = User::factory()->admin()->create();
        $venue = EventVenue::factory()->create();
        $payload = $this->validEventPayload($venue->id);
        $payload['capacity'] = 'not-a-number';

        $this->actingAs($admin)
            ->post('/admin/events', $payload)
            ->assertRedirect();

        $this->assertDatabaseCount('events', 0);
    }

    #[Test]
    public function event_creation_rejects_duplicate_event_name(): void
    {
        $admin = User::factory()->admin()->create();
        $venue = EventVenue::factory()->create();
        // First event succeeds
        $this->actingAs($admin)->post('/admin/events', $this->validEventPayload($venue->id));
        // Second event with same name should fail
        $payloadDuplicate = $this->validEventPayload($venue->id);
        $payloadDuplicate['event_name'] = 'Test LAN Party'; // same name
        $this->actingAs($admin)
            ->post('/admin/events', $payloadDuplicate)
            ->assertRedirect();

        $this->assertDatabaseCount('events', 1);
    }

    #[Test]
    public function non_admin_cannot_create_events(): void
    {
        $user  = User::factory()->create();
        $venue = EventVenue::factory()->create();

        $this->actingAs($user)
            ->post('/admin/events', $this->validEventPayload($venue->id))
            ->assertRedirect('/');

        $this->assertDatabaseCount('events', 0);
    }

    // -------------------------------------------------------------------------
    // GET /admin/events/{event} — event detail
    // -------------------------------------------------------------------------

    #[Test]
    public function admin_can_view_event_detail_page(): void
    {
        $admin = User::factory()->admin()->create();
        $event = Event::factory()->create();

        $this->actingAs($admin)
            ->get('/admin/events/' . $event->slug)
            ->assertOk();
    }
}
