<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\AI\ProductDescriptionGeneratorService;
use App\Services\AI\ImageEnhancementService;
use App\Services\AI\ComputerVisionCategorizationService;

class AIServicesServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Bind AI services to the container
        $this->app->singleton(ProductDescriptionGeneratorService::class, function ($app) {
            return new ProductDescriptionGeneratorService(
                $app->make(\App\Services\MySqlToSqliteCacheService::class)
            );
        });
        
        $this->app->singleton(ImageEnhancementService::class, function ($app) {
            return new ImageEnhancementService(
                $app->make(\App\Services\MySqlToSqliteCacheService::class)
            );
        });
        
        $this->app->singleton(ComputerVisionCategorizationService::class, function ($app) {
            return new ComputerVisionCategorizationService(
                $app->make(\App\Services\MySqlToSqliteCacheService::class)
            );
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