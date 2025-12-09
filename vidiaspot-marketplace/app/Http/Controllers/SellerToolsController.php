<?php

namespace App\Http\Controllers;

use App\Services\SellerToolsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SellerToolsController extends Controller
{
    protected $sellerToolsService;

    public function __construct(SellerToolsService $sellerToolsService)
    {
        $this->sellerToolsService = $sellerToolsService;
    }

    /**
     * Get inventory locations
     */
    public function getInventoryLocations()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User must be logged in'
            ], 401);
        }

        $locations = $this->sellerToolsService->getInventoryLocations($user->id);

        return response()->json([
            'success' => true,
            'locations' => $locations
        ]);
    }

    /**
     * Create inventory location
     */
    public function createInventoryLocation(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User must be logged in'
            ], 401);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|array',
            'address.street' => 'required|string',
            'address.city' => 'required|string',
            'address.state' => 'required|string',
            'address.country' => 'required|string',
            'address.postal_code' => 'required|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'contact_person' => 'nullable|string',
            'contact_phone' => 'nullable|string',
            'contact_email' => 'nullable|email',
            'operating_hours' => 'array',
            'capacity' => 'nullable|integer|min:0',
            'max_storage_units' => 'nullable|integer|min:0',
        ]);

        try {
            $location = $this->sellerToolsService->createInventoryLocation($user->id, $request->all());

            return response()->json([
                'success' => true,
                'message' => 'Inventory location created successfully',
                'location' => $location
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create inventory location: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Update inventory location
     */
    public function updateInventoryLocation(Request $request, $locationId)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User must be logged in'
            ], 401);
        }

        try {
            $location = $this->sellerToolsService->updateInventoryLocation($locationId, $request->all(), $user->id);

            return response()->json([
                'success' => true,
                'message' => 'Inventory location updated successfully',
                'location' => $location
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update inventory location: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Delete inventory location
     */
    public function deleteInventoryLocation($locationId)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User must be logged in'
            ], 401);
        }

        try {
            $result = $this->sellerToolsService->deleteInventoryLocation($locationId, $user->id);

            return response()->json([
                'success' => true,
                'message' => 'Inventory location deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete inventory location: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get inventory items for location
     */
    public function getInventoryItems($locationId)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User must be logged in'
            ], 401);
        }

        try {
            $items = $this->sellerToolsService->getInventoryItems($locationId, $user->id);

            return response()->json([
                'success' => true,
                'items' => $items
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get inventory items: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Bulk update inventory items
     */
    public function bulkUpdateInventory(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User must be logged in'
            ], 401);
        }

        $request->validate([
            'items' => 'required|array',
            'items.*.inventory_location_id' => 'required|exists:inventory_locations,id',
            'items.*.name' => 'required|string',
            'items.*.sku' => 'nullable|string|unique_when_needed',
            'items.*.quantity_available' => 'required|integer|min:0',
            'items.*.selling_price' => 'required|numeric|min:0',
        ]);

        try {
            $results = $this->sellerToolsService->bulkUpdateInventory($user->id, $request->items);

            return response()->json([
                'success' => true,
                'message' => 'Bulk inventory update completed',
                'results' => $results
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to bulk update inventory: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get seller dashboard analytics
     */
    public function getSellerDashboard(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User must be logged in'
            ], 401);
        }

        $timeRange = $request->get('time_range', 'monthly');

        $dashboard = $this->sellerToolsService->getSellerDashboard($user->id, $timeRange);

        return response()->json([
            'success' => true,
            'dashboard' => $dashboard
        ]);
    }

    /**
     * Get seasonal inventory planning
     */
    public function getSeasonalPlanning(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User must be logged in'
            ], 401);
        }

        $year = $request->get('year');

        $planning = $this->sellerToolsService->getSeasonalPlanning($user->id, $year);

        return response()->json([
            'success' => true,
            'seasonal_planning' => $planning
        ]);
    }

    /**
     * Setup automated repricing rules
     */
    public function setupAutomatedRepricing(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User must be logged in'
            ], 401);
        }

        $request->validate([
            'ad_id' => 'required|exists:ads,id',
            'price_strategy' => 'required|in:match_competitors,beat_competitors,fixed_price,percentage_above_competitors',
            'repricing_rules' => 'required|array',
            'repricing_rules.min_price' => 'nullable|numeric|min:0',
            'repricing_rules.max_price' => 'nullable|numeric|min:0',
            'repricing_rules.min_profit_margin' => 'nullable|numeric|min:0|max:100',
            'repricing_rules.match_competitor_within' => 'nullable|numeric|min:0', // Percentage difference allowed
        ]);

        try {
            $monitoring = $this->sellerToolsService->setupAutomatedRepricing(
                $user->id,
                $request->ad_id,
                $request->repricing_rules
            );

            return response()->json([
                'success' => true,
                'message' => 'Automated repricing setup successfully',
                'monitoring' => $monitoring
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to setup automated repricing: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get repricing recommendations
     */
    public function getRepricingRecommendations()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User must be logged in'
            ], 401);
        }

        $recommendations = $this->sellerToolsService->getRepricingRecommendations($user->id);

        return response()->json([
            'success' => true,
            'recommendations' => $recommendations
        ]);
    }

    /**
     * Get cross-platform selling opportunities
     */
    public function getCrossPlatformOpportunities()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User must be logged in'
            ], 401);
        }

        $opportunities = $this->sellerToolsService->getCrossPlatformOpportunities($user->id);

        return response()->json([
            'success' => true,
            'opportunities' => $opportunities
        ]);
    }

    /**
     * Get customer relationship management data
     */
    public function getCustomerManagementData()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User must be logged in'
            ], 401);
        }

        $crmData = $this->sellerToolsService->getCustomerManagementData($user->id);

        return response()->json([
            'success' => true,
            'crm_data' => $crmData
        ]);
    }

    /**
     * Set up loyalty program
     */
    public function setupLoyaltyProgram(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User must be logged in'
            ], 401);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'reward_type' => 'in:points,cashback,percent_discount,gift_cards|default:points',
            'reward_value' => 'required_with:reward_type|numeric|min:0',
            'minimum_purchases_for_reward' => 'integer|min:1',
            'tiers' => 'array',
            'tiers.*.name' => 'required_with:tiers|string',
            'tiers.*.min_spending' => 'required_with:tiers|numeric|min:0',
            'tiers.*.discount' => 'required_with:tiers|numeric|min:0|max:100',
        ]);

        try {
            $loyaltyProgram = $this->sellerToolsService->setupLoyaltyProgram($user->id, $request->all());

            return response()->json([
                'success' => true,
                'message' => 'Loyalty program created successfully',
                'loyalty_program' => $loyaltyProgram
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create loyalty program: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get customer segmentation and insights
     */
    public function getCustomerSegmentation()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User must be logged in'
            ], 401);
        }

        $segmentation = $this->sellerToolsService->getCustomerSegmentation($user->id);

        return response()->json([
            'success' => true,
            'segmentation' => $segmentation
        ]);
    }

    /**
     * Get inventory performance report
     */
    public function getInventoryPerformanceReport(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User must be logged in'
            ], 401);
        }

        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $locationId = $request->get('location_id');

        // This would be implemented as a comprehensive report with real logic
        $report = [
            'summary' => [
                'total_items' => 125,
                'total_inventory_value' => 2500000,
                'low_stock_items' => 8,
                'out_of_stock_items' => 3,
                'turnover_rate' => 4.2,
                'days_of_inventory' => 87,
            ],
            'by_category' => [
                [
                    'category' => 'Electronics',
                    'items_count' => 45,
                    'value' => 1250000,
                    'turnover_rate' => 5.8,
                    'trend' => 'increasing'
                ],
                [
                    'category' => 'Fashion',
                    'items_count' => 38,
                    'value' => 750000,
                    'turnover_rate' => 3.2,
                    'trend' => 'stable'
                ],
                [
                    'category' => 'Home & Garden',
                    'items_count' => 42,
                    'value' => 500000,
                    'turnover_rate' => 2.1,
                    'trend' => 'decreasing'
                ]
            ],
            'recommendations' => [
                'reorder_urgently' => [
                    'item_ids' => [12, 34, 56],
                    'category' => 'Electronics',
                    'suggested_reorder_quantity' => 50
                ],
                'reduce_inventory' => [
                    'item_ids' => [78, 90],
                    'category' => 'Home & Garden',
                    'suggested_reduction' => 30
                ],
                'seasonal_advice' => [
                    'categories' => ['Fashion'],
                    'action' => 'increase_stock',
                    'timing' => 'before_holiday_season'
                ]
            ]
        ];

        return response()->json([
            'success' => true,
            'report' => $report
        ]);
    }

    /**
     * Get sales forecasting
     */
    public function getSalesForecast(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User must be logged in'
            ], 401);
        }

        $period = $request->get('period', 'monthly'); // weekly, monthly, quarterly, yearly
        $months = $request->get('months', 3); // Forecast for next X months

        // This would be calculated based on historical data
        $forecast = [
            'period' => $period,
            'months_ahead' => $months,
            'forecasts' => [
                [
                    'month' => 'January 2025',
                    'predicted_sales_amount' => 1500000,
                    'predicted_sales_count' => 120,
                    'confidence_level' => 85,
                    'key_factors' => ['seasonal_low', 'post_holiday_slowdown']
                ],
                [
                    'month' => 'February 2025',
                    'predicted_sales_amount' => 1800000,
                    'predicted_sales_count' => 145,
                    'confidence_level' => 87,
                    'key_factors' => ['valentines_boost', 'spring_fashion_launch']
                ],
                [
                    'month' => 'March 2025',
                    'predicted_sales_amount' => 2100000,
                    'predicted_sales_count' => 165,
                    'confidence_level' => 83,
                    'key_factors' => ['spring_season', 'new_collection_launch']
                ]
            ],
            'recommendations' => [
                'inventory_advice' => 'Increase stock of fashion items by 25% in January',
                'marketing_budget' => 'Allocate 15% more budget in February for valentine promotion',
                'staffing' => 'Plan for increased staffing during March rush season'
            ],
            'risk_factors' => [
                'supply_chain_disruptions' => 'Medium risk due to global logistics issues',
                'competition' => 'High risk from new market entrants',
                'economic_factors' => 'Low to medium risk based on current economic stability in Nigeria'
            ]
        ];

        return response()->json([
            'success' => true,
            'forecast' => $forecast
        ]);
    }
}
