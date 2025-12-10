<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\Ad;
use App\Models\User;
use App\Models\Category;
use App\Models\PaymentTransaction;
use Carbon\Carbon;

class RiskManagementService
{
    /**
     * Calculate portfolio risk
     */
    public function calculatePortfolioRisk($userId, $portfolioItems = null)
    {
        if (!$portfolioItems) {
            $user = User::find($userId);
            if (!$user) {
                return ['error' => 'User not found'];
            }

            // Get user's active ads as portfolio items
            $portfolioItems = $user->ads()->where('status', 'active')->get();
        }

        $riskMetrics = [
            'total_exposure' => 0,
            'concentration_risk' => 0,
            'liquidity_risk' => 0,
            'price_volatility' => 0,
            'category_diversification' => 0,
            'overall_risk_score' => 0
        ];

        $totalValue = 0;
        $categoryValues = [];
        $priceHistory = [];

        foreach ($portfolioItems as $item) {
            $value = $item->price;
            $totalValue += $value;

            // Track category values for diversification calculation
            $categoryName = $item->category ? $item->category->name : 'Uncategorized';
            $categoryValues[$categoryName] = ($categoryValues[$categoryName] ?? 0) + $value;

            // Track prices for volatility calculation
            $priceHistory[] = $value;
        }

        // Calculate risk metrics
        $riskMetrics['total_exposure'] = $totalValue;

        // Concentration Risk (Herfindahl-Hirschman Index approach)
        $concentrationSum = 0;
        foreach ($categoryValues as $value) {
            $proportion = $value / $totalValue;
            $concentrationSum += $proportion * $proportion;
        }
        $riskMetrics['concentration_risk'] = min(1, $concentrationSum * 100); // Scale to 0-100

        // Price volatility risk
        $riskMetrics['price_volatility'] = $this->calculateVolatility($priceHistory);

        // Diversification score (opposite of concentration)
        $riskMetrics['category_diversification'] = 100 - $riskMetrics['concentration_risk'];

        // Calculate overall risk score (weighted combination)
        $riskMetrics['overall_risk_score'] = (
            ($riskMetrics['concentration_risk'] * 0.4) +
            ($riskMetrics['price_volatility'] * 0.3) +
            ($riskMetrics['liquidity_risk'] * 0.3)  // liquidity risk would need more data to calculate properly
        );

        $riskMetrics['risk_level'] = $this->getRiskLevel($riskMetrics['overall_risk_score']);
        $riskMetrics['recommendations'] = $this->generateRiskRecommendations($riskMetrics, $categoryValues);

        return $riskMetrics;
    }

    /**
     * Calculate price volatility
     */
    private function calculateVolatility($prices)
    {
        if (count($prices) < 2) {
            return 0;
        }

        $mean = array_sum($prices) / count($prices);
        $squaredDiffs = array_map(function($price) use ($mean) {
            return pow($price - $mean, 2);
        }, $prices);

        $variance = array_sum($squaredDiffs) / count($squaredDiffs);
        $volatility = sqrt($variance);

        // Normalize to 0-100 scale
        return min(100, ($volatility / $mean) * 100);
    }

    /**
     * Get risk level based on score
     */
    private function getRiskLevel($score)
    {
        if ($score < 20) return 'very_low';
        if ($score < 40) return 'low';
        if ($score < 60) return 'medium';
        if ($score < 80) return 'high';
        return 'very_high';
    }

    /**
     * Generate risk recommendations
     */
    private function generateRiskRecommendations($riskMetrics, $categoryValues)
    {
        $recommendations = [];

        if ($riskMetrics['concentration_risk'] > 60) {
            $topCategory = array_keys($categoryValues, max($categoryValues))[0];
            $recommendations[] = [
                'type' => 'diversification',
                'message' => "High concentration risk detected. Your portfolio is heavily weighted in {$topCategory}. Consider diversifying across more categories.",
                'severity' => 'high'
            ];
        }

        if ($riskMetrics['price_volatility'] > 50) {
            $recommendations[] = [
                'type' => 'volatility',
                'message' => 'High price volatility in your portfolio. Consider adding more stable items.',
                'severity' => 'medium'
            ];
        }

        if ($riskMetrics['category_diversification'] < 30) {
            $recommendations[] = [
                'type' => 'diversification',
                'message' => 'Low diversification in your portfolio. Consider adding items from different categories.',
                'severity' => 'high'
            ];
        }

        return $recommendations;
    }

