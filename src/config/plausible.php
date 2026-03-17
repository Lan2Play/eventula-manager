<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Plausible Analytics Script URL
    |--------------------------------------------------------------------------
    |
    | Set PLAUSIBLE_SCRIPT_URL to the personalized script URL shown in your
    | Plausible site settings (e.g. https://plausible.io/js/pa-XXXXX.js).
    | Leave empty to disable Plausible tracking entirely.
    |
    */

    'script_url' => env('PLAUSIBLE_SCRIPT_URL'),

    /*
    |--------------------------------------------------------------------------
    | Plausible Domain
    |--------------------------------------------------------------------------
    |
    | The domain registered in Plausible. Defaults to APP_URL when not set.
    | Set PLAUSIBLE_DOMAIN when your public analytics domain differs from
    | APP_URL (e.g. you track 'example.com' but APP_URL is an internal host).
    |
    */

    'domain' => env('PLAUSIBLE_DOMAIN', env('APP_URL')),

    /*
    |--------------------------------------------------------------------------
    | Plausible Event Endpoint Path
    |--------------------------------------------------------------------------
    |
    | The local path the Plausible script POSTs events to. Keep this generic
    | to avoid being blocked by ad blockers. Change PLAUSIBLE_EVENT_PATH
    | if /api/event conflicts with another route in your application.
    |
    */

    'event_path' => env('PLAUSIBLE_EVENT_PATH', 'api/event'),

    /*
    |--------------------------------------------------------------------------
    | Plausible API Base URL
    |--------------------------------------------------------------------------
    |
    | The base URL of the Plausible instance events are forwarded to.
    | Change PLAUSIBLE_API_BASE_URL to point at a self-hosted Plausible
    | server (e.g. https://plausible.yourdomain.com).
    |
    */

    'api_base_url' => env('PLAUSIBLE_API_BASE_URL', 'https://plausible.io'),

];
