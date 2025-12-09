<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SellerAnalytics extends Model
{
    protected $fillable = [
        'user_id',
        'vendor_store_id',
        'ad_id',
        'period_start',
        'period_end',
        'views',
        'clicks',
        'conversion_rate',
        'sales_count',
        'sales_amount',
        'average_order_value',
        'customer_acquisition_cost',
        'return_on_ad_spend',
        'profit_margin',
        'inventory_turnover_rate',
        'days_of_inventory_outstanding',
        'customer_lifetime_value',
        'repeat_purchase_rate',
        'net_promoter_score',
        'customer_satisfaction_score',
        'response_time_average',
        'dispute_rate',
        'return_rate',
        'fulfillment_speed',
        'location_performance',
        'category_performance',
        'product_performance',
        'seasonal_trends',
        'competitor_analysis',
        'market_share_estimates',
        'pricing_effectiveness',
        'cross_sell_opportunities',
        'up_sell_opportunities',
        'customer_segmentation_data',
        'retention_metrics',
        'churn_prediction',
        'growth_potential',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'views' => 'integer',
        'clicks' => 'integer',
        'conversion_rate' => 'decimal:2',
        'sales_count' => 'integer',
        'sales_amount' => 'decimal:2',
        'average_order_value' => 'decimal:2',
        'customer_acquisition_cost' => 'decimal:2',
        'return_on_ad_spend' => 'decimal:2',
        'profit_margin' => 'decimal:2',
        'inventory_turnover_rate' => 'decimal:2',
        'days_of_inventory_outstanding' => 'integer',
        'customer_lifetime_value' => 'decimal:2',
        'repeat_purchase_rate' => 'decimal:2',
        'net_promoter_score' => 'decimal:2',
        'customer_satisfaction_score' => 'decimal:2',
        'response_time_average' => 'decimal:2',
        'dispute_rate' => 'decimal:2',
        'return_rate' => 'decimal:2',
        'fulfillment_speed' => 'integer', // Days
        'location_performance' => 'array',
        'category_performance' => 'array',
        'product_performance' => 'array',
        'seasonal_trends' => 'array',
        'competitor_analysis' => 'array',
        'market_share_estimates' => 'array',
        'pricing_effectiveness' => 'array',
        'cross_sell_opportunities' => 'array',
        'up_sell_opportunities' => 'array',
        'customer_segmentation_data' => 'array',
        'retention_metrics' => 'array',
        'churn_prediction' => 'array',
        'growth_potential' => 'array',
    ];

    /**
     * Get the user this analytics belongs to
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the vendor store this analytics is for
     */
    public function vendorStore(): BelongsTo
    {
        return $this->belongsTo(VendorStore::class);
    }

    /**
     * Get the ad this analytics is for
     */
    public function ad(): BelongsTo
    {
        return $this->belongsTo(Ad::class);
    }

    /**
     * Scope to get analytics for a specific period
     */
    public function scopeForPeriod($query, $startDate, $endDate)
    {
        return $query->whereBetween('period_start', [$startDate, $endDate])
                    ->orWhereBetween('period_end', [$startDate, $endDate])
                    ->orWhere(function($q) use ($startDate, $endDate) {
                        $q->where('period_start', '<=', $startDate)
                          ->where('period_end', '>=', $endDate);
                    });
    }

    /**
     * Scope to get analytics by time range
     */
    public function scopeTimeRange($query, $range)
    {
        $now = now();
        switch($range) {
            case 'daily':
                return $query->whereDate('period_start', $now->toDateString());
            case 'weekly':
                return $query->where('period_start', '>=', $now->startOfWeek());
            case 'monthly':
                return $query->whereMonth('period_start', $now->month)
                           ->whereYear('period_start', $now->year);
            case 'quarterly':
                return $query->whereBetween('period_start', [
                    $now->startOfQuarter(),
                    $now->endOfQuarter()
                ]);
            case 'yearly':
                return $query->whereYear('period_start', $now->year);
            default:
                return $query;
        }
    }

    /**
     * Calculate growth percentage compared to previous period
     */
    public function getGrowthPercentage($metric): float
    {
        // This would compare with previous period data
        // Implementation would require joining with previous records
        return 0.0; // Placeholder
    }

    /**
     * Get performance rating
     */
    public function getPerformanceRating(): string
    {
        if ($this->conversion_rate >= 5.0) return 'excellent';
        if ($this->conversion_rate >= 3.0) return 'good';
        if ($this->conversion_rate >= 1.0) return 'average';
        return 'needs_improvement';
    }
}
