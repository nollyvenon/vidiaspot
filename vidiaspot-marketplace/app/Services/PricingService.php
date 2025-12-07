<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Models\Ad;
use App\Models\Category;
use App\Models\Payment;

class PricingService
{
    protected $redisService;
    protected $trendAnalysisWindow; // Days to analyze for trend data

    public function __construct(RedisService $redisService)
    {
        $this->redisService = $redisService;
        $this->trendAnalysisWindow = 30; // Analyze last 30 days for trends
    }

    /**
     * Suggest optimal price for a product based on market trends
     *
     * @param array $productData Product details including category_id, condition, etc.
     * @return array
     */
    public function suggestOptimalPrice(array $productData): array
    {
        $categoryId = $productData['category_id'] ?? null;
        $condition = $productData['condition'] ?? 'used';
        $productName = $productData['name'] ?? $productData['title'] ?? '';
        $description = $productData['description'] ?? '';
        
        $cacheKey = "price_suggestion:" . sha1(serialize($productData));
        
        // Check cache first
        if ($cached = $this->redisService->get($cacheKey)) {
            return $cached;
        }

        $suggestedPrice = [
            'base_price' => 0,
            'suggested_price' => 0,
            'market_average' => 0,
            'competing_prices' => [],
            'pricing_factors' => [],
            'confidence_level' => 'low', // low, medium, high
            'price_range' => [
                'min' => 0,
                'max' => 0,
            ],
            'trending_direction' => 'stable', // up, down, stable
            'seasonal_adjustment' => 0,
            'condition_factor' => 1.0,
            'demand_indicator' => 'normal', // high, normal, low
        ];

        if (!$categoryId) {
            // If no category provided, return default values
            return $suggestedPrice;
        }

        // Get category-specific pricing data
        $categoryData = $this->getCategoryPricingData($categoryId);
        $suggestedPrice['market_average'] = $categoryData['average_price'] ?? 0;
        $suggestedPrice['competing_prices'] = $categoryData['competing_prices'] ?? [];
        $suggestedPrice['demand_indicator'] = $categoryData['demand_level'] ?? 'normal';

        // Calculate base price based on market average
        $basePrice = $categoryData['average_price'] ?? 0;
        $suggestedPrice['base_price'] = $basePrice;

        // Apply condition factor
        $conditionFactor = $this->getConditionFactor($condition);
        $suggestedPrice['condition_factor'] = $conditionFactor;
        
        $adjustedPrice = $basePrice * $conditionFactor;
        
        // Apply seasonal adjustment
        $seasonalAdjustment = $this->getSeasonalAdjustment($categoryId);
        $suggestedPrice['seasonal_adjustment'] = $seasonalAdjustment;
        $adjustedPrice = $adjustedPrice * (1 + $seasonalAdjustment);

        // Apply trending direction adjustment
        $trendAdjustment = $this->getTrendAdjustment($categoryId);
        $suggestedPrice['trending_direction'] = $trendAdjustment['direction'];
        $adjustedPrice = $adjustedPrice * $trendAdjustment['factor'];

        // Apply demand-based adjustment
        $demandAdjustment = $this->getDemandAdjustment($categoryData['demand_level'] ?? 'normal');
        $adjustedPrice = $adjustedPrice * $demandAdjustment;

        // Add pricing factors to result
        $suggestedPrice['pricing_factors'] = [
            'condition' => $conditionFactor,
            'seasonal' => $seasonalAdjustment,
            'trend' => $trendAdjustment['factor'],
            'demand' => $demandAdjustment,
        ];

        // Calculate price range (±20% of suggested price)
        $rangeBuffer = 0.2;
        $suggestedPrice['price_range'] = [
            'min' => max(0, $adjustedPrice * (1 - $rangeBuffer)),
            'max' => $adjustedPrice * (1 + $rangeBuffer),
        ];

        $suggestedPrice['suggested_price'] = $adjustedPrice;

        // Determine confidence level based on data availability
        $dataPoints = count($categoryData['competing_prices'] ?? []);
        if ($dataPoints >= 10) {
            $suggestedPrice['confidence_level'] = 'high';
        } elseif ($dataPoints >= 3) {
            $suggestedPrice['confidence_level'] = 'medium';
        } else {
            $suggestedPrice['confidence_level'] = 'low';
        }

        // Cache the result for 6 hours
        $this->redisService->put($cacheKey, $suggestedPrice, 21600);

        return $suggestedPrice;
    }

