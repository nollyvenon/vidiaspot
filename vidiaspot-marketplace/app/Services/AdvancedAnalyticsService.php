<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Models\Ad;
use App\Models\Category;
use App\Models\User;
use App\Models\Transaction;
use App\Models\PaymentTransaction;
use Carbon\Carbon;

class AdvancedAnalyticsService
{
    /**
     * Get real-time market data and news
     */
    public function getRealTimeMarketData($filters = [])
    {
        // Get real-time market data based on filters
        $cacheKey = 'real_time_market_data_' . md5(serialize($filters));
        
        return Cache::remember($cacheKey, 300, function() use ($filters) {
            $categories = Category::withCount('ads')->get();
            $totalListings = Ad::count();
            $avgPrice = Ad::avg('price');
            $trendingCategories = $this->getTrendingCategories();
            
            // Simulate real-time market data
            $marketData = [
                'timestamp' => now()->toISOString(),
                'total_active_listings' => $totalListings,
                'average_price' => $avgPrice,
                'trending_categories' => $trendingCategories,
                'market_sentiment' => $this->calculateMarketSentiment(),
                'price_fluctuations' => $this->calculatePriceFluctuations(),
                'user_activity' => $this->getUserActivityMetrics(),
                'regional_stats' => $this->getRegionalStatistics(),
                'category_performance' => $this->getCategoryPerformance(),
            ];

            return $marketData;
        });
    }

    /**
     * Get trending categories based on recent activity
     */
    private function getTrendingCategories()
    {
        $trending = DB::table('ads')
            ->join('categories', 'ads.category_id', '=', 'categories.id')
            ->select('categories.name', 'categories.slug', DB::raw('COUNT(*) as count'))
            ->where('ads.created_at', '>=', now()->subWeek())
            ->groupBy('categories.id', 'categories.name', 'categories.slug')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        return $trending;
    }

    /**
     * Calculate market sentiment
     */
    private function calculateMarketSentiment()
    {
        // In a real implementation, this would analyze user feedback, reviews, etc.
        // For now, calculate based on listing activity
        $weekAgo = now()->subWeek();
        $lastWeekCount = Ad::where('created_at', '>=', $weekAgo)->count();
        $twoWeeksAgo = now()->subWeeks(2);
        $prevWeekCount = Ad::whereBetween('created_at', [$twoWeeksAgo, $weekAgo])->count();

        if ($prevWeekCount === 0) {
            $change = $lastWeekCount > 0 ? 100 : 0;
        } else {
            $change = (($lastWeekCount - $prevWeekCount) / $prevWeekCount) * 100;
        }

        $sentiment = 'neutral';
        if ($change > 10) {
            $sentiment = 'positive';
        } elseif ($change < -10) {
            $sentiment = 'negative';
        }

        return [
            'sentiment' => $sentiment,
            'change_percentage' => $change,
            'description' => $sentiment === 'positive' ? 'Market is growing' : ($sentiment === 'negative' ? 'Market is declining' : 'Market is stable')
        ];
    }

    /**
     * Calculate price fluctuations
     */
    private function calculatePriceFluctuations()
    {
        // Calculate price changes over time for different categories
        $priceFluctuations = [];
        
        $categories = Category::limit(10)->get();
        foreach ($categories as $category) {
            $currentAvg = Ad::where('category_id', $category->id)
                           ->where('created_at', '>=', now()->subWeek())
                           ->avg('price') ?? 0;
            
            $prevWeekAvg = Ad::where('category_id', $category->id)
                            ->whereBetween('created_at', [now()->subWeeks(2), now()->subWeek()])
                            ->avg('price') ?? 0;

            $change = $prevWeekAvg > 0 ? (($currentAvg - $prevWeekAvg) / $prevWeekAvg) * 100 : 0;

            $priceFluctuations[] = [
                'category' => $category->name,
                'current_avg' => $currentAvg,
                'previous_avg' => $prevWeekAvg,
                'change_percentage' => $change,
                'trend' => $change > 5 ? 'increasing' : ($change < -5 ? 'decreasing' : 'stable')
            ];
        }

        return $priceFluctuations;
    }

    /**
     * Get user activity metrics
     */
    private function getUserActivityMetrics()
    {
        $totalUsers = User::count();
        $activeThisWeek = User::where('last_login_at', '>=', now()->subWeek())->count();
        $newUsersThisWeek = User::where('created_at', '>=', now()->subWeek())->count();
        
        return [
            'total_users' => $totalUsers,
            'active_users_this_week' => $activeThisWeek,
            'new_users_this_week' => $newUsersThisWeek,
            'active_user_percentage' => $totalUsers > 0 ? round(($activeThisWeek / $totalUsers) * 100, 2) : 0
        ];
    }

