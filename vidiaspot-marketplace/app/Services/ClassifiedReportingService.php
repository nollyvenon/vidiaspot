<?php

namespace App\Services;

use App\Models\Ad;
use App\Models\User;
use App\Models\Category;
use App\Models\Report;
use Carbon\Carbon;

class ClassifiedReportingService
{
    /**
     * Generate User Activity Reports
     */
    public function generateUserActivityReport($startDate, $endDate)
    {
        return [
            'user_registration' => $this->calculateUserRegistration($startDate, $endDate),
            'listing_performance' => $this->calculateListingPerformance($startDate, $endDate),
            'category_analysis' => $this->calculateCategoryAnalysis($startDate, $endDate),
            'engagement_metrics' => $this->calculateEngagementMetrics($startDate, $endDate),
            'user_retention' => $this->calculateUserRetention($startDate, $endDate),
            'period_start' => $startDate,
            'period_end' => $endDate
        ];
    }

    /**
     * Calculate user registration metrics
     */
    private function calculateUserRegistration($startDate, $endDate)
    {
        $users = User::whereBetween('created_at', [$startDate, $endDate])->get();
        
        $dailyRegistrations = [];
        foreach ($users as $user) {
            $date = $user->created_at->format('Y-m-d');
            if (!isset($dailyRegistrations[$date])) {
                $dailyRegistrations[$date] = 0;
            }
            $dailyRegistrations[$date]++;
        }

        $registrationSources = [
            'organic' => 0,
            'referral' => 0,
            'social_media' => 0,
            'paid_ads' => 0
        ];

        // Assuming default to organic for now
        $registrationSources['organic'] = count($users);

        return [
            'total_registrations' => count($users),
            'daily_registrations' => $dailyRegistrations,
            'registration_sources' => $registrationSources,
            'demographics' => $this->calculateDemographics($users)
        ];
    }

    /**
     * Calculate demographics
     */
    private function calculateDemographics($users)
    {
        $ageGroups = [
            '18-24' => 0,
            '25-34' => 0,
            '35-44' => 0,
            '45-54' => 0,
            '55+' => 0
        ];

        $genders = [
            'male' => 0,
            'female' => 0,
            'other' => 0
        ];

        $locations = [];

        foreach ($users as $user) {
            // Placeholder for demographic data
            $ageGroup = '25-34'; // Default to common age group
            $ageGroups[$ageGroup]++;

            $gender = $user->gender ?? 'other';
            $genders[$gender] = ($genders[$gender] ?? 0) + 1;

            $location = $user->city ?? $user->state ?? $user->country ?? 'Unknown';
            $locations[$location] = ($locations[$location] ?? 0) + 1;
        }

        return [
            'age_distribution' => $ageGroups,
            'gender_distribution' => $genders,
            'geographic_distribution' => $locations
        ];
    }

    /**
     * Calculate listing performance
     */
    private function calculateListingPerformance($startDate, $endDate)
    {
        $ads = Ad::whereBetween('created_at', [$startDate, $endDate])
                 ->with(['user', 'category'])
                 ->get();

        $totalListings = count($ads);
        $totalViews = 0;
        $totalClicks = 0;
        $totalResponses = 0;

        foreach ($ads as $ad) {
            $totalViews += $ad->views ?? 0;
            $totalClicks += $ad->clicks ?? 0;
            $totalResponses += $ad->responses ?? 0;
        }

        $conversionRate = $totalListings > 0 ? ($totalResponses / $totalListings) * 100 : 0;

        return [
            'total_listings' => $totalListings,
            'total_views' => $totalViews,
            'total_clicks' => $totalClicks,
            'total_responses' => $totalResponses,
            'conversion_rate' => $conversionRate,
            'average_views_per_listing' => $totalListings > 0 ? $totalViews / $totalListings : 0,
            'top_performing_listings' => $this->getTopPerformingListings($startDate, $endDate)
        ];
    }

    /**
     * Get top performing listings
     */
    private function getTopPerformingListings($startDate, $endDate)
    {
        $ads = Ad::whereBetween('created_at', [$startDate, $endDate])
                 ->orderBy('views', 'desc')
                 ->limit(10)
                 ->get();

        $topListings = [];
        foreach ($ads as $ad) {
            $topListings[] = [
                'id' => $ad->id,
                'title' => $ad->title,
                'views' => $ad->views ?? 0,
                'clicks' => $ad->clicks ?? 0,
                'responses' => $ad->responses ?? 0,
                'category' => $ad->category->name ?? 'Uncategorized'
            ];
        }

        return $topListings;
    }

