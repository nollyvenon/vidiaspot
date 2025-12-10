<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketTrend extends Model
{
    protected $fillable = [
        'category_id',
        'subcategory_id',
        'location_id',
        'region',
        'country',
        'city',
        'trend_type', // 'price', 'volume', 'seasonal', 'demand'
        'trend_data', // Contains trend information e.g. {dates: [...], values: [...] }
        'current_value',
        'baseline_value',
        'change_percentage',
        'trend_direction', // 'up', 'down', 'stable', 'volatile'
        'trend_strength', // 1-10 scale
        'confidence_level', // 0-100%
        'seasonal_pattern',
        'forecast_data', // {predictions: [...], confidence_intervals: [...]}
        'seasonal_insights',
        'demand_patterns',
        'price_volatility',
        'peak_seasons',
        'off_peak_seasons',
        'growth_rate',
        'saturation_level', // 0-100% - how saturated the market is
        'competition_index', // 1-100 scale
        'market_health_score', // Overall health score
        'last_updated',
        'next_prediction_date',
        'trend_period', // 'daily', 'weekly', 'monthly', 'quarterly', 'yearly'
        'data_source', // Source of trend data
        'is_active',
        'custom_fields',
        'metadata',
    ];

    protected $casts = [
        'trend_data' => 'array',
        'current_value' => 'decimal:2',
        'baseline_value' => 'decimal:2',
        'change_percentage' => 'decimal:2',
        'trend_strength' => 'integer',
        'confidence_level' => 'decimal:2',
        'seasonal_pattern' => 'array',
        'forecast_data' => 'array',
        'seasonal_insights' => 'array',
        'demand_patterns' => 'array',
        'price_volatility' => 'array',
        'peak_seasons' => 'array',
        'off_peak_seasons' => 'array',
        'growth_rate' => 'decimal:2',
        'saturation_level' => 'decimal:2',
        'competition_index' => 'integer',
        'market_health_score' => 'decimal:2',
        'last_updated' => 'datetime',
        'next_prediction_date' => 'datetime',
        'trend_period' => 'string',
        'data_source' => 'string',
        'is_active' => 'boolean',
        'custom_fields' => 'array',
        'metadata' => 'array',
        'category_id' => 'integer',
        'subcategory_id' => 'integer',
        'location_id' => 'integer',
    ];

    /**
     * Get the category this trend belongs to
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Category::class);
    }

    /**
     * Get the subcategory this trend belongs to
     */
    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Category::class, 'subcategory_id');
    }

    /**
     * Get the location this trend is for
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Location::class);
    }

    /**
     * Scope to get trends for a specific category
     */
    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope to get trends for a specific region
     */
    public function scopeByRegion($query, $region)
    {
        return $query->where('region', $region);
    }

    /**
     * Scope to get trends for a specific country
     */
    public function scopeByCountry($query, $country)
    {
        return $query->where('country', $country);
    }

    /**
     * Scope to get active trends
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get by trend type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('trend_type', $type);
    }

    /**
     * Scope to get trending categories
     */
    public function scopeTrending($query)
    {
        return $query->where('trend_direction', 'up')
                    ->where('trend_strength', '>', 7)
                    ->orderBy('confidence_level', 'desc');
    }

    /**
     * Scope to get seasonal patterns
     */
    public function scopeSeasonal($query)
    {
        return $query->where('trend_type', 'seasonal');
    }

    /**
     * Scope to get by location
     */
    public function scopeByLocation($query, $locationId)
    {
        return $query->where('location_id', $locationId);
    }

    /**
     * Check if market is oversaturated
     */
    public function isOversaturated(): bool
    {
        return $this->saturation_level > 80; // Above 80% is oversaturated
    }

    /**
     * Check if market is growing
     */
    public function isGrowing(): bool
    {
        return $this->trend_direction === 'up' && $this->change_percentage > 5;
    }

    /**
     * Check if market is stable
     */
    public function isStable(): bool
    {
        return $this->trend_direction === 'stable' ||
               (abs($this->change_percentage) < 5 && $this->trend_direction !== 'volatile');
    }

    /**
     * Get market insight summary
     */
    public function getInsightSummary(): array
    {
        return [
            'market_health' => $this->market_health_score,
            'saturation_level' => $this->saturation_level,
            'competition_intensity' => $this->competition_index,
            'growth_trend' => $this->trend_direction,
            'seasonal_opportunities' => $this->peak_seasons,
            'demand_volume' => $this->current_value,
            'price_trend' => $this->change_percentage,
            'confidence_in_trend' => $this->confidence_level,
        ];
    }

    /**
     * Calculate opportunity score
     */
    public function calculateOpportunityScore(): float
    {
        // Calculate opportunity score based on growth, saturation, and competition
        $growthFactor = $this->trend_direction === 'up' ? 1 : ($this->trend_direction === 'stable' ? 0.5 : 0);
        $saturationFactor = max(0, 100 - $this->saturation_level) / 100; // Lower saturation = higher opportunity
        $competitionFactor = max(0, 100 - $this->competition_index) / 100; // Lower competition = higher opportunity

        $opportunityScore = ($growthFactor * 0.4) + ($saturationFactor * 0.4) + ($competitionFactor * 0.2);

        return round($opportunityScore * 100, 2); // Return as percentage
    }
}
