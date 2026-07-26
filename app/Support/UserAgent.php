<?php

namespace App\Support;

/**
 * Minimal user-agent sniffing for the browser-sessions list (account settings
 * "log out other sessions" screen). Replaces Jetstream's bundled Agent (a
 * thin wrapper around mobiledetect/mobiledetectlib) now that Jetstream is
 * removed (issue #19 Phase 6) — this only needs to be good enough for a
 * human-readable session label, not authoritative device detection.
 */
class UserAgent
{
    public static function isDesktop(?string $userAgent): bool
    {
        if (! $userAgent) {
            return false;
        }

        return ! preg_match('/Mobile|Android|iPhone|iPad|iPod/i', $userAgent);
    }

    public static function platform(?string $userAgent): ?string
    {
        return self::match($userAgent, [
            '/Windows/i' => 'Windows',
            '/Mac OS X/i' => 'macOS',
            '/Android/i' => 'Android',
            '/iPhone|iPad|iPod/i' => 'iOS',
            '/Linux/i' => 'Linux',
        ]);
    }

    public static function browser(?string $userAgent): ?string
    {
        return self::match($userAgent, [
            '/Edg\//i' => 'Edge',
            '/OPR\/|Opera/i' => 'Opera',
            '/Chrome\//i' => 'Chrome',
            '/Firefox\//i' => 'Firefox',
            '/Safari\//i' => 'Safari',
        ]);
    }

    /** @param  array<string, string>  $patterns  ordered regex => label, first match wins */
    private static function match(?string $userAgent, array $patterns): ?string
    {
        if (! $userAgent) {
            return null;
        }

        foreach ($patterns as $pattern => $label) {
            if (preg_match($pattern, $userAgent)) {
                return $label;
            }
        }

        return null;
    }
}
