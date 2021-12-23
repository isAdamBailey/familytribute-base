<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link rel="home" href="{{ config('app.url') }}">
        <link rel="icon" href="/favicon.ico">
        <title inertia>{{ config('app.name', 'FamilyTribute') }}</title>
        <meta inertia name="description" content="{{ config('app.meta.description') }}"/>
        <meta inertia  name="keywords" content="{{ config('app.meta.keywords') }}" />
        <meta property="og:image" content="/images/{{ config('app.meta.social_image') }}"/>
        <meta property="og:image:width" content="1200" />
        <meta property="og:image:height" content="630" />
        <meta property="og:site_name" content="{{ config('app.name', 'FamilyTribute') }}"/>
        <meta property="og:url" content="{{ config('app.url') }}" />
        <meta property="og:title" content="{{ config('app.name', 'FamilyTribute') }}" />
        <meta property="og:description" content="{{ config('app.meta.description') }}"/>
        <meta property="og:type" content="website" />
        <meta name="twitter:title" content="{{ config('app.name', 'FamilyTribute') }}"/>
        <meta name="twitter:site" content="@FamilyTribute"/>
        <meta name="twitter:creator" content="@isAdamBailey"/>
        <meta name="twitter:description" content="{{ config('app.meta.description') }}"/>
        <meta name="twitter:card" content="summary_large_image"/>
        <meta name="twitter:image:alt" content="{{ config('app.name', 'FamilyTribute') }}">

        @env ('production')
            <!-- Global site tag (gtag.js) - Google Analytics -->
                <script async src="https://www.googletagmanager.com/gtag/js?id={{ config('services.google.site_tag') }}"></script>
                <script>
                    window.dataLayer = window.dataLayer || [];
                    function gtag(){dataLayer.push(arguments);}
                    gtag('js', new Date());

                    gtag('config', '{{ config('services.google.site_tag') }}');
                </script>
        @endenv

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Gwendolyn:wght@700&display=swap" rel="stylesheet">

        <!-- Styles -->
        <link rel="stylesheet" href="{{ mix('css/app.css') }}">

        <!-- Scripts -->
        @routes
        <script src="{{ mix('js/app.js') }}" defer></script>
    </head>
    <body class="font-sans antialiased">
        @inertia

        @env ('local')
            <script src="http://localhost:3000/browser-sync/browser-sync-client.js"></script>
        @endenv
    </body>
</html>
