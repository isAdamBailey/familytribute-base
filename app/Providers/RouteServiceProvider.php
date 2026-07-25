<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    public const HOME = '/dashboard';

    /**
     * The controller namespace for the application.
     *
     * When present, controller route declarations will automatically be prefixed with this namespace.
     *
     * @var string|null
     */
    // protected $namespace = 'App\\Http\\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::prefix('api')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/web.php'));
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            // The Nuxt frontend (issue #19) hits /api/* far more heavily than the
            // old Inertia app ever did — every SSR page render fires several
            // requests (site-settings, tagging options, user, the page's own
            // resource), so a full e2e run can legitimately clear 60 req/min from
            // a single IP well before the DB-level assertions it's covering run
            // out. Raise the ceiling in local/testing/e2e so throttling never
            // masks real coverage as flaky 429s; production keeps the original
            // limit.
            $perMinute = app()->environment(['local', 'testing', 'e2e']) ? 1000 : 60;

            return Limit::perMinute($perMinute)->by(optional($request->user())->id ?: $request->ip());
        });
    }
}