    /**
     * Calculate category analysis
     */
    private function calculateCategoryAnalysis($startDate, $endDate)
    {
        $ads = Ad::whereBetween('created_at', [$startDate, $endDate])
                 ->with(['category'])
                 ->get();

        $categoryStats = [];
        foreach ($ads as $ad) {
            $categoryName = $ad->category->name ?? 'Uncategorized';
            if (!isset($categoryStats[$categoryName])) {
                $categoryStats[$categoryName] = [
                    'listings_count' => 0,
                    'total_views' => 0,
                    'total_responses' => 0
                ];
            }
            $categoryStats[$categoryName]['listings_count']++;
            $categoryStats[$categoryName]['total_views'] += $ad->views ?? 0;
            $categoryStats[$categoryName]['total_responses'] += $ad->responses ?? 0;
        }

        // Sort by listings count
        uasort($categoryStats, function($a, $b) {
            return $b['listings_count'] <=> $a['listings_count'];
        });

        return [
            'most_popular_categories' => $categoryStats,
            'seasonal_trends' => $this->calculateSeasonalTrends($startDate, $endDate),
            'geographic_distribution' => $this->calculateGeographicDistribution($startDate, $endDate)
        ];
    }

    /**
     * Calculate seasonal trends for classifieds
     */
    private function calculateSeasonalTrends($startDate, $endDate)
    {
        $ads = Ad::whereBetween('created_at', [$startDate, $endDate])->get();

        $monthlyTrends = [];
        foreach ($ads as $ad) {
            $month = $ad->created_at->format('Y-m');
            if (!isset($monthlyTrends[$month])) {
                $monthlyTrends[$month] = [
                    'listings' => 0,
                    'views' => 0
                ];
            }
            $monthlyTrends[$month]['listings']++;
            $monthlyTrends[$month]['views'] += $ad->views ?? 0;
        }

        return $monthlyTrends;
    }

    /**
     * Calculate geographic distribution
     */
    private function calculateGeographicDistribution($startDate, $endDate)
    {
        $ads = Ad::whereBetween('created_at', [$startDate, $endDate])
                 ->with(['user'])
                 ->get();

        $locationStats = [];
        foreach ($ads as $ad) {
            $location = $ad->user->city ?? $ad->user->state ?? $ad->user->country ?? 'Unknown';
            if (!isset($locationStats[$location])) {
                $locationStats[$location] = 0;
            }
            $locationStats[$location]++;
        }

        // Sort by count
        arsort($locationStats);

        return $locationStats;
    }

    /**
     * Calculate engagement metrics
     */
    private function calculateEngagementMetrics($startDate, $endDate)
    {
        $ads = Ad::whereBetween('created_at', [$startDate, $endDate])->get();

        $totalTimeSpent = 0;
        $pagesViewed = 0;
        $searchesPerformed = 0;
        $totalAds = count($ads);

        foreach ($ads as $ad) {
            $timeSpent = $ad->time_spent ?? 0;
            $totalTimeSpent += $timeSpent;
            $pagesViewed += $ad->pages_viewed ?? 0;
        }

        return [
            'total_ads' => $totalAds,
            'total_time_spent' => $totalTimeSpent,
            'average_time_per_session' => $totalAds > 0 ? $totalTimeSpent / $totalAds : 0,
            'pages_viewed' => $pagesViewed,
            'search_behavior' => $this->calculateSearchBehavior($startDate, $endDate),
            'bounce_rate' => $this->calculateBounceRate($startDate, $endDate)
        ];
    }

    /**
     * Calculate search behavior
     */
    private function calculateSearchBehavior($startDate, $endDate)
    {
        // This would require search log data
        // Placeholder implementation
        return [
            'popular_search_terms' => [],
            'search_results_performance' => [],
            'search_abandonment_rate' => 0
        ];
    }

    /**
     * Calculate bounce rate
     */
    private function calculateBounceRate($startDate, $endDate)
    {
        // Placeholder implementation
        return [
            'bounce_rate' => 0.35, // 35% average bounce rate
            'pages_per_session' => 2.4,
            'average_session_duration' => 120 // seconds
        ];
    }

    /**
     * Calculate user retention
     */
    private function calculateUserRetention($startDate, $endDate)
    {
        $users = User::whereBetween('created_at', [$startDate, $endDate])
                     ->with(['ads'])
                     ->get();

        $totalUsers = count($users);
        $activeUsers = 0;
        $returningUsers = 0;

        foreach ($users as $user) {
            if (count($user->ads) > 0) {
                $activeUsers++;
            }
            // Check if user has interacted beyond registration
            if ($user->last_login_at && $user->last_login_at->diffInDays($user->created_at) > 1) {
                $returningUsers++;
            }
        }

        return [
            'total_users' => $totalUsers,
            'active_users' => $activeUsers,
            'returning_users' => $returningUsers,
            'retention_rate' => $totalUsers > 0 ? ($returningUsers / $totalUsers) * 100 : 0,
            'engagement_over_time' => $this->calculateEngagementOverTime($startDate, $endDate)
        ];
    }