    /**
     * Get regional statistics
     */
    private function getRegionalStatistics()
    {
        $regionalStats = DB::table('ads')
            ->select('location', DB::raw('COUNT(*) as count'), DB::raw('AVG(price) as avg_price'))
            ->groupBy('location')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        return $regionalStats;
    }

    /**
     * Get category performance metrics
     */
    private function getCategoryPerformance()
    {
        $categoryPerformance = DB::table('ads')
            ->join('categories', 'ads.category_id', '=', 'categories.id')
            ->select(
                'categories.name',
                'categories.slug',
                DB::raw('COUNT(*) as total_ads'),
                DB::raw('AVG(price) as avg_price'),
                DB::raw('AVG(view_count) as avg_views'),
                DB::raw('AVG(inquiries_count) as avg_inquiries')
            )
            ->groupBy('categories.id', 'categories.name', 'categories.slug')
            ->get();

        return $categoryPerformance;
    }

    /**
     * Get technical analysis indicators for a category
     */
    public function getTechnicalIndicators($categoryId, $days = 30)
    {
        $cacheKey = "technical_indicators_{$categoryId}_{$days}";
        
        return Cache::remember($cacheKey, 600, function() use ($categoryId, $days) {
            $ads = Ad::where('category_id', $categoryId)
                    ->where('created_at', '>=', now()->subDays($days))
                    ->orderBy('created_at')
                    ->get();

            if ($ads->count() === 0) {
                return ['error' => 'No data available for this category'];
            }

            // Calculate various indicators
            $priceData = $ads->pluck('price')->values()->toArray();
            $timeData = $ads->pluck('created_at')->values()->toArray();

            $indicators = [
                'moving_averages' => $this->calculateMovingAverages($priceData),
                'rsi' => $this->calculateRSI($priceData),
                'bollinger_bands' => $this->calculateBollingerBands($priceData),
                'volume_profile' => $this->calculateVolumeProfile($priceData, $timeData),
                'support_resistance' => $this->calculateSupportResistance($priceData),
                'trend_analysis' => $this->analyzeTrend($priceData),
            ];

            return $indicators;
        });
    }

    /**
     * Calculate moving averages
     */
    private function calculateMovingAverages($priceData)
    {
        $sma5 = [];
        $sma10 = [];
        $sma20 = [];

        for ($i = 0; $i < count($priceData); $i++) {
            // 5-day SMA
            if ($i >= 4) {
                $sma5[] = array_sum(array_slice($priceData, $i - 4, 5)) / 5;
            }

            // 10-day SMA
            if ($i >= 9) {
                $sma10[] = array_sum(array_slice($priceData, $i - 9, 10)) / 10;
            }

            // 20-day SMA
            if ($i >= 19) {
                $sma20[] = array_sum(array_slice($priceData, $i - 19, 20)) / 20;
            }
        }

        return [
            'sma5' => $sma5,
            'sma10' => $sma10,
            'sma20' => $sma20
        ];
    }

    /**
     * Calculate RSI (Relative Strength Index)
     */
    private function calculateRSI($priceData, $period = 14)
    {
        if (count($priceData) < $period + 1) {
            return ['error' => 'Not enough data for RSI calculation'];
        }

        $gains = [];
        $losses = [];

        for ($i = 1; $i < count($priceData); $i++) {
            $change = $priceData[$i] - $priceData[$i - 1];
            $gains[] = $change > 0 ? $change : 0;
            $losses[] = $change < 0 ? abs($change) : 0;
        }

        // Calculate initial average gain and loss
        $avgGain = array_sum(array_slice($gains, 0, $period)) / $period;
        $avgLoss = array_sum(array_slice($losses, 0, $period)) / $period;

        $rsi = [];
        if ($avgLoss !== 0) {
            $rs = $avgGain / $avgLoss;
            $rsiValue = 100 - (100 / (1 + $rs));
            $rsi[] = $rsiValue;
        }

        // Calculate subsequent RSI values
        for ($i = $period; $i < count($gains); $i++) {
            $avgGain = (($avgGain * ($period - 1)) + $gains[$i]) / $period;
            $avgLoss = (($avgLoss * ($period - 1)) + $losses[$i]) / $period;

            if ($avgLoss !== 0) {
                $rs = $avgGain / $avgLoss;
                $rsiValue = 100 - (100 / (1 + $rs));
                $rsi[] = $rsiValue;
            } else {
                $rsi[] = 100; // Avoid division by zero
            }
        }

        return end($rsi) ?: 50; // Return last RSI value or default
    }

