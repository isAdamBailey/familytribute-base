<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    // api/* + sanctum/csrf-cookie cover the JSON API; the rest are Fortify's own
    // routes (not under api/*) that the Nuxt SPA also calls cross-origin via
    // the browser's fetch (login/register directly, profile/password/2FA via
    // useAccount()'s backendFetch) — without CORS enabled here the browser
    // silently blocks reading the response even though Laravel still sets the
    // session cookie, so the request appears to hang/no-op from the frontend.
    // forgot-password/reset-password/email/* aren't called cross-origin yet
    // (Phase 5's auth pages aren't built), but are listed now so that phase
    // doesn't have to rediscover this same silent-block failure mode.
    'paths' => [
        'api/*', 'sanctum/csrf-cookie', 'login', 'logout', 'register', 'user/*',
        'forgot-password', 'reset-password', 'email/*',
    ],

    'allowed_methods' => ['*'],

    'allowed_origins' => array_filter(explode(',', env('FRONTEND_URLS', env('APP_URL', 'http://localhost')))),

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];
