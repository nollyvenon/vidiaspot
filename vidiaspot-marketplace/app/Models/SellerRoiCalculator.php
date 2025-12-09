<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SellerRoiCalculator extends Model
{
    protected $fillable = [
        'user_id',
        'product_id',
        'ad_id',
        'investment_amount',
        'investment_type', // 'advertising', 'listing_boost', 'featured_placement', 'premium_subscription', 'package'
        'investment_duration_months',
        'investment_start_date',
        'investment_end_date',
        'initial_costs',
        'ongoing_costs',
        'revenue_generated',
        'revenue_per_month',
        'sales_count',
        'sales_per_month',
        'profit_margin_percentage',
        'break_even_point_days', // When investment will be recovered
        'roi_percentage',
        'roi_amount',
        'roi_annualized_percentage', // Annualized ROI
        'return_on_ad_spend', // ROAS
        'customer_acquisition_cost',
        'customer_lifetime_value',
        'payback_period_months',
        'net_present_value', // NPV
        'internal_rate_of_return', // IRR
        'revenue_growth_rate',
        'profit_growth_rate',
        'market_share_impact',
        'brand_awareness_impact', // Measured by engagement metrics
        'future_revenue_projection',
        'investment_summary',
        'performance_metrics',
        'benchmark_comparison', // How this investment performs compared to benchmarks
        'competitor_analysis', // Comparison to competitor performance
        'optimization_recommendations',
        'risk_factors',
        'market_condition_during_investment',
        'seasonal_impact',
        'category_performance_during_investment',
        'geographic_performance_during_investment',
        'device_performance_during_investment',
        'traffic_source_performance',
        'conversion_rate_during_investment',
        'average_order_value_during_investment',
        'customer_saturation_rate',
        'repeat_purchase_rate',
        'churn_rate',
        'customer_acquisition_efficiency',
        'cost_per_lead',
        'lead_conversion_rate',
        'cost_per_action',
        'engagement_rate',
        'impression_reach',
        'click_through_rate',
        'impression_to_sale_conversion_rate',
        'cost_per_conversion',
        'ad_spend_efficiency',
        'inventory_turnover_rate',
        'days_of_inventory_outstanding',
        'inventory_efficiency_score',
        'supply_chain_efficiency_score',
        'fulfillment_efficiency_score',
        'customer_service_efficiency_score',
        'overall_platform_efficiency_score',
        'investment_efficiency_score',
        'growth_potential_score',
        'market_expansion_potential',
        'customer_base_expansion_score',
        'brand_building_score',
        'long_term_value_creation',
        'sustainability_score',
        'scalability_score',
        'risk_score',
        'investment_confidence_score',
        'benchmark_roi_percentage',
        'benchmark_roi_amount',
        'performance_vs_benchmark_ratio',
        'custom_fields',
        'metadata',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'product_id' => 'integer',
        'ad_id' => 'integer',
        'investment_amount' => 'decimal:2',
        'investment_duration_months' => 'integer',
        'investment_start_date' => 'date',
        'investment_end_date' => 'date',
        'initial_costs' => 'decimal:2',
        'ongoing_costs' => 'decimal:2',
        'revenue_generated' => 'decimal:2',
        'revenue_per_month' => 'decimal:2',
        'sales_count' => 'integer',
        'sales_per_month' => 'integer',
        'profit_margin_percentage' => 'decimal:2',
        'break_even_point_days' => 'integer',
        'roi_percentage' => 'decimal:2',
        'roi_amount' => 'decimal:2',
        'roi_annualized_percentage' => 'decimal:2',
        'return_on_ad_spend' => 'decimal:2',
        'customer_acquisition_cost' => 'decimal:2',
        'customer_lifetime_value' => 'decimal:2',
        'payback_period_months' => 'decimal:2',
        'net_present_value' => 'decimal:2',
        'internal_rate_of_return' => 'decimal:2',
        'revenue_growth_rate' => 'decimal:2',
        'profit_growth_rate' => 'decimal:2',
        'market_share_impact' => 'decimal:2',
        'brand_awareness_impact' => 'decimal:2',
        'future_revenue_projection' => 'decimal:2',
        'investment_summary' => 'array',
        'performance_metrics' => 'array',
        'benchmark_comparison' => 'array',
        'competitor_analysis' => 'array',
        'optimization_recommendations' => 'array',
        'risk_factors' => 'array',
        'traffic_source_performance' => 'array',
        'market_condition_during_investment' => 'array',
        'seasonal_impact' => 'array',
        'category_performance_during_investment' => 'array',
        'geographic_performance_during_investment' => 'array',
        'device_performance_during_investment' => 'array',
        'conversion_rate_during_investment' => 'decimal:2',
        'average_order_value_during_investment' => 'decimal:2',
        'customer_saturation_rate' => 'decimal:2',
        'repeat_purchase_rate' => 'decimal:2',
        'churn_rate' => 'decimal:2',
        'customer_acquisition_efficiency' => 'decimal:2',
        'cost_per_lead' => 'decimal:2',
        'lead_conversion_rate' => 'decimal:2',
        'cost_per_action' => 'decimal:2',
        'engagement_rate' => 'decimal:2',
        'impression_reach' => 'integer',
        'click_through_rate' => 'decimal:2',
        'impression_to_sale_conversion_rate' => 'decimal:2',
        'cost_per_conversion' => 'decimal:2',
        'ad_spend_efficiency' => 'decimal:2',
        'inventory_turnover_rate' => 'decimal:2',
        'days_of_inventory_outstanding' => 'integer',
        'inventory_efficiency_score' => 'decimal:2',
        'supply_chain_efficiency_score' => 'decimal:2',
        'fulfillment_efficiency_score' => 'decimal:2',
        'customer_service_efficiency_score' => 'decimal:2',
        'overall_platform_efficiency_score' => 'decimal:2',
        'investment_efficiency_score' => 'decimal:2',
        'growth_potential_score' => 'decimal:2',
        'market_expansion_potential' => 'decimal:2',
        'customer_base_expansion_score' => 'decimal:2',
        'brand_building_score' => 'decimal:2',
        'long_term_value_creation' => 'decimal:2',
        'sustainability_score' => 'decimal:2',
        'scalability_score' => 'decimal:2',
        'risk_score' => 'decimal:2',
        'investment_confidence_score' => 'decimal:2',
        'benchmark_roi_percentage' => 'decimal:2',
        'benchmark_roi_amount' => 'decimal:2',
        'performance_vs_benchmark_ratio' => 'decimal:2',
        'custom_fields' => 'array',
        'metadata' => 'array',
    ];

    /**
     * Get the user this ROI calculation is for
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the ad this ROI calculation is for
     */
    public function ad(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Ad::class);
    }

    /**
     * Calculate ROI percentage
     */
    public function calculateROIPercentage($netProfit, $investmentAmount)
    {
        if ($investmentAmount == 0) {
            return 0;
        }

        return (($netProfit / $investmentAmount) * 100);
    }

    /**
     * Calculate payback period in months
     */
    public function calculatePaybackPeriod($monthlyNetRevenue, $investmentAmount)
    {
        if ($monthlyNetRevenue == 0) {
            return 0;
        }

        return $investmentAmount / $monthlyNetRevenue;
    }

    /**
     * Get ROI performance rating
     */
    public function getROIRating(): string
    {
        if ($this->roi_percentage >= 50) {
            return 'excellent';
        } elseif ($this->roi_percentage >= 25) {
            return 'good';
        } elseif ($this->roi_percentage >= 10) {
            return 'fair';
        } elseif ($this->roi_percentage >= 0) {
            return 'poor';
        } else {
            return 'loss';
        }
    }

    /**
     * Get investment efficiency score
     */
    public function getInvestmentEfficiencyScore(): float
    {
        $roaFactor = min(100, ($this->roi_percentage * 2)); // Up to 100 points for ROI > 50%
        $paybackFactor = max(0, 100 - ($this->payback_period_months * 5)); // Each month adds 5 penalty points
        $roasFactor = min(100, ($this->return_on_ad_spend * 0.5)); // Up to 100 points for ROAS > 200

        $efficiencyScore = ($roaFactor * 0.5) + ($paybackFactor * 0.3) + ($roasFactor * 0.2);

        return min(100, max(0, round($efficiencyScore, 2)));
    }

    /**
     * Get optimization recommendations based on performance
     */
    public function getOptimizationRecommendations(): array
    {
        $recommendations = [];

        // ROI-based recommendations
        if ($this->roi_percentage < 10) {
            $recommendations[] = "Investment ROI is low. Consider adjusting strategy or targeting.";
        }

        if ($this->customer_acquisition_cost > $this->customer_lifetime_value / 3) {
            $recommendations[] = "Customer acquisition cost is too high relative to lifetime value. Focus on improving retention.";
        }

        if ($this->churn_rate > 0.3) {
            $recommendations[] = "High churn rate detected. Improve customer service and product quality.";
        }

        if ($this->click_through_rate < 2.0) {
            $recommendations[] = "Low click-through rate. Optimize ad creatives and targeting.";
        }

        if ($this->conversion_rate_during_investment < 1.0) {
            $recommendations[] = "Low conversion rate. Consider improving product listing and checkout process.";
        }

        // Seasonal recommendations
        if ($this->seasonal_impact['high_season_roi'] ?? 0 > $this->roi_percentage * 1.5) {
            $recommendations[] = "Invest during high season for better returns.";
        }

        return $recommendations;
    }
}
