<?php

namespace App\Support;

/**
 * Parses the comma-separated FRONTEND_URLS env var, shared by config/cors.php
 * (allowed origins) and config/app.php (`frontend_url`, used by AuthLinks) so
 * both configs derive from a single parsed source instead of two hand-rolled
 * copies of the same explode/filter. Falls back to this app's own APP_URL
 * when FRONTEND_URLS is unset, preserving same-origin behavior until a real
 * frontend origin is configured.
 */
class FrontendUrls
{
    public static function all(): array
    {
        $urls = array_values(array_filter(array_map('trim', explode(',', (string) env('FRONTEND_URLS', '')))));

        return $urls ?: [env('APP_URL', 'http://localhost')];
    }

    public static function first(): string
    {
        return self::all()[0];
    }
}