    /**
     * Calculate Bollinger Bands
     */
    private function calculateBollingerBands($priceData, $period = 20, $stdDev = 2)
    {
        $bands = [];
        $multiplier = $stdDev;

        for ($i = $period - 1; $i < count($priceData); $i++) {
            $slice = array_slice($priceData, $i - $period + 1, $period);
            $sma = array_sum($slice) / count($slice);

            // Calculate standard deviation
            $sumOfSquares = 0;
            foreach ($slice as $price) {
                $sumOfSquares += pow($price - $sma, 2);
            }
            $std = sqrt($sumOfSquares / count($slice));

            $upper = $sma + ($multiplier * $std);
            $lower = $sma - ($multiplier * $std);

            $bands[] = [
                'sma' => $sma,
                'upper' => $upper,
                'lower' => $lower,
                'price' => $priceData[$i]
            ];
        }

        return $bands;
    }

    /**
     * Calculate volume profile (in this context, listing volume)
     */
    private function calculateVolumeProfile($priceData, $timeData)
    {
        // Group prices into ranges to simulate volume
        $priceRanges = [];
        $minPrice = min($priceData);
        $maxPrice = max($priceData);
        $rangeSize = ($maxPrice - $minPrice) / 10; // 10 ranges

        for ($i = 0; $i < 10; $i++) {
            $start = $minPrice + ($i * $rangeSize);
            $end = $minPrice + (($i + 1) * $rangeSize);
            
            $count = 0;
            foreach ($priceData as $price) {
                if ($price >= $start && $price < $end) {
                    $count++;
                }
            }
            
            $priceRanges[] = [
                'range' => [$start, $end],
                'volume' => $count,
                'percentage' => count($priceData) > 0 ? ($count / count($priceData)) * 100 : 0
            ];
        }

        // Add final range to include max value
        $start = $minPrice + (9 * $rangeSize);
        $end = $maxPrice;
        $count = 0;
        foreach ($priceData as $price) {
            if ($price >= $start && $price <= $end) {
                $count++;
            }
        }
        
        $priceRanges[] = [
            'range' => [$start, $end],
            'volume' => $count,
            'percentage' => count($priceData) > 0 ? ($count / count($priceData)) * 100 : 0
        ];

        return $priceRanges;
    }

    /**
     * Calculate support and resistance levels
     */
    private function calculateSupportResistance($priceData, $period = 20)
    {
        $supports = [];
        $resistances = [];
        
        // Find local minima (supports) and maxima (resistances)
        for ($i = 1; $i < count($priceData) - 1; $i++) {
            if ($priceData[$i] < $priceData[$i - 1] && $priceData[$i] < $priceData[$i + 1]) {
                // Local minimum (support)
                $supports[] = $priceData[$i];
            } elseif ($priceData[$i] > $priceData[$i - 1] && $priceData[$i] > $priceData[$i + 1]) {
                // Local maximum (resistance)
                $resistances[] = $priceData[$i];
            }
        }

        // Get significant levels (filter by frequency)
        $supportCounts = array_count_values(array_map(function($s) { return round($s, -2); }, $supports)); // Round to hundreds
        $resistanceCounts = array_count_values(array_map(function($r) { return round($r, -2); }, $resistances));

        // Sort by frequency and get top levels
        arsort($supportCounts);
        arsort($resistanceCounts);

        return [
            'supports' => array_keys(array_slice($supportCounts, 0, 5, true)),
            'resistances' => array_keys(array_slice($resistanceCounts, 0, 5, true)),
        ];
    }

    /**
     * Analyze trend direction
     */
    private function analyzeTrend($priceData)
    {
        if (count($priceData) < 2) {
            return ['trend' => 'unknown', 'strength' => 0];
        }

        $start = $priceData[0];
        $end = $priceData[count($priceData) - 1];
        $change = (($end - $start) / $start) * 100;
        
        $trend = 'sideways';
        if ($change > 5) {
            $trend = 'uptrend';
        } elseif ($change < -5) {
            $trend = 'downtrend';
        }

        return [
            'trend' => $trend,
            'change_percentage' => $change,
            'strength' => abs($change) / 10 // Normalize strength
        ];
    }

