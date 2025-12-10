<?php

namespace App\Providers;

use App\Services\FeatureFlagService;
use Illuminate\Support\ServiceProvider;

class FeatureFlagServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Bind FeatureFlagService to the container
        $this->app->singleton('feature-flag', function ($app) {
            return new FeatureFlagService();
        });

        $this->app->singleton(FeatureFlagService::class, function ($app) {
            return new FeatureFlagService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}