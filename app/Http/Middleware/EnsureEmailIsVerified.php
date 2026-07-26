<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * JSON-only replacement for Illuminate's EnsureEmailIsVerified.
 *
 * The framework version redirects unverified users to the `verification.notice`
 * route, which no longer exists here: Fortify's view routes are disabled and
 * Nuxt owns the /email/verify page (issue #19 Phases 5/6). Rather than depend
 * on every caller setting `Accept: application/json` to avoid a RouteNotFound
 * exception, this always answers with a 403 the frontend can branch on.
 */
class EnsureEmailIsVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user
            || ($user instanceof MustVerifyEmail && ! $user->hasVerifiedEmail())) {
            return response()->json([
                'message' => 'Your email address is not verified.',
            ], 403);
        }

        return $next($request);
    }
}
