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
        Schema::create('price_monitoring', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('ad_id');
            $table->string('tracked_product_name'); // Name of the product being tracked
            $table->decimal('current_price', 10, 2)->default(0.00);
            $table->json('competitor_prices')->nullable(); // Array of competitor prices {name, price, source}
            $table->json('historical_prices')->nullable(); // Historical price data with timestamps
            $table->string('price_trend')->default('stable'); // up, down, stable, volatile
            $table->json('optimization_suggestions')->nullable(); // Price optimization suggestions
            $table->json('recommended_actions')->nullable(); // Recommended pricing actions
            $table->string('monitoring_strategy')->default('standard'); // standard, aggressive, defensive
            $table->json('alert_thresholds')->nullable(); // {percentage_change, absolute_change}
            $table->timestamp('last_updated')->nullable();
            $table->timestamp('next_update')->nullable();
            $table->decimal('price_elasticity', 5, 2)->default(0.00); // Price elasticity coefficient
            $table->string('market_position')->default('competitive'); // leader, competitive, follower
            $table->decimal('competitiveness_score', 5, 2)->default(50.00); // Competitiveness score (0-100)
            $table->decimal('revenue_impact_prediction', 10, 2)->default(0.00); // Revenue impact prediction
            $table->json('demand_fluctuation_tracking')->nullable(); // Demand fluctuations over time
            $table->json('seasonal_pricing_adjustments')->nullable(); // Seasonal pricing adjustments
            $table->json('automated_repricing_rules')->nullable(); // Automated repricing rules
            $table->json('integration_sources')->nullable(); // Sources for price comparison
            $table->boolean('is_active')->default(true);
            $table->json('custom_rules')->nullable(); // Custom pricing rules
            $table->timestamps();

            // Indexes
            $table->index('user_id');
            $table->index('ad_id');
            $table->index('tracked_product_name');
            $table->index('current_price');
            $table->index('price_trend');
            $table->index('is_active');
            $table->index(['user_id', 'is_active']); // For user's active monitoring
            $table->index(['ad_id', 'is_active']); // For ad's active monitoring
            $table->index(['current_price', 'competitiveness_score']); // For pricing optimization
            $table->index('last_updated');
            $table->index('next_update');
            $table->index(['next_update', 'is_active']); // For scheduled updates

            // Foreign key constraints
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('ad_id')->references('id')->on('ads')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('price_monitoring');
    }
};
