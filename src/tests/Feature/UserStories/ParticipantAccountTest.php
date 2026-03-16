<?php

namespace Tests\Feature\UserStories;

use App\Event;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * User Story 2.A — As a Participant I want to manage my account.
 *
 * GET  /account         — view account dashboard
 * POST /account         — update account profile
 */
class ParticipantAccountTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedMinimumSettings();
        Event::clearBootedModels();
    }

    // -------------------------------------------------------------------------
    // US 2.A — View account (GET /account)
    // -------------------------------------------------------------------------

    #[Test]
    public function authenticated_user_can_view_account_page(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/account')
            ->assertOk();
    }

    #[Test]
    public function unauthenticated_user_is_redirected_from_account_page(): void
    {
        $this->get('/account')->assertRedirect();
    }

    // -------------------------------------------------------------------------
    // US 2.A — Update profile (POST /account)
    // -------------------------------------------------------------------------

    #[Test]
    public function authenticated_user_can_update_their_profile(): void
    {
        $user = User::factory()->create(['firstname' => 'OldFirst', 'surname' => 'OldLast']);

        $response = $this->actingAs($user)->post('/account', [
            'firstname' => 'NewFirst',
            'surname'   => 'NewLast',
        ]);

        // Should redirect back (success) rather than returning validation errors.
        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'id'        => $user->id,
            'firstname' => 'NewFirst',
            'surname'   => 'NewLast',
        ]);
    }

    #[Test]
    public function unauthenticated_user_cannot_post_to_account(): void
    {
        $this->post('/account', ['username' => 'Hacker'])->assertRedirect();
    }

    // -------------------------------------------------------------------------
    // US 2.A — Banned user is blocked
    // -------------------------------------------------------------------------

    #[Test]
    public function banned_user_is_redirected_away_from_account_page(): void
    {
        $banned = User::factory()->banned()->create();

        // The 'banned' middleware redirects a banned user back.
        $this->actingAs($banned)
            ->get('/account')
            ->assertRedirect();
    }
}
