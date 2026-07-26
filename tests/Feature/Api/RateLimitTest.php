<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class RateLimitTest extends TestCase
{
    use RefreshDatabase;

    /**
     * The Nuxt frontend's SSR pages fire several /api/* requests per render,
     * so the testing/e2e environment needs a much higher ceiling than
     * production's 60/min or a full e2e run trips the throttle mid-suite.
     */
    public function test_api_rate_limit_is_raised_for_testing_environment()
    {
        $this->assertTrue(app()->environment('testing'));

        $user = User::factory()->create();

        $callback = RateLimiter::limiter('api');
        $request = \Illuminate\Http\Request::create('/api/home', 'GET');
        $request->setUserResolver(fn () => $user);

        $limit = $callback($request);

        $this->assertSame(1000, $limit->maxAttempts);
    }
}