    /**
     * Get category-specific pricing data
     */
    protected function getCategoryPricingData(int $categoryId): array
    {
        $cacheKey = "category_pricing:{$categoryId}";

        if ($cached = $this->redisService->get($cacheKey)) {
            return $cached;
        }

        // Get average price for the category
        $averagePrice = DB::table('ads')
                         ->where('category_id', $categoryId)
                         ->where('status', 'active')
                         ->where('created_at', '>', now()->subDays($this->trendAnalysisWindow))
                         ->avg('price');

        // Get recent competing prices
        $competingPrices = DB::table('ads')
                            ->where('category_id', $categoryId)
                            ->where('status', 'active')
                            ->where('created_at', '>', now()->subDays(7))
                            ->orderBy('price', 'asc')
                            ->limit(20)
                            ->pluck('price')
                            ->toArray();

        // Determine demand level based on listing activity
        $recentListings = DB::table('ads')
                           ->where('category_id', $categoryId)
                           ->where('created_at', '>', now()->subDays(7))
                           ->count();

        $historicalListings = DB::table('ads')
                               ->where('category_id', $categoryId)
                               ->where('created_at', '>', now()->subDays(30))
                               ->where('created_at', '<', now()->subDays(7))
                               ->count();

        $demandLevel = 'normal';
        if ($recentListings > $historicalListings * 1.5) {
            $demandLevel = 'high';
        } elseif ($recentListings < $historicalListings * 0.7) {
            $demandLevel = 'low';
        }

        $pricingData = [
            'average_price' => $averagePrice ?? 0,
            'competing_prices' => $competingPrices,
            'demand_level' => $demandLevel,
            'recent_listings_count' => $recentListings,
            'historical_average_listings' => $historicalListings > 0 ? $historicalListings / 23 : 0, // roughly daily average
        ];

        // Cache for 2 hours
        $this->redisService->put($cacheKey, $pricingData, 7200);

        return $pricingData;
    }

    /**
     * Get condition factor for price adjustment
     */
    protected function getConditionFactor(string $condition): float
    {
        $conditionFactors = [
            'new' => 1.0,
            'like_new' => 0.9,
            'excellent' => 0.85,
            'very_good' => 0.8,
            'good' => 0.7,
            'fair' => 0.5,
            'poor' => 0.3,
            'used' => 0.65, // Default for used items
        ];

        return $conditionFactors[$condition] ?? $conditionFactors['used'];
    }

    /**
     * Get seasonal adjustment factor
     */
    protected function getSeasonalAdjustment(int $categoryId): float
    {
        $month = now()->month;
        
        // Define seasonal adjustments by category
        $seasonalAdjustments = [
            1 => [ // Electronics category
                11 => 0.1, // Black Friday boost
                12 => 0.15, // Christmas boost
            ],
            2 => [ // Vehicles category
                5 => 0.05, // Spring cleaning boost
                8 => 0.03, // Back to school boost
            ],
            3 => [ // Fashion category
                8 => 0.08, // Back to school boost
                1 => -0.05, // Post-holiday slow
            ],
        ];

        return $seasonalAdjustments[$categoryId][$month] ?? 0;
    }

    /**
     * Get trending adjustment
     */
    protected function getTrendAdjustment(int $categoryId): array
    {
        $cacheKey = "trend_adjustment:{$categoryId}";

        if ($cached = $this->redisService->get($cacheKey)) {
            return $cached;
        }

        // Calculate if prices are trending up or down in the category
        $recentAvg = DB::table('ads')
                      ->where('category_id', $categoryId)
                      ->where('created_at', '>', now()->subWeek())
                      ->avg('price');

        $previousAvg = DB::table('ads')
                        ->where('category_id', $categoryId)
                        ->where('created_at', '>', now()->subDays(14))
                        ->where('created_at', '<', now()->subWeek())
                        ->avg('price');

        $factor = 1.0;
        $direction = 'stable';

        if ($previousAvg && $recentAvg) {
            $change = ($recentAvg - $previousAvg) / $previousAvg;
            
            if ($change > 0.05) {
                $direction = 'up';
                $factor = 1 + min($change, 0.1); // Max 10% increase
            } elseif ($change < -0.05) {
                $direction = 'down';
                $factor = max(1 + $change, 0.9); // Min 10% decrease
            }
        }

        $result = [
            'factor' => $factor,
            'direction' => $direction,
            'recent_average' => $recentAvg,
            'previous_average' => $previousAvg,
        ];

        // Cache for 4 hours
        $this->redisService->put($cacheKey, $result, 14400);

        return $result;
    }

