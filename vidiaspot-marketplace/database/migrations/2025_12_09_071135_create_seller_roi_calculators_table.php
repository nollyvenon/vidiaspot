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
        Schema::create('seller_roi_calculators', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // The seller who made this investment
            $table->unsignedBigInteger('product_id')->nullable(); // If investment is for specific product
            $table->unsignedBigInteger('ad_id')->nullable(); // If investment is for specific ad
            $table->decimal('investment_amount', 15, 2)->default(0.00); // Amount invested
            $table->string('investment_type')->default('advertising'); // advertising, listing_boost, featured_placement, premium_subscription, package
            $table->integer('investment_duration_months')->default(1); // Duration in months
            $table->timestamp('investment_start_date')->nullable(); // When investment started
            $table->timestamp('investment_end_date')->nullable(); // When investment ends
            $table->decimal('initial_costs', 10, 2)->default(0.00); // Initial setup costs
            $table->decimal('ongoing_costs', 10, 2)->default(0.00); // Recurring costs
            $table->decimal('revenue_generated', 15, 2)->default(0.00); // Revenue generated from investment
            $table->decimal('revenue_per_month', 10, 2)->default(0.00); // Revenue per month
            $table->integer('sales_count')->default(0); // Number of sales attributed to investment
            $table->integer('sales_per_month')->default(0); // Sales per month during investment
            $table->decimal('profit_margin_percentage', 5, 2)->default(20.00); // Profit margin percentage
            $table->integer('break_even_point_days')->default(0); // Days to recover investment
            $table->decimal('roi_percentage', 8, 2)->default(0.00); // Return on investment percentage
            $table->decimal('roi_amount', 15, 2)->default(0.00); // Actual ROI amount
            $table->decimal('roi_annualized_percentage', 8, 2)->default(0.00); // Annualized ROI
            $table->decimal('return_on_ad_spend', 8, 2)->default(0.00); // ROAS
            $table->decimal('customer_acquisition_cost', 10, 2)->default(0.00); // Average cost to acquire one customer
            $table->decimal('customer_lifetime_value', 15, 2)->default(0.00); // Customer lifetime value
            $table->decimal('payback_period_months', 8, 2)->default(0.00); // Months to pay back investment
            $table->decimal('net_present_value', 15, 2)->default(0.00); // Net Present Value
            $table->decimal('internal_rate_of_return', 8, 2)->default(0.00); // Internal Rate of Return
            $table->decimal('revenue_growth_rate', 8, 2)->default(0.00); // Revenue growth rate
            $table->decimal('profit_growth_rate', 8, 2)->default(0.00); // Profit growth rate
            $table->decimal('market_share_impact', 5, 2)->default(0.00); // Impact on market share
            $table->decimal('brand_awareness_impact', 5, 2)->default(50.00); // Brand awareness impact (0-100)
            $table->decimal('future_revenue_projection', 15, 2)->default(0.00); // Projected future revenue
            $table->json('investment_summary')->nullable(); // Summary of the investment
            $table->json('performance_metrics')->nullable(); // Detailed performance metrics
            $table->json('benchmark_comparison')->nullable(); // Comparison to industry benchmarks
            $table->json('competitor_analysis')->nullable(); // Comparison to competitor performance
            $table->json('optimization_recommendations')->nullable(); // Recommendations for optimization
            $table->json('risk_factors')->nullable(); // Risk factors associated with investment
            $table->json('market_condition_during_investment')->nullable(); // Market conditions during investment
            $table->json('seasonal_impact')->nullable(); // Seasonal impact on investment
            $table->json('category_performance_during_investment')->nullable(); // Category performance during investment
            $table->json('geographic_performance_during_investment')->nullable(); // Geographic performance during investment
            $table->json('device_performance_during_investment')->nullable(); // Device performance during investment
            $table->json('traffic_source_performance')->nullable(); // Traffic source performance
            $table->decimal('conversion_rate_during_investment', 8, 2)->default(0.00); // Conversion rate during investment
            $table->decimal('average_order_value_during_investment', 10, 2)->default(0.00); // Average order value during investment
            $table->decimal('customer_saturation_rate', 5, 2)->default(0.00); // Customer saturation rate
            $table->decimal('repeat_purchase_rate', 5, 2)->default(0.00); // Repeat purchase rate
            $table->decimal('churn_rate', 5, 2)->default(0.00); // Customer churn rate
            $table->decimal('customer_acquisition_efficiency', 5, 2)->default(0.00); // Customer acquisition efficiency
            $table->decimal('cost_per_lead', 8, 2)->default(0.00); // Cost per lead
            $table->decimal('lead_conversion_rate', 5, 2)->default(0.00); // Lead conversion rate
            $table->decimal('cost_per_action', 8, 2)->default(0.00); // Cost per action
            $table->decimal('engagement_rate', 5, 2)->default(0.00); // Engagement rate
            $table->integer('impression_reach')->default(0); // Number of impressions reached
            $table->decimal('click_through_rate', 5, 2)->default(0.00); // Click through rate
            $table->decimal('impression_to_sale_conversion_rate', 5, 2)->default(0.00); // Impression to sale conversion rate
            $table->decimal('cost_per_conversion', 8, 2)->default(0.00); // Cost per conversion
            $table->decimal('ad_spend_efficiency', 5, 2)->default(0.00); // Ad spend efficiency
            $table->decimal('inventory_turnover_rate', 8, 2)->default(0.00); // Inventory turnover rate
            $table->integer('days_of_inventory_outstanding')->default(0); // Days of inventory outstanding
            $table->decimal('inventory_efficiency_score', 5, 2)->default(50.00); // Inventory efficiency score (0-100)
            $table->decimal('supply_chain_efficiency_score', 5, 2)->default(50.00); // Supply chain efficiency score (0-100)
            $table->decimal('fulfillment_efficiency_score', 5, 2)->default(50.00); // Fulfillment efficiency score (0-100)
            $table->decimal('customer_service_efficiency_score', 5, 2)->default(50.00); // Customer service efficiency score (0-100)
            $table->decimal('overall_platform_efficiency_score', 5, 2)->default(50.00); // Overall platform efficiency score (0-100)
            $table->decimal('investment_efficiency_score', 5, 2)->default(50.00); // Investment efficiency score (0-100)
            $table->decimal('growth_potential_score', 5, 2)->default(50.00); // Growth potential score (0-100)
            $table->decimal('market_expansion_potential', 5, 2)->default(50.00); // Market expansion potential (0-100)
            $table->decimal('customer_base_expansion_score', 5, 2)->default(50.00); // Customer base expansion score (0-100)
            $table->decimal('brand_building_score', 5, 2)->default(50.00); // Brand building score (0-100)
            $table->decimal('long_term_value_creation', 5, 2)->default(50.00); // Long term value creation score (0-100)
            $table->decimal('sustainability_score', 5, 2)->default(50.00); // Sustainability score (0-100)
            $table->decimal('scalability_score', 5, 2)->default(50.00); // Scalability score (0-100)
            $table->decimal('risk_score', 5, 2)->default(50.00); // Risk score (0-100)
            $table->decimal('investment_confidence_score', 5, 2)->default(50.00); // Investment confidence score (0-100)
            $table->decimal('benchmark_roi_percentage', 8, 2)->default(0.00); // Benchmark ROI percentage for comparison
            $table->decimal('benchmark_roi_amount', 15, 2)->default(0.00); // Benchmark ROI amount for comparison
            $table->decimal('performance_vs_benchmark_ratio', 5, 2)->default(1.00); // Performance ratio vs benchmark (1.0 = equal to benchmark)
            $table->json('custom_fields')->nullable(); // For extendability
            $table->json('metadata')->nullable(); // Additional metadata
            $table->timestamps(); // created_at and updated_at

            // Indexes for better query performance
            $table->index('user_id');
            $table->index('product_id');
            $table->index('ad_id');
            $table->index('investment_type');
            $table->index('investment_amount');
            $table->index('roi_percentage');
            $table->index('roi_amount');
            $table->index('investment_start_date');
            $table->index('investment_end_date');
            $table->index(['user_id', 'investment_type']); // For user's investment type analysis
            $table->index(['user_id', 'created_at']); // For chronological analysis
            $table->index(['user_id', 'roi_percentage']); // For comparing user's investments
            $table->index('break_even_point_days');
            $table->index('payback_period_months');
            $table->index('customer_acquisition_cost');
            $table->index('customer_lifetime_value');
            $table->index('sales_count');
            $table->index('revenue_generated');
            $table->index('sales_per_month');
            $table->index('conversion_rate_during_investment');
            $table->index('click_through_rate');
            $table->index('engagement_rate');
            $table->index('investment_efficiency_score');
            $table->index('roi_annualized_percentage');
            $table->index('return_on_ad_spend');
            $table->index('risk_score');

            // Foreign key constraints
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('ads')->onDelete('set null'); // Assuming products are stored as ads
            $table->foreign('ad_id')->references('id')->on('ads')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seller_roi_calculators');
    }
};