    /**
     * Calculate diversification analyzer
     */
    public function calculateDiversification($userId)
    {
        $user = User::find($userId);
        if (!$user) {
            return ['error' => 'User not found'];
        }

        $ads = $user->ads()->where('status', 'active')->get();

        $categoryDistribution = [];
        $totalValue = 0;

        foreach ($ads as $ad) {
            $totalValue += $ad->price;
            $categoryName = $ad->category ? $ad->category->name : 'Uncategorized';
            $categoryDistribution[$categoryName] = ($categoryDistribution[$categoryName] ?? 0) + $ad->price;
        }

        // Calculate distribution percentages
        $distributionPercentages = [];
        foreach ($categoryDistribution as $category => $value) {
            $distributionPercentages[$category] = ($value / $totalValue) * 100;
        }

        // Calculate diversification metrics
        $diversificationMetrics = [
            'total_categories' => count($categoryDistribution),
            'category_distribution' => $distributionPercentages,
            'concentration_ratio' => $this->calculateConcentrationRatio($distributionPercentages),
            'herfindahl_hirschman_index' => $this->calculateHHI($distributionPercentages),
            'shannon_diversity_index' => $this->calculateShannonIndex($distributionPercentages),
            'diversification_score' => $this->calculateDiversificationScore($distributionPercentages),
            'recommendations' => $this->generateDiversificationRecommendations($distributionPercentages)
        ];

        return $diversificationMetrics;
    }

    /**
     * Calculate concentration ratio (top 3 categories)
     */
    private function calculateConcentrationRatio($distributionPercentages)
    {
        arsort($distributionPercentages);
        $top3 = array_slice($distributionPercentages, 0, 3, true);
        return array_sum($top3);
    }

    /**
     * Calculate Herfindahl-Hirschman Index
     */
    private function calculateHHI($distributionPercentages)
    {
        $hhi = 0;
        foreach ($distributionPercentages as $percentage) {
            $hhi += pow($percentage, 2);
        }
        return $hhi;
    }

    /**
     * Calculate Shannon Diversity Index
     */
    private function calculateShannonIndex($distributionPercentages)
    {
        $shannon = 0;
        $totalP = array_sum($distributionPercentages);

        foreach ($distributionPercentages as $p) {
            if ($p > 0) {
                $proportion = $p / $totalP;
                $shannon += $proportion * log($proportion);
            }
        }

        return -$shannon;
    }

    /**
     * Calculate diversification score (0-100)
     */
    private function calculateDiversificationScore($distributionPercentages)
    {
        // Normalize Shannon Index to 0-100 scale
        $shannon = $this->calculateShannonIndex($distributionPercentages);
        $maxPossible = log(count($distributionPercentages)); // Max possible for current categories

        if ($maxPossible === 0) return 0;

        $score = ($shannon / $maxPossible) * 100;
        return min(100, $score);
    }

    /**
     * Generate diversification recommendations
     */
    private function generateDiversificationRecommendations($distributionPercentages)
    {
        arsort($distributionPercentages);
        $topCategory = array_keys($distributionPercentages)[0];
        $topPercentage = array_values($distributionPercentages)[0];

        $recommendations = [];

        if ($topPercentage > 50) {
            $recommendations[] = [
                'type' => 'oversaturation',
                'message' => "Your portfolio is heavily concentrated in {$topCategory} ({$topPercentage}%), which increases risk. Consider diversifying into other categories.",
                'priority' => 'high'
            ];
        } elseif ($topPercentage > 30) {
            $recommendations[] = [
                'type' => 'concentration',
                'message' => "Your portfolio has some concentration in {$topCategory} ({$topPercentage}%). Consider adding more variety.",
                'priority' => 'medium'
            ];
        }

        return $recommendations;
    }

    /**
     * Calculate volatility indicators
     */
    public function calculateVolatilityIndicators($categoryId, $days = 30)
    {
        $ads = Ad::where('category_id', $categoryId)
                 ->where('created_at', '>=', now()->subDays($days))
                 ->get();

        if ($ads->count() < 2) {
            return ['error' => 'Not enough data for volatility calculation'];
        }

        $prices = $ads->pluck('price')->toArray();

        $volatilityIndicators = [
            'std_deviation' => $this->calculateStandardDeviation($prices),
            'variance' => $this->calculateVariance($prices),
            'coefficient_of_variation' => $this->calculateCoefficientOfVariation($prices),
            'price_range' => ['min' => min($prices), 'max' => max($prices), 'spread' => max($prices) - min($prices)],
            'average_price' => array_sum($prices) / count($prices),
            'volatility_percentage' => $this->calculateVolatilityPercentage($prices),
            'trend_stability' => $this->calculateTrendStability($prices),
            'risk_assessment' => $this->assessRiskLevel($prices),
        ];

        return $volatilityIndicators;
    }