    /**
     * Get market sentiment analysis
     */
    public function getMarketSentimentAnalysis($categoryId = null, $days = 30)
    {
        $cacheKey = "market_sentiment_" . ($categoryId ?: 'all') . "_{$days}";
        
        return Cache::remember($cacheKey, 900, function() use ($categoryId, $days) {
            // In a real implementation, this would analyze user reviews, feedback, etc.
            // For now, we'll calculate sentiment based on price and activity trends
            $sentimentData = [
                'overall_sentiment' => $this->calculateOverallSentiment($categoryId, $days),
                'category_sentiment' => $this->calculateCategorySentiment($categoryId, $days),
                'regional_sentiment' => $this->calculateRegionalSentiment($categoryId, $days),
                'time_series_sentiment' => $this->calculateTimeSeriesSentiment($categoryId, $days),
                'forecasted_sentiment' => $this->forecastSentiment($categoryId, $days)
            ];

            return $sentimentData;
        });
    }

    /**
     * Calculate overall market sentiment
     */
    private function calculateOverallSentiment($categoryId, $days)
    {
        $query = Ad::where('created_at', '>=', now()->subDays($days));
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        $recentAds = $query->get();
        $totalAds = $recentAds->count();

        if ($totalAds === 0) {
            return ['sentiment' => 'neutral', 'score' => 0.5, 'confidence' => 0.0];
        }

        // Calculate based on various factors
        $priceTrend = $this->analyzePriceTrend($recentAds);
        $activityTrend = $this->analyzeActivityTrend($recentAds, $days);
        $engagementLevel = $this->analyzeEngagementLevel($recentAds);

        $combinedScore = ($priceTrend['score'] * 0.4) + ($activityTrend['score'] * 0.4) + ($engagementLevel['score'] * 0.2);

        $sentiment = 'neutral';
        if ($combinedScore > 0.6) {
            $sentiment = 'positive';
        } elseif ($combinedScore < 0.4) {
            $sentiment = 'negative';
        }

        return [
            'sentiment' => $sentiment,
            'score' => $combinedScore,
            'confidence' => min(1.0, $totalAds / 50) // Higher confidence with more data
        ];
    }

    /**
     * Analyze price trend
     */
    private function analyzePriceTrend($ads)
    {
        if ($ads->count() < 2) {
            return ['score' => 0.5, 'trend' => 'stable'];
        }

        $sortedAds = $ads->sortBy('created_at');
        $firstBatch = $sortedAds->take($sortedAds->count() / 2);
        $secondBatch = $sortedAds->skip($sortedAds->count() / 2);

        $firstAvg = $firstBatch->avg('price') ?: 0;
        $secondAvg = $secondBatch->avg('price') ?: 0;

        if ($firstAvg === 0) {
            $trend = $secondAvg > 0 ? 1.0 : 0.5; // Positive if prices emerged, neutral if no change
        } else {
            $change = ($secondAvg - $firstAvg) / $firstAvg;
            $trend = max(0, min(1, 0.5 + $change)); // Normalize to 0-1 range
        }

        return [
            'score' => $trend,
            'trend' => $trend > 0.6 ? 'increasing' : ($trend < 0.4 ? 'decreasing' : 'stable')
        ];
    }

    /**
     * Analyze activity trend
     */
    private function analyzeActivityTrend($ads, $days)
    {
        if ($days < 7) {
            return ['score' => 0.5, 'trend' => 'stable'];
        }

        $firstPeriod = $ads->filter(function($ad) use ($days) {
            return $ad->created_at->diffInDays(now()) > $days / 2;
        });
        
        $secondPeriod = $ads->filter(function($ad) use ($days) {
            return $ad->created_at->diffInDays(now()) <= $days / 2;
        });

        $firstCount = $firstPeriod->count();
        $secondCount = $secondPeriod->count();

        if ($firstCount === 0) {
            $trend = $secondCount > 0 ? 1.0 : 0.5; // Positive if activity started, neutral if not
        } else {
            $change = ($secondCount - $firstCount) / $firstCount;
            $trend = max(0, min(1, 0.5 + $change)); // Normalize to 0-1 range
        }

        return [
            'score' => $trend,
            'trend' => $trend > 0.6 ? 'increasing' : ($trend < 0.4 ? 'decreasing' : 'stable'),
            'first_period_count' => $firstCount,
            'second_period_count' => $secondCount
        ];
    }

