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
    // the browser's fetch — login/register/logout and profile/password/2FA via
    // useAccount()'s backendFetch, plus forgot-password/reset-password/email/*
    // via useAuth()'s backendFetch (issue #19 Phase 5's login/register/forgot-
    // /reset-password/email-verification pages) — without CORS enabled here
    // the browser silently blocks reading the response even though Laravel
    // still sets the session cookie, so the request appears to hang/no-op
    // from the frontend.
    'paths' => [
        'api/*', 'sanctum/csrf-cookie', 'login', 'logout', 'register', 'user/*',
        'forgot-password', 'reset-password', 'email/*',
    ],

    'allowed_methods' => ['*'],

    'allowed_origins' => \App\Support\FrontendUrls::all(),

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];
