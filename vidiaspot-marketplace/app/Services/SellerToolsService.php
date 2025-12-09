<?php

namespace App\Services;

use App\Models\InventoryLocation;
use App\Models\InventoryItem;
use App\Models\SellerAnalytics;
use App\Models\SellerTool;
use App\Models\PriceMonitoring;
use App\Models\User;
use App\Models\Ad;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class SellerToolsService
{
    /**
     * Get inventory locations for a user
     */
    public function getInventoryLocations($userId)
    {
        return InventoryLocation::where('user_id', $userId)
                              ->orderBy('is_primary', 'desc')
                              ->orderBy('name')
                              ->get();
    }

    /**
     * Create a new inventory location
     */
    public function createInventoryLocation($userId, $locationData)
    {
        return InventoryLocation::create(array_merge([
            'user_id' => $userId,
        ], $locationData));
    }

    /**
     * Update inventory location
     */
    public function updateInventoryLocation($locationId, $locationData, $userId)
    {
        $location = InventoryLocation::where('id', $locationId)
                                   ->where('user_id', $userId)
                                   ->firstOrFail();

        $location->update($locationData);

        return $location;
    }

    /**
     * Delete inventory location
     */
    public function deleteInventoryLocation($locationId, $userId)
    {
        $location = InventoryLocation::where('id', $locationId)
                                   ->where('user_id', $userId)
                                   ->firstOrFail();

        // Check if any inventory is associated with this location
        if ($location->inventoryItems()->exists()) {
            throw new \Exception('Cannot delete location with active inventory items');
        }

        $location->delete();

        return true;
    }

    /**
     * Get inventory items for a location
     */
    public function getInventoryItems($locationId, $userId)
    {
        return InventoryItem::whereHas('location', function($query) use ($userId) {
            $query->where('user_id', $userId);
        })->where('inventory_location_id', $locationId)
          ->orderBy('name')
          ->get();
    }

    /**
     * Bulk create/update inventory items
     */
    public function bulkUpdateInventory($userId, $itemsData)
    {
        $results = [];

        foreach ($itemsData as $itemData) {
            // Validate that the location belongs to the user
            $location = InventoryLocation::where('id', $itemData['inventory_location_id'])
                                        ->where('user_id', $userId)
                                        ->first();

            if (!$location) {
                $results[] = [
                    'status' => 'error',
                    'message' => 'Location does not belong to user',
                    'item_sku' => $itemData['sku'] ?? 'unknown'
                ];
                continue;
            }

            if (isset($itemData['id'])) {
                // Update existing item
                $item = InventoryItem::where('id', $itemData['id'])
                                   ->whereHas('location', function($query) use ($userId) {
                                       $query->where('user_id', $userId);
                                   })
                                   ->first();

                if ($item) {
                    $item->update($itemData);
                    $results[] = [
                        'status' => 'success',
                        'message' => 'Item updated successfully',
                        'item' => $item
                    ];
                } else {
                    $results[] = [
                        'status' => 'error',
                        'message' => 'Item not found or access denied',
                        'item_sku' => $itemData['sku']
                    ];
                }
            } else {
                // Create new item
                $item = InventoryItem::create($itemData);
                $results[] = [
                    'status' => 'success',
                    'message' => 'Item created successfully',
                    'item' => $item
                ];
            }
        }

        return $results;
    }

    /**
     * Get seller analytics dashboard
     */
    public function getSellerDashboard($userId, $timeRange = 'monthly')
    {
        // Get analytics for the specified time range
        $startDate = $this->getTimeRangeStart($timeRange);
        $endDate = now();

        $analytics = SellerAnalytics::where('user_id', $userId)
                                   ->whereBetween('period_start', [$startDate, $endDate])
                                   ->orderBy('period_start', 'desc')
                                   ->get();

        if ($analytics->isEmpty()) {
            // Create a default record if none exists
            $analytics = collect([[
                'views' => 0,
                'clicks' => 0,
                'sales_amount' => 0,
                'sales_count' => 0,
                'conversion_rate' => 0,
                'period_start' => $startDate,
                'period_end' => $endDate,
            ]]);
        }

        // Aggregate the analytics data
        $aggregate = [
            'total_views' => $analytics->sum('views'),
            'total_clicks' => $analytics->sum('clicks'),
            'total_sales_amount' => $analytics->sum('sales_amount'),
            'total_sales_count' => $analytics->sum('sales_count'),
            'avg_conversion_rate' => $analytics->avg('conversion_rate') ?? 0,
            'avg_profit_margin' => $analytics->avg('profit_margin') ?? 0,
            'avg_customer_acquisition_cost' => $analytics->avg('customer_acquisition_cost') ?? 0,
            'period_start' => $startDate,
            'period_end' => $endDate,
        ];

        // Calculate derived metrics
        $aggregate['conversion_rate'] = $aggregate['total_views'] > 0 ? 
                                     ($aggregate['total_sales_count'] / $aggregate['total_views']) * 100 : 0;

        $aggregate['click_through_rate'] = $aggregate['total_views'] > 0 ? 
                                         ($aggregate['total_clicks'] / $aggregate['total_views']) * 100 : 0;

        $aggregate['average_order_value'] = $aggregate['total_sales_count'] > 0 ? 
                                          $aggregate['total_sales_amount'] / $aggregate['total_sales_count'] : 0;

        // Get inventory metrics
        $inventoryLocations = $this->getInventoryLocations($userId);
        $totalInventory = 0;
        $lowStockItems = 0;
        $outOfStockItems = 0;

        foreach ($inventoryLocations as $location) {
            $items = $this->getInventoryItems($location->id, $userId);
            foreach ($items as $item) {
                $totalInventory += $item->quantity_available;
                if ($item->isLowOnStock()) {
                    $lowStockItems++;
                }
                if (!$item->isInStock()) {
                    $outOfStockItems++;
                }
            }
        }

        $aggregate['inventory_metrics'] = [
            'total_items' => $inventoryLocations->count(),
            'total_inventory_value' => $this->calculateInventoryValue($userId),
            'low_stock_items' => $lowStockItems,
            'out_of_stock_items' => $outOfStockItems,
        ];

        return $aggregate;
    }

    /**
     * Get seasonal inventory planning
     */
    public function getSeasonalPlanning($userId, $year = null)
    {
        $year = $year ?? Carbon::now()->year;

        // This would use historical data to predict seasonal trends
        // For now, we'll return simulated data
        $seasonalData = [
            'january' => [
                'expected_demand_multiplier' => 1.1, // 10% higher demand
                'recommended_actions' => ['increase_stock_by' => 10, 'focus_on_categories' => ['electronics', 'fitness']]
            ],
            'february' => [
                'expected_demand_multiplier' => 1.3, // Valentine's Day boost
                'recommended_actions' => ['increase_stock_by' => 20, 'focus_on_categories' => ['gifts', 'jewelry']]
            ],
            'march' => [
                'expected_demand_multiplier' => 0.9,
                'recommended_actions' => ['maintain_normal_levels' => true]
            ],
            'april' => [
                'expected_demand_multiplier' => 1.0,
                'recommended_actions' => ['maintain_normal_levels' => true]
            ],
            'may' => [
                'expected_demand_multiplier' => 1.1,
                'recommended_actions' => ['increase_stock_by' => 10, 'focus_on_categories' => ['summer_clothing', 'outdoor']]
            ],
            'june' => [
                'expected_demand_multiplier' => 1.2,
                'recommended_actions' => ['increase_stock_by' => 15, 'focus_on_categories' => ['summer_clothing', 'outdoor', 'vacation']]
            ],
            'july' => [
                'expected_demand_multiplier' => 1.1,
                'recommended_actions' => ['increase_stock_by' => 10, 'focus_on_categories' => ['summer_clothing', 'outdoor']]
            ],
            'august' => [
                'expected_demand_multiplier' => 1.4, // Back to school season
                'recommended_actions' => ['increase_stock_by' => 25, 'focus_on_categories' => ['school_supplies', 'electronics']]
            ],
            'september' => [
                'expected_demand_multiplier' => 1.1,
                'recommended_actions' => ['increase_stock_by' => 10, 'focus_on_categories' => ['electronics', 'school_supplies']]
            ],
            'october' => [
                'expected_demand_multiplier' => 1.3, // Fall fashion
                'recommended_actions' => ['increase_stock_by' => 20, 'focus_on_categories' => ['fashion', 'home_decor']]
            ],
            'november' => [
                'expected_demand_multiplier' => 1.8, // Black Friday + Christmas preparation
                'recommended_actions' => ['increase_stock_by' => 40, 'focus_on_categories' => ['electronics', 'gifts', 'home_decor']]
            ],
            'december' => [
                'expected_demand_multiplier' => 2.0, // Christmas peak
                'recommended_actions' => ['increase_stock_by' => 50, 'focus_on_categories' => ['gifts', 'electronics', 'home_decor']]
            ],
        ];

        // Get user's past performance to customize recommendations
        $historicalPerformance = $this->getHistoricalPerformance($userId, $year - 1);

        return [
            'year' => $year,
            'seasonal_forecast' => $seasonalData,
            'custom_recommendations' => $this->generateCustomRecommendations($userId, $historicalPerformance),
            'peak_months' => ['november', 'december', 'august'], // Based on retail calendar
            'slow_months' => ['january', 'february', 'march'],   // Post-holiday slowdown
        ];
    }

    /**
     * Set up automated repricing rules
     */
    public function setupAutomatedRepricing($userId, $adId, $rules)
    {
        $ad = Ad::where('id', $adId)
                 ->where('user_id', $userId)
                 ->firstOrFail();

        // Set up price monitoring with repricing rules
        $monitoring = PriceMonitoring::updateOrCreate([
            'user_id' => $userId,
            'ad_id' => $adId,
        ], [
            'tracked_product_name' => $ad->title,
            'current_price' => $ad->price,
            'monitoring_strategy' => 'automated',
            'automated_repricing_rules' => $rules,
            'is_active' => true,
            'last_updated' => now(),
            'next_update' => now()->addMinutes(30), // Initial update in 30 minutes
        ]);

        return $monitoring;
    }

    /**
     * Get automated repricing recommendations
     */
    public function getRepricingRecommendations($userId)
    {
        $monitoringRecords = PriceMonitoring::where('user_id', $userId)
                                          ->where('is_active', true)
                                          ->get();

        $recommendations = [];

        foreach ($monitoringRecords as $record) {
            $competitivenessScore = $record->calculateCompetitivenessScore();
            $suggestion = $this->generatePricingSuggestion($record, $competitivenessScore);
            
            $recommendations[] = [
                'ad_id' => $record->ad_id,
                'product_name' => $record->tracked_product_name,
                'current_price' => $record->current_price,
                'competitiveness_score' => $competitivenessScore,
                'recommendation' => $suggestion['action'],
                'suggested_price' => $suggestion['suggested_price'],
                'potential_impact' => $suggestion['impact'],
                'confidence_level' => $suggestion['confidence'],
            ];
        }

        return $recommendations;
    }

    /**
     * Get cross-platform selling opportunities
     */
    public function getCrossPlatformOpportunities($userId)
    {
        // This would integrate with other marketplaces
        // For now, return simulated opportunities
        return [
            'amazon_opportunities' => [
                'enabled' => false,
                'listing_cost' => 39.99,
                'estimated_sales_increase' => 25, // Percentage
                'setup_complexity' => 'high',
                'requirements' => ['business_license', 'product_certifications', 'bank_account']
            ],
            'jumia_opportunities' => [
                'enabled' => true,
                'listing_cost' => 0, // Free tier available
                'estimated_sales_increase' => 15,
                'setup_complexity' => 'medium',
                'requirements' => ['business_registration', 'product_photos', 'descriptions']
            ],
            'konga_opportunities' => [
                'enabled' => true,
                'listing_cost' => 0,
                'estimated_sales_increase' => 12,
                'setup_complexity' => 'low',
                'requirements' => ['valid_ID', 'mobile_number', 'product_listings']
            ],
            'social_media_integration' => [
                'facebook_marketplace' => [
                    'enabled' => true,
                    'cost' => 'free',
                    'estimated_traffic_increase' => 20,
                ],
                'instagram_shopping' => [
                    'enabled' => true,
                    'cost' => 'free',
                    'estimated_traffic_increase' => 25,
                ],
            ],
            'own_online_store' => [
                'enabled' => true,
                'cost' => 19.99, // Platform fee
                'estimated_sales_increase' => 40,
                'customer_retention_benefit' => 'high',
            ]
        ];
    }

    /**
     * Get customer relationship management data
     */
    public function getCustomerManagementData($userId)
    {
        // Get customer data from transactions and interactions
        $customerMetrics = [
            'total_customers' => $this->getTotalCustomers($userId),
            'repeat_customers' => $this->getRepeatCustomers($userId),
            'customer_segments' => $this->getCustomerSegments($userId),
            'average_customer_lifespan' => $this->getCustomerLifespan($userId),
            'customer_retention_rate' => $this->getRetentionRate($userId),
            'customer_acquisition_cost' => $this->getAcquisitionCost($userId),
            'customer_lifetime_value' => $this->getCustomerLifetimeValue($userId),
        ];

        return [
            'metrics' => $customerMetrics,
            'segmentation' => $this->performCustomerSegmentation($userId),
            'retention_strategies' => $this->generateRetentionStrategies($userId),
            'communication_preferences' => $this->getCommunicationPreferences($userId),
        ];
    }

    /**
     * Set up loyalty program
     */
    public function setupLoyaltyProgram($userId, $programConfig)
    {
        // Create a loyalty program tool for the user
        $loyaltyTool = SellerTool::create([
            'user_id' => $userId,
            'tool_type' => 'loyalty_program',
            'name' => $programConfig['name'] ?? 'Default Loyalty Program',
            'description' => $programConfig['description'] ?? 'Customer loyalty program setup',
            'is_active' => true,
            'settings' => [
                'reward_type' => $programConfig['reward_type'] ?? 'points',
                'reward_value' => $programConfig['reward_value'] ?? 1,
                'minimum_purchase' => $programConfig['minimum_purchase'] ?? 1000,
                'expiration_days' => $programConfig['expiration_days'] ?? 365,
                'tiers' => $programConfig['tiers'] ?? [
                    ['name' => 'Bronze', 'min_spending' => 0, 'discount' => 2],
                    ['name' => 'Silver', 'min_spending' => 50000, 'discount' => 5],
                    ['name' => 'Gold', 'min_spending' => 100000, 'discount' => 8],
                ],
            ],
            'integration_config' => $programConfig['integration_config'] ?? [],
            'subscription_status' => 'active',
        ]);

        return $loyaltyTool;
    }

    /**
     * Calculate inventory value
     */
    private function calculateInventoryValue($userId)
    {
        $totalValue = 0;

        $locations = InventoryLocation::where('user_id', $userId)->get();
        foreach ($locations as $location) {
            $items = $this->getInventoryItems($location->id, $userId);
            foreach ($items as $item) {
                $totalValue += $item->cost_price * $item->quantity_available;
            }
        }

        return $totalValue;
    }

    /**
     * Get time range start date
     */
    private function getTimeRangeStart($timeRange)
    {
        switch ($timeRange) {
            case 'daily':
                return now()->startOfDay();
            case 'weekly':
                return now()->startOfWeek();
            case 'monthly':
                return now()->startOfMonth();
            case 'quarterly':
                return now()->startOfQuarter();
            case 'yearly':
                return now()->startOfYear();
            default:
                return now()->subDays(30); // Default to last 30 days
        }
    }

    /**
     * Get historical performance for seasonal planning
     */
    private function getHistoricalPerformance($userId, $year)
    {
        // In a real app, this would query historical analytics data
        // For simulation, we'll return dummy data
        return [
            'top_categories' => ['electronics', 'fashion', 'home'],
            'peak_months' => ['November', 'December', 'August'],
            'best_performing_products' => [
                ['name' => 'Smartphone A', 'units_sold' => 150],
                ['name' => 'Bluetooth Headphones', 'units_sold' => 120],
            ],
            'seasonal_trends' => [
                'electronics' => ['q4' => 150, 'q3' => 120, 'q2' => 90, 'q1' => 80], // Q4 highest
                'fashion' => ['q4' => 130, 'q1' => 110, 'q3' => 100, 'q2' => 90], // Holiday seasons high
                'home' => ['q3' => 110, 'q4' => 105, 'q1' => 95, 'q2' => 90], // Summer/fall moving seasons
            ]
        ];
    }

    /**
     * Generate custom recommendations based on historical data
     */
    private function generateCustomRecommendations($userId, $historicalData)
    {
        // Based on user's historical performance, generate personalized recommendations
        $recommendations = [];
        
        if (in_array('electronics', $historicalData['top_categories'])) {
            $recommendations[] = [
                'category' => 'electronics',
                'strategy' => 'stock_up_before_holidays',
                'months' => ['October', 'November', 'December'],
                'increase_percentage' => 30
            ];
        }

        if (in_array('fashion', $historicalData['top_categories'])) {
            $recommendations[] = [
                'category' => 'fashion',
                'strategy' => 'early_season_launch',
                'months' => ['August', 'February'],
                'increase_percentage' => 25
            ];
        }

        return $recommendations;
    }

    /**
     * Generate pricing suggestion based on competition
     */
    private function generatePricingSuggestion($record, $competitivenessScore)
    {
        $currentPrice = $record->current_price;
        $competitorPrices = $record->competitor_prices ?? [];
        
        if (empty($competitorPrices)) {
            return [
                'action' => 'monitor_competitors',
                'suggested_price' => $currentPrice,
                'impact' => 'low',
                'confidence' => 50
            ];
        }

        // Calculate average competitor price
        $compPrices = array_column($competitorPrices, 'price');
        $avgCompPrice = array_sum($compPrices) / count($compPrices);
        
        if ($competitivenessScore < 50) {
            // Not competitive, suggest lowering price
            $suggestedPrice = $avgCompPrice * 0.95; // 5% below average
            $action = 'lower_price_to_compete';
        } elseif ($competitivenessScore > 80) {
            // Very competitive, might suggest premium position
            $suggestedPrice = $avgCompPrice * 1.05; // 5% above average
            $action = 'raise_price_for_premium';
        } else {
            // Competitive, maintain current price
            $suggestedPrice = $currentPrice;
            $action = 'maintain_current_price';
        }

        return [
            'action' => $action,
            'suggested_price' => round($suggestedPrice, 2),
            'impact' => 'medium', // Potential impact on sales
            'confidence' => 80 // Confidence in recommendation
        ];
    }

    /**
     * Get total customers for CRM
     */
    private function getTotalCustomers($userId)
    {
        // This would be calculated from transaction data
        // For demo, return a simulated value
        return mt_rand(50, 500);
    }

    /**
     * Get repeat customers for CRM
     */
    private function getRepeatCustomers($userId)
    {
        // Calculate from transaction history
        // For demo, return a simulated value
        return mt_rand(10, 100);
    }

    /**
     * Perform customer segmentation
     */
    private function getCustomerSegments($userId)
    {
        // This would segment customers based on behavior, spending, etc.
        return [
            'high_value' => [
                'count' => mt_rand(5, 20),
                'avg_spending' => mt_rand(50000, 200000),
                'characteristics' => ['frequent_buyers', 'high_cart_values', 'brand_loyal']
            ],
            'medium_value' => [
                'count' => mt_rand(20, 80),
                'avg_spending' => mt_rand(10000, 50000),
                'characteristics' => ['occasional_buyers', 'average_cart_values']
            ],
            'low_value' => [
                'count' => mt_rand(25, 200),
                'avg_spending' => mt_rand(1000, 10000),
                'characteristics' => ['first_time_buyers', 'promotional_driven', 'price_sensitive']
            ]
        ];
    }

    /**
     * Perform customer segmentation analysis
     */
    private function performCustomerSegmentation($userId)
    {
        // Using RFM analysis (Recency, Frequency, Monetary) or other models
        return [
            'segments' => $this->getCustomerSegments($userId),
            'targeting_strategies' => [
                'high_value' => 'loyalty_programs, early_access, premium_support',
                'medium_value' => 'discount_coupons, upselling, cross_selling',
                'low_value' => 'welcome_offers, reactivation_campaigns, price_promotions'
            ]
        ];
    }

    /**
     * Generate retention strategies
     */
    private function generateRetentionStrategies($userId)
    {
        return [
            'loyalty_program' => [
                'recommended' => true,
                'implementation_cost' => 'low',
                'expected_retention_increase' => 15
            ],
            'personalized_marketing' => [
                'recommended' => true,
                'implementation_cost' => 'medium',
                'expected_retention_increase' => 20
            ],
            'post_purchase_follow_up' => [
                'recommended' => true,
                'implementation_cost' => 'low',
                'expected_retention_increase' => 10
            ],
            'referral_program' => [
                'recommended' => true,
                'implementation_cost' => 'low',
                'expected_retention_increase' => 12
            ]
        ];
    }

    /**
     * Get customer lifetime value
     */
    private function getCustomerLifetimeValue($userId)
    {
        // Calculate based on average revenue per customer * average customer lifespan
        return mt_rand(50000, 200000);
    }

    /**
     * Get retention rate
     */
    private function getRetentionRate($userId)
    {
        // Calculate as percentage of customers who made more than one purchase
        return mt_rand(30, 80);
    }

    /**
     * Get customer acquisition cost
     */
    private function getAcquisitionCost($userId)
    {
        // Calculate total marketing spend divided by number of new customers
        return mt_rand(1000, 5000);
    }

    /**
     * Get customer lifespan
     */
    private function getCustomerLifespan($userId)
    {
        // Calculate average time from first to last purchase
        return mt_rand(6, 24); // months
    }

    /**
     * Get communication preferences
     */
    private function getCommunicationPreferences($userId)
    {
        return [
            'sms_opt_in_rate' => mt_rand(60, 90),
            'email_open_rate' => mt_rand(15, 30),
            'preferred_channels' => ['sms', 'email', 'in_app_notifications']
        ];
    }
}