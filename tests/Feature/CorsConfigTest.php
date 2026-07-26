<?php

namespace Tests\Feature;

use Tests\TestCase;

class CorsConfigTest extends TestCase
{
    /** See config/cors.php for why these two paths are the full surface. */
    public function test_cors_paths_cover_the_full_api_surface()
    {
        $paths = config('cors.paths');

        $this->assertContains('api/*', $paths);
        $this->assertContains('sanctum/csrf-cookie', $paths);
    }
}