    /**
     * Analyze engagement level
     */
    private function analyzeEngagementLevel($ads)
    {
        $avgViews = $ads->avg('view_count') ?: 0;
        $avgInquiries = $ads->avg('inquiries_count') ?: 0;

        // Normalize engagement (assuming max of 1000 views and 100 inquiries as high engagement)
        $viewsScore = min(1.0, $avgViews / 1000);
        $inquiriesScore = min(1.0, $avgInquiries / 100);

        // Weighted score (inquiries might be more important than views)
        $engagementScore = ($viewsScore * 0.3) + ($inquiriesScore * 0.7);

        return [
            'score' => $engagementScore,
            'avg_views' => $avgViews,
            'avg_inquiries' => $avgInquiries
        ];
    }

    /**
     * Get price prediction algorithms
     */
    public function getPricePredictions($categoryId, $daysForward = 7)
    {
        $cacheKey = "price_predictions_{$categoryId}_{$daysForward}";
        
        return Cache::remember($cacheKey, 1800, function() use ($categoryId, $daysForward) {
            $ads = Ad::where('category_id', $categoryId)
                    ->where('created_at', '>=', now()->subMonths(3))
                    ->orderBy('created_at')
                    ->get();

            if ($ads->count() < 10) {
                return ['error' => 'Not enough data for prediction'];
            }

            // Use simple regression analysis for price prediction
            $priceData = $ads->pluck('price')->values()->toArray();
            $timeData = $ads->pluck('created_at')->values()->map(function($date) {
                return $date->timestamp;
            })->toArray();

            $prediction = $this->linearRegressionPrediction($timeData, $priceData, $daysForward);

            return [
                'category_id' => $categoryId,
                'predictions' => $prediction,
                'confidence' => min(0.9, 0.3 + ($ads->count() / 1000)), // Confidence based on data points
                'method' => 'linear_regression',
                'historical_data_points' => count($priceData),
                'forecast_period' => $daysForward
            ];
        });
    }

    /**
     * Simple linear regression prediction
     */
    private function linearRegressionPrediction($x, $y, $daysForward)
    {
        $n = count($x);
        
        if ($n < 2) return [];

        // Calculate means
        $xMean = array_sum($x) / $n;
        $yMean = array_sum($y) / $n;

        // Calculate slope (m) and y-intercept (b)
        $numerator = 0;
        $denominator = 0;
        
        for ($i = 0; $i < $n; $i++) {
            $numerator += ($x[$i] - $xMean) * ($y[$i] - $yMean);
            $denominator += pow($x[$i] - $xMean, 2);
        }

        if ($denominator == 0) {
            // If all x values are the same, return the mean
            $predictions = [];
            for ($i = 1; $i <= $daysForward; $i++) {
                $predictions[] = [
                    'day' => $i,
                    'predicted_price' => $yMean,
                    'date' => now()->addDays($i)->format('Y-m-d')
                ];
            }
            return $predictions;
        }

        $slope = $numerator / $denominator;
        $intercept = $yMean - ($slope * $xMean);

        // Generate predictions
        $predictions = [];
        $lastX = end($x);
        
        for ($i = 1; $i <= $daysForward; $i++) {
            $futureX = $lastX + ($i * 86400); // Add one day in seconds
            $predictedY = $slope * $futureX + $intercept;
            
            $predictions[] = [
                'day' => $i,
                'predicted_price' => $predictedY,
                'date' => Carbon::createFromTimestamp($futureX)->format('Y-m-d'),
                'slope' => $slope
            ];
        }

        return $predictions;
    }

