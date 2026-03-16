<?php

namespace Tests\Feature\Auth;

use App\Event;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Feature tests for standard email/password authentication.
 *
 * Routes under test:
 *   GET  /login               — login prompt page
 *   POST /login/standard      — credential submission
 *   GET  /logout              — session teardown
 */
class LoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedMinimumSettings();
        Event::clearBootedModels();
    }

    // -------------------------------------------------------------------------
    // Login page
    // -------------------------------------------------------------------------

    #[Test]
    public function guest_can_view_login_page(): void
    {
        $this->get('/login')->assertOk();
    }

    // -------------------------------------------------------------------------
    // POST /login/standard
    // -------------------------------------------------------------------------

    #[Test]
    public function user_is_redirected_home_after_valid_login(): void
    {
        $user = User::factory()->create([
            'email'    => 'test@example.com',
            'password' => Hash::make('secret123'),
        ]);

        $response = $this->post('/login/standard', [
            'email'    => 'test@example.com',
            'password' => 'secret123',
        ]);

        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($user);
    }

    #[Test]
    public function wrong_password_fails_login_with_validation_error(): void
    {
        User::factory()->create([
            'email'    => 'test@example.com',
            'password' => Hash::make('correct-password'),
        ]);

        $this->post('/login/standard', [
            'email'    => 'test@example.com',
            'password' => 'wrong-password',
        ])->assertRedirect(); // redirected back

        $this->assertGuest();
    }

    #[Test]
    public function unknown_email_fails_login(): void
    {
        $this->post('/login/standard', [
            'email'    => 'nobody@example.com',
            'password' => 'anything',
        ])->assertRedirect();

        $this->assertGuest();
    }

    #[Test]
    public function banned_user_cannot_log_in(): void
    {
        User::factory()->banned()->create([
            'email'    => 'banned@example.com',
            'password' => Hash::make('secret123'),
        ]);

        $this->post('/login/standard', [
            'email'    => 'banned@example.com',
            'password' => 'secret123',
        ])->assertRedirect();

        $this->assertGuest();
    }

    // -------------------------------------------------------------------------
    // GET /logout
    // -------------------------------------------------------------------------

    #[Test]
    public function authenticated_user_can_log_out(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/logout')
            ->assertRedirect();

        $this->assertGuest();
    }

    #[Test]
    public function guest_accessing_logout_is_redirected(): void
    {
        $this->get('/logout')->assertRedirect();
    }
}
