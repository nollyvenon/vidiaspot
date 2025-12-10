<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerBehavior extends Model
{
    protected $fillable = [
        'user_id',
        'category_id',
        'ad_clicked_count',
        'ad_saved_count',
        'ad_shared_count',
        'ad_purchased_count',
        'time_spent_seconds',
        'pages_visited',
        'search_queries',
        'preferred_categories',
        'price_range_preferences',
        'location_preferences',
        'device_used',
        'browser_used',
        'session_duration_minutes',
        'visit_frequency',
        'peak_active_hours',
        'preferred_payment_methods',
        'average_cart_value',
        'purchase_frequency',
        'abandoned_cart_count',
        'return_behavior',
        'review_submission_rate',
        'feedback_positivity_rate',
        'brand_love_score',
        'category_diversity_index',
        'engagement_score',
        'loyalty_score',
        'churn_probability',
        'predicted_lifetime_value',
        'customer_segment',
        'preferred_discount_types',
        'seasonal_purchase_patterns',
        'shopping_cart_behavior',
        'social_sharing_behavior',
        'referral_activity',
        'customer_journey_stage', // awareness, consideration, purchase, loyalty
        'last_activity_date',
        'last_purchase_date',
        'first_purchase_date',
        'total_spent',
        'total_orders',
        'average_order_value',
        'days_since_last_activity',
        'days_since_last_purchase',
        'recency_score', // RFM analysis recency score
        'frequency_score', // RFM analysis frequency score
        'monetary_score', // RFM analysis monetary score
        'rfm_score', // Combined RFM score
        'customer_lifetime_value',
        'acquisition_channel',
        'retention_probability',
        'upsell_potential',
        'cross_sell_potential',
        'customer_satisfaction_score',
        'support_tickets_raised',
        'avg_support_resolution_time',
        'issue_resolution_satisfaction',
        'custom_fields',
        'metadata',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'category_id' => 'integer',
        'ad_clicked_count' => 'integer',
        'ad_saved_count' => 'integer',
        'ad_shared_count' => 'integer',
        'ad_purchased_count' => 'integer',
        'time_spent_seconds' => 'integer',
        'pages_visited' => 'integer',
        'search_queries' => 'array',
        'preferred_categories' => 'array',
        'price_range_preferences' => 'array',
        'location_preferences' => 'array',
        'device_used' => 'string',
        'session_duration_minutes' => 'decimal:2',
        'visit_frequency' => 'integer',
        'peak_active_hours' => 'array',
        'preferred_payment_methods' => 'array',
        'average_cart_value' => 'decimal:2',
        'purchase_frequency' => 'integer',
        'abandoned_cart_count' => 'integer',
        'return_behavior' => 'array',
        'review_submission_rate' => 'decimal:2',
        'feedback_positivity_rate' => 'decimal:2',
        'brand_love_score' => 'decimal:2',
        'category_diversity_index' => 'decimal:2',
        'engagement_score' => 'decimal:2',
        'loyalty_score' => 'decimal:2',
        'churn_probability' => 'decimal:2',
        'predicted_lifetime_value' => 'decimal:2',
        'customer_segment' => 'string',
        'preferred_discount_types' => 'array',
        'seasonal_purchase_patterns' => 'array',
        'shopping_cart_behavior' => 'array',
        'social_sharing_behavior' => 'array',
        'referral_activity' => 'array',
        'customer_journey_stage' => 'string',
        'last_activity_date' => 'datetime',
        'last_purchase_date' => 'datetime',
        'first_purchase_date' => 'datetime',
        'total_spent' => 'decimal:2',
        'total_orders' => 'integer',
        'average_order_value' => 'decimal:2',
        'days_since_last_activity' => 'integer',
        'days_since_last_purchase' => 'integer',
        'recency_score' => 'integer',
        'frequency_score' => 'integer',
        'monetary_score' => 'integer',
        'rfm_score' => 'decimal:2',
        'customer_lifetime_value' => 'decimal:2',
        'acquisition_channel' => 'string',
        'retention_probability' => 'decimal:2',
        'upsell_potential' => 'decimal:2',
        'cross_sell_potential' => 'decimal:2',
        'customer_satisfaction_score' => 'decimal:2',
        'support_tickets_raised' => 'integer',
        'avg_support_resolution_time' => 'decimal:2',
        'issue_resolution_satisfaction' => 'decimal:2',
        'custom_fields' => 'array',
        'metadata' => 'array',
        'customer_id' => 'integer', // Fixed field name
    ];

    /**
     * Get the user this behavior record is for
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the category this behavior record is for
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