    /**
     * Get portfolio tracking analytics for a user
     */
    public function getPortfolioAnalytics($userId)
    {
        $user = User::find($userId);
        if (!$user) {
            return ['error' => 'User not found'];
        }

        // Get user's active ads (portfolio items)
        $ads = $user->ads()->where('status', 'active')->get();

        $portfolioValue = 0;
        $portfolioViews = 0;
        $portfolioInquiries = 0;
        $categoryBreakdown = [];
        $priceRangeDistribution = [];
        
        foreach ($ads as $ad) {
            $portfolioValue += $ad->price;
            $portfolioViews += $ad->view_count ?? 0;
            $portfolioInquiries += $ad->inquiries_count ?? 0;

            // Category breakdown
            $categoryName = $ad->category ? $ad->category->name : 'Uncategorized';
            if (!isset($categoryBreakdown[$categoryName])) {
                $categoryBreakdown[$categoryName] = ['count' => 0, 'total_value' => 0];
            }
            $categoryBreakdown[$categoryName]['count']++;
            $categoryBreakdown[$categoryName]['total_value'] += $ad->price;

            // Price range distribution
            $range = $this->getPriceRange($ad->price);
            if (!isset($priceRangeDistribution[$range])) {
                $priceRangeDistribution[$range] = 0;
            }
            $priceRangeDistribution[$range]++;
        }

        $analytics = [
            'portfolio_summary' => [
                'total_items' => $ads->count(),
                'total_value' => $portfolioValue,
                'total_views' => $portfolioViews,
                'total_inquiries' => $portfolioInquiries,
                'average_price' => $ads->count() > 0 ? $portfolioValue / $ads->count() : 0,
                'average_views' => $ads->count() > 0 ? $portfolioViews / $ads->count() : 0,
                'average_inquiries' => $ads->count() > 0 ? $portfolioInquiries / $ads->count() : 0,
            ],
            'category_breakdown' => $categoryBreakdown,
            'price_range_distribution' => $priceRangeDistribution,
            'performance_metrics' => [
                'views_to_inquiry_ratio' => $portfolioViews > 0 ? $portfolioInquiries / $portfolioViews : 0,
                'engagement_rate' => $ads->count() > 0 ? $portfolioInquiries / $ads->count() : 0,
            ],
            'recommendations' => $this->generatePortfolioRecommendations($categoryBreakdown, $ads)
        ];

        return $analytics;
    }

    /**
     * Get price range for distribution
     */
    private function getPriceRange($price)
    {
        if ($price < 10000) return '0 - 10,000';
        if ($price < 50000) return '10,000 - 50,000';
        if ($price < 100000) return '50,000 - 100,000';
        if ($price < 500000) return '100,000 - 500,000';
        return '500,000+';
    }

    /**
     * Generate portfolio recommendations
     */
    private function generatePortfolioRecommendations($categoryBreakdown, $ads)
    {
        $recommendations = [];
        
        // Suggest category diversification if too concentrated
        if (count($categoryBreakdown) < 3) {
            $recommendations[] = [
                'type' => 'diversification',
                'message' => 'Consider diversifying your portfolio across more categories to reduce risk',
                'priority' => 'high'
            ];
        }

        // Look for underperforming items
        foreach ($ads as $ad) {
            if (($ad->view_count ?? 0) < 10 && $ad->inquiries_count < 2) {
                $recommendations[] = [
                    'type' => 'optimization',
                    'message' => "Ad '{$ad->title}' has low engagement. Consider updating the description or images",
                    'priority' => 'medium',
                    'ad_id' => $ad->id
                ];
            }
        }

        // Suggest pricing optimization
        $categories = array_keys($categoryBreakdown);
        foreach ($categories as $category) {
            $categoryAds = $ads->filter(function($ad) use ($category) {
                return $ad->category && $ad->category->name === $category;
            });

            if ($categoryAds->count() > 1) {
                $categoryAvg = $categoryAds->avg('price');
                $outliers = $categoryAds->filter(function($ad) use ($categoryAvg) {
                    return abs($ad->price - $categoryAvg) > ($categoryAvg * 0.5); // More than 50% from average
                });

                if ($outliers->count() > 0) {
                    $recommendations[] = [
                        'type' => 'pricing',
                        'message' => 'Some items in ' . $category . ' are priced significantly differently from the category average. Review pricing strategy',
                        'priority' => 'medium'
                    ];
                }
            }
        }

        return $recommendations;
    }

    /**
     * Get tax reporting data
     */
    public function getTaxReportingData($userId, $year)
    {
        $user = User::find($userId);
        if (!$user) {
            return ['error' => 'User not found'];
        }

        // Get all transactions for the user in the specified year
        $startDate = Carbon::create($year, 1, 1);
        $endDate = Carbon::create($year, 12, 31, 23, 59, 59);

        $transactions = PaymentTransaction::where('user_id', $userId)
                                         ->whereBetween('created_at', [$startDate, $endDate])
                                         ->get();

        $taxData = [
            'user_id' => $userId,
            'year' => $year,
            'total_income' => $transactions->sum('amount'),
            'total_transactions' => $transactions->count(),
            'monthly_breakdown' => $this->getMonthlyBreakdown($transactions, $year),
            'category_breakdown' => $this->getCategoryBreakdown($transactions),
            'tax_summary' => [
                'estimated_tax_liability' => $this->calculateEstimatedTax($transactions),
                'total_taxable_income' => $transactions->sum('amount'),
            ],
            'reporting_periods' => [
                'quarterly' => $this->getQuarterlyBreakdown($transactions, $year),
                'annual_total' => $transactions->sum('amount')
            ]
        ];

        return $taxData;
    }

