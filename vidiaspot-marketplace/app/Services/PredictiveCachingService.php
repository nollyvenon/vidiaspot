<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PredictiveCachingService
{
    /**
     * User behavior tracking patterns
     */
    private array $behaviorPatterns = [
        'browsing_patterns' => [
            'frequently_visited_categories' => [],
            'peak_browsing_times' => [],
            'preferred_device_types' => [],
            'session_duration_avg' => 0,
        ],
        'search_patterns' => [
            'frequent_search_terms' => [],
            'search_categories' => [],
            'time_between_searches' => 0,
        ],
        'purchase_patterns' => [
            'frequently_bought_items' => [],
            'purchase_timing' => [],
            'average_order_value' => 0,
            'cart_abandonment_patterns' => [],
        ],
        'interaction_patterns' => [
            'preferred_actions' => [],
            'time_spent_on_pages' => [],
            'click_through_rates' => [],
            'conversion_paths' => [],
        ],
    ];

    /**
     * Content popularity metrics
     */
    private array $popularityMetrics = [
        'view_count' => 0,
        'view_duration_avg' => 0,
        'share_count' => 0,
        'save_for_later_count' => 0,
        'related_content_clicks' => 0,
        'user_engagement_score' => 0,
    ];

    /**
     * Cache prediction algorithms
     */
    private array $predictionAlgorithms = [
        'collaborative_filtering' => [
            'name' => 'Collaborative Filtering',
            'description' => 'Predict content based on similar users\' behavior',
            'accuracy' => 0.85,
            'computation_cost' => 'high',
        ],
        'content_based_filtering' => [
            'name' => 'Content-Based Filtering',
            'description' => 'Predict content based on item attributes and user preferences',
            'accuracy' => 0.78,
            'computation_cost' => 'medium',
        ],
        'sequential_pattern_mining' => [
            'name' => 'Sequential Pattern Mining',
            'description' => 'Predict next actions based on sequence of previous actions',
            'accuracy' => 0.82,
            'computation_cost' => 'high',
        ],
        'time_series_analysis' => [
            'name' => 'Time Series Analysis',
            'description' => 'Predict based on temporal patterns and seasonality',
            'accuracy' => 0.75,
            'computation_cost' => 'low',
        ],
    ];

    /**
     * Get user behavior patterns
     */
    public function getUserBehaviorPatterns(string $userId): array
    {
        $cacheKey = "user_behavior_{$userId}";
        $patterns = Cache::get($cacheKey, [
            'browsing_patterns' => [
                'frequently_visited_categories' => [],
                'peak_browsing_times' => [],
                'preferred_device_types' => [],
                'session_duration_avg' => 0,
            ],
            'search_patterns' => [
                'frequent_search_terms' => [],
                'search_categories' => [],
                'time_between_searches' => 0,
            ],
            'purchase_patterns' => [
                'frequently_bought_items' => [],
                'purchase_timing' => [],
                'average_order_value' => 0,
                'cart_abandonment_patterns' => [],
            ],
            'interaction_patterns' => [
                'preferred_actions' => [],
                'time_spent_on_pages' => [],
                'click_through_rates' => [],
                'conversion_paths' => [],
            ],
        ]);

        return [
            'user_id' => $userId,
            'behavior_patterns' => $patterns,
            'calculated_at' => now()->toISOString(),
            'recommendations' => $this->generatePredictiveRecommendations($patterns, $userId),
        ];
    }

    /**
     * Record user activity for behavior analysis
     */
    public function recordUserActivity(string $userId, array $activity): void
    {
        // Validate required fields
        $required = ['action', 'timestamp', 'page_url'];
        foreach ($required as $field) {
            if (!isset($activity[$field])) {
                throw new \InvalidArgumentException("Missing required field: {$field}");
            }
        }

        // Store activity in cache for short-term analysis
        $activityKey = "user_activity_{$userId}_" . date('Y-m-d');
        $dailyActivities = Cache::get($activityKey, []);
        
        $activity['id'] = 'act-' . Str::uuid();
        $activity['recorded_at'] = now()->toISOString();
        
        $dailyActivities[] = $activity;
        Cache::put($activityKey, $dailyActivities, now()->addDay());

        // Update user behavior patterns
        $this->updateBehaviorPatterns($userId, $activity);

        // Update content popularity metrics
        if (isset($activity['content_id'])) {
            $this->updateContentPopularity($activity['content_id'], $activity);
        }
    }

    /**
     * Update behavior patterns based on user activity
     */
    private function updateBehaviorPatterns(string $userId, array $activity): void
    {
        $patterns = $this->getUserBehaviorPatterns($userId)['behavior_patterns'];
        
        switch ($activity['action']) {
            case 'view_page':
                $this->updateBrowsingPatterns($patterns['browsing_patterns'], $activity);
                break;
            case 'search':
                $this->updateSearchPatterns($patterns['search_patterns'], $activity);
                break;
            case 'purchase':
            case 'add_to_cart':
                $this->updatePurchasePatterns($patterns['purchase_patterns'], $activity);
                break;
            case 'click':
            case 'interact':
                $this->updateInteractionPatterns($patterns['interaction_patterns'], $activity);
                break;
        }

        // Update the user's behavior patterns
        $cacheKey = "user_behavior_{$userId}";
        Cache::put($cacheKey, $patterns, now()->addMonths(6));
    }

    /**
     * Update browsing patterns
     */
    private function updateBrowsingPatterns(array &$patterns, array $activity): void
    {
        // Update frequently visited categories
        if (isset($activity['category'])) {
            if (!isset($patterns['frequently_visited_categories'][$activity['category']])) {
                $patterns['frequently_visited_categories'][$activity['category']] = 0;
            }
            $patterns['frequently_visited_categories'][$activity['category']]++;
        }

        // Update peak browsing times
        $hour = Carbon::parse($activity['timestamp'])->hour;
        if (!isset($patterns['peak_browsing_times'][$hour])) {
            $patterns['peak_browsing_times'][$hour] = 0;
        }
        $patterns['peak_browsing_times'][$hour]++;

        // Update preferred device types
        $device = $activity['device_type'] ?? 'unknown';
        if (!isset($patterns['preferred_device_types'][$device])) {
            $patterns['preferred_device_types'][$device] = 0;
        }
        $patterns['preferred_device_types'][$device]++;
    }

    /**
     * Update search patterns
     */
    private function updateSearchPatterns(array &$patterns, array $activity): void
    {
        if (isset($activity['search_term'])) {
            if (!isset($patterns['frequent_search_terms'][$activity['search_term']])) {
                $patterns['frequent_search_terms'][$activity['search_term']] = 0;
            }
            $patterns['frequent_search_terms'][$activity['search_term']]++;
        }

        if (isset($activity['category'])) {
            if (!isset($patterns['search_categories'][$activity['category']])) {
                $patterns['search_categories'][$activity['category']] = 0;
            }
            $patterns['search_categories'][$activity['category']]++;
        }
    }

    /**
     * Update purchase patterns
     */
    private function updatePurchasePatterns(array &$patterns, array $activity): void
    {
        if (isset($activity['product_id'])) {
            if (!isset($patterns['frequently_bought_items'][$activity['product_id']])) {
                $patterns['frequently_bought_items'][$activity['product_id']] = 0;
            }
            $patterns['frequently_bought_items'][$activity['product_id']]++;
        }

        if (isset($activity['timestamp'])) {
            $hour = Carbon::parse($activity['timestamp'])->hour;
            if (!isset($patterns['purchase_timing'][$hour])) {
                $patterns['purchase_timing'][$hour] = 0;
            }
            $patterns['purchase_timing'][$hour]++;
        }

        if (isset($activity['amount'])) {
            $patterns['average_order_value'] = (
                ($patterns['average_order_value'] * $patterns['purchase_count'] ?? 0) + $activity['amount']
            ) / (($patterns['purchase_count'] ?? 0) + 1);
            $patterns['purchase_count'] = ($patterns['purchase_count'] ?? 0) + 1;
        }
    }

    /**
     * Update interaction patterns
     */
    private function updateInteractionPatterns(array &$patterns, array $activity): void
    {
        $actionType = $activity['action'] ?? 'unknown';
        if (!isset($patterns['preferred_actions'][$actionType])) {
            $patterns['preferred_actions'][$actionType] = 0;
        }
        $patterns['preferred_actions'][$actionType]++;
    }

    /**
     * Update content popularity metrics
     */
    private function updateContentPopularity(string $contentId, array $activity): void
    {
        $cacheKey = "content_popularity_{$contentId}";
        $popularity = Cache::get($cacheKey, $this->popularityMetrics);
        
        switch ($activity['action']) {
            case 'view_page':
                $popularity['view_count']++;
                
                // Update average view duration if provided
                if (isset($activity['view_duration'])) {
                    $totalDuration = $popularity['view_duration_avg'] * ($popularity['view_count'] - 1) + $activity['view_duration'];
                    $popularity['view_duration_avg'] = $totalDuration / $popularity['view_count'];
                }
                break;
                
            case 'share':
                $popularity['share_count']++;
                break;
                
            case 'save_for_later':
                $popularity['save_for_later_count']++;
                break;
                
            case 'click_related':
                $popularity['related_content_clicks']++;
                break;
        }

        // Calculate engagement score (weighted combination of various metrics)
        $popularity['user_engagement_score'] = $this->calculateEngagementScore($popularity);

        Cache::put($cacheKey, $popularity, now()->addMonths(6));
    }

    /**
     * Calculate engagement score based on metrics
     */
    private function calculateEngagementScore(array $popularity): float
    {
        // Weighted scoring based on different engagement metrics
        $score = 0;
        
        // Views (lower weight - everyone views content)
        $score += $popularity['view_count'] * 1;
        
        // Shares (higher weight - indicates strong interest)
        $score += $popularity['share_count'] * 10;
        
        // Saves for later (higher weight - intentional revisit intent)
        $score += $popularity['save_for_later_count'] * 8;
        
        // Related clicks (indicates content relevance)
        $score += $popularity['related_content_clicks'] * 5;
        
        // Average view duration (longer view = higher engagement)
        $score += $popularity['view_duration_avg'] * 0.5;
        
        return min(100, $score / 100); // Normalize to 0-100 scale
    }

    /**
     * Generate predictive recommendations for a user
     */
    public function generatePredictiveRecommendations(array $behaviorPatterns, string $userId): array
    {
        $recommendations = [
            'content_to_preload' => [],
            'timing_suggestions' => [],
            'personalized_content' => [],
            'cache_priority' => [],
        ];

        // Based on browsing patterns
        $frequentCategories = $behaviorPatterns['browsing_patterns']['frequently_visited_categories'];
        arsort($frequentCategories);
        $topCategories = array_slice($frequentCategories, 0, 5, true);

        foreach ($topCategories as $category => $count) {
            // Get popular content in these categories
            $popularInCategory = $this->getPopularContentByCategory($category, 10);
            $recommendations['content_to_preload'] = array_merge(
                $recommendations['content_to_preload'],
                $popularInCategory
            );
        }

        // Based on search patterns
        $frequentTerms = $behaviorPatterns['search_patterns']['frequent_search_terms'];
        arsort($frequentTerms);
        $topSearches = array_slice($frequentTerms, 0, 5, true);

        foreach ($topSearches as $term => $count) {
            // Get content related to frequent searches
            $relatedContent = $this->getRelatedContentBySearchTerm($term, 5);
            $recommendations['content_to_preload'] = array_merge(
                $recommendations['content_to_preload'],
                $relatedContent
            );
        }

        // Based on purchase patterns
        $frequentItems = $behaviorPatterns['purchase_patterns']['frequently_bought_items'];
        arsort($frequentItems);
        $topPurchases = array_slice($frequentItems, 0, 3, true);

        foreach ($topPurchases as $itemId => $count) {
            // Get complementary or frequently bought together items
            $complementaryItems = $this->getComplementaryItems($itemId, 5);
            $recommendations['content_to_preload'] = array_merge(
                $recommendations['content_to_preload'],
                $complementaryItems
            );
        }

        // Determine cache priority based on likelihood of interaction
        $recommendations['cache_priority'] = $this->determineCachePriority(
            $recommendations['content_to_preload'],
            $behaviorPatterns
        );

        // Timing suggestions based on peak browsing times
        $peakBrowsingHours = $behaviorPatterns['browsing_patterns']['peak_browsing_times'];
        $recommendations['timing_suggestions'] = $this->getOptimalTimingForUser($peakBrowsingHours);

        return [
            'user_id' => $userId,
            'recommendations' => $recommendations,
            'algorithm_used' => 'combined_predictive_model',
            'confidence_level' => 0.85,
            'generated_at' => now()->toISOString(),
        ];
    }

    /**
     * Get popular content by category
     */
    private function getPopularContentByCategory(string $category, int $limit = 10): array
    {
        // In a real implementation, this would query the database for popular content
        // For this implementation, we'll return sample data
        $sampleContent = [];
        
        for ($i = 0; $i < $limit; $i++) {
            $sampleContent[] = [
                'id' => 'content-' . Str::random(8),
                'title' => "Popular Content in {$category} - " . ($i + 1),
                'category' => $category,
                'type' => 'article', // Could be article, video, product, etc.
                'popularity_score' => mt_rand(70, 100),
                'estimated_load_time' => mt_rand(100, 500) . 'ms',
                'size_kb' => mt_rand(100, 2000),
            ];
        }

        return $sampleContent;
    }

    /**
     * Get related content by search term
     */
    private function getRelatedContentBySearchTerm(string $searchTerm, int $limit = 5): array
    {
        $sampleContent = [];
        
        for ($i = 0; $i < $limit; $i++) {
            $sampleContent[] = [
                'id' => 'search-content-' . Str::random(8),
                'title' => "Related to: {$searchTerm} - " . ($i + 1),
                'search_term' => $searchTerm,
                'type' => 'article',
                'popularity_score' => mt_rand(60, 95),
                'estimated_load_time' => mt_rand(150, 600) . 'ms',
                'size_kb' => mt_rand(200, 3000),
            ];
        }

        return $sampleContent;
    }

    /**
     * Get complementary items for a product
     */
    private function getComplementaryItems(string $itemId, int $limit = 5): array
    {
        $sampleItems = [];
        
        for ($i = 0; $i < $limit; $i++) {
            $sampleItems[] = [
                'id' => 'complement-' . Str::random(8),
                'original_item_id' => $itemId,
                'type' => 'complementary',
                'popularity_score' => mt_rand(65, 90),
                'estimated_load_time' => mt_rand(100, 400) . 'ms',
                'size_kb' => mt_rand(150, 1500),
            ];
        }

        return $sampleItems;
    }

    /**
     * Determine cache priority for content
     */
    private function determineCachePriority(array $contentItems, array $behaviorPatterns): array
    {
        $prioritized = [];
        
        foreach ($contentItems as $item) {
            $priorityScore = 0;
            
            // Boost priority based on user's behavior patterns
            if (isset($behaviorPatterns['browsing_patterns']['frequently_visited_categories'][$item['category']])) {
                $priorityScore += 20;
            }
            
            $priorityScore += ($item['popularity_score'] ?? 50) / 2; // Higher popularity = higher priority
            $priorityScore += mt_rand(1, 10); // Add some randomness for variety
            
            $item['priority_score'] = $priorityScore;
            $item['cache_priority'] = $this->getPriorityLevel($priorityScore);
            
            $prioritized[] = $item;
        }

        // Sort by priority score (highest first)
        usort($prioritized, function ($a, $b) {
            return $b['priority_score'] <=> $a['priority_score'];
        });

        return $prioritized;
    }

    /**
     * Get priority level based on score
     */
    private function getPriorityLevel(int $score): string
    {
        if ($score >= 80) return 'high';
        if ($score >= 60) return 'medium';
        if ($score >= 40) return 'low';
        return 'minimal';
    }

    /**
     * Get optimal timing for user engagement
     */
    private function getOptimalTimingForUser(array $peakBrowsingHours): array
    {
        $suggestions = [];
        
        // Find peak hours for this user
        arsort($peakBrowsingHours);
        $topHours = array_slice($peakBrowsingHours, 0, 3, true);
        
        foreach ($topHours as $hour => $count) {
            $suggestions[] = [
                'hour' => $hour,
                'activity_count' => $count,
                'suggested_preload_time' => $hour . ':00-' . ($hour + 1) . ':00',
                'probability_of_engagement' => round($count / array_sum($peakBrowsingHours) * 100, 2) . '%',
            ];
        }
        
        return $suggestions;
    }

    /**
     * Pre-cache predicted content for a user
     */
    public function preCachePredictedContent(string $userId, array $predictions = null): array
    {
        $behaviors = $this->getUserBehaviorPatterns($userId)['behavior_patterns'];
        
        if (!$predictions) {
            $predictions = $this->generatePredictiveRecommendations($behaviors, $userId)['recommendations'];
        }

        $cachedItems = [];
        $cacheResults = [];
        
        // Pre-cache content with appropriate priorities
        foreach ($predictions['cache_priority'] as $item) {
            $cacheKey = "predicted_content_{$userId}_{$item['id']}";
            $cacheDuration = $this->getCacheDurationForPriority($item['cache_priority']);
            
            // In a real implementation, this would fetch and cache the actual content
            // For this example, we'll store the prediction data
            $cacheItem = [
                'user_id' => $userId,
                'content_id' => $item['id'],
                'priority' => $item['cache_priority'],
                'timestamp' => now()->toISOString(),
                'estimated_size_kb' => $item['size_kb'] ?? 1000,
            ];
            
            \Cache::put($cacheKey, $cacheItem, now()->addMinutes($cacheDuration));
            $cachedItems[] = $item['id'];
            
            $cacheResults[] = [
                'content_id' => $item['id'],
                'cached' => true,
                'priority' => $item['cache_priority'],
                'cache_duration_minutes' => $cacheDuration,
            ];
        }

        return [
            'success' => true,
            'cached_items' => $cacheResults,
            'total_items_cached' => count($cachedItems),
            'user_id' => $userId,
            'executed_at' => now()->toISOString(),
            'message' => 'Predicted content cached successfully',
        ];
    }

    /**
     * Get cache duration based on priority level
     */
    private function getCacheDurationForPriority(string $priority): int
    {
        $durations = [
            'high' => 1440,    // 24 hours
            'medium' => 720,   // 12 hours
            'low' => 180,      // 3 hours
            'minimal' => 60,   // 1 hour
        ];

        return $durations[$priority] ?? 60;
    }

    /**
     * Get content likely to be accessed by user soon
     */
    public function getLikelyToBeAccessedContent(string $userId): array
    {
        $predictions = $this->generatePredictiveRecommendations(
            $this->getUserBehaviorPatterns($userId)['behavior_patterns'],
            $userId
        );

        return [
            'user_id' => $userId,
            'likely_content' => $predictions['recommendations']['cache_priority'],
            'confidence_interval' => 0.8,
            'calculated_at' => now()->toISOString(),
        ];
    }

    /**
     * Get system-wide popular content predictions
     */
    public function getSystemWideContentPredictions(): array
    {
        // In a real implementation, this would analyze all user behaviors across the system
        // For this example, we'll return sample data based on general trends
        $categories = ['electronics', 'fashion', 'home', 'food', 'services'];
        $trendingContent = [];

        foreach ($categories as $category) {
            $trendingContent[$category] = $this->getPopularContentByCategory($category, 5);
        }

        return [
            'trending_content' => $trendingContent,
            'system_wide_predictions' => [
                'seasonal_trends' => $this->getSeasonalTrends(),
                'emerging_categories' => $this->getEmergingCategories(),
                'user_behavior_insights' => $this->getSystemBehaviorInsights(),
            ],
            'calculated_at' => now()->toISOString(),
        ];
    }

    /**
     * Get seasonal trends
     */
    private function getSeasonalTrends(): array
    {
        $month = now()->month;
        $seasonalKeywords = [
            12 => ['holiday', 'gifts', 'winter clothing', 'celebration'],
            1 => ['new year', 'resolutions', 'fitness', 'organizing'],
            2 => ['valentine', 'heart', 'romance'],
            3 => ['spring', 'garden', 'outdoor'],
            6 => ['summer', 'vacation', 'outdoor'],
            9 => ['back to school', 'study', 'office'],
            10 => ['fall', 'seasonal', 'decorations'],
        ];

        return $seasonalKeywords[$month] ?? ['general'];
    }

    /**
     * Get emerging categories
     */
    private function getEmergingCategories(): array
    {
        // Simulate detection of new trending categories
        $emerging = [];
        $categoryGrowthRates = [
            'sustainable products' => 0.45,
            'local businesses' => 0.38,
            'digital services' => 0.32,
            'health products' => 0.28,
            'educational content' => 0.25,
        ];

        foreach ($categoryGrowthRates as $category => $rate) {
            if ($rate > 0.25) { // Threshold for "emerging"
                $emerging[] = [
                    'category' => $category,
                    'growth_rate' => $rate,
                    'confidence' => 'high',
                ];
            }
        }

        return $emerging;
    }

    /**
     * Get system behavior insights
     */
    private function getSystemBehaviorInsights(): array
    {
        // Simulate system-wide behavioral insights
        return [
            'most_active_hours' => [14, 15, 16], // 2-4 PM
            'popular_categories' => ['electronics', 'fashion', 'home'],
            'average_session_duration' => '8.5 minutes',
            'peak_traffic_day' => 'Tuesday',
            'mobile_vs_desktop_ratio' => '68:32',
        ];
    }

    /**
     * Get cache optimization recommendations for the system
     */
    public function getCacheOptimizationRecommendations(): array
    {
        // In a real implementation, this would analyze system cache performance
        $recommendations = [
            'increase_cache_ttl' => [
                'resources' => ['CSS', 'JavaScript', 'Images'],
                'suggested_ttl_increase' => '2x current TTL',
                'estimated_performance_improvement' => '15-25%',
            ],
            'pre_load_frequently_accessed' => [
                'content_types' => ['Homepage', 'Category Pages', 'Product Pages'],
                'time_windows' => ['7AM-9AM', '12PM-2PM', '7PM-9PM'],
                'estimated_reduction_in_origin_requests' => '40-60%',
            ],
            'optimize_cache_invalidation' => [
                'strategy' => 'Smart invalidation based on content change probability',
                'estimated_storage_efficiency_improvement' => '20-30%',
            ],
            'dynamic_caching' => [
                'strategy' => 'User-segmented caching',
                'estimated_hit_rate_improvement' => '10-15%',
            ],
        ];

        return [
            'recommendations' => $recommendations,
            'last_analysis' => now()->toISOString(),
            'implementation_complexity' => [
                'increase_cache_ttl' => 'low',
                'pre_load_frequently_accessed' => 'medium',
                'optimize_cache_invalidation' => 'high',
                'dynamic_caching' => 'high',
            ],
        ];
    }

    /**
     * Get user-specific cache warming schedule
     */
    public function getUserCacheWarmingSchedule(string $userId): array
    {
        $behavior = $this->getUserBehaviorPatterns($userId);
        $prediction = $this->generatePredictiveRecommendations($behavior['behavior_patterns'], $userId);

        $schedule = [
            'user_id' => $userId,
            'warming_times' => [],
            'content_to_warm' => [],
            'priority_levels' => [],
        ];

        // Schedule cache warming based on user's peak activity times
        foreach ($prediction['recommendations']['timing_suggestions'] as $timeSlot) {
            $schedule['warming_times'][] = [
                'time_slot' => $timeSlot['suggested_preload_time'],
                'engagement_probability' => $timeSlot['probability_of_engagement'],
                'content_to_preload_count' => min(10, count($prediction['recommendations']['content_to_preload'])),
            ];
        }

        $schedule['content_to_warm'] = array_slice(
            $prediction['recommendations']['content_to_preload'], 
            0, 
            10 // Limit to 10 most relevant items
        );

        $schedule['priority_levels'] = [
            'high_priority_count' => count(array_filter(
                $prediction['recommendations']['cache_priority'], 
                fn($item) => $item['cache_priority'] === 'high'
            )),
            'medium_priority_count' => count(array_filter(
                $prediction['recommendations']['cache_priority'], 
                fn($item) => $item['cache_priority'] === 'medium'
            )),
            'low_priority_count' => count(array_filter(
                $prediction['recommendations']['cache_priority'], 
                fn($item) => $item['cache_priority'] === 'low'
            )),
        ];

        return $schedule;
    }

    /**
     * Update predictive model based on feedback
     */
    public function updateModelWithFeedback(string $userId, array $feedback): bool
    {
        // In a real implementation, this would update the machine learning model
        // with user feedback to improve future predictions
        
        $feedbackCacheKey = "predictive_feedback_{$userId}_" . date('Y-m');
        $monthlyFeedback = \Cache::get($feedbackCacheKey, []);
        
        $feedback['id'] = 'fdbk-' . Str::uuid();
        $feedback['timestamp'] = now()->toISOString();
        
        $monthlyFeedback[] = $feedback;
        
        // Keep only last 100 feedback items to prevent cache growth
        if (count($monthlyFeedback) > 100) {
            $monthlyFeedback = array_slice($monthlyFeedback, -100);
        }
        
        \Cache::put($feedbackCacheKey, $monthlyFeedback, now()->addMonths(3));
        
        // Update the user's behavior model based on feedback
        if (isset($feedback['accuracy_rating'])) {
            // Adjust model weights based on accuracy feedback
            $this->adjustPredictionAccuracy($userId, $feedback);
        }
        
        return true;
    }

    /**
     * Adjust prediction accuracy based on user feedback
     */
    private function adjustPredictionAccuracy(string $userId, array $feedback): void
    {
        // Simulate adjusting model parameters based on feedback
        $accuracyKey = "prediction_accuracy_{$userId}";
        $accuracyData = \Cache::get($accuracyKey, [
            'total_predictions' => 0,
            'accurate_predictions' => 0,
            'user_feedback_score' => 5, // Scale of 1-10
        ]);
        
        $accuracyData['total_predictions']++;
        if ($feedback['accuracy_rating'] >= 7) {
            $accuracyData['accurate_predictions']++;
        }
        
        $accuracyData['user_feedback_score'] = (
            ($accuracyData['user_feedback_score'] * ($accuracyData['total_predictions'] - 1)) + 
            $feedback['accuracy_rating']
        ) / $accuracyData['total_predictions'];
        
        \Cache::put($accuracyKey, $accuracyData, now()->addMonths(6));
    }
}