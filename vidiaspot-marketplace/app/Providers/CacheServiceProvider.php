<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\RedisService;
use App\Services\SessionManager;
use App\Services\ElasticsearchService;

class CacheServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Bind RedisService
        $this->app->singleton(RedisService::class, function ($app) {
            return new RedisService();
        });

        // Bind SessionManager
        $this->app->singleton(SessionManager::class, function ($app) {
            return new SessionManager($app->make(RedisService::class));
        });

        // Bind ElasticsearchService
        $this->app->singleton(ElasticsearchService::class, function ($app) {
            return new ElasticsearchService();
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