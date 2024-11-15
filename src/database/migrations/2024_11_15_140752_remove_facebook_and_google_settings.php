<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use App\ApiKey;
use App\Setting;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Remove specific ApiKey entries
        ApiKey::where('key', 'facebook_pixel_id')->delete();
        ApiKey::where('key', 'facebook_app_id')->delete();
        ApiKey::where('key', 'facebook_app_secret')->delete();
        ApiKey::where('key', 'google_analytics_tracking_id')->delete();

        // Remove specific Setting entries
        Setting::where('setting', 'social_facebook_page_access_token')->delete();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        ApiKey::firstOrCreate(
            ['key' => 'facebook_pixel_id'],
            [
                'value' => env('FACEBOOK_PIXEL_ID', null),
            ]
        );
        ApiKey::firstOrCreate(
            ['key' => 'facebook_app_id'],
            [
                'value' => env('FACEBOOK_APP_ID', null),
            ]
        );
        ApiKey::firstOrCreate(
            ['key' => 'facebook_app_secret'],
            [
                'value' => env('FACEBOOK_APP_SECRET', null),
            ]
        );
        ApiKey::firstOrCreate(
            ['key'          => 'google_analytics_tracking_id'],
            [
                'value'     => env('GOOGLE_ANALYTICS_TRACKING_ID', null),
            ]
        );
        Setting::firstOrCreate(
            ['setting' => 'social_facebook_page_access_token'],
            [
                'value' => null,
                'default' => true,
            ]
        );
    }
};
