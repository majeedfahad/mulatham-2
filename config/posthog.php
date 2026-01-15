<?php

return [

    /*
    |--------------------------------------------------------------------------
    | PostHog API Key
    |--------------------------------------------------------------------------
    |
    | This is your PostHog project API key. You can find this in your PostHog
    | project settings under "Project API Key".
    |
    */

    'api_key' => env('POSTHOG_API_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | PostHog Host
    |--------------------------------------------------------------------------
    |
    | The PostHog instance host. Use 'https://app.posthog.com' for cloud,
    | or your self-hosted instance URL.
    |
    */

    'host' => env('POSTHOG_HOST', 'https://app.posthog.com'),

    /*
    |--------------------------------------------------------------------------
    | Enable PostHog
    |--------------------------------------------------------------------------
    |
    | Enable or disable PostHog tracking. Useful for disabling in development.
    |
    */

    'enabled' => env('POSTHOG_ENABLED', false),

];
