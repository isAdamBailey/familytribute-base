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

    // Every path this app answers on: the JSON API and Fortify (now prefixed
    // with api/, see config/fortify.php) plus Sanctum's CSRF-cookie route.
    //
    // In the single-origin production topology (issue #19 Phase 6) CORS never
    // actually engages — nginx serves Nuxt and this API from one host, so the
    // browser's requests aren't cross-origin. It still matters for local dev
    // and CI, where Nuxt runs on :3000 against the API on :8000: without CORS
    // the browser silently blocks reading the response even though Laravel
    // still sets the session cookie, so requests appear to hang from the
    // frontend.
    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => \App\Support\FrontendUrls::all(),

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];