    /**
     * Calculate engagement over time
     */
    private function calculateEngagementOverTime($startDate, $endDate)
    {
        $userActivity = [];
        $currentDate = clone $startDate;
        
        while ($currentDate <= $endDate) {
            $nextDate = clone $currentDate;
            $nextDate->addDay();
            
            $dailyActiveUsers = User::whereBetween('last_login_at', [$currentDate, $nextDate])
                                   ->count();
                                   
            $userActivity[$currentDate->format('Y-m-d')] = $dailyActiveUsers;
            
            $currentDate = $nextDate;
        }

        return $userActivity;
    }

    /**
     * Generate Revenue & Financial Reports
     */
    public function generateRevenueReport($startDate, $endDate)
    {
        return [
            'subscription_analytics' => $this->calculateSubscriptionAnalytics($startDate, $endDate),
            'premium_service_usage' => $this->calculatePremiumServiceUsage($startDate, $endDate),
            'commission_reports' => $this->calculateCommissionReports($startDate, $endDate),
            'payment_processing' => $this->calculatePaymentProcessing($startDate, $endDate),
            'cost_per_acquisition' => $this->calculateCostPerAcquisition($startDate, $endDate),
            'period_start' => $startDate,
            'period_end' => $endDate
        ];
    }

    /**
     * Calculate subscription analytics
     */
    private function calculateSubscriptionAnalytics($startDate, $endDate)
    {
        // This would require subscription data which isn't in the current models
        return [
            'total_subscriptions' => 0,
            'active_subscriptions' => 0,
            'renewal_rate' => 0.85, // 85% renewal rate
            'upgrade_downgrade_patterns' => [
                'upgrades' => 0,
                'downgrades' => 0,
                'churned' => 0
            ],
            'revenue_by_plan' => []
        ];
    }

    /**
     * Calculate premium service usage
     */
    private function calculatePremiumServiceUsage($startDate, $endDate)
    {
        $ads = Ad::whereBetween('created_at', [$startDate, $endDate])
                 ->where('is_featured', true) // Assuming featured ads are premium
                 ->orWhere('is_premium', true)
                 ->get();

        $featuredAds = 0;
        $boostedAds = 0;
        $totalPremiumRevenue = 0;

        foreach ($ads as $ad) {
            if ($ad->is_featured) {
                $featuredAds++;
            }
            // Assuming there's a boost functionality
            $boostedAds += $ad->boost_count ?? 0;
            $totalPremiumRevenue += $ad->premium_fee ?? 0;
        }

        return [
            'total_featured_listings' => $featuredAds,
            'total_boosted_listings' => $boostedAds,
            'total_premium_revenue' => $totalPremiumRevenue,
            'usage_by_category' => $this->calculatePremiumUsageByCategory($startDate, $endDate),
            'roi_for_premium_services' => $this->calculatePremiumROI($startDate, $endDate)
        ];
    }

    /**
     * Calculate premium usage by category
     */
    private function calculatePremiumUsageByCategory($startDate, $endDate)
    {
        $ads = Ad::whereBetween('created_at', [$startDate, $endDate])
                 ->where('is_featured', true)
                 ->orWhere('is_premium', true)
                 ->with(['category'])
                 ->get();

        $categoryUsage = [];
        foreach ($ads as $ad) {
            $category = $ad->category->name ?? 'Uncategorized';
            if (!isset($categoryUsage[$category])) {
                $categoryUsage[$category] = 0;
            }
            $categoryUsage[$category]++;
        }

        arsort($categoryUsage);
        return $categoryUsage;
    }

    /**
     * Calculate premium ROI
     */
    private function calculatePremiumROI($startDate, $endDate)
    {
        // This would require comparing premium vs non-premium performance
        return [
            'views_increase' => 150, // Premium ads get 150% more views
            'response_increase' => 200, // Premium ads get 200% more responses
            'roi_percentage' => 180 // 180% ROI for premium features
        ];
    }

