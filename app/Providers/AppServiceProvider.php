<?php

namespace App\Providers;

use App\Support\AuthLinks;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if (! $this->app->environment('production')) {
            $this->app->register('App\Providers\FakerServiceProvider');
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        JsonResource::withoutWrapping();

        // Point emailed auth links at the Nuxt frontend (issue #19 Phase 5),
        // see App\Support\AuthLinks for why this is a no-op when FRONTEND_URLS
        // is unset.
        VerifyEmail::createUrlUsing(fn ($notifiable) => AuthLinks::verificationUrl($notifiable));
        ResetPassword::createUrlUsing(fn ($notifiable, string $token) => AuthLinks::passwordResetUrl($notifiable->getEmailForPasswordReset(), $token));
    }
}