    /**
     * Get demand-based adjustment
     */
    protected function getDemandAdjustment(string $demandLevel): float
    {
        $adjustments = [
            'high' => 1.15,   // 15% premium for high demand
            'normal' => 1.0,  // No adjustment for normal demand
            'low' => 0.85,    // 15% discount for low demand
        ];

        return $adjustments[$demandLevel] ?? $adjustments['normal'];
    }

    /**
     * Get competitor pricing analysis
     */
    public function getCompetitorPricing(int $categoryId, string $productName = ''): array
    {
        $query = Ad::where('category_id', $categoryId)
                   ->where('status', 'active')
                   ->where('created_at', '>', now()->subDays(30));

        if ($productName) {
            $query->where('title', 'LIKE', "%{$productName}%");
        }

        $competitors = $query->orderBy('price', 'asc')
                             ->limit(20)
                             ->get(['title', 'price', 'condition', 'location', 'created_at']);

        $stats = [
            'competitor_count' => $competitors->count(),
            'lowest_price' => $competitors->min('price'),
            'highest_price' => $competitors->max('price'),
            'median_price' => $this->calculateMedian($competitors->pluck('price')->toArray()),
            'average_price' => $competitors->avg('price'),
            'price_distribution' => $this->calculatePriceDistribution($competitors->pluck('price')->toArray()),
            'competitors' => $competitors->toArray(),
        ];

        return $stats;
    }

    /**
     * Calculate median of an array
     */
    protected function calculateMedian(array $numbers): float
    {
        if (empty($numbers)) {
            return 0;
        }

        sort($numbers);
        $count = count($numbers);
        
        if ($count % 2 == 0) {
            return ($numbers[$count / 2 - 1] + $numbers[$count / 2]) / 2;
        } else {
            return $numbers[floor($count / 2)];
        }
    }

    /**
     * Calculate price distribution
     */
    protected function calculatePriceDistribution(array $prices): array
    {
        if (empty($prices)) {
            return [];
        }

        $min = min($prices);
        $max = max($prices);
        $range = $max - $min;

        if ($range == 0) {
            return ['all_same' => true, 'value' => $min];
        }

        // Create quartiles
        sort($prices);
        $count = count($prices);
        
        return [
            'first_quartile' => $prices[floor($count * 0.25)],
            'median' => $prices[floor($count * 0.5)],
            'third_quartile' => $prices[floor($count * 0.75)],
            'distribution_spread' => 'medium', // low, medium, high based on std dev
        ];
    }

    /**
     * Get historical price trends for a category
     */
    public function getPriceTrends(int $categoryId, int $days = 90): array
    {
        $cacheKey = "price_trends:category:{$categoryId}:days:{$days}";

        if ($cached = $this->redisService->get($cacheKey)) {
            return $cached;
        }

        // Get daily average prices for the period
        $trends = DB::table('ads')
                    ->select(
                        DB::raw('DATE(created_at) as date'),
                        DB::raw('AVG(price) as average_price'),
                        DB::raw('COUNT(*) as listing_count')
                    )
                    ->where('category_id', $categoryId)
                    ->where('created_at', '>', now()->subDays($days))
                    ->where('status', 'active')
                    ->groupBy(DB::raw('DATE(created_at)'))
                    ->orderBy('date', 'asc')
                    ->get();

        // Calculate trend indicators
        $trendData = [
            'data_points' => $trends->count(),
            'current_average' => $trends->last() ? $trends->last()->average_price : 0,
            'previous_average' => $trends->count() > 1 ? $trends->get($trends->count() - 2)->average_price : 0,
            'overall_trend' => 'stable',
            'daily_data' => $trends->toArray(),
            'volatility' => $this->calculateVolatility($trends->pluck('average_price')->toArray()),
        ];

        // Determine overall trend
        if ($trendData['current_average'] > $trendData['previous_average'] * 1.05) {
            $trendData['overall_trend'] = 'increasing';
        } elseif ($trendData['current_average'] < $trendData['previous_average'] * 0.95) {
            $trendData['overall_trend'] = 'decreasing';
        }

        $this->redisService->put($cacheKey, $trendData, 3600); // Cache for 1 hour

        return $trendData;
    }