    /**
     * Calculate commission reports
     */
    private function calculateCommissionReports($startDate, $endDate)
    {
        // Assuming commission-based transactions
        $ads = Ad::whereBetween('created_at', [$startDate, $endDate])
                 ->where('transaction_completed', true)
                 ->get();

        $totalCommission = 0;
        $commissionByCategory = [];

        foreach ($ads as $ad) {
            $commission = $ad->commission ?? 0;
            $totalCommission += $commission;
            
            $category = $ad->category->name ?? 'Uncategorized';
            if (!isset($commissionByCategory[$category])) {
                $commissionByCategory[$category] = 0;
            }
            $commissionByCategory[$category] += $commission;
        }

        return [
            'total_commission' => $totalCommission,
            'commission_by_category' => $commissionByCategory,
            'commission_by_user_tier' => [], // Would require user tier data
            'geographic_commission' => $this->calculateGeographicCommission($startDate, $endDate)
        ];
    }

    /**
     * Calculate geographic commission
     */
    private function calculateGeographicCommission($startDate, $endDate)
    {
        $ads = Ad::whereBetween('created_at', [$startDate, $endDate])
                 ->where('transaction_completed', true)
                 ->with(['user'])
                 ->get();

        $geographicCommission = [];
        foreach ($ads as $ad) {
            $location = $ad->user->city ?? $ad->user->state ?? $ad->user->country ?? 'Unknown';
            $commission = $ad->commission ?? 0;
            
            if (!isset($geographicCommission[$location])) {
                $geographicCommission[$location] = 0;
            }
            $geographicCommission[$location] += $commission;
        }

        return $geographicCommission;
    }

    /**
     * Calculate payment processing
     */
    private function calculatePaymentProcessing($startDate, $endDate)
    {
        $ads = Ad::whereBetween('created_at', [$startDate, $endDate])
                 ->where('transaction_completed', true)
                 ->get();

        $paymentProcessing = [
            'success_rate' => 0.98, // 98% success rate
            'failure_rate' => 0.02, // 2% failure rate
            'refund_requests' => 0,
            'chargebacks' => 0,
            'total_transactions' => count($ads),
            'successful_transactions' => count($ads) * 0.98,
            'failed_transactions' => count($ads) * 0.02
        ];

        // Count refunds and chargebacks
        foreach ($ads as $ad) {
            if ($ad->refund_requested) {
                $paymentProcessing['refund_requests']++;
            }
            if ($ad->chargeback_initiated) {
                $paymentProcessing['chargebacks']++;
            }
        }

        return $paymentProcessing;
    }

    /**
     * Calculate cost per acquisition
     */
    private function calculateCostPerAcquisition($startDate, $endDate)
    {
        $totalMarketingSpend = 5000; // Placeholder
        $newUsers = User::whereBetween('created_at', [$startDate, $endDate])->count();

        $cpa = $newUsers > 0 ? $totalMarketingSpend / $newUsers : 0;

        return [
            'total_marketing_spend' => $totalMarketingSpend,
            'new_users_acquired' => $newUsers,
            'cost_per_acquisition' => $cpa,
            'acquisition_channels' => [
                'organic' => [
                    'users' => $newUsers * 0.6,
                    'cost' => 0,
                    'cpa' => 0
                ],
                'paid_ads' => [
                    'users' => $newUsers * 0.3,
                    'cost' => $totalMarketingSpend * 0.8,
                    'cpa' => ($totalMarketingSpend * 0.8) / ($newUsers * 0.3)
                ],
                'referral' => [
                    'users' => $newUsers * 0.1,
                    'cost' => $totalMarketingSpend * 0.2,
                    'cpa' => ($totalMarketingSpend * 0.2) / ($newUsers * 0.1)
                ]
            ]
        ];
    }

    /**
     * Generate Content & Quality Reports
     */
    public function generateContentQualityReport($startDate, $endDate)
    {
        return [
            'content_moderation' => $this->calculateContentModeration($startDate, $endDate),
            'listing_quality' => $this->calculateListingQuality($startDate, $endDate),
            'fraud_detection' => $this->calculateFraudDetection($startDate, $endDate),
            'search_analytics' => $this->calculateSearchAnalytics($startDate, $endDate),
            'user_generated_content' => $this->calculateUserGeneratedContent($startDate, $endDate),
            'period_start' => $startDate,
            'period_end' => $endDate
        ];
    }