    /**
     * Calculate standard deviation
     */
    private function calculateStandardDeviation($values)
    {
        $mean = array_sum($values) / count($values);
        $sumOfSquares = 0;

        foreach ($values as $value) {
            $sumOfSquares += pow($value - $mean, 2);
        }

        $variance = $sumOfSquares / count($values);
        return sqrt($variance);
    }

    /**
     * Calculate variance
     */
    private function calculateVariance($values)
    {
        $mean = array_sum($values) / count($values);
        $sumOfSquares = 0;

        foreach ($values as $value) {
            $sumOfSquares += pow($value - $mean, 2);
        }

        return $sumOfSquares / count($values);
    }

    /**
     * Calculate coefficient of variation
     */
    private function calculateCoefficientOfVariation($values)
    {
        $mean = array_sum($values) / count($values);
        $stdDev = $this->calculateStandardDeviation($values);

        if ($mean == 0) return 0;

        return ($stdDev / $mean) * 100;
    }

    /**
     * Calculate volatility percentage
     */
    private function calculateVolatilityPercentage($prices)
    {
        if (count($prices) < 2) return 0;

        $mean = array_sum($prices) / count($prices);
        $stdDev = $this->calculateStandardDeviation($prices);

        return ($stdDev / $mean) * 100;
    }

    /**
     * Calculate trend stability
     */
    private function calculateTrendStability($prices)
    {
        if (count($prices) < 2) return 0;

        $changes = [];
        for ($i = 1; $i < count($prices); $i++) {
            $changes[] = abs($prices[$i] - $prices[$i-1]);
        }

        $avgChange = array_sum($changes) / count($changes);
        $maxPrice = max($prices);

        // Stability as inverse of percentage changes
        return max(0, 100 - (($avgChange / $maxPrice) * 1000)); // Scaling factor to make it reasonable
    }

    /**
     * Assess risk level based on volatility
     */
    private function assessRiskLevel($prices)
    {
        $volatility = $this->calculateVolatilityPercentage($prices);
        
        if ($volatility < 5) return 'low';
        if ($volatility < 15) return 'medium';
        if ($volatility < 30) return 'high';
        return 'very_high';
    }

    /**
     * Calculate risk/reward ratio calculator
     */
    public function calculateRiskRewardRatio($potentialGain, $potentialLoss, $probabilityOfGain = 0.5)
    {
        // Risk/Reward ratio = (Potential Gain * Probability of Gain) / (Potential Loss * Probability of Loss)
        $probabilityOfLoss = 1 - $probabilityOfGain;
        
        $riskRewardRatio = ($potentialGain * $probabilityOfGain) / ($potentialLoss * $probabilityOfLoss);
        
        return [
            'potential_gain' => $potentialGain,
            'potential_loss' => $potentialLoss,
            'probability_of_gain' => $probabilityOfGain,
            'probability_of_loss' => $probabilityOfLoss,
            'risk_reward_ratio' => $riskRewardRatio,
            'is_favorable' => $riskRewardRatio > 1,
            'analysis' => $this->analyzeRiskReward($riskRewardRatio, $potentialGain, $potentialLoss)
        ];
    }

    /**
     * Analyze risk/reward ratio
     */
    private function analyzeRiskReward($ratio, $gain, $loss)
    {
        $analysis = [
            'interpretation' => '',
            'recommendation' => '',
            'risk_level' => ''
        ];

        if ($ratio > 2) {
            $analysis['interpretation'] = 'Highly favorable risk/reward ratio';
            $analysis['recommendation'] = 'This investment has a very good risk/reward profile';
            $analysis['risk_level'] = 'low';
        } elseif ($ratio > 1.5) {
            $analysis['interpretation'] = 'Favorable risk/reward ratio';
            $analysis['recommendation'] = 'Good risk/reward profile';
            $analysis['risk_level'] = 'low_to_medium';
        } elseif ($ratio > 1) {
            $analysis['interpretation'] = 'Acceptable risk/reward ratio';
            $analysis['recommendation'] = 'Acceptable risk/reward profile';
            $analysis['risk_level'] = 'medium';
        } elseif ($ratio > 0.5) {
            $analysis['interpretation'] = 'Unfavorable risk/reward ratio';
            $analysis['recommendation'] = 'Consider carefully before investing';
            $analysis['risk_level'] = 'medium_to_high';
        } else {
            $analysis['interpretation'] = 'Highly unfavorable risk/reward ratio';
            $analysis['recommendation'] = 'Not recommended to invest';
            $analysis['risk_level'] = 'high';
        }

        return $analysis;
    }

