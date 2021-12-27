<?php

use Butschster\Head\MetaTags\Viewport;

return [
    /*
     * Meta title section
     */
    'title' => [
        'default' => env('APP_NAME'),
        'separator' => '-',
        'max_length' => 100,
    ],

    /*
     * Meta description section
     */
    'description' => [
        'default' => env('META_DESCRIPTION'),
        'max_length' => 100,
    ],

    /*
     * Meta keywords section
     */
    'keywords' => [
        'default' => env('META_KEYWORDS'),
        'max_length' => 255,
    ],

    'image' => [
        'default' => env('META_SOCIAL_IMAGE'),
    ],

    'twitter' => [
        'site' => env('META_TWITTER_SITE'),
        'creator' => env('META_TWITTER_CREATOR'),
    ],

    /*
     * Default packages
     *
     * Packages, that should be included everywhere
     */
    'packages' => [],

    'charset' => 'utf-8',
    'robots' => null,
    'viewport' => Viewport::RESPONSIVE,
    'csrf_token' => false,
];
