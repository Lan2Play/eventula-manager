<?php

namespace Tests\Feature\Admin;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UsersLastLoginsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedMinimumSettings();
    }

    // -------------------------------------------------------------------------
    // Access Control
    // -------------------------------------------------------------------------

    #[Test]
    public function guest_is_redirected_from_last_logins_page(): void
    {
        $this->get('/admin/users/last-logins')->assertRedirect();
    }

    #[Test]
    public function regular_user_is_redirected_from_last_logins_page(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/admin/users/last-logins')
            ->assertRedirect('/');
    }

    #[Test]
    public function admin_can_access_last_logins_page(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get('/admin/users/last-logins')
            ->assertOk();
    }

    // -------------------------------------------------------------------------
    // Page Content
    // -------------------------------------------------------------------------

    #[Test]
    public function page_shows_user_with_last_login(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create(['last_login' => now()]);

        $this->actingAs($admin)
            ->get('/admin/users/last-logins')
            ->assertSee($user->username);
    }

    #[Test]
    public function page_excludes_users_who_never_logged_in(): void
    {
        $admin = User::factory()->admin()->create();
        // last_login is NOT NULL but defaults to '0000-00-00 00:00:00' for new users
        $neverLoggedIn = User::factory()->create();

        $this->actingAs($admin)
            ->get('/admin/users/last-logins')
            ->assertDontSee($neverLoggedIn->username);
    }

    #[Test]
    public function username_links_to_user_settings_page(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create(['last_login' => now()]);

        $this->actingAs($admin)
            ->get('/admin/users/last-logins')
            ->assertSee('/admin/users/' . $user->id, false);
    }

    // -------------------------------------------------------------------------
    // Search
    // -------------------------------------------------------------------------

    #[Test]
    public function search_returns_matching_username(): void
    {
        $admin = User::factory()->admin()->create();
        $match = User::factory()->create(['username' => 'findme', 'last_login' => now()]);
        $other = User::factory()->create(['username' => 'otherone', 'last_login' => now()]);

        $this->actingAs($admin)
            ->get('/admin/users/last-logins?search=findme')
            ->assertSee('findme')
            ->assertDontSee('otherone');
    }

    #[Test]
    public function search_excludes_non_matching_usernames(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->create(['username' => 'alpha', 'last_login' => now()]);
        User::factory()->create(['username' => 'beta', 'last_login' => now()]);

        $this->actingAs($admin)
            ->get('/admin/users/last-logins?search=alpha')
            ->assertDontSee('beta');
    }

    #[Test]
    public function no_search_param_returns_all_logged_in_users(): void
    {
        $admin = User::factory()->admin()->create();
        $userA = User::factory()->create(['last_login' => now()]);
        $userB = User::factory()->create(['last_login' => now()->subDay()]);

        $this->actingAs($admin)
            ->get('/admin/users/last-logins')
            ->assertSee($userA->username)
            ->assertSee($userB->username);
    }

    // -------------------------------------------------------------------------
    // Sorting
    // -------------------------------------------------------------------------

    #[Test]
    public function default_sort_is_last_login_descending(): void
    {
        $admin = User::factory()->admin()->create();
        $older = User::factory()->create(['username' => 'older_user', 'last_login' => now()->subHour()]);
        $newer = User::factory()->create(['username' => 'newer_user', 'last_login' => now()]);

        $response = $this->actingAs($admin)->get('/admin/users/last-logins');
        $content = $response->getContent();

        $this->assertLessThan(
            strpos($content, 'older_user'),
            strpos($content, 'newer_user'),
            'newer_user should appear before older_user in default (last_login desc) sort'
        );
    }

    #[Test]
    public function sort_by_last_login_ascending_reverses_order(): void
    {
        $admin = User::factory()->admin()->create();
        $older = User::factory()->create(['username' => 'older_user', 'last_login' => now()->subHour()]);
        $newer = User::factory()->create(['username' => 'newer_user', 'last_login' => now()]);

        $response = $this->actingAs($admin)
            ->get('/admin/users/last-logins?sort_by=last_login&sort_dir=asc');
        $content = $response->getContent();

        $this->assertLessThan(
            strpos($content, 'newer_user'),
            strpos($content, 'older_user'),
            'older_user should appear before newer_user when sorted last_login asc'
        );
    }

    #[Test]
    public function sort_by_username_ascending_orders_alphabetically(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->create(['username' => 'zebra', 'last_login' => now()]);
        User::factory()->create(['username' => 'apple', 'last_login' => now()->subMinute()]);

        $response = $this->actingAs($admin)
            ->get('/admin/users/last-logins?sort_by=username&sort_dir=asc');
        $content = $response->getContent();

        $this->assertGreaterThan(
            strpos($content, 'apple'),
            strpos($content, 'zebra'),
            'apple should appear before zebra when sorted by username asc'
        );
    }

    #[Test]
    public function invalid_sort_column_falls_back_to_last_login(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->create(['last_login' => now()]);

        $this->actingAs($admin)
            ->get('/admin/users/last-logins?sort_by=password&sort_dir=desc')
            ->assertOk();
    }

    // -------------------------------------------------------------------------
    // Pagination
    // -------------------------------------------------------------------------

    #[Test]
    public function pagination_renders_for_more_than_twenty_users(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->count(21)->create(['last_login' => now()]);

        $this->actingAs($admin)
            ->get('/admin/users/last-logins')
            ->assertSee('page=2', false);
    }

    // -------------------------------------------------------------------------
    // Dashboard link
    // -------------------------------------------------------------------------

    #[Test]
    public function dashboard_users_card_contains_see_more_link(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get('/admin')
            ->assertSee('/admin/users/last-logins', false);
    }
}