    /**
     * Get monthly breakdown of transactions
     */
    private function getMonthlyBreakdown($transactions, $year)
    {
        $monthlyData = [];
        for ($month = 1; $month <= 12; $month++) {
            $monthTransactions = $transactions->filter(function($t) use ($month, $year) {
                return $t->created_at->month === $month && $t->created_at->year === $year;
            });

            $monthlyData[] = [
                'month' => Carbon::create($year, $month, 1)->format('F'),
                'month_number' => $month,
                'total_amount' => $monthTransactions->sum('amount'),
                'transaction_count' => $monthTransactions->count(),
                'average_amount' => $monthTransactions->count() > 0 ? $monthTransactions->sum('amount') / $monthTransactions->count() : 0
            ];
        }

        return $monthlyData;
    }

    /**
     * Get category breakdown of transactions
     */
    private function getCategoryBreakdown($transactions)
    {
        // Group transactions by type
        $transactionTypes = $transactions->groupBy('type')->map(function($group) {
            return [
                'count' => $group->count(),
                'total_amount' => $group->sum('amount'),
                'average_amount' => $group->avg('amount')
            ];
        })->toArray();

        return $transactionTypes;
    }

    /**
     * Calculate estimated tax based on transactions
     */
    private function calculateEstimatedTax($transactions)
    {
        // This is a simplified calculation - in reality, tax rules are complex
        $totalIncome = $transactions->sum('amount');
        
        // Simplified tax calculation based on Nigerian tax brackets
        $taxableAmount = max(0, $totalIncome - 300000); // Personal allowance
        $tax = min(300000, $taxableAmount * 0.07); // First bracket: 7% on first 300k
        $tax += max(0, min(300000, ($taxableAmount - 300000)) * 0.11); // Second bracket: 11% on next 300k
        $tax += max(0, min(500000, ($taxableAmount - 600000)) * 0.15); // Third bracket: 15% on next 500k
        $tax += max(0, min(500000, ($taxableAmount - 1100000)) * 0.19); // Fourth bracket: 19% on next 500k
        $tax += max(0, min(1600000, ($taxableAmount - 1600000)) * 0.21); // Fifth bracket: 21% on next 1.6m
        $tax += max(0, ($taxableAmount - 3200000) * 0.24); // Highest bracket: 24% on amounts above 3.2m

        return round($tax, 2);
    }

    /**
     * Get quarterly breakdown
     */
    private function getQuarterlyBreakdown($transactions, $year)
    {
        $quarters = [];
        for ($q = 1; $q <= 4; $q++) {
            $startMonth = ($q - 1) * 3 + 1;
            $endMonth = $q * 3;
            
            $quarterTransactions = $transactions->filter(function($t) use ($startMonth, $endMonth, $year) {
                return $t->created_at->year === $year && 
                       $t->created_at->month >= $startMonth && 
                       $t->created_at->month <= $endMonth;
            });

            $quarters[] = [
                'quarter' => 'Q' . $q,
                'start_date' => Carbon::create($year, $startMonth, 1)->format('Y-m-d'),
                'end_date' => Carbon::create($year, $endMonth, cal_days_in_month(CAL_GREGORIAN, $endMonth, $year))->format('Y-m-d'),
                'total_amount' => $quarterTransactions->sum('amount'),
                'transaction_count' => $quarterTransactions->count()
            ];
        }

        return $quarters;
    }

    /**
     * Get historical data for backtesting
     */
    public function getHistoricalData($categoryId = null, $days = 90)
    {
        $query = Ad::where('created_at', '>=', now()->subDays($days));
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        $ads = $query->orderBy('created_at')->get();

        // Group by date for historical analysis
        $historicalData = [];
        $dailyData = $ads->groupBy(function($ad) {
            return $ad->created_at->format('Y-m-d');
        });

        foreach ($dailyData as $date => $dailyAds) {
            $historicalData[] = [
                'date' => $date,
                'total_listings' => $dailyAds->count(),
                'average_price' => $dailyAds->avg('price'),
                'total_views' => $dailyAds->sum('view_count'),
                'total_inquiries' => $dailyAds->sum('inquiries_count'),
                'category_breakdown' => $this->getCategoryBreakdownForDate($dailyAds)
            ];
        }

        return [
            'data' => $historicalData,
            'timeframe' => $days,
            'category_filter' => $categoryId,
            'total_data_points' => count($historicalData),
            'summary' => [
                'total_listings' => $ads->count(),
                'average_price' => $ads->avg('price'),
                'price_volatility' => $this->calculateVolatility($ads->pluck('price')->toArray())
            ]
        ];
    }