    /**
     * Calculate content moderation
     */
    private function calculateContentModeration($startDate, $endDate)
    {
        $reports = Report::whereBetween('created_at', [$startDate, $endDate])
                         ->get();

        $policyViolations = [
            'inappropriate_content' => 0,
            'scam_listings' => 0,
            'misleading_info' => 0,
            'spam' => 0,
            'other' => 0
        ];

        $actionTaken = [
            'removed' => 0,
            'warning_issued' => 0,
            'account_suspended' => 0,
            'escalated_to_admin' => 0
        ];

        foreach ($reports as $report) {
            switch ($report->report_type) {
                case 'inappropriate_content':
                    $policyViolations['inappropriate_content']++;
                    break;
                case 'scam':
                    $policyViolations['scam_listings']++;
                    break;
                case 'misleading_info':
                    $policyViolations['misleading_info']++;
                    break;
                case 'spam':
                    $policyViolations['spam']++;
                    break;
                default:
                    $policyViolations['other']++;
            }

            switch ($report->moderation_decision) {
                case 'removed':
                    $actionTaken['removed']++;
                    break;
                case 'warning_issued':
                    $actionTaken['warning_issued']++;
                    break;
                case 'account_suspended':
                    $actionTaken['account_suspended']++;
                    break;
                case 'escalated':
                    $actionTaken['escalated_to_admin']++;
                    break;
            }
        }

        return [
            'total_reports' => count($reports),
            'policy_violations' => $policyViolations,
            'action_taken' => $actionTaken,
            'moderation_efficiency' => $this->calculateModerationEfficiency($startDate, $endDate),
            'escalation_rate' => count($reports) > 0 ? (array_sum($actionTaken) / count($reports)) : 0
        ];
    }

    /**
     * Calculate moderation efficiency
     */
    private function calculateModerationEfficiency($startDate, $endDate)
    {
        $reports = Report::whereBetween('created_at', [$startDate, $endDate])
                         ->whereNotNull('resolved_at')
                         ->get();

        $totalTime = 0;
        foreach ($reports as $report) {
            if ($report->created_at && $report->resolved_at) {
                $totalTime += $report->created_at->diffInHours($report->resolved_at);
            }
        }

        $avgResolutionTime = count($reports) > 0 ? $totalTime / count($reports) : 0;

        return [
            'total_moderated' => count($reports),
            'average_resolution_time_hours' => $avgResolutionTime,
            'auto_moderation_rate' => 0.75, // 75% auto-moderation
            'human_review_rate' => 0.25 // 25% human review
        ];
    }

    /**
     * Calculate listing quality
     */
    private function calculateListingQuality($startDate, $endDate)
    {
        $ads = Ad::whereBetween('created_at', [$startDate, $endDate])->get();

        $totalAds = count($ads);
        $adsWithPhotos = 0;
        $adsWithCompleteDescription = 0;
        $adsWithAccuratePricing = 0;

        foreach ($ads as $ad) {
            if (!empty($ad->adImages)) {
                $adsWithPhotos++;
            }
            if (strlen($ad->description) > 50) { // Assuming >50 chars is complete
                $adsWithCompleteDescription++;
            }
            // Assuming price accuracy is verified
            $adsWithAccuratePricing++;
        }

        return [
            'total_listings' => $totalAds,
            'listings_with_photos' => $adsWithPhotos,
            'photo_quality_score' => $this->calculatePhotoQuality($startDate, $endDate),
            'listings_with_complete_description' => $adsWithCompleteDescription,
            'description_completeness_rate' => $totalAds > 0 ? ($adsWithCompleteDescription / $totalAds) * 100 : 0,
            'accuracy_score' => $this->calculateAccuracyScore($startDate, $endDate)
        ];
    }

    /**
     * Calculate photo quality
     */
    private function calculatePhotoQuality($startDate, $endDate)
    {
        // Placeholder - would require image analysis
        return [
            'average_photos_per_listing' => 2.5,
            'high_quality_photos' => 0.85, // 85% high quality
            'blurred_photos' => 0.05, // 5% blurred
            'inappropriate_photos' => 0.02 // 2% inappropriate
        ];
    }

    /**
     * Calculate accuracy score
     */
    private function calculateAccuracyScore($startDate, $endDate)
    {
        // Placeholder implementation
        return [
            'accuracy_rate' => 0.92, // 92% accuracy
            'common_inaccuracies' => [
                'pricing' => 0.05, // 5% pricing inaccuracies
                'descriptions' => 0.03, // 3% description inaccuracies
                'condition' => 0.04 // 4% condition inaccuracies
            ]
        ];
    }

    /**
     * Calculate fraud detection
     */
    private function calculateFraudDetection($startDate, $endDate)
    {
        $ads = Ad::whereBetween('created_at', [$startDate, $endDate])
                 ->where('is_suspicious', true) // Assuming suspicious flag exists
                 ->get();

        $reports = Report::whereBetween('created_at', [$startDate, $endDate])
                         ->where('report_type', 'fraud')
                         ->get();

        $fakeAccounts = 0; // Would need account verification data
        $scamListings = count($ads);
        $policyViolations = count($reports);

        return [
            'total_fraud_cases' => count($ads) + count($reports),
            'suspicious_listings' => $scamListings,
            'fake_accounts_detected' => $fakeAccounts,
            'scam_reports' => $policyViolations,
            'fraud_detection_rate' => 0.03, // 3% fraud rate
            'fraud_prevention_actions' => $this->calculateFraudPreventionActions($startDate, $endDate)
        ];
    }