    /**
     * Calculate position sizing calculator
     */
    public function calculatePositionSize($accountSize, $riskPercentage, $entryPrice, $stopLossPrice)
    {
        // Calculate risk amount
        $riskAmount = ($accountSize * $riskPercentage) / 100;

        // Calculate stop loss distance
        $stopLossDistance = abs($entryPrice - $stopLossPrice);

        // Prevent division by zero
        if ($stopLossDistance == 0) {
            return ['error' => 'Stop loss price must be different from entry price'];
        }

        // Calculate position size
        $positionSize = $riskAmount / $stopLossDistance;

        // Calculate total cost
        $totalCost = $positionSize * $entryPrice;

        return [
            'account_size' => $accountSize,
            'risk_percentage' => $riskPercentage,
            'risk_amount' => $riskAmount,
            'entry_price' => $entryPrice,
            'stop_loss_price' => $stopLossPrice,
            'stop_loss_distance' => $stopLossDistance,
            'position_size' => $positionSize,
            'total_cost' => $totalCost,
            'risk_to_position_ratio' => $riskAmount / $totalCost,
            'recommendations' => $this->generatePositionSizingRecommendations($totalCost, $accountSize, $stopLossDistance)
        ];
    }

    /**
     * Generate position sizing recommendations
     */
    private function generatePositionSizingRecommendations($totalCost, $accountSize, $stopLossDistance)
    {
        $recommendations = [];

        if ($totalCost > $accountSize * 0.1) { // More than 10% of account
            $recommendations[] = [
                'type' => 'position_size',
                'message' => 'Position size is quite large relative to account size. Consider reducing position size.',
                'severity' => 'high'
            ];
        }

        if ($stopLossDistance / $accountSize > 0.01) { // Stop loss distance is large relative to account
            $recommendations[] = [
                'type' => 'stop_loss',
                'message' => 'Stop loss is too far from entry point. Consider a closer stop loss.',
                'severity' => 'medium'
            ];
        }

        return $recommendations;
    }

    /**
     * Perform drawdown analysis
     */
    public function calculateDrawdownAnalysis($userId, $days = 90)
    {
        $user = User::find($userId);
        if (!$user) {
            return ['error' => 'User not found'];
        }

        // Get transaction history for the user
        $transactions = PaymentTransaction::where('user_id', $userId)
                                         ->where('created_at', '>=', now()->subDays($days))
                                         ->orderBy('created_at')
                                         ->get();

        if ($transactions->count() === 0) {
            return ['error' => 'No transaction history found'];
        }

        // Calculate running balance over time
        $balanceHistory = [];
        $runningBalance = 0;
        $maxBalance = 0;
        $maxBalanceDate = null;
        $currentBalance = 0;

        foreach ($transactions as $transaction) {
            if ($transaction->status === 'success') {
                $runningBalance += $transaction->amount;
                $currentBalance = $runningBalance;

                if ($runningBalance > $maxBalance) {
                    $maxBalance = $runningBalance;
                    $maxBalanceDate = $transaction->created_at;
                }

                $balanceHistory[] = [
                    'date' => $transaction->created_at->format('Y-m-d'),
                    'balance' => $runningBalance,
                    'transaction_id' => $transaction->id
                ];
            }
        }

        // Calculate drawdown metrics
        $drawdownMetrics = [
            'max_balance' => $maxBalance,
            'current_balance' => $currentBalance,
            'max_drawdown_amount' => $maxBalance - $currentBalance,
            'max_drawdown_percentage' => $maxBalance > 0 ? (($maxBalance - $currentBalance) / $maxBalance) * 100 : 0,
            'balance_history' => $balanceHistory,
            'recovery_needed' => $maxBalance > $currentBalance ? ($maxBalance - $currentBalance) : 0,
            'recovery_percentage_needed' => $currentBalance > 0 ? ((($maxBalance / $currentBalance) - 1) * 100) : 0,
            'analysis' => $this->analyzeDrawdown($maxBalance, $currentBalance, $balanceHistory)
        ];

        return $drawdownMetrics;
    }

