<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Plausible Enabled
    |--------------------------------------------------------------------------
    |
    | Master switch. Set PLAUSIBLE_ENABLE=true to activate tracking.
    | This is also configurable in Admin → Settings → API when ENV_OVERRIDE
    | is false. Setting PLAUSIBLE_ENABLE=false in the environment always
    | overrides the database value and disables Plausible completely.
    |
    */

    'enabled' => (bool) env('PLAUSIBLE_ENABLE', false),

    /*
    |--------------------------------------------------------------------------
    | Plausible Script URL
    |--------------------------------------------------------------------------
    |
    | The personalized script URL from your Plausible site settings
    | (e.g. https://plausible.io/js/pa-XXXXX.js).
    |
    */

    'script_url' => env('PLAUSIBLE_SCRIPT_URL'),

    /*
    |--------------------------------------------------------------------------
    | Plausible Domain
    |--------------------------------------------------------------------------
    |
    | The domain registered in your Plausible site. Defaults to APP_URL.
    | Override with PLAUSIBLE_DOMAIN when the tracked domain differs from
    | APP_URL (e.g. you track 'example.com' but APP_URL is an internal host).
    |
    */

    'domain' => env('PLAUSIBLE_DOMAIN', env('APP_URL')),

    /*
    |--------------------------------------------------------------------------
    | Plausible Events API URL
    |--------------------------------------------------------------------------
    |
    | The full URL of the Plausible events endpoint that the proxy forwards
    | requests to. Change PLAUSIBLE_API_URL to point at a self-hosted instance,
    | e.g. https://plausible.yourdomain.com/api/event
    |
    */

    'api_url' => env('PLAUSIBLE_API_URL', 'https://plausible.io/api/event'),

];
