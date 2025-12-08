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
        Schema::create('seller_analytics', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('vendor_store_id')->nullable();
            $table->unsignedBigInteger('ad_id')->nullable(); // If analytics are for a specific ad/product
            $table->date('period_start');
            $table->date('period_end');
            $table->integer('views')->default(0);
            $table->integer('clicks')->default(0);
            $table->decimal('conversion_rate', 5, 2)->default(0.00); // Conversion rate percentage
            $table->integer('sales_count')->default(0);
            $table->decimal('sales_amount', 15, 2)->default(0.00);
            $table->decimal('average_order_value', 10, 2)->default(0.00);
            $table->decimal('customer_acquisition_cost', 10, 2)->default(0.00);
            $table->decimal('return_on_ad_spend', 5, 2)->default(0.00);
            $table->decimal('profit_margin', 5, 2)->default(0.00);
            $table->decimal('inventory_turnover_rate', 5, 2)->default(0.00);
            $table->integer('days_of_inventory_outstanding')->default(0);
            $table->decimal('customer_lifetime_value', 10, 2)->default(0.00);
            $table->decimal('repeat_purchase_rate', 5, 2)->default(0.00);
            $table->decimal('net_promoter_score', 5, 2)->default(0.00);
            $table->decimal('customer_satisfaction_score', 5, 2)->default(0.00);
            $table->decimal('response_time_average', 5, 2)->default(0.00); // Average response time in hours
            $table->decimal('dispute_rate', 5, 2)->default(0.00); // Percentage of transactions that end in disputes
            $table->decimal('return_rate', 5, 2)->default(0.00); // Percentage of orders returned
            $table->integer('fulfillment_speed')->default(0); // Average fulfillment speed in days
            $table->json('location_performance')->nullable(); // Performance by location
            $table->json('category_performance')->nullable(); // Performance by category
            $table->json('product_performance')->nullable(); // Performance by individual products
            $table->json('seasonal_trends')->nullable(); // Seasonal sales trends
            $table->json('competitor_analysis')->nullable(); // Competitor performance analysis
            $table->json('market_share_estimates')->nullable(); // Estimated market share
            $table->json('pricing_effectiveness')->nullable(); // Effectiveness of pricing strategies
            $table->json('cross_sell_opportunities')->nullable(); // Cross-selling opportunities
            $table->json('up_sell_opportunities')->nullable(); // Up-selling opportunities
            $table->json('customer_segmentation_data')->nullable(); // Data about customer segments
            $table->json('retention_metrics')->nullable(); // Customer retention metrics
            $table->json('churn_prediction')->nullable(); // Churn prediction data
            $table->json('growth_potential')->nullable(); // Growth potential estimates
            $table->timestamps();

            // Indexes
            $table->index('user_id');
            $table->index('vendor_store_id');
            $table->index('ad_id');
            $table->index('period_start');
            $table->index('period_end');
            $table->index(['user_id', 'period_start', 'period_end']); // For user-specific reports in date range
            $table->index(['vendor_store_id', 'period_start', 'period_end']); // For store-specific reports
            $table->index(['ad_id', 'period_start', 'period_end']); // For ad-specific reports
            $table->index('conversion_rate');
            $table->index('sales_amount');
            $table->index(['sales_count', 'sales_amount']); // Performance ranking
            $table->index('profit_margin');
            $table->index('customer_acquisition_cost');
            $table->index('dispute_rate');
            $table->index('return_rate');

            // Foreign key constraints
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('vendor_store_id')->references('id')->on('vendor_stores')->onDelete('set null');
            $table->foreign('ad_id')->references('id')->on('ads')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seller_analytics');
    }
};
