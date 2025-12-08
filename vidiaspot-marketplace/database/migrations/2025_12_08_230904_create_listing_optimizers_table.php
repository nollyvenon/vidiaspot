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
        Schema::create('listing_optimizers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('ad_id');
            $table->string('optimizer_type'); // automatic_renewal, performance_optimization, seo_enhancement, pricing_optimization
            $table->json('optimizer_config')->nullable(); // Configuration for the optimizer
            $table->json('optimization_rules')->nullable(); // Rules to determine when to optimize/renew
            $table->json('active_schedule')->nullable(); // Schedule for optimization runs
            $table->boolean('auto_renew_enabled')->default(false);
            $table->string('renewal_interval')->default('monthly'); // daily, weekly, monthly, custom
            $table->timestamp('next_renewal_date')->nullable();
            $table->integer('renewal_count')->default(0);
            $table->decimal('renewal_budget', 10, 2)->default(0.00);
            $table->json('performance_goals')->nullable(); // Target goals for listing performance
            $table->json('current_performance_metrics')->nullable(); // Current metrics against goals
            $table->json('optimization_strategies')->nullable(); // Strategies to improve performance
            $table->json('listing_variants')->nullable(); // Different versions of the listing for A/B testing
            $table->string('best_performing_variant')->nullable(); // Currently performing best version
            $table->json('conversion_probabilities')->nullable(); // Predicted conversion rates for different strategies
            $table->json('roi_calculations')->nullable(); // ROI projections for different optimization strategies
            $table->json('budget_distribution')->nullable(); // How budget is distributed across strategies
            $table->json('performance_predictions')->nullable(); // Predicted performance metrics
            $table->json('competitor_analysis')->nullable(); // Analysis of competitor listings
            $table->json('market_positioning')->nullable(); // How the listing compares to competitors
            $table->json('keyword_optimization')->nullable(); // SEO optimization data
            $table->json('content_optimization')->nullable(); // Content improvement suggestions
            $table->json('image_optimization')->nullable(); // Image improvement suggestions
            $table->json('pricing_optimization')->nullable(); // Pricing improvement suggestions
            $table->json('timing_optimization')->nullable(); // Best times to post/list
            $table->json('targeting_optimization')->nullable(); // Audience targeting suggestions
            $table->json('automated_actions')->nullable(); // Actions performed automatically
            $table->json('manual_reviews_needed')->nullable(); // Areas needing manual attention
            $table->decimal('optimization_score', 5, 2)->default(50.00); // Overall optimization score (0-100)
            $table->timestamp('last_optimization_run')->nullable();
            $table->timestamp('next_optimization_run')->nullable();
            $table->json('optimization_history')->nullable(); // History of optimization runs
            $table->json('cpa_improvements')->nullable(); // Improvements in cost per acquisition
            $table->json('ctr_improvements')->nullable(); // Improvements in click-through rate
            $table->json('cvr_improvements')->nullable(); // Improvements in conversion rate
            $table->text('notes')->nullable(); // Additional notes
            $table->json('custom_fields')->nullable(); // For extended functionality
            $table->timestamps();

            // Indexes
            $table->index('user_id');
            $table->index('ad_id');
            $table->index('optimizer_type');
            $table->index('auto_renew_enabled');
            $table->index('next_renewal_date');
            $table->index('optimization_score');
            $table->index(['user_id', 'optimizer_type']); // For user's optimizers by type
            $table->index(['ad_id', 'optimizer_type']); // For ad's optimizers by type
            $table->index(['auto_renew_enabled', 'next_renewal_date']); // For auto-renewal queue
            $table->index('last_optimization_run');
            $table->index('next_optimization_run');
            $table->index('renewal_interval');

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
        Schema::dropIfExists('listing_optimizers');
    }
};
