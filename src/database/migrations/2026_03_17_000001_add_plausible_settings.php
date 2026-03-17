<?php

use Illuminate\Database\Migrations\Migration;

class AddPlausibleSettings extends Migration
{
    public function up()
    {
        \App\Setting::upsertSetting('plausible_enabled',    false,                              'Master switch for Plausible Analytics.',                    true);
        \App\Setting::upsertSetting('plausible_script_url', null,                               'Personalized Plausible script URL (pa-XXXXX.js).',          true);
        \App\Setting::upsertSetting('plausible_domain',     null,                               'Domain registered in Plausible. Defaults to APP_URL.',      true);
        \App\Setting::upsertSetting('plausible_api_url',    'https://plausible.io/api/event',   'Plausible events API endpoint (override for self-hosted).', true);
    }

    public function down()
    {
        \App\Setting::where('setting', 'plausible_enabled')->delete();
        \App\Setting::where('setting', 'plausible_script_url')->delete();
        \App\Setting::where('setting', 'plausible_domain')->delete();
        \App\Setting::where('setting', 'plausible_api_url')->delete();
    }
}