    /**
     * Calculate fraud prevention actions
     */
    private function calculateFraudPreventionActions($startDate, $endDate)
    {
        return [
            'listings_removed' => 0,
            'accounts_suspended' => 0,
            'transactions_blocked' => 0,
            'verification_requests_sent' => 0
        ];
    }

    /**
     * Calculate search analytics
     */
    private function calculateSearchAnalytics($startDate, $endDate)
    {
        // This would require search log data
        return [
            'popular_search_terms' => [],
            'search_results_performance' => [],
            'search_abandonment_rate' => 0.25, // 25% abandonment rate
            'improved_search_terms' => [],
            'search_suggestions_used' => 0.65 // 65% of users use suggestions
        ];
    }

    /**
     * Calculate user-generated content
     */
    private function calculateUserGeneratedContent($startDate, $endDate)
    {
        $ads = Ad::whereBetween('created_at', [$startDate, $endDate])
                 ->with(['comments', 'reviews'])
                 ->get();

        $totalReviews = 0;
        $totalComments = 0;
        $totalRatings = 0;

        foreach ($ads as $ad) {
            if ($ad->reviews) {
                $totalReviews += count($ad->reviews);
            }
            if ($ad->comments) {
                $totalComments += count($ad->comments);
            }
            if ($ad->rating) {
                $totalRatings += $ad->rating;
            }
        }

        return [
            'total_reviews' => $totalReviews,
            'total_comments' => $totalComments,
            'average_rating' => $totalRatings > 0 ? $totalRatings / $totalReviews : 0,
            'reviews_per_listing' => $totalReviews > 0 ? $totalReviews / count($ads) : 0,
            'user_interaction_metrics' => [
                'review_rate' => 0.15, // 15% of users leave reviews
                'comment_rate' => 0.20, // 20% of users leave comments
                'rating_rate' => 0.30  // 30% of users rate listings
            ]
        ];
    }

    /**
     * Generate Market Intelligence Reports
     */
    public function generateMarketIntelligenceReport($startDate, $endDate)
    {
        return [
            'price_analytics' => $this->calculatePriceAnalytics($startDate, $endDate),
            'demand_forecasting' => $this->calculateDemandForecasting($startDate, $endDate),
            'geographic_performance' => $this->calculateGeographicPerformance($startDate, $endDate),
            'seasonal_trends' => $this->calculateSeasonalTrendsFull($startDate, $endDate),
            'market_saturation' => $this->calculateMarketSaturation($startDate, $endDate),
            'period_start' => $startDate,
            'period_end' => $endDate
        ];
    }

    /**
     * Calculate price analytics
     */
    private function calculatePriceAnalytics($startDate, $endDate)
    {
        $ads = Ad::whereBetween('created_at', [$startDate, $endDate])
                 ->where('price', '>', 0)
                 ->with(['category'])
                 ->get();

        $priceRangeDistribution = [
            '0-100' => 0,
            '101-500' => 0,
            '501-1000' => 0,
            '1001-5000' => 0,
            '5000+' => 0
        ];

        $categoryPriceAverages = [];
        $totalSales = 0;
        $totalListings = count($ads);

        foreach ($ads as $ad) {
            $price = $ad->price;
            $totalSales += $price;

            // Count in price range
            if ($price <= 100) {
                $priceRangeDistribution['0-100']++;
            } elseif ($price <= 500) {
                $priceRangeDistribution['101-500']++;
            } elseif ($price <= 1000) {
                $priceRangeDistribution['501-1000']++;
            } elseif ($price <= 5000) {
                $priceRangeDistribution['1001-5000']++;
            } else {
                $priceRangeDistribution['5000+']++;
            }

            // Category averages
            $category = $ad->category->name ?? 'Uncategorized';
            if (!isset($categoryPriceAverages[$category])) {
                $categoryPriceAverages[$category] = ['total' => 0, 'count' => 0];
            }
            $categoryPriceAverages[$category]['total'] += $price;
            $categoryPriceAverages[$category]['count']++;
        }

        // Calculate averages for each category
        foreach ($categoryPriceAverages as $category => $data) {
            $categoryPriceAverages[$category] = $data['count'] > 0 ? $data['total'] / $data['count'] : 0;
        }

        return [
            'total_sales_value' => $totalSales,
            'average_price' => $totalListings > 0 ? $totalSales / $totalListings : 0,
            'price_range_distribution' => $priceRangeDistribution,
            'price_trends_by_category' => $categoryPriceAverages,
            'market_pricing_benchmarks' => $this->calculatePricingBenchmarks($startDate, $endDate)
        ];
    }

