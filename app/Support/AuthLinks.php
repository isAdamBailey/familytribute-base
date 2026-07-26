<?php

namespace App\Support;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\URL;

/**
 * Builds the password-reset and email-verification links emailed to users,
 * pointed at `config('app.frontend_url')` (issue #19 Phase 5) instead of this
 * app's own routes. When FRONTEND_URLS is unset, `frontend_url` falls back to
 * APP_URL, so these produce the exact same links Fortify's defaults would —
 * only environments with a real Nuxt origin configured get redirected there.
 *
 * Shared between AppServiceProvider's notification overrides (real emails)
 * and E2eSignedUrlCommand (Playwright's signed-link shortcuts), so both stay
 * in sync with whichever frontend is actually configured.
 */
class AuthLinks
{
    public static function frontendOrigin(): string
    {
        return rtrim((string) config('app.frontend_url'), '/');
    }

    public static function verificationUrl(MustVerifyEmail $notifiable): string
    {
        $id = $notifiable->getKey();
        $hash = sha1($notifiable->getEmailForVerification());

        // Build the real signed backend URL first so the signature is computed
        // over Fortify's own route (id/hash path + expires query) — the Nuxt
        // click-through page then replays this exact query string against the
        // backend, so the signature it sends back still validates.
        $backendUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(config('auth.verification.expire', 60)),
            ['id' => $id, 'hash' => $hash]
        );

        $query = parse_url($backendUrl, PHP_URL_QUERY);

        return sprintf('%s/email/verify/%s/%s?%s', self::frontendOrigin(), $id, $hash, $query);
    }

    public static function passwordResetUrl(string $email, string $token): string
    {
        return sprintf('%s/reset-password/%s?email=%s', self::frontendOrigin(), $token, urlencode($email));
    }
}