    /**
     * Analyze drawdown
     */
    private function analyzeDrawdown($maxBalance, $currentBalance, $balanceHistory)
    {
        $analysis = [
            'status' => '',
            'health' => '',
            'recommendations' => []
        ];

        if ($maxBalance === 0) {
            $analysis['status'] = 'No positive balance recorded';
            $analysis['health'] = 'very_poor';
            return $analysis;
        }

        $drawdownPercent = (($maxBalance - $currentBalance) / $maxBalance) * 100;

        if ($drawdownPercent < 10) {
            $analysis['status'] = 'Healthy portfolio with minimal drawdown';
            $analysis['health'] = 'excellent';
        } elseif ($drawdownPercent < 20) {
            $analysis['status'] = 'Moderate drawdown, acceptable level';
            $analysis['health'] = 'good';
        } elseif ($drawdownPercent < 30) {
            $analysis['status'] = 'Significant drawdown, consider reviewing strategy';
            $analysis['health'] = 'fair';
        } else {
            $analysis['status'] = 'High drawdown, immediate attention required';
            $analysis['health'] = 'poor';
        }

        // Generate recommendations based on drawdown analysis
        if ($drawdownPercent > 20) {
            $analysis['recommendations'][] = [
                'type' => 'risk_management',
                'message' => 'High drawdown detected. Consider implementing or adjusting stop-loss strategies.',
                'priority' => 'high'
            ];
        }

        if ($drawdownPercent > 30) {
            $analysis['recommendations'][] = [
                'type' => 'review',
                'message' => 'Significant drawdown. Review your portfolio and investment strategies.',
                'priority' => 'critical'
            ];
        }

        return $analysis;
    }

    /**
     * Calculate performance attribution
     */
    public function calculatePerformanceAttribution($userId, $startDate, $endDate)
    {
        $user = User::find($userId);
        if (!$user) {
            return ['error' => 'User not found'];
        }

        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate);

        // Get ads created in the period
        $ads = $user->ads()
                   ->whereBetween('created_at', [$startDate, $endDate])
                   ->get();

        // Get successful transactions during the period
        $transactions = PaymentTransaction::where('user_id', $userId)
                                         ->whereBetween('created_at', [$startDate, $endDate])
                                         ->where('status', 'success')
                                         ->get();

        $performanceAttribution = [
            'period' => [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'duration_days' => $startDate->diffInDays($endDate)
            ],
            'ad_performance' => $this->calculateAdPerformance($ads),
            'transaction_performance' => $this->calculateTransactionPerformance($transactions),
            'category_performance' => $this->calculateCategoryPerformance($ads),
            'time_based_performance' => $this->calculateTimeBasedPerformance($ads, $startDate, $endDate),
            'attributions' => $this->generatePerformanceAttributions($ads, $transactions)
        ];