    /**
     * Get category breakdown for a date
     */
    private function getCategoryBreakdownForDate($ads)
    {
        return $ads->groupBy('category_id')->map(function($group) {
            return [
                'count' => $group->count(),
                'avg_price' => $group->avg('price'),
            ];
        })->toArray();
    }

    /**
     * Calculate volatility
     */
    private function calculateVolatility($priceData)
    {
        if (count($priceData) < 2) return 0;

        $mean = array_sum($priceData) / count($priceData);
        $squaredDiffs = array_map(function($price) use ($mean) {
            return pow($price - $mean, 2);
        }, $priceData);

        $variance = array_sum($squaredDiffs) / count($squaredDiffs);
        $volatility = sqrt($variance);

        return $volatility;
    }

    /**
     * Perform correlation analysis between categories
     */
    public function getCorrelationAnalysis($categoryIds = [], $days = 30)
    {
        if (empty($categoryIds)) {
            // Get the top 5 categories by listing count
            $topCategories = DB::table('ads')
                ->select('category_id', DB::raw('COUNT(*) as count'))
                ->where('created_at', '>=', now()->subDays($days))
                ->groupBy('category_id')
                ->orderByDesc('count')
                ->limit(5)
                ->pluck('category_id');
            $categoryIds = $topCategories->toArray();
        }

        $correlations = [];
        
        // Get price data for each category
        $categoryData = [];
        foreach ($categoryIds as $categoryId) {
            $dailyPrices = DB::table('ads')
                ->select(DB::raw('DATE(created_at) as date, AVG(price) as avg_price'))
                ->where('category_id', $categoryId)
                ->where('created_at', '>=', now()->subDays($days))
                ->groupBy(DB::raw('DATE(created_at)'))
                ->orderBy('date')
                ->pluck('avg_price', 'date');
            
            $categoryData[$categoryId] = $dailyPrices;
        }

        // Calculate correlations between all pairs
        foreach ($categoryIds as $cat1) {
            foreach ($categoryIds as $cat2) {
                if ($cat1 >= $cat2) continue; // Avoid duplicates and self-correlations

                // Get common dates
                $commonDates = collect($categoryData[$cat1])->keys()
                    ->intersect(collect($categoryData[$cat2])->keys())
                    ->values();

                if ($commonDates->count() < 2) continue; // Need at least 2 data points

                $cat1Prices = [];
                $cat2Prices = [];

                foreach ($commonDates as $date) {
                    $cat1Prices[] = $categoryData[$cat1][$date];
                    $cat2Prices[] = $categoryData[$cat2][$date];
                }

                $correlation = $this->calculateCorrelation($cat1Prices, $cat2Prices);

                $correlations[] = [
                    'category1' => Category::find($cat1)->name ?? $cat1,
                    'category2' => Category::find($cat2)->name ?? $cat2,
                    'correlation' => round($correlation, 3),
                    'common_data_points' => count($commonDates)
                ];
            }
        }

        return [
            'correlations' => $correlations,
            'categories_analyzed' => $categoryIds,
            'days_analyzed' => $days,
            'total_pairs' => count($correlations)
        ];
    }

    /**
     * Calculate correlation between two arrays
     */
    private function calculateCorrelation($array1, $array2)
    {
        if (count($array1) !== count($array2) || count($array1) < 2) {
            return 0;
        }

        $n = count($array1);
        $sumX = array_sum($array1);
        $sumY = array_sum($array2);
        $sumXY = 0;
        $sumX2 = 0;
        $sumY2 = 0;

        for ($i = 0; $i < $n; $i++) {
            $sumXY += $array1[$i] * $array2[$i];
            $sumX2 += $array1[$i] * $array1[$i];
            $sumY2 += $array2[$i] * $array2[$i];
        }

        $numerator = $n * $sumXY - $sumX * $sumY;
        $denominator = sqrt(($n * $sumX2 - $sumX * $sumX) * ($n * $sumY2 - $sumY * $sumY));

        if ($denominator == 0) {
            return 0;
        }

        return $numerator / $denominator;
    }
}