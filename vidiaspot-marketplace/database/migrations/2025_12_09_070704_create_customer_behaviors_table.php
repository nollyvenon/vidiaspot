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
        Schema::create('customer_behaviors', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('category_id')->nullable(); // Specific category behavior
            $table->integer('ad_clicked_count')->default(0); // Number of ads clicked
            $table->integer('ad_saved_count')->default(0); // Number of ads saved for later
            $table->integer('ad_shared_count')->default(0); // Number of ads shared
            $table->integer('ad_purchased_count')->default(0); // Number of ads with purchases
            $table->integer('time_spent_seconds')->default(0); // Total time spent on platform
            $table->integer('pages_visited')->default(0); // Number of pages visited
            $table->json('search_queries')->nullable(); // Search queries made by user
            $table->json('preferred_categories')->nullable(); // Categories user prefers
            $table->json('price_range_preferences')->nullable(); // Price ranges user prefers
            $table->json('location_preferences')->nullable(); // Preferred locations/regions for shopping
            $table->string('device_used')->nullable(); // Most commonly used device
            $table->string('browser_used')->nullable(); // Most commonly used browser
            $table->decimal('session_duration_minutes', 8, 2)->default(0.00); // Average session duration
            $table->integer('visit_frequency')->default(0); // Visits per week/month
            $table->json('peak_active_hours')->nullable(); // Hours when user is most active {morning: true, afternoon: false, evening: true}
            $table->json('preferred_payment_methods')->nullable(); // Preferred payment methods
            $table->decimal('average_cart_value', 10, 2)->default(0.00); // Average cart value
            $table->integer('purchase_frequency')->default(0); // Purchases per month
            $table->integer('abandoned_cart_count')->default(0); // Number of abandoned carts
            $table->json('return_behavior')->nullable(); // Return behavior patterns
            $table->decimal('review_submission_rate', 5, 2)->default(0.00); // Rate of review submissions
            $table->decimal('feedback_positivity_rate', 5, 2)->default(85.00); // Positive feedback rate
            $table->decimal('brand_love_score', 5, 2)->default(50.00); // Brand love score (0-100)
            $table->decimal('category_diversity_index', 5, 2)->default(50.00); // How diverse user's shopping is
            $table->decimal('engagement_score', 5, 2)->default(50.00); // Overall engagement score
            $table->decimal('loyalty_score', 5, 2)->default(50.00); // Loyalty score (0-100)
            $table->decimal('churn_probability', 5, 2)->default(20.00); // Churn probability percentage
            $table->decimal('predicted_lifetime_value', 15, 2)->default(0.00); // Predicted LTV
            $table->string('customer_segment')->default('standard'); // Customer segment: standard, premium, loyal, at_risk, churned
            $table->json('preferred_discount_types')->nullable(); // Preferred discount types {percentage: true, flat: false, combo: true}
            $table->json('seasonal_purchase_patterns')->nullable(); // Seasonal purchasing patterns
            $table->json('shopping_cart_behavior')->nullable(); // Shopping cart behavior patterns
            $table->json('social_sharing_behavior')->nullable(); // Social sharing patterns
            $table->json('referral_activity')->nullable(); // Referral activities and success rate
            $table->string('customer_journey_stage')->default('awareness'); // awareness, consideration, purchase, loyalty, advocacy
            $table->timestamp('last_activity_date')->nullable(); // Last activity timestamp
            $table->timestamp('last_purchase_date')->nullable(); // Last purchase timestamp
            $table->timestamp('first_purchase_date')->nullable(); // First purchase timestamp
            $table->decimal('total_spent', 15, 2)->default(0.00); // Total amount spent
            $table->integer('total_orders')->default(0); // Total number of orders
            $table->decimal('average_order_value', 10, 2)->default(0.00); // Average order value
            $table->integer('days_since_last_activity')->default(0); // Days since last activity
            $table->integer('days_since_last_purchase')->default(0); // Days since last purchase
            $table->integer('recency_score')->default(5); // RFM analysis recency score (1-10)
            $table->integer('frequency_score')->default(5); // RFM analysis frequency score (1-10)
            $table->integer('monetary_score')->default(5); // RFM analysis monetary score (1-10)
            $table->decimal('rfm_score', 4, 2)->default(5.50); // Combined RFM score
            $table->decimal('customer_lifetime_value', 15, 2)->default(0.00); // Customer lifetime value
            $table->string('acquisition_channel')->default('organic'); // How customer was acquired
            $table->decimal('retention_probability', 5, 2)->default(80.00); // Probability of retention
            $table->decimal('upsell_potential', 5, 2)->default(50.00); // Upsell potential score
            $table->decimal('cross_sell_potential', 5, 2)->default(50.00); // Cross-sell potential score
            $table->decimal('customer_satisfaction_score', 5, 2)->default(75.00); // Customer satisfaction score
            $table->integer('support_tickets_raised')->default(0); // Number of support tickets raised
            $table->decimal('avg_support_resolution_time', 8, 2)->default(0.00); // Average support resolution time in hours
            $table->decimal('issue_resolution_satisfaction', 5, 2)->default(75.00); // Issue resolution satisfaction score
            $table->json('custom_fields')->nullable(); // For extendability
            $table->json('metadata')->nullable(); // Additional metadata
            $table->timestamps(); // created_at and updated_at

            // Indexes for better query performance
            $table->index('user_id');
            $table->index('category_id');
            $table->index('customer_segment');
            $table->index('customer_journey_stage');
            $table->index('device_used');
            $table->index('recency_score');
            $table->index('frequency_score');
            $table->index('monetary_score');
            $table->index('rfm_score');
            $table->index('churn_probability');
            $table->index('loyalty_score');
            $table->index('acquisition_channel');
            $table->index('retention_probability');
            $table->index(['user_id', 'created_at']); // For chronological user behavior analysis
            $table->index('last_activity_date');
            $table->index('last_purchase_date');
            $table->index('total_spent');
            $table->index('total_orders');
            $table->index(['user_id', 'customer_segment']); // For segment-based analysis
            $table->index(['customer_segment', 'churn_probability']); // For retention-focused analysis
            $table->index(['customer_journey_stage', 'engagement_score']); // For journey-stage analysis
            $table->index('predicted_lifetime_value');

            // Foreign key constraints
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_behaviors');
    }
};