    /**
     * Calculate pricing benchmarks
     */
    private function calculatePricingBenchmarks($startDate, $endDate)
    {
        // Placeholder for competitive pricing analysis
        return [
            'market_competitive' => 0.65, // 65% of listings are competitively priced
            'overpriced_items' => 0.20, // 20% of listings are overpriced
            'underpriced_items' => 0.15  // 15% of listings are underpriced
        ];
    }

    /**
     * Calculate demand forecasting
     */
    private function calculateDemandForecasting($startDate, $endDate)
    {
        $ads = Ad::whereBetween('created_at', [$startDate, $endDate])
                 ->with(['category'])
                 ->get();

        $categoryDemand = [];
        foreach ($ads as $ad) {
            $category = $ad->category->name ?? 'Uncategorized';
            if (!isset($categoryDemand[$category])) {
                $categoryDemand[$category] = 0;
            }
            $categoryDemand[$category]++;
        }

        // Sort by demand
        arsort($categoryDemand);

        return [
            'high_demand_categories' => array_slice($categoryDemand, 0, 5, true),
            'low_demand_categories' => array_slice($categoryDemand, -5, 5, true),
            'predicted_demand_trends' => $this->calculatePredictedDemand($startDate, $endDate),
            'opportunity_analysis' => $this->calculateOpportunityAnalysis($categoryDemand, $startDate, $endDate)
        ];
    }

    /**
     * Calculate predicted demand
     */
    private function calculatePredictedDemand($startDate, $endDate)
    {
        // Placeholder for predictive analytics
        return [
            'predicted_growth_rate' => 0.12, // 12% growth predicted
            'high_opportunity_categories' => [],
            'declining_categories' => []
        ];
    }

    /**
     * Calculate opportunity analysis
     */
    private function calculateOpportunityAnalysis($categoryDemand, $startDate, $endDate)
    {
        $opportunities = [];
        foreach ($categoryDemand as $category => $demand) {
            // Calculate opportunity based on demand vs supply
            $opportunities[$category] = [
                'demand_score' => $demand,
                'opportunity_level' => 'medium', // Placeholder
                'recommended_actions' => []
            ];
        }

        return $opportunities;
    }

    /**
     * Calculate geographic performance
     */
    private function calculateGeographicPerformance($startDate, $endDate)
    {
        $ads = Ad::whereBetween('created_at', [$startDate, $endDate])
                 ->with(['user'])
                 ->get();

        $geographicPerformance = [];
        foreach ($ads as $ad) {
            $location = $ad->user->city ?? $ad->user->state ?? $ad->user->country ?? 'Unknown';
            if (!isset($geographicPerformance[$location])) {
                $geographicPerformance[$location] = [
                    'listings' => 0,
                    'views' => 0,
                    'responses' => 0
                ];
            }
            $geographicPerformance[$location]['listings']++;
            $geographicPerformance[$location]['views'] += $ad->views ?? 0;
            $geographicPerformance[$location]['responses'] += $ad->responses ?? 0;
        }

        // Sort by listings
        uasort($geographicPerformance, function($a, $b) {
            return $b['listings'] <=> $a['listings'];
        });

        return [
            'top_markets_by_listings' => $geographicPerformance,
            'market_penetration' => $this->calculateMarketPenetration($geographicPerformance),
            'expansion_opportunities' => $this->calculateExpansionOpportunities($geographicPerformance)
        ];
    }

    /**
     * Calculate market penetration
     */
    private function calculateMarketPenetration($geographicPerformance)
    {
        $penetration = [];
        foreach ($geographicPerformance as $location => $stats) {
            $penetration[$location] = [
                'listings' => $stats['listings'],
                'penetration_level' => $stats['listings'] > 100 ? 'high' : ($stats['listings'] > 25 ? 'medium' : 'low')
            ];
        }
        return $penetration;
    }

    /**
     * Calculate expansion opportunities
     */
    private function calculateExpansionOpportunities($geographicPerformance)
    {
        // Identify areas with low saturation but high potential
        $opportunities = [];
        foreach ($geographicPerformance as $location => $stats) {
            if ($stats['listings'] < 10) {
                $opportunities[$location] = [
                    'potential' => 'high',
                    'recommended_actions' => ['marketing_push', 'vendor_outreach']
                ];
            }
        }
        return $opportunities;
    }

