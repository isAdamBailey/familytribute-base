<?php

namespace Tests\Feature;

use Tests\TestCase;

class CorsConfigTest extends TestCase
{
    /**
     * Fortify's own routes (login/register/logout/user/*) aren't under
     * api/*, but the Nuxt SPA calls them cross-origin via the browser's
     * fetch (see frontend/app/composables/useAuth.ts and useAccount.ts).
     * Without CORS enabled for them, the browser silently blocks reading
     * the response even though Laravel still sets the session cookie —
     * the request just appears to hang/no-op from the frontend.
     */
    public function test_cors_paths_cover_fortify_routes_called_cross_origin()
    {
        $paths = config('cors.paths');

        $this->assertContains('api/*', $paths);
        $this->assertContains('sanctum/csrf-cookie', $paths);
        $this->assertContains('login', $paths);
        $this->assertContains('logout', $paths);
        $this->assertContains('register', $paths);
        $this->assertContains('user/*', $paths);
        $this->assertContains('forgot-password', $paths);
        $this->assertContains('reset-password', $paths);
        $this->assertContains('email/*', $paths);
    }
}
