<?php

namespace Tests\Unit;

use App\CreditLog;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Unit tests for the User model — credit management, admin flag, and related logic.
 */
class UserModelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedMinimumSettings();
        // Reset Event/User boot caches so global scopes reflect current auth state.
        \App\Event::clearBootedModels();
    }

    // -------------------------------------------------------------------------
    // checkCredit
    // -------------------------------------------------------------------------

    #[Test]
    public function check_credit_returns_true_when_credit_is_sufficient(): void
    {
        $user = User::factory()->create(['credit_total' => 50]);

        $this->assertTrue($user->checkCredit(-30));
    }

    #[Test]
    public function check_credit_returns_true_when_adding_credit(): void
    {
        $user = User::factory()->create(['credit_total' => 0]);

        $this->assertTrue($user->checkCredit(100));
    }

    #[Test]
    public function check_credit_returns_false_when_credit_would_go_negative(): void
    {
        $user = User::factory()->create(['credit_total' => 10]);

        $this->assertFalse($user->checkCredit(-20));
    }

    #[Test]
    public function check_credit_returns_true_at_exact_zero_balance(): void
    {
        $user = User::factory()->create(['credit_total' => 10]);

        $this->assertTrue($user->checkCredit(-10));
    }

    // -------------------------------------------------------------------------
    // editCredit
    // -------------------------------------------------------------------------

    #[Test]
    public function edit_credit_increases_total_and_creates_add_log(): void
    {
        $user = User::factory()->create(['credit_total' => 0]);

        $user->editCredit(100, false, 'Test Add');

        $this->assertEquals(100, $user->fresh()->credit_total);
        $this->assertDatabaseHas('credit_log', [
            'user_id' => $user->id,
            'action'  => 'ADD',
            'amount'  => 100,
            'reason'  => 'Test Add',
        ]);
    }

    #[Test]
    public function edit_credit_decreases_total_and_creates_sub_log(): void
    {
        $user = User::factory()->create(['credit_total' => 50]);

        $user->editCredit(-20, false, 'Test Sub');

        $this->assertEquals(30, $user->fresh()->credit_total);
        $this->assertDatabaseHas('credit_log', [
            'user_id' => $user->id,
            'action'  => 'SUB',
            'amount'  => -20,
        ]);
    }

    #[Test]
    public function edit_credit_with_zero_amount_creates_no_log(): void
    {
        $user = User::factory()->create(['credit_total' => 10]);

        $user->editCredit(0);

        $this->assertDatabaseCount('credit_log', 0);
    }

    #[Test]
    public function edit_credit_uses_buy_action_when_buy_flag_is_true(): void
    {
        $user = User::factory()->create(['credit_total' => 100]);

        $user->editCredit(-40, false, 'Shop purchase', true);

        $this->assertDatabaseHas('credit_log', [
            'user_id' => $user->id,
            'action'  => 'BUY',
            'amount'  => -40,
        ]);
    }

    // -------------------------------------------------------------------------
    // getAdmin
    // -------------------------------------------------------------------------

    #[Test]
    public function get_admin_returns_false_for_regular_user(): void
    {
        $user = User::factory()->create(['admin' => false]);

        $this->assertFalse((bool) $user->getAdmin());
    }

    #[Test]
    public function get_admin_returns_true_for_admin_user(): void
    {
        $user = User::factory()->admin()->create();

        $this->assertTrue((bool) $user->getAdmin());
    }
}
