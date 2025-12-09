<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PriceMonitoring extends Model
{
    protected $fillable = [
        'user_id',
        'ad_id',
        'tracked_product_name',
        'current_price',
        'competitor_prices',
        'historical_prices',
        'price_trend',
        'optimization_suggestions',
        'recommended_actions',
        'monitoring_strategy',
        'alert_thresholds',
        'last_updated',
        'next_update',
        'price_elasticity',
        'market_position',
        'competitiveness_score',
        'revenue_impact_prediction',
        'demand_fluctuation_tracking',
        'seasonal_pricing_adjustments',
        'automated_repricing_rules',
        'integration_sources',
        'is_active',
        'custom_rules',
    ];

    protected $casts = [
        'current_price' => 'decimal:2',
        'competitor_prices' => 'array',
        'historical_prices' => 'array',
        'price_trend' => 'string',
        'optimization_suggestions' => 'array',
        'recommended_actions' => 'array',
        'monitoring_strategy' => 'string',
        'alert_thresholds' => 'array',
        'price_elasticity' => 'decimal:2',
        'market_position' => 'string',
        'competitiveness_score' => 'decimal:2',
        'revenue_impact_prediction' => 'decimal:2',
        'demand_fluctuation_tracking' => 'array',
        'seasonal_pricing_adjustments' => 'array',
        'automated_repricing_rules' => 'array',
        'integration_sources' => 'array',
        'is_active' => 'boolean',
        'custom_rules' => 'array',
        'last_updated' => 'datetime',
        'next_update' => 'datetime',
    ];

    /**
     * Get the user associated with this price monitoring
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the ad being monitored
     */
    public function ad(): BelongsTo
    {
        return $this->belongsTo(Ad::class);
    }

    /**
     * Scope to get active monitoring
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get by product name
     */
    public function scopeByProduct($query, $productName)
    {
        return $query->where('tracked_product_name', 'LIKE', "%{$productName}%");
    }

    /**
     * Check if price change threshold has been met
     */
    public function isThresholdMet($newPrice): bool
    {
        if (!$this->current_price) return false;

        $threshold = $this->alert_thresholds['percentage_change'] ?? 5; // Default 5% change
        $changePercent = abs(($newPrice - $this->current_price) / $this->current_price) * 100;

        return $changePercent >= $threshold;
    }

    /**
     * Calculate competitiveness score
     */
    public function calculateCompetitivenessScore(): float
    {
        if (empty($this->competitor_prices)) return 50.0; // Neutral score

        $myPrice = $this->current_price;
        if (!$myPrice) return 50.0;

        $competitorPrices = array_column($this->competitor_prices, 'price');
        if (empty($competitorPrices)) return 50.0;

        $avgCompetitorPrice = array_sum($competitorPrices) / count($competitorPrices);

        // Score based on how close to market average (closer to average = more competitive)
        if ($avgCompetitorPrice == 0) return 50.0;

        $ratio = $myPrice / $avgCompetitorPrice;
        if ($ratio <= 0.8) return 80.0; // Very competitive if 20% below avg
        if ($ratio <= 0.9) return 70.0; // Competitive if 10% below avg
        if ($ratio <= 1.0) return 60.0; // Competitive if close to avg
        if ($ratio <= 1.1) return 40.0; // Less competitive if 10% above avg
        if ($ratio <= 1.2) return 30.0; // Less competitive if 20% above avg

        return 20.0; // Not competitive
    }
}