        return $performanceAttribution;
    }

    /**
     * Calculate ad performance
     */
    private function calculateAdPerformance($ads)
    {
        $totalRevenue = 0;
        $totalViews = 0;
        $totalInquiries = 0;
        $successfulSales = 0;

        foreach ($ads as $ad) {
            $totalViews += $ad->view_count ?? 0;
            $totalInquiries += $ad->inquiries_count ?? 0;
            
            // If we had actual sales data, we would use it here
            if (method_exists($ad, 'transactions')) {
                $successfulSales += $ad->transactions()->where('status', 'completed')->count();
            }

            $totalRevenue += $ad->price; // This is just listing price, not actual revenue
        }

        return [
            'total_ads' => $ads->count(),
            'total_views' => $totalViews,
            'total_inquiries' => $totalInquiries,
            'successful_sales' => $successfulSales,
            'average_views_per_ad' => $ads->count() > 0 ? $totalViews / $ads->count() : 0,
            'average_inquiries_per_ad' => $ads->count() > 0 ? $totalInquiries / $ads->count() : 0,
            'conversion_rate' => $totalInquiries > 0 ? ($successfulSales / $totalInquiries) * 100 : 0
        ];
    }

    /**
     * Calculate transaction performance
     */
    private function calculateTransactionPerformance($transactions)
    {
        $totalRevenue = $transactions->sum('amount');
        $transactionCount = $transactions->count();
        $averageTransactionValue = $transactionCount > 0 ? $totalRevenue / $transactionCount : 0;

        return [
            'total_revenue' => $totalRevenue,
            'transaction_count' => $transactionCount,
            'average_transaction_value' => $averageTransactionValue,
            'daily_average_revenue' => $transactionCount > 0 ? $totalRevenue / $transactions->first()->created_at->diffInDays($transactions->last()->created_at) : 0
        ];
    }

    /**
     * Calculate category performance
     */
    private function calculateCategoryPerformance($ads)
    {
        $categoryPerformance = [];
        
        foreach ($ads as $ad) {
            $categoryName = $ad->category ? $ad->category->name : 'Uncategorized';
            
            if (!isset($categoryPerformance[$categoryName])) {
                $categoryPerformance[$categoryName] = [
                    'count' => 0,
                    'total_price' => 0,
                    'total_views' => 0,
                    'total_inquiries' => 0
                ];
            }
            
            $categoryPerformance[$categoryName]['count']++;
            $categoryPerformance[$categoryName]['total_price'] += $ad->price;
            $categoryPerformance[$categoryName]['total_views'] += $ad->view_count ?? 0;
            $categoryPerformance[$categoryName]['total_inquiries'] += $ad->inquiries_count ?? 0;
        }

        // Calculate averages
        foreach ($categoryPerformance as $category => $data) {
            $categoryPerformance[$category]['avg_price'] = $data['count'] > 0 ? $data['total_price'] / $data['count'] : 0;
            $categoryPerformance[$category]['avg_views'] = $data['count'] > 0 ? $data['total_views'] / $data['count'] : 0;
            $categoryPerformance[$category]['avg_inquiries'] = $data['count'] > 0 ? $data['total_inquiries'] / $data['count'] : 0;
        }

        return $categoryPerformance;
    }

    /**
     * Calculate time-based performance
     */
    private function calculateTimeBasedPerformance($ads, $startDate, $endDate)
    {
        $totalDays = $startDate->diffInDays($endDate);
        $intervals = min(30, $totalDays); // Don't have too many intervals for performance
        
        $intervalSize = max(1, $totalDays / $intervals);
        $timeIntervals = [];

        for ($i = 0; $i < $intervals; $i++) {
            $intervalStart = $startDate->copy()->addDays($i * $intervalSize);
            $intervalEnd = $startDate->copy()->addDays(($i + 1) * $intervalSize);

            $intervalAds = $ads->filter(function($ad) use ($intervalStart, $intervalEnd) {
                return $ad->created_at->between([$intervalStart, $intervalEnd]);
            });

            $timeIntervals[] = [
                'period' => $intervalStart->format('M j') . ' - ' . $intervalEnd->format('M j'),
                'start_date' => $intervalStart,
                'end_date' => $intervalEnd,
                'ads_count' => $intervalAds->count(),
                'total_value' => $intervalAds->sum('price'),
                'total_views' => $intervalAds->sum('view_count'),
                'total_inquiries' => $intervalAds->sum('inquiries_count')
            ];
        }

        return $timeIntervals;
    }

    /**
     * Generate performance attributions
     */
    private function generatePerformanceAttributions($ads, $transactions)
    {
        $attributions = [];

        // Check for high-performing categories
        $categoryPerformance = $this->calculateCategoryPerformance($ads);
        arsort($categoryPerformance, SORT_NUMERIC);

        $topCategories = array_slice($categoryPerformance, 0, 3, true);
        foreach ($topCategories as $category => $data) {
            $attributions[] = [
                'type' => 'category_performance',
                'factor' => $category,
                'contribution' => 'High performer',
                'impact' => 'Positive',
                'value' => $data['total_price']
            ];
        }

        // Check for time-based patterns
        $avgDailyRevenue = $this->calculateTransactionPerformance($transactions)['daily_average_revenue'];
        if ($avgDailyRevenue > 0) {
            $attributions[] = [
                'type' => 'time_performance',
                'factor' => 'Daily average revenue',
                'contribution' => $avgDailyRevenue,
                'impact' => 'Revenue driver',
                'value' => $avgDailyRevenue
            ];
        }

        return $attributions;
    }
}