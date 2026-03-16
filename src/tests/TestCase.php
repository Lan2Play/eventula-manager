<?php

namespace Tests;

use App\Setting;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Seed all application settings via RequiredDatabaseSeeder, then mark the
     * app as installed. Must be called AFTER parent::setUp() so RefreshDatabase
     * has already run the migrations.
     *
     * Using the full seeder prevents crashes in middleware that call
     * Setting::where(…)->first()->value directly (e.g. LanguageSwitcher,
     * Installed, NoPhoneNumber, AccountController, LoginController).
     */
    protected function seedMinimumSettings(): void
    {
        $this->seed(\Database\Seeders\RequiredDatabaseSeeder::class);
        // RequiredDatabaseSeeder sets installed=false; flip it to pass Installed middleware.
        Setting::where('setting', 'installed')->update(['value' => '1']);
    }
}