    /**
     * Calculate price volatility
     */
    protected function calculateVolatility(array $prices): float
    {
        if (count($prices) < 2) {
            return 0;
        }

        $mean = array_sum($prices) / count($prices);
        $variance = 0;

        foreach ($prices as $price) {
            $variance += pow($price - $mean, 2);
        }

        $variance /= count($prices);
        $stdDev = sqrt($variance);

        // Normalize volatility (0-1 scale)
        return min(1, $stdDev / $mean);
    }

    /**
     * Get dynamic pricing for promotional periods
     */
    public function getDynamicPricing(int $categoryId, string $eventType = 'normal'): array
    {
        $baseData = $this->getCategoryPricingData($categoryId);

        $multipliers = [
            'black_friday' => 0.8,
            'cyber_monday' => 0.82,
            'christmas' => 0.85,
            'new_year' => 0.88,
            'back_to_school' => 0.9,
            'flash_sale' => 0.75,
            'clearance' => 0.6,
            'normal' => 1.0,
            'premium' => 1.1,
            'luxury' => 1.2,
        ];

        $multiplier = $multipliers[$eventType] ?? $multipliers['normal'];

        $dynamicPrice = [
            'base_average' => $baseData['average_price'],
            'event_type' => $eventType,
            'multiplier' => $multiplier,
            'suggested_price' => $baseData['average_price'] * $multiplier,
            'discount_percentage' => ($multiplier < 1) ? round((1 - $multiplier) * 100, 2) : 0,
            'premium_percentage' => ($multiplier > 1) ? round(($multiplier - 1) * 100, 2) : 0,
        ];

        return $dynamicPrice;
    }

    /**
     * Get price optimization for profit maximization
     */
    public function getProfitMaximizingPrice(array $productData): array
    {
        $suggested = $this->suggestOptimalPrice($productData);

        // Simulate demand curve
        $basePrice = $suggested['suggested_price'];
        $optimalPrices = [];

        // Test various price points around suggested price
        $testPoints = [
            $basePrice * 0.8, // 20% below
            $basePrice * 0.9, // 10% below
            $basePrice,       // suggested price
            $basePrice * 1.1, // 10% above
            $basePrice * 1.2, // 20% above
        ];

        foreach ($testPoints as $price) {
            // Estimate demand at this price point (inversely related to price)
            $estimatedDemand = max(0.1, 1 - (($price - $basePrice) / $basePrice) * 0.5); // 50% sensitivity
            $estimatedRevenue = $price * $estimatedDemand;
            
            $optimalPrices[] = [
                'price' => $price,
                'estimated_demand' => $estimatedDemand,
                'estimated_revenue' => $estimatedRevenue,
                'distance_from_suggested' => abs($price - $basePrice),
            ];
        }

        // Find price that maximizes revenue
        $bestPrice = collect($optimalPrices)->sortByDesc('estimated_revenue')->first();

        return [
            'recommended_price' => $bestPrice['price'],
            'estimated_demand' => $bestPrice['estimated_demand'],
            'estimated_revenue' => $bestPrice['estimated_revenue'],
            'compared_to_suggested' => $bestPrice['price'] - $suggested['suggested_price'],
            'pricing_strategy' => 'profit_maximization',
            'suggested_optimization' => $bestPrice['price'] < $suggested['suggested_price'] ? 'reduce_price_for_volume' : 'increase_price_for_margin',
        ];
    }

    /**
     * Validate price against market standards
     */
    public function validatePrice(int $price, int $categoryId): array
    {
        $categoryData = $this->getCategoryPricingData($categoryId);
        $marketAverage = $categoryData['average_price'];

        if (!$marketAverage) {
            return [
                'valid' => true,
                'message' => 'Price validation skipped - insufficient category data',
                'price_ratio_to_average' => 1.0,
            ];
        }

        $ratio = $price / $marketAverage;
        $isValid = true;
        $message = 'Price is within normal range';

        if ($ratio > 3) { // Price is 3x above average
            $isValid = false;
            $message = 'Price is significantly higher than market average. Consider reducing for better competitiveness.';
        } elseif ($ratio < 0.2) { // Price is 1/5th of average
            $isValid = false;
            $message = 'Price is significantly lower than market average. Ensure this is intentional.';
        } elseif ($ratio > 1.5) {
            $message = 'Price is higher than market average. Consider competitive positioning.';
        } elseif ($ratio < 0.7) {
            $message = 'Price is lower than market average. May attract more buyers.';
        }

        return [
            'valid' => $isValid,
            'message' => $message,
            'price_ratio_to_average' => round($ratio, 2),
            'market_average' => $marketAverage,
        ];
    }
}