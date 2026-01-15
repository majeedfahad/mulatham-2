<?php

namespace App\Providers;

use App\Services\PostHogService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(PostHogService::class, function ($app) {
            return new PostHogService;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Authorize Pulse dashboard access
        Gate::define('viewPulse', function ($user) {
            return $user->isAdmin();
        });
    }
}
