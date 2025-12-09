<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('market_trends', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id')->nullable(); // Category the trend is for
            $table->unsignedBigInteger('subcategory_id')->nullable(); // Subcategory if applicable
            $table->unsignedBigInteger('location_id')->nullable(); // Specific location the trend applies to
            $table->string('region')->nullable(); // Region name
            $table->string('country')->default('Nigeria'); // Country the trend applies to
            $table->string('city')->nullable(); // City-specific trends
            $table->string('trend_type')->default('price'); // price, volume, seasonal, demand, competition
            $table->json('trend_data')->nullable(); // Contains trend information e.g. {dates: [...], values: [...] }
            $table->decimal('current_value', 15, 2)->nullable(); // Current market value for this trend
            $table->decimal('baseline_value', 15, 2)->nullable(); // Baseline value for comparison
            $table->decimal('change_percentage', 8, 2)->default(0.00); // Percentage change
            $table->string('trend_direction')->default('stable'); // up, down, stable, volatile
            $table->integer('trend_strength')->default(5); // 1-10 scale strength of trend
            $table->decimal('confidence_level', 5, 2)->default(75.00); // 0-100% confidence in the trend
            $table->json('seasonal_pattern')->nullable(); // Seasonal pattern data
            $table->json('forecast_data')->nullable(); // {predictions: [...], confidence_intervals: [...]}
            $table->json('seasonal_insights')->nullable(); // Seasonal insights
            $table->json('demand_patterns')->nullable(); // Demand patterns
            $table->json('price_volatility')->nullable(); // Price volatility data
            $table->json('peak_seasons')->nullable(); // Peak seasons for this category
            $table->json('off_peak_seasons')->nullable(); // Off-peak seasons
            $table->decimal('growth_rate', 8, 2)->default(0.00); // Growth rate percentage
            $table->decimal('saturation_level', 5, 2)->default(0.00); // 0-100% - how saturated the market is
            $table->integer('competition_index')->default(50); // Competition intensity (1-100 scale)
            $table->decimal('market_health_score', 5, 2)->default(50.00); // Overall market health score (0-100)
            $table->timestamp('last_updated')->nullable();
            $table->timestamp('next_prediction_date')->nullable();
            $table->string('trend_period')->default('monthly'); // daily, weekly, monthly, quarterly, yearly
            $table->string('data_source')->default('platform'); // Source of trend data (platform, external, mixed)
            $table->boolean('is_active')->default(true); // Whether trend is currently active
            $table->json('custom_fields')->nullable(); // For extendability
            $table->json('metadata')->nullable(); // Additional metadata
            $table->timestamps(); // created_at and updated_at

            // Indexes for better query performance
            $table->index('category_id');
            $table->index('subcategory_id');
            $table->index('location_id');
            $table->index('region');
            $table->index('country');
            $table->index('city');
            $table->index('trend_type');
            $table->index('trend_direction');
            $table->index('trend_strength');
            $table->index('confidence_level');
            $table->index(['category_id', 'trend_type']);
            $table->index(['country', 'trend_type']);
            $table->index(['city', 'trend_type']);
            $table->index(['is_active', 'last_updated']);
            $table->index('last_updated');
            $table->index('next_prediction_date');
            $table->index('saturation_level');
            $table->index('competition_index');
            $table->index('market_health_score');
            $table->index('data_source');

            // Foreign key constraints
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
            $table->foreign('subcategory_id')->references('id')->on('categories')->onDelete('set null');
            $table->foreign('location_id')->references('id')->on('locations')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('market_trends');
    }
};
