<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ListingOptimizer extends Model
{
    protected $fillable = [
        'user_id',
        'ad_id',
        'optimizer_type', // 'automatic_renewal', 'performance_optimization', 'seo_enhancement', 'pricing_optimization'
        'optimizer_config', // Configuration for the optimizer
        'optimization_rules', // Rules to determine when to optimize/renew
        'active_schedule', // Schedule for optimization runs
        'auto_renew_enabled',
        'renewal_interval', // 'daily', 'weekly', 'monthly', 'custom'
        'next_renewal_date',
        'renewal_count',
        'renewal_budget',
        'performance_goals', // Target goals for listing performance
        'current_performance_metrics', // Current metrics against goals
        'optimization_strategies', // Strategies to improve performance
        'listing_variants', // Different versions of the listing for A/B testing
        'best_performing_variant', // Currently performing best version
        'conversion_probabilities', // Predicted conversion rates for different strategies
        'roi_calculations', // ROI projections for different optimization strategies
        'budget_distribution', // How budget is distributed across strategies
        'performance_predictions', // Predicted performance metrics
        'competitor_analysis', // Analysis of competitor listings
        'market_positioning', // How the listing compares to competitors
        'keyword_optimization', // SEO optimization data
        'content_optimization', // Content improvement suggestions
        'image_optimization', // Image improvement suggestions
        'pricing_optimization', // Pricing improvement suggestions
        'timing_optimization', // Best times to post/list
        'targeting_optimization', // Audience targeting suggestions
        'automated_actions', // Actions performed automatically
        'manual_reviews_needed', // Areas needing manual attention
        'optimization_score', // Overall optimization score (0-100)
        'last_optimization_run',
        'next_optimization_run',
        'optimization_history', // History of optimization runs
        'cost_per_acquisition_improvements', // Improvements in cost per acquisition
        'click_through_rate_improvements', // Improvements in click-through rate
        'conversion_rate_improvements', // Improvements in conversion rate
        'notes',
        'custom_fields',
    ];

    protected $casts = [
        'optimizer_config' => 'array',
        'optimization_rules' => 'array',
        'active_schedule' => 'array',
        'auto_renew_enabled' => 'boolean',
        'renewal_count' => 'integer',
        'renewal_budget' => 'decimal:2',
        'performance_goals' => 'array',
        'current_performance_metrics' => 'array',
        'optimization_strategies' => 'array',
        'listing_variants' => 'array',
        'conversion_probabilities' => 'array',
        'roi_calculations' => 'array',
        'budget_distribution' => 'array',
        'performance_predictions' => 'array',
        'competitor_analysis' => 'array',
        'market_positioning' => 'array',
        'keyword_optimization' => 'array',
        'content_optimization' => 'array',
        'image_optimization' => 'array',
        'pricing_optimization' => 'array',
        'timing_optimization' => 'array',
        'targeting_optimization' => 'array',
        'automated_actions' => 'array',
        'manual_reviews_needed' => 'array',
        'optimization_score' => 'decimal:2',
        'last_optimization_run' => 'datetime',
        'next_optimization_run' => 'datetime',
        'optimization_history' => 'array',
        'cpa_improvements' => 'array',
        'ctr_improvements' => 'array',
        'cvr_improvements' => 'array',
        'notes' => 'string',
        'custom_fields' => 'array',
        'next_renewal_date' => 'datetime',
        'renewal_interval' => 'string',
    ];

    /**
     * Get the user associated with this optimizer
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the ad associated with this optimizer
     */
    public function ad(): BelongsTo
    {
        return $this->belongsTo(Ad::class);
    }

    /**
     * Scope to get optimizers by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('optimizer_type', $type);
    }

    /**
     * Scope to get active optimizers
     */
    public function scopeActive($query)
    {
        return $query->where('auto_renew_enabled', true);
    }

    /**
     * Scope to get optimizers due for renewal
     */
    public function scopeDueForRenewal($query)
    {
        return $query->where('auto_renew_enabled', true)
                    ->where('next_renewal_date', '<=', now());
    }

    /**
     * Scope to get optimizers for specific ad
     */
    public function scopeForAd($query, $adId)
    {
        return $query->where('ad_id', $adId);
    }

    /**
     * Check if listing is due for optimization
     */
    public function isDueForOptimization(): bool
    {
        return $this->next_optimization_run && now() >= $this->next_optimization_run;
    }

    /**
     * Check if listing is due for renewal
     */
    public function isDueForRenewal(): bool
    {
        return $this->auto_renew_enabled &&
               $this->next_renewal_date &&
               now() >= $this->next_renewal_date;
    }

    /**
     * Calculate optimization effectiveness
     */
    public function getOptimizationEffectiveness(): float
    {
        if (empty($this->optimization_history)) {
            return 0;
        }

        // Calculate improvement based on performance metrics
        $initialMetrics = $this->optimization_history[0]['metrics'] ?? [];
        $currentMetrics = $this->current_performance_metrics ?? [];

        // Calculate improvement in key metrics
        $improvement = 0;

        // Calculate improvement in conversion rate
        if (isset($initialMetrics['conversion_rate']) && isset($currentMetrics['conversion_rate'])) {
            $improvement += ($currentMetrics['conversion_rate'] - $initialMetrics['conversion_rate']) * 100;
        }

        // Calculate improvement in click-through rate
        if (isset($initialMetrics['ctr']) && isset($currentMetrics['ctr'])) {
            $improvement += ($currentMetrics['ctr'] - $initialMetrics['ctr']) * 100;
        }

        // Calculate improvement in impressions
        if (isset($initialMetrics['impressions']) && isset($currentMetrics['impressions'])) {
            $improvement += (($currentMetrics['impressions'] - $initialMetrics['impressions']) / max(1, $initialMetrics['impressions'])) * 50;
        }

        return min(100, max(0, $improvement)); // Clamp between 0-100%
    }

    /**
     * Update optimization score based on various factors
     */
    public function updateOptimizationScore()
    {
        $score = 0;

        // Add points for having good images
        if (count($this->image_optimization) > 0) {
            $score += 15;
        }

        // Add points for keyword optimization
        if (count($this->keyword_optimization) > 0) {
            $score += 15;
        }

        // Add points for content optimization
        if (count($this->content_optimization) > 0) {
            $score += 15;
        }

        // Add points for active A/B testing
        if (count($this->listing_variants) > 1) {
            $score += 20;
        }

        // Add points for automatic renewal enabled
        if ($this->auto_renew_enabled) {
            $score += 10;
        }

        // Add points based on performance improvements
        $effectiveness = $this->getOptimizationEffectiveness();
        $score += ($effectiveness * 0.25); // 25% of effectiveness score

        // Ensure score stays within bounds
        $score = min(100, max(0, $score));

        $this->update(['optimization_score' => $score]);

        return $score;
    }
}