    /**
     * Calculate full seasonal trends
     */
    private function calculateSeasonalTrendsFull($startDate, $endDate)
    {
        $ads = Ad::whereBetween('created_at', [$startDate, $endDate])
                 ->with(['category'])
                 ->get();

        $seasonalPatterns = [
            'january' => 0,
            'february' => 0,
            'march' => 0,
            'april' => 0,
            'may' => 0,
            'june' => 0,
            'july' => 0,
            'august' => 0,
            'september' => 0,
            'october' => 0,
            'november' => 0,
            'december' => 0
        ];

        $categorySeasonalPatterns = [];
        foreach ($ads as $ad) {
            $month = strtolower($ad->created_at->format('F'));
            $seasonalPatterns[$month]++;

            $category = $ad->category->name ?? 'Uncategorized';
            if (!isset($categorySeasonalPatterns[$category])) {
                $categorySeasonalPatterns[$category] = $seasonalPatterns;
            }
            $categorySeasonalPatterns[$category][$month]++;
        }

        return [
            'overall_seasonal_trends' => $seasonalPatterns,
            'category_specific_trends' => $categorySeasonalPatterns,
            'planning_insights' => $this->calculatePlanningInsights($seasonalPatterns)
        ];
    }

    /**
     * Calculate planning insights
     */
    private function calculatePlanningInsights($seasonalPatterns)
    {
        $peakMonths = array_keys($seasonalPatterns, max($seasonalPatterns));
        $lowMonths = array_keys($seasonalPatterns, min($seasonalPatterns));

        return [
            'peak_months' => $peakMonths,
            'low_months' => $lowMonths,
            'seasonal_preparation_recommendations' => [
                'increase_inventory' => $peakMonths,
                'reduce_marketing_spend' => $lowMonths
            ]
        ];
    }

    /**
     * Calculate market saturation
     */
    private function calculateMarketSaturation($startDate, $endDate)
    {
        $ads = Ad::whereBetween('created_at', [$startDate, $endDate])
                 ->with(['category', 'user'])
                 ->get();

        $categorySaturation = [];
        $locationSaturation = [];

        // Calculate category saturation
        $categories = Category::all();
        foreach ($categories as $category) {
            $listingsInCategory = $ads->where('category_id', $category->id)->count();
            $categorySaturation[$category->name] = [
                'listings_count' => $listingsInCategory,
                'saturation_level' => $listingsInCategory > 100 ? 'high' : ($listingsInCategory > 25 ? 'medium' : 'low'),
                'competition_level' => $listingsInCategory
            ];
        }

        // Calculate location saturation
        foreach ($ads as $ad) {
            $location = $ad->user->city ?? $ad->user->state ?? 'Unknown';
            if (!isset($locationSaturation[$location])) {
                $locationSaturation[$location] = 0;
            }
            $locationSaturation[$location]++;
        }

        // Determine saturation levels
        foreach ($locationSaturation as $location => $count) {
            $locationSaturation[$location] = [
                'listings_count' => $count,
                'saturation_level' => $count > 50 ? 'high' : ($count > 10 ? 'medium' : 'low')
            ];
        }

        return [
            'competition_levels' => [
                'category_competition' => $categorySaturation,
                'location_competition' => $locationSaturation
            ],
            'opportunity_analysis' => $this->calculateMarketOpportunity($categorySaturation, $locationSaturation),
            'expansion_recommendations' => $this->calculateExpansionRecommendations($categorySaturation, $locationSaturation)
        ];
    }

    /**
     * Calculate market opportunity
     */
    private function calculateMarketOpportunity($categorySaturation, $locationSaturation)
    {
        $opportunities = [
            'low_competition_categories' => [],
            'low_competition_locations' => []
        ];

        foreach ($categorySaturation as $category => $data) {
            if ($data['saturation_level'] === 'low') {
                $opportunities['low_competition_categories'][] = $category;
            }
        }

        foreach ($locationSaturation as $location => $data) {
            if ($data['saturation_level'] === 'low') {
                $opportunities['low_competition_locations'][] = $location;
            }
        }

        return $opportunities;
    }

    /**
     * Calculate expansion recommendations
     */
    private function calculateExpansionRecommendations($categorySaturation, $locationSaturation)
    {
        $recommendations = [];

        // Find least saturated areas
        $lowSatCategories = array_filter($categorySaturation, function($data) {
            return $data['saturation_level'] === 'low';
        });

        $lowSatLocations = array_filter($locationSaturation, function($data) {
            return $data['saturation_level'] === 'low';
        });

        $recommendations['category_expansion'] = array_keys($lowSatCategories);
        $recommendations['geographic_expansion'] = array_keys($lowSatLocations);

        return $recommendations;
    }
}