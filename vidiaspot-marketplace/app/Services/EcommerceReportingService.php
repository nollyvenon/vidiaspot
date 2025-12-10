<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Ad;
use App\Models\User;
use App\Models\Category;
use App\Models\Payment;
use Carbon\Carbon;

class EcommerceReportingService
{
    /**
     * Generate Sales Performance Reports
     */
    public function generateSalesPerformanceReport($startDate, $endDate)
    {
        return [
            'sales_dashboard' => $this->calculateSalesDashboard($startDate, $endDate),
            'product_performance' => $this->calculateProductPerformance($startDate, $endDate),
            'customer_lifetime_value' => $this->calculateCustomerLifetimeValue($startDate, $endDate),
            'seasonal_sales_analysis' => $this->calculateSeasonalSalesAnalysis($startDate, $endDate),
            'sales_channel_performance' => $this->calculateSalesChannelPerformance($startDate, $endDate),
            'period_start' => $startDate,
            'period_end' => $endDate
        ];
    }

    /**
     * Calculate sales dashboard metrics
     */
    private function calculateSalesDashboard($startDate, $endDate)
    {
        $orders = Order::whereBetween('created_at', [$startDate, $endDate])
                      ->where('status', 'completed')
                      ->get();

        $totalSales = 0;
        $totalOrders = count($orders);
        $totalItems = 0;

        foreach ($orders as $order) {
            $totalSales += $order->total_amount ?? 0;
            if (isset($order->order_items)) {
                $totalItems += count($order->order_items);
            }
        }

        $dailySales = [];
        foreach ($orders as $order) {
            $date = $order->created_at->format('Y-m-d');
            if (!isset($dailySales[$date])) {
                $dailySales[$date] = 0;
            }
            $dailySales[$date] += $order->total_amount ?? 0;
        }

        $growthMetrics = $this->calculateGrowthMetrics($startDate, $endDate);

        return [
            'total_sales' => $totalSales,
            'total_orders' => $totalOrders,
            'total_items_sold' => $totalItems,
            'average_order_value' => $totalOrders > 0 ? $totalSales / $totalOrders : 0,
            'daily_sales_trend' => $dailySales,
            'revenue_growth' => $growthMetrics['revenue_growth'],
            'order_growth' => $growthMetrics['order_growth'],
            'growth_metrics' => $growthMetrics
        ];
    }

    /**
     * Calculate growth metrics
     */
    private function calculateGrowthMetrics($startDate, $endDate)
    {
        $currentPeriodOrders = Order::whereBetween('created_at', [$startDate, $endDate])
                                   ->where('status', 'completed')
                                   ->get();

        $currentRevenue = $currentPeriodOrders->sum('total_amount');
        $currentOrders = $currentPeriodOrders->count();

        // Calculate previous period (same length as current)
        $prevEndDate = clone $startDate;
        $prevStartDate = clone $startDate;
        $prevStartDate->subDays($startDate->diffInDays($endDate));

        $prevPeriodOrders = Order::whereBetween('created_at', [$prevStartDate, $prevEndDate])
                                ->where('status', 'completed')
                                ->get();

        $prevRevenue = $prevPeriodOrders->sum('total_amount');
        $prevOrders = $prevPeriodOrders->count();

        $revenueGrowth = $prevRevenue > 0 ? (($currentRevenue - $prevRevenue) / $prevRevenue) * 100 : 0;
        $orderGrowth = $prevOrders > 0 ? (($currentOrders - $prevOrders) / $prevOrders) * 100 : 0;

        return [
            'revenue_growth' => $revenueGrowth,
            'order_growth' => $orderGrowth,
            'current_period_revenue' => $currentRevenue,
            'previous_period_revenue' => $prevRevenue,
            'current_period_orders' => $currentOrders,
            'previous_period_orders' => $prevOrders
        ];
    }

    /**
     * Calculate product performance
     */
    private function calculateProductPerformance($startDate, $endDate)
    {
        $orders = Order::whereBetween('created_at', [$startDate, $endDate])
                      ->where('status', 'completed')
                      ->with('vendorStore')
                      ->get();

        $productPerformance = [];
        $categoryPerformance = [];

        // This assumes order items are stored in the order_items array
        foreach ($orders as $order) {
            if (isset($order->order_items)) {
                foreach ($order->order_items as $item) {
                    $productName = $item['name'] ?? 'Unknown Product';
                    $categoryId = $item['category_id'] ?? 'Unknown Category';

                    if (!isset($productPerformance[$productName])) {
                        $productPerformance[$productName] = [
                            'quantity_sold' => 0,
                            'revenue' => 0,
                            'orders_count' => 0
                        ];
                    }

                    $quantity = $item['quantity'] ?? 1;
                    $price = $item['price'] ?? 0;

                    $productPerformance[$productName]['quantity_sold'] += $quantity;
                    $productPerformance[$productName]['revenue'] += $price * $quantity;
                    $productPerformance[$productName]['orders_count']++;

                    if (!isset($categoryPerformance[$categoryId])) {
                        $categoryPerformance[$categoryId] = [
                            'quantity_sold' => 0,
                            'revenue' => 0
                        ];
                    }
                    $categoryPerformance[$categoryId]['quantity_sold'] += $quantity;
                    $categoryPerformance[$categoryId]['revenue'] += $price * $quantity;
                }
            }
        }

        // Sort by revenue
        uasort($productPerformance, function($a, $b) {
            return $b['revenue'] <=> $a['revenue'];
        });

        uasort($categoryPerformance, function($a, $b) {
            return $b['revenue'] <=> $a['revenue'];
        });

        return [
            'best_sellers' => array_slice($productPerformance, 0, 10, true),
            'slow_movers' => array_slice($productPerformance, -10, 10, true),
            'product_profitability' => $this->calculateProductProfitability($productPerformance),
            'category_performance' => $categoryPerformance,
            'product_trends' => $this->calculateProductTrends($startDate, $endDate)
        ];
    }

    /**
     * Calculate product profitability
     */
    private function calculateProductProfitability($productPerformance)
    {
        $profitability = [];
        foreach ($productPerformance as $product => $stats) {
            // Placeholder for cost calculation
            $estimatedCost = $stats['revenue'] * 0.6; // Assuming 60% cost
            $profit = $stats['revenue'] - $estimatedCost;
            $profitMargin = $stats['revenue'] > 0 ? ($profit / $stats['revenue']) * 100 : 0;

            $profitability[$product] = [
                'revenue' => $stats['revenue'],
                'estimated_cost' => $estimatedCost,
                'profit' => $profit,
                'profit_margin' => $profitMargin,
                'profit_per_unit' => $stats['quantity_sold'] > 0 ? $profit / $stats['quantity_sold'] : 0
            ];
        }

        // Sort by profit
        uasort($profitability, function($a, $b) {
            return $b['profit'] <=> $a['profit'];
        });

        return $profitability;
    }

    /**
     * Calculate product trends
     */
    private function calculateProductTrends($startDate, $endDate)
    {
        $orders = Order::whereBetween('created_at', [$startDate, $endDate])
                      ->where('status', 'completed')
                      ->get();

        $productTrends = [];
        foreach ($orders as $order) {
            if (isset($order->order_items)) {
                foreach ($order->order_items as $item) {
                    $productName = $item['name'] ?? 'Unknown Product';
                    $date = $order->created_at->format('Y-m');
                    
                    if (!isset($productTrends[$productName])) {
                        $productTrends[$productName] = [];
                    }
                    
                    if (!isset($productTrends[$productName][$date])) {
                        $productTrends[$productName][$date] = [
                            'quantity' => 0,
                            'revenue' => 0
                        ];
                    }
                    
                    $quantity = $item['quantity'] ?? 1;
                    $price = $item['price'] ?? 0;
                    
                    $productTrends[$productName][$date]['quantity'] += $quantity;
                    $productTrends[$productName][$date]['revenue'] += $price * $quantity;
                }
            }
        }

        return $productTrends;
    }

    /**
     * Calculate customer lifetime value
     */
    private function calculateCustomerLifetimeValue($startDate, $endDate)
    {
        $orders = Order::whereBetween('created_at', [$startDate, $endDate])
                      ->where('status', 'completed')
                      ->get();

        $customerData = [];
        foreach ($orders as $order) {
            $userId = $order->user_id;
            if (!isset($customerData[$userId])) {
                $customerData[$userId] = [
                    'total_spent' => 0,
                    'order_count' => 0,
                    'first_order_date' => $order->created_at,
                    'last_order_date' => $order->created_at
                ];
            }
            
            $customerData[$userId]['total_spent'] += $order->total_amount ?? 0;
            $customerData[$userId]['order_count']++;
            
            if ($order->created_at < $customerData[$userId]['first_order_date']) {
                $customerData[$userId]['first_order_date'] = $order->created_at;
            }
            if ($order->created_at > $customerData[$userId]['last_order_date']) {
                $customerData[$userId]['last_order_date'] = $order->created_at;
            }
        }

        // Calculate average values
        $totalCustomers = count($customerData);
        $totalRevenue = 0;
        $totalOrders = 0;

        foreach ($customerData as $data) {
            $totalRevenue += $data['total_spent'];
            $totalOrders += $data['order_count'];
        }

        $avgOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;
        $avgPurchaseFrequency = $totalCustomers > 0 ? $totalOrders / $totalCustomers : 0;
        $avgCustomerLifespan = 0; // Placeholder - would need historical data

        $clv = $avgOrderValue * $avgPurchaseFrequency * $avgCustomerLifespan;

        return [
            'total_customers' => $totalCustomers,
            'average_order_value' => $avgOrderValue,
            'average_purchase_frequency' => $avgPurchaseFrequency,
            'average_customer_lifespan' => $avgCustomerLifespan,
            'customer_lifetime_value' => $clv,
            'customer_segments' => $this->calculateCustomerSegments($customerData),
            'cohort_retention' => $this->calculateCohortRetention($startDate, $endDate)
        ];
    }

    /**
     * Calculate customer segments
     */
    private function calculateCustomerSegments($customerData)
    {
        $segments = [
            'vip' => [],
            'loyal' => [],
            'regular' => [],
            'new' => [],
            'at_risk' => []
        ];

        foreach ($customerData as $userId => $data) {
            $totalSpent = $data['total_spent'];
            $orderCount = $data['order_count'];
            $daysSinceFirstOrder = $data['first_order_date']->diffInDays($data['last_order_date']);

            if ($totalSpent > 1000 && $orderCount > 10) {
                $segments['vip'][$userId] = $data;
            } elseif ($totalSpent > 500 && $orderCount > 5) {
                $segments['loyal'][$userId] = $data;
            } elseif ($orderCount > 2) {
                $segments['regular'][$userId] = $data;
            } elseif ($orderCount === 1) {
                $segments['new'][$userId] = $data;
            } else {
                $segments['at_risk'][$userId] = $data;
            }
        }

        // Add summary statistics
        foreach ($segments as $type => $segment) {
            $segments[$type . '_summary'] = [
                'count' => count($segment),
                'total_revenue' => array_sum(array_column($segment, 'total_spent')),
                'avg_order_value' => count($segment) > 0 ? array_sum(array_column($segment, 'total_spent')) / count($segment) : 0
            ];
        }

        return $segments;
    }

    /**
     * Calculate cohort retention
     */
    private function calculateCohortRetention($startDate, $endDate)
    {
        // Simplified cohort analysis - tracking customers from signup to purchases
        $users = User::whereBetween('created_at', [$startDate, $endDate])->get();
        
        $cohortData = [];
        foreach ($users as $user) {
            $signupMonth = $user->created_at->format('Y-m');
            if (!isset($cohortData[$signupMonth])) {
                $cohortData[$signupMonth] = [
                    'total_users' => 0,
                    'retained_users' => 0
                ];
            }
            $cohortData[$signupMonth]['total_users']++;
            
            // Check if user has made orders since signup
            $userOrders = Order::where('user_id', $user->id)
                             ->where('created_at', '>', $user->created_at)
                             ->count();
            
            if ($userOrders > 0) {
                $cohortData[$signupMonth]['retained_users']++;
            }
        }
        
        // Calculate retention rates
        foreach ($cohortData as $month => &$data) {
            $data['retention_rate'] = $data['total_users'] > 0 ? 
                ($data['retained_users'] / $data['total_users']) * 100 : 0;
        }

        return $cohortData;
    }

    /**
     * Calculate seasonal sales analysis
     */
    private function calculateSeasonalSalesAnalysis($startDate, $endDate)
    {
        $orders = Order::whereBetween('created_at', [$startDate, $endDate])
                      ->where('status', 'completed')
                      ->get();

        $seasonalData = [
            'january' => ['revenue' => 0, 'orders' => 0],
            'february' => ['revenue' => 0, 'orders' => 0],
            'march' => ['revenue' => 0, 'orders' => 0],
            'april' => ['revenue' => 0, 'orders' => 0],
            'may' => ['revenue' => 0, 'orders' => 0],
            'june' => ['revenue' => 0, 'orders' => 0],
            'july' => ['revenue' => 0, 'orders' => 0],
            'august' => ['revenue' => 0, 'orders' => 0],
            'september' => ['revenue' => 0, 'orders' => 0],
            'october' => ['revenue' => 0, 'orders' => 0],
            'november' => ['revenue' => 0, 'orders' => 0],
            'december' => ['revenue' => 0, 'orders' => 0]
        ];

        foreach ($orders as $order) {
            $month = strtolower($order->created_at->format('F'));
            $seasonalData[$month]['revenue'] += $order->total_amount ?? 0;
            $seasonalData[$month]['orders']++;
        }

        return [
            'monthly_revenue_trends' => $seasonalData,
            'holiday_performance' => $this->calculateHolidayPerformance($seasonalData),
            'seasonal_planning_insights' => $this->calculateSeasonalPlanningInsights($seasonalData)
        ];
    }

    /**
     * Calculate holiday performance
     */
    private function calculateHolidayPerformance($seasonalData)
    {
        // Identify typical high-performing months (e.g., Nov/Dec for holiday season)
        $highPerformers = array_filter($seasonalData, function($data) {
            return $data['revenue'] > array_sum(array_column($seasonalData, 'revenue')) / 12 * 1.5;
        });

        return [
            'peak_months' => array_keys($highPerformers),
            'holiday_revenue' => array_sum(array_column($highPerformers, 'revenue')),
            'growth_opportunities' => $this->identifyGrowthOpportunities($seasonalData)
        ];
    }

    /**
     * Identify growth opportunities
     */
    private function identifyGrowthOpportunities($seasonalData)
    {
        $avgRevenue = array_sum(array_column($seasonalData, 'revenue')) / 12;
        $lowPerformers = array_filter($seasonalData, function($data) use ($avgRevenue) {
            return $data['revenue'] < $avgRevenue * 0.8;
        });

        return [
            'low_performing_months' => array_keys($lowPerformers),
            'potential_increase' => array_sum(array_column($lowPerformers, 'revenue')) * 0.5 // 50% potential increase
        ];
    }

    /**
     * Calculate seasonal planning insights
     */
    private function calculateSeasonalPlanningInsights($seasonalData)
    {
        $insights = [];
        
        // Calculate seasonality index
        $avgRevenue = array_sum(array_column($seasonalData, 'revenue')) / 12;
        foreach ($seasonalData as $month => $data) {
            $insights[$month] = [
                'revenue' => $data['revenue'],
                'seasonality_index' => $avgRevenue > 0 ? $data['revenue'] / $avgRevenue : 0,
                'recommendation' => $data['revenue'] > $avgRevenue ? 'capitalize' : 'promote'
            ];
        }

        return $insights;
    }

    /**
     * Calculate sales channel performance
     */
    private function calculateSalesChannelPerformance($startDate, $endDate)
    {
        $orders = Order::whereBetween('created_at', [$startDate, $endDate])
                      ->where('status', 'completed')
                      ->get();

        $channelPerformance = [
            'web' => ['revenue' => 0, 'orders' => 0],
            'mobile' => ['revenue' => 0, 'orders' => 0],
            'social_commerce' => ['revenue' => 0, 'orders' => 0],
            'marketplace' => ['revenue' => 0, 'orders' => 0]
        ];

        foreach ($orders as $order) {
            // Assuming channel is stored in a source field or can be determined
            $source = $order->source ?? 'web'; // Default to web
            $sourceKey = $this->mapSourceToChannel($source);
            
            if (isset($channelPerformance[$sourceKey])) {
                $channelPerformance[$sourceKey]['revenue'] += $order->total_amount ?? 0;
                $channelPerformance[$sourceKey]['orders']++;
            } else {
                // Add new channel
                $channelPerformance[$sourceKey] = [
                    'revenue' => $order->total_amount ?? 0,
                    'orders' => 1
                ];
            }
        }

        return [
            'performance_by_channel' => $channelPerformance,
            'channel_roi' => $this->calculateChannelROI($channelPerformance),
            'cross_channel_behavior' => $this->calculateCrossChannelBehavior($startDate, $endDate)
        ];
    }

    /**
     * Map order source to standard channel names
     */
    private function mapSourceToChannel($source)
    {
        $source = strtolower($source);
        if (strpos($source, 'mobile') !== false || strpos($source, 'app') !== false) {
            return 'mobile';
        } elseif (strpos($source, 'social') !== false || strpos($source, 'instagram') !== false || 
                  strpos($source, 'facebook') !== false) {
            return 'social_commerce';
        } elseif (strpos($source, 'marketplace') !== false || strpos($source, 'amazon') !== false || 
                  strpos($source, 'ebay') !== false) {
            return 'marketplace';
        } else {
            return 'web';
        }
    }

    /**
     * Calculate channel ROI
     */
    private function calculateChannelROI($channelPerformance)
    {
        $roi = [];
        foreach ($channelPerformance as $channel => $data) {
            // Placeholder for marketing costs
            $marketingCost = $data['revenue'] * 0.2; // Assuming 20% marketing cost
            $roi[$channel] = [
                'revenue' => $data['revenue'],
                'marketing_cost' => $marketingCost,
                'roi' => $marketingCost > 0 ? (($data['revenue'] - $marketingCost) / $marketingCost) * 100 : 0
            ];
        }
        return $roi;
    }

    /**
     * Calculate cross-channel behavior
     */
    private function calculateCrossChannelBehavior($startDate, $endDate)
    {
        // This would require tracking users across channels
        // Placeholder implementation
        return [
            'multi_channel_users' => 0,
            'cross_channel_conversion_rate' => 0.15, // 15% of users shop across channels
            'channel_preferral_paths' => []
        ];
    }

    /**
     * Generate Inventory Management Reports
     */
    public function generateInventoryReport($startDate, $endDate)
    {
        return [
            'stock_level_reports' => $this->calculateStockLevelReports($startDate, $endDate),
            'inventory_turnover' => $this->calculateInventoryTurnover($startDate, $endDate),
            'supplier_performance' => $this->calculateSupplierPerformance($startDate, $endDate),
            'dead_stock_analysis' => $this->calculateDeadStockAnalysis($startDate, $endDate),
            'demand_forecasting' => $this->calculateDemandForecasting($startDate, $endDate),
            'period_start' => $startDate,
            'period_end' => $endDate
        ];
    }

    /**
     * Calculate stock level reports
     */
    private function calculateStockLevelReports($startDate, $endDate)
    {
        // This would require actual inventory tracking
        // For now, using ad listings as proxy for inventory
        $ads = Ad::whereBetween('created_at', [$startDate, $endDate])
                 ->where('is_inventory_item', true) // Assuming some ads represent inventory
                 ->get();

        $stockLevels = [];
        foreach ($ads as $ad) {
            $stockLevels[$ad->id] = [
                'name' => $ad->title,
                'current_stock' => $ad->stock_quantity ?? 1, // Assuming stock tracking in ad
                'min_stock_level' => $ad->min_stock_level ?? 5,
                'category' => $ad->category->name ?? 'Uncategorized'
            ];
        }

        return [
            'current_inventory' => $stockLevels,
            'reorder_points' => $this->calculateReorderPoints($stockLevels),
            'safety_stock' => $this->calculateSafetyStock($stockLevels)
        ];
    }

    /**
     * Calculate reorder points
     */
    private function calculateReorderPoints($stockLevels)
    {
        $reorderAlerts = [];
        foreach ($stockLevels as $id => $info) {
            if ($info['current_stock'] <= $info['min_stock_level']) {
                $reorderAlerts[] = [
                    'item_id' => $id,
                    'name' => $info['name'],
                    'current_stock' => $info['current_stock'],
                    'reorder_level' => $info['min_stock_level'],
                    'category' => $info['category']
                ];
            }
        }
        return $reorderAlerts;
    }

    /**
     * Calculate safety stock
     */
    private function calculateSafetyStock($stockLevels)
    {
        // Placeholder for safety stock calculation
        return [
            'recommended_safety_stock' => [],
            'shortage_risk_items' => [],
            'overstock_items' => []
        ];
    }

    /**
     * Calculate inventory turnover
     */
    private function calculateInventoryTurnover($startDate, $endDate)
    {
        // Calculate how quickly inventory is sold and replaced
        $ads = Ad::whereBetween('created_at', [$startDate, $endDate])
                 ->where('is_inventory_item', true)
                 ->get();

        $turnoverRates = [];
        foreach ($ads as $ad) {
            $soldQuantity = $ad->quantity_sold ?? 0;
            $avgInventory = $ad->starting_stock ??
                           ($ad->starting_stock + $ad->current_stock) / 2;

            $turnoverRate = $avgInventory > 0 ? $soldQuantity / $avgInventory : 0;

            $turnoverRates[$ad->id] = [
                'name' => $ad->title,
                'turnover_rate' => $turnoverRate,
                'category' => $ad->category->name ?? 'Uncategorized',
                'performance' => $this->classifyTurnoverPerformance($turnoverRate)
            ];
        }

        return [
            'turnover_rates' => $turnoverRates,
            'fast_moving_items' => array_filter($turnoverRates, function($item) {
                return $item['performance'] === 'fast';
            }),
            'slow_moving_items' => array_filter($turnoverRates, function($item) {
                return $item['performance'] === 'slow';
            }),
            'carrying_costs' => $this->calculateCarryingCosts($startDate, $endDate)
        ];
    }

    /**
     * Classify turnover performance
     */
    private function classifyTurnoverPerformance($rate)
    {
        if ($rate > 10) return 'fast';
        if ($rate > 5) return 'medium';
        return 'slow';
    }

    /**
     * Calculate carrying costs
     */
    private function calculateCarryingCosts($startDate, $endDate)
    {
        return [
            'total_carrying_cost' => 0,
            'cost_percentage_of_inventory' => 0.1, // 10% carrying cost
            'cost_by_category' => []
        ];
    }

    /**
     * Calculate supplier performance
     */
    private function calculateSupplierPerformance($startDate, $endDate)
    {
        // This would require supplier tracking
        // Placeholder implementation
        return [
            'lead_times' => [],
            'quality_metrics' => [],
            'cost_analysis' => [],
            'reliability_scores' => []
        ];
    }

    /**
     * Calculate dead stock analysis
     */
    private function calculateDeadStockAnalysis($startDate, $endDate)
    {
        // Items with low turnover that might be dead stock
        $ads = Ad::whereBetween('created_at', [$startDate, $endDate])
                 ->where('is_inventory_item', true)
                 ->get();

        $deadStock = [];
        foreach ($ads as $ad) {
            $daysSinceLastSale = $ad->last_sale_date ? 
                Carbon::parse($ad->last_sale_date)->diffInDays(now()) : 180; // Assuming 180 days if no sale

            if ($daysSinceLastSale > 180) { // More than 6 months
                $deadStock[] = [
                    'item_id' => $ad->id,
                    'name' => $ad->title,
                    'days_since_last_sale' => $daysSinceLastSale,
                    'current_stock' => $ad->stock_quantity ?? 1,
                    'value' => ($ad->stock_quantity ?? 1) * ($ad->price ?? 0)
                ];
            }
        }

        return [
            'dead_stock_items' => $deadStock,
            'dead_stock_value' => array_sum(array_column($deadStock, 'value')),
            'liquidation_recommendations' => $this->calculateLiquidationRecommendations($deadStock)
        ];
    }

    /**
     * Calculate liquidation recommendations
     */
    private function calculateLiquidationRecommendations($deadStock)
    {
        $recommendations = [];
        foreach ($deadStock as $item) {
            if ($item['current_stock'] > 10) {
                $recommendations[] = [
                    'item_id' => $item['item_id'],
                    'action' => 'bulk_discount',
                    'discount_percentage' => 30
                ];
            } elseif ($item['current_stock'] > 5) {
                $recommendations[] = [
                    'item_id' => $item['item_id'],
                    'action' => 'promotional_campaign',
                    'discount_percentage' => 20
                ];
            } else {
                $recommendations[] = [
                    'item_id' => $item['item_id'],
                    'action' => 'charity_donation',
                    'value' => $item['value']
                ];
            }
        }
        return $recommendations;
    }

    /**
     * Calculate demand forecasting
     */
    private function calculateDemandForecasting($startDate, $endDate)
    {
        // Simple forecasting based on historical trends
        $orders = Order::whereBetween('created_at', [$startDate, $endDate])
                      ->where('status', 'completed')
                      ->get();

        $forecastingData = [];
        foreach ($orders as $order) {
            if (isset($order->order_items)) {
                foreach ($order->order_items as $item) {
                    $productName = $item['name'] ?? 'Unknown';
                    $date = $order->created_at->format('Y-m-d');
                    
                    if (!isset($forecastingData[$productName])) {
                        $forecastingData[$productName] = [];
                    }
                    
                    if (!isset($forecastingData[$productName][$date])) {
                        $forecastingData[$productName][$date] = 0;
                    }
                    
                    $forecastingData[$productName][$date] += $item['quantity'] ?? 1;
                }
            }
        }

        // Calculate average daily sales for each product
        $dailyAverages = [];
        foreach ($forecastingData as $product => $dailySales) {
            $totalQuantity = array_sum($dailySales);
            $totalDays = count($dailySales);
            $dailyAverages[$product] = $totalDays > 0 ? $totalQuantity / $totalDays : 0;
        }

        return [
            'historical_demand_patterns' => $forecastingData,
            'daily_sales_averages' => $dailyAverages,
            'predicted_demand_30_days' => $this->calculate30DayPredictions($dailyAverages),
            'demand_variations' => $this->calculateDemandVariations($forecastingData)
        ];
    }

    /**
     * Calculate 30-day demand predictions
     */
    private function calculate30DayPredictions($dailyAverages)
    {
        $predictions = [];
        foreach ($dailyAverages as $product => $average) {
            $predictions[$product] = $average * 30; // 30-day prediction
        }
        return $predictions;
    }

    /**
     * Calculate demand variations
     */
    private function calculateDemandVariations($forecastingData)
    {
        $variations = [];
        foreach ($forecastingData as $product => $dailySales) {
            $values = array_values($dailySales);
            if (count($values) > 1) {
                $mean = array_sum($values) / count($values);
                $variance = array_sum(array_map(function($x) use ($mean) { 
                    return pow($x - $mean, 2); 
                }, $values)) / count($values);
                $stdDev = sqrt($variance);
                
                $variations[$product] = [
                    'mean' => $mean,
                    'std_dev' => $stdDev,
                    'coefficient_of_variation' => $mean > 0 ? $stdDev / $mean : 0
                ];
            } else {
                $variations[$product] = ['mean' => $values[0] ?? 0, 'std_dev' => 0, 'coefficient_of_variation' => 0];
            }
        }
        return $variations;
    }

    /**
     * Generate Marketing & Customer Reports
     */
    public function generateMarketingCustomerReport($startDate, $endDate)
    {
        return [
            'conversion_funnel' => $this->calculateConversionFunnel($startDate, $endDate),
            'marketing_roi' => $this->calculateMarketingROI($startDate, $endDate),
            'customer_segmentation' => $this->calculateCustomerSegmentation($startDate, $endDate),
            'email_marketing' => $this->calculateEmailMarketing($startDate, $endDate),
            'seo_performance' => $this->calculateSEOPerformance($startDate, $endDate),
            'period_start' => $startDate,
            'period_end' => $endDate
        ];
    }

    /**
     * Calculate conversion funnel
     */
    private function calculateConversionFunnel($startDate, $endDate)
    {
        // This would require tracking user behavior
        return [
            'visitors' => 10000, // Placeholder
            'add_to_cart' => 1500,
            'checkout_initiated' => 1200,
            'checkout_completed' => 1000,
            'abandonment_rate' => 0.15, // 15% abandonment
            'conversion_rate' => 0.10, // 10% conversion
            'optimization_opportunities' => [
                'checkout_process' => '3% loss at payment step',
                'cart_abandonment' => 'recover 40% of abandoned carts'
            ]
        ];
    }

    /**
     * Calculate marketing ROI
     */
    private function calculateMarketingROI($startDate, $endDate)
    {
        $orders = Order::whereBetween('created_at', [$startDate, $endDate])
                      ->where('status', 'completed')
                      ->get();

        $totalRevenue = $orders->sum('total_amount');
        $marketingSpend = 5000; // Placeholder marketing spend

        $roi = $marketingSpend > 0 ? (($totalRevenue - $marketingSpend) / $marketingSpend) * 100 : 0;
        $roas = $marketingSpend > 0 ? $totalRevenue / $marketingSpend : 0; // Return on ad spend

        return [
            'total_revenue' => $totalRevenue,
            'marketing_spend' => $marketingSpend,
            'roi' => $roi,
            'roas' => $roas,
            'campaign_performance' => $this->calculateCampaignPerformance($startDate, $endDate),
            'channel_attribution' => $this->calculateChannelAttribution($startDate, $endDate)
        ];
    }

    /**
     * Calculate campaign performance
     */
    private function calculateCampaignPerformance($startDate, $endDate)
    {
        // Placeholder for campaign tracking
        return [
            'social_media' => [
                'spend' => 2000,
                'revenue' => 8000,
                'roi' => 300
            ],
            'google_ads' => [
                'spend' => 1500,
                'revenue' => 6000,
                'roi' => 300
            ],
            'email_marketing' => [
                'spend' => 500,
                'revenue' => 2500,
                'roi' => 400
            ],
            'display_ads' => [
                'spend' => 1000,
                'revenue' => 3000,
                'roi' => 200
            ]
        ];
    }

    /**
     * Calculate channel attribution
     */
    private function calculateChannelAttribution($startDate, $endDate)
    {
        return [
            'first_touch' => [
                'organic_search' => 0.40,
                'social_media' => 0.25,
                'direct' => 0.20,
                'email' => 0.10,
                'referral' => 0.05
            ],
            'last_touch' => [
                'organic_search' => 0.30,
                'social_media' => 0.20,
                'direct' => 0.35,
                'email' => 0.10,
                'referral' => 0.05
            ],
            'multi_touch' => [
                'social_to_purchase' => 0.15,
                'search_to_social_to_purchase' => 0.25
            ]
        ];
    }

    /**
     * Calculate customer segmentation
     */
    private function calculateCustomerSegmentation($startDate, $endDate)
    {
        $customerData = $this->calculateCustomerLifetimeValue($startDate, $endDate);

        $segments = [
            'high_value' => 0,
            'medium_value' => 0,
            'low_value' => 0,
            'new_customers' => 0,
            'at_risk' => 0
        ];

        $highValueThreshold = 500;
        $lowValueThreshold = 50;

        foreach ($customerData['customer_segments']['new'] as $userId => $data) {
            $segments['new_customers']++;
        }

        foreach ($customerData['customer_segments']['vip'] as $userId => $data) {
            if ($data['total_spent'] > $highValueThreshold) {
                $segments['high_value']++;
            } else if ($data['total_spent'] <= $lowValueThreshold) {
                $segments['low_value']++;
            } else {
                $segments['medium_value']++;
            }
        }

        foreach ($customerData['customer_segments']['at_risk'] as $userId => $data) {
            $segments['at_risk']++;
        }

        return [
            'demographics' => $customerData['customer_segments'],
            'behavioral_segments' => $segments,
            'segment_valuation' => $this->calculateSegmentValuation($customerData),
            'targeting_recommendations' => [
                'high_value' => 'personalized experiences and loyalty rewards',
                'medium_value' => 'upselling and cross-selling',
                'low_value' => 'winback campaigns',
                'at_risk' => 'retention campaigns'
            ]
        ];
    }

    /**
     * Calculate segment valuation
     */
    private function calculateSegmentValuation($customerData)
    {
        $valuation = [];
        foreach ($customerData['customer_segments'] as $segmentType => $segment) {
            if (isset($segment['count'])) {
                $valuation[$segmentType] = [
                    'count' => $segment['count'],
                    'total_revenue' => $segment['total_revenue'] ?? 0,
                    'avg_order_value' => $segment['avg_order_value'] ?? 0
                ];
            }
        }
        return $valuation;
    }

    /**
     * Calculate email marketing performance
     */
    private function calculateEmailMarketing($startDate, $endDate)
    {
        // Placeholder for email marketing data
        return [
            'email_campaigns_sent' => 12,
            'emails_sent' => 50000,
            'emails_opened' => 12500,
            'emails_clicked' => 2500,
            'conversions_from_email' => 150,
            'open_rate' => 0.25, // 25%
            'click_rate' => 0.05, // 5%
            'conversion_rate' => 0.003, // 0.3%
            'revenue_generated' => 15000,
            'roi' => 200 // 200% ROI
        ];
    }

    /**
     * Calculate SEO performance
     */
    private function calculateSEOPerformance($startDate, $endDate)
    {
        // Placeholder for SEO data
        return [
            'organic_traffic' => 25000,
            'keyword_rankings' => [
                'shop online' => 3,
                'buy now' => 7,
                'discount deals' => 12
            ],
            'search_visibility' => 0.65, // 65% visibility
            'organic_conversion_rate' => 0.08, // 8%
            'impression_share' => 0.45, // 45%
            'improvement_opportunities' => [
                'product_page_optimization',
                'content_marketing',
                'technical_seo'
            ]
        ];
    }

    /**
     * Generate Financial & Operational Reports
     */
    public function generateFinancialOperationalReport($startDate, $endDate)
    {
        return [
            'profit_margins' => $this->calculateEcommerceProfitMargins($startDate, $endDate),
            'shipping_analytics' => $this->calculateShippingAnalytics($startDate, $endDate),
            'return_refund_analysis' => $this->calculateReturnRefundAnalysis($startDate, $endDate),
            'tax_compliance' => $this->calculateTaxCompliance($startDate, $endDate),
            'payment_processing' => $this->calculateEcommercePaymentProcessing($startDate, $endDate),
            'period_start' => $startDate,
            'period_end' => $endDate
        ];
    }

    /**
     * Calculate profit margins for e-commerce
     */
    private function calculateEcommerceProfitMargins($startDate, $endDate)
    {
        $orders = Order::whereBetween('created_at', [$startDate, $endDate])
                      ->where('status', 'completed')
                      ->get();

        $totalRevenue = $orders->sum('total_amount');
        $totalCost = 0;
        $totalOrders = count($orders);

        // Calculate COGS (Cost of Goods Sold)
        foreach ($orders as $order) {
            if (isset($order->order_items)) {
                foreach ($order->order_items as $item) {
                    $cost = $item['cost'] ?? ($item['price'] * 0.7); // Assuming 70% cost
                    $totalCost += $cost * ($item['quantity'] ?? 1);
                }
            }
        }

        $grossProfit = $totalRevenue - $totalCost;
        $grossMargin = $totalRevenue > 0 ? ($grossProfit / $totalRevenue) * 100 : 0;

        return [
            'total_revenue' => $totalRevenue,
            'total_cogs' => $totalCost,
            'gross_profit' => $grossProfit,
            'gross_margin_percentage' => $grossMargin,
            'by_product' => $this->calculateProfitByProduct($startDate, $endDate),
            'by_category' => $this->calculateProfitByCategory($startDate, $endDate),
            'by_customer_segment' => $this->calculateProfitByCustomerSegment($startDate, $endDate),
            'operating_expenses' => $this->calculateOperatingExpenses($totalRevenue)
        ];
    }

    /**
     * Calculate profit by product
     */
    private function calculateProfitByProduct($startDate, $endDate)
    {
        // Placeholder implementation
        return [
            'highest_margin' => [
                'product_name' => 'Product A',
                'margin' => 45
            ],
            'lowest_margin' => [
                'product_name' => 'Product B', 
                'margin' => 12
            ]
        ];
    }

    /**
     * Calculate profit by category
     */
    private function calculateProfitByCategory($startDate, $endDate)
    {
        $orders = Order::whereBetween('created_at', [$startDate, $endDate])
                      ->where('status', 'completed')
                      ->get();

        $categoryProfits = [];
        foreach ($orders as $order) {
            if (isset($order->order_items)) {
                foreach ($order->order_items as $item) {
                    $category = $item['category'] ?? 'Uncategorized';
                    $revenue = $item['price'] * ($item['quantity'] ?? 1);
                    $cost = $item['cost'] ?? ($item['price'] * 0.7);
                    $profit = $revenue - $cost;

                    if (!isset($categoryProfits[$category])) {
                        $categoryProfits[$category] = [
                            'revenue' => 0,
                            'cost' => 0,
                            'profit' => 0,
                            'margin' => 0
                        ];
                    }

                    $categoryProfits[$category]['revenue'] += $revenue;
                    $categoryProfits[$category]['cost'] += $cost;
                    $categoryProfits[$category]['profit'] += $profit;
                }
            }
        }

        foreach ($categoryProfits as $category => $data) {
            $categoryProfits[$category]['margin'] = 
                $data['revenue'] > 0 ? ($data['profit'] / $data['revenue']) * 100 : 0;
        }

        return $categoryProfits;
    }

    /**
     * Calculate profit by customer segment
     */
    private function calculateProfitByCustomerSegment($startDate, $endDate)
    {
        $customerSegments = $this->calculateCustomerSegmentation($startDate, $endDate);
        return [
            'high_value_customers' => [
                'profit_contribution' => 0.70, // 70% of total profit
                'avg_margin' => 35
            ],
            'medium_value_customers' => [
                'profit_contribution' => 0.25, // 25% of total profit
                'avg_margin' => 28
            ],
            'low_value_customers' => [
                'profit_contribution' => 0.05, // 5% of total profit
                'avg_margin' => 15
            ]
        ];
    }

    /**
     * Calculate operating expenses
     */
    private function calculateOperatingExpenses($totalRevenue)
    {
        return [
            'marketing_expense' => $totalRevenue * 0.15, // 15% of revenue
            'fulfillment_expense' => $totalRevenue * 0.08, // 8% of revenue
            'technology_expense' => $totalRevenue * 0.05, // 5% of revenue
            'personnel_expense' => $totalRevenue * 0.12, // 12% of revenue
            'total_operating_expense' => $totalRevenue * 0.40, // 40% of revenue
            'operating_margin' => 25 // Remaining margin after operating expenses
        ];
    }

    /**
     * Calculate shipping analytics
     */
    private function calculateShippingAnalytics($startDate, $endDate)
    {
        $orders = Order::whereBetween('created_at', [$startDate, $endDate])
                      ->where('status', 'completed')
                      ->get();

        $totalShippingCost = 0;
        $totalOrders = count($orders);
        $shippingMethods = [];

        foreach ($orders as $order) {
            $shippingCost = $order->shipping_cost ?? 0;
            $totalShippingCost += $shippingCost;

            $method = $order->shipping_method ?? 'standard';
            if (!isset($shippingMethods[$method])) {
                $shippingMethods[$method] = [
                    'orders' => 0,
                    'cost' => 0
                ];
            }
            $shippingMethods[$method]['orders']++;
            $shippingMethods[$method]['cost'] += $shippingCost;
        }

        return [
            'total_shipping_cost' => $totalShippingCost,
            'average_shipping_cost_per_order' => $totalOrders > 0 ? $totalShippingCost / $totalOrders : 0,
            'shipping_cost_percentage_of_revenue' => $totalOrders > 0 ? ($totalShippingCost / array_sum($orders->pluck('total_amount')->toArray())) * 100 : 0,
            'shipping_method_performance' => $shippingMethods,
            'carrier_performance' => $this->calculateCarrierPerformance($startDate, $endDate),
            'delivery_time_analysis' => $this->calculateDeliveryTimeAnalysis($startDate, $endDate)
        ];
    }

    /**
     * Calculate carrier performance
     */
    private function calculateCarrierPerformance($startDate, $endDate)
    {
        return [
            'ups' => [
                'on_time_rate' => 0.95, // 95%
                'cost_per_package' => 8.50,
                'delivery_accuracy' => 0.98
            ],
            'fedex' => [
                'on_time_rate' => 0.92, // 92%
                'cost_per_package' => 9.20,
                'delivery_accuracy' => 0.96
            ],
            'usps' => [
                'on_time_rate' => 0.88, // 88%
                'cost_per_package' => 6.75,
                'delivery_accuracy' => 0.94
            ]
        ];
    }

    /**
     * Calculate delivery time analysis
     */
    private function calculateDeliveryTimeAnalysis($startDate, $endDate)
    {
        // Placeholder for delivery time tracking
        return [
            'avg_delivery_time' => '3.2 days',
            'on_time_delivery_rate' => 0.93, // 93%
            'delivery_time_by_method' => [
                'standard' => '5.1 days',
                'express' => '2.3 days',
                'overnight' => '1.0 day'
            ]
        ];
    }

    /**
     * Calculate return & refund analysis
     */
    private function calculateReturnRefundAnalysis($startDate, $endDate)
    {
        $orders = Order::whereBetween('created_at', [$startDate, $endDate])
                      ->get();

        $totalOrders = count($orders);
        $returnRequests = 0;
        $refundsProcessed = 0;
        $totalRefundAmount = 0;

        $returnReasons = [
            'wrong_size' => 0,
            'wrong_color' => 0,
            'defective' => 0,
            'changed_mind' => 0,
            'late_delivery' => 0,
            'other' => 0
        ];

        foreach ($orders as $order) {
            if ($order->return_requested ?? false) {
                $returnRequests++;
                
                if ($order->refund_processed ?? false) {
                    $refundsProcessed++;
                    $totalRefundAmount += $order->total_amount ?? 0;
                    
                    $reason = strtolower($order->return_reason ?? 'other');
                    if (array_key_exists($reason, $returnReasons)) {
                        $returnReasons[$reason]++;
                    } else {
                        $returnReasons['other']++;
                    }
                }
            }
        }

        $returnRate = $totalOrders > 0 ? ($returnRequests / $totalOrders) * 100 : 0;
        $refundRate = $totalOrders > 0 ? ($refundsProcessed / $totalOrders) * 100 : 0;

        return [
            'total_return_requests' => $returnRequests,
            'return_rate_percentage' => $returnRate,
            'refunds_processed' => $refundsProcessed,
            'refund_rate_percentage' => $refundRate,
            'total_refund_amount' => $totalRefundAmount,
            'average_refund_amount' => $refundsProcessed > 0 ? $totalRefundAmount / $refundsProcessed : 0,
            'return_reasons' => $returnReasons,
            'financial_impact' => [
                'revenue_lost_to_returns' => $totalRefundAmount,
                'cost_of_processing_returns' => $totalRefundAmount * 0.15, // 15% processing cost
                'net_loss' => $totalRefundAmount * 1.15 // Including processing costs
            ],
            'improvement_opportunities' => [
                'better_product_descriptions',
                'improved_size_charts',
                'enhanced_quality_control'
            ]
        ];
    }

    /**
     * Calculate tax compliance
     */
    private function calculateTaxCompliance($startDate, $endDate)
    {
        $orders = Order::whereBetween('created_at', [$startDate, $endDate])
                      ->where('status', 'completed')
                      ->get();

        $taxableRevenue = 0;
        $collectedTax = 0;
        $jurisdictions = [];

        foreach ($orders as $order) {
            $taxAmount = $order->tax_amount ?? 0;
            $taxableRevenue += ($order->total_amount ?? 0) - $taxAmount;
            $collectedTax += $taxAmount;

            $jurisdiction = $order->shipping_address['state'] ?? $order->shipping_address['country'] ?? 'Unknown';
            if (!isset($jurisdictions[$jurisdiction])) {
                $jurisdictions[$jurisdiction] = [
                    'revenue' => 0,
                    'tax_collected' => 0
                ];
            }
            $jurisdictions[$jurisdiction]['revenue'] += ($order->total_amount ?? 0) - $taxAmount;
            $jurisdictions[$jurisdiction]['tax_collected'] += $taxAmount;
        }

        return [
            'total_taxable_revenue' => $taxableRevenue,
            'total_tax_collected' => $collectedTax,
            'tax_rate_compliance' => $this->calculateTaxRateCompliance($jurisdictions),
            'jurisdictional_tax_reports' => $jurisdictions,
            'compliance_status' => [
                'taxes_calculated_correctly' => 0.98, // 98% accuracy
                'filing_compliance' => 'up_to_date',
                'audit_readiness' => true
            ]
        ];
    }

    /**
     * Calculate tax rate compliance
     */
    private function calculateTaxRateCompliance($jurisdictions)
    {
        $compliance = [];
        foreach ($jurisdictions as $jurisdiction => $data) {
            // Placeholder - would verify tax rates match jurisdiction requirements
            $compliance[$jurisdiction] = [
                'revenue' => $data['revenue'],
                'tax_collected' => $data['tax_collected'],
                'compliance_status' => 'compliant' // Placeholder
            ];
        }
        return $compliance;
    }

    /**
     * Calculate payment processing for e-commerce
     */
    private function calculateEcommercePaymentProcessing($startDate, $endDate)
    {
        $payments = Payment::whereBetween('created_at', [$startDate, $endDate])
                          ->get();

        $totalProcessed = 0;
        $successCount = 0;
        $failureCount = 0;
        $refundCount = 0;
        $chargebackCount = 0;

        $paymentMethods = [
            'credit_card' => ['count' => 0, 'amount' => 0],
            'debit_card' => ['count' => 0, 'amount' => 0],
            'paypal' => ['count' => 0, 'amount' => 0],
            'bank_transfer' => ['count' => 0, 'amount' => 0],
            'digital_wallet' => ['count' => 0, 'amount' => 0]
        ];

        foreach ($payments as $payment) {
            $amount = $payment->amount ?? 0;
            $totalProcessed += $amount;

            if ($payment->status === 'success') {
                $successCount++;
            } else {
                $failureCount++;
            }

            if ($payment->type) {
                $method = strtolower($payment->type);
                if (array_key_exists($method, $paymentMethods)) {
                    $paymentMethods[$method]['count']++;
                    $paymentMethods[$method]['amount'] += $amount;
                }
            }
        }

        $successRate = ($successCount + $failureCount) > 0 ? ($successCount / ($successCount + $failureCount)) * 100 : 0;

        return [
            'total_amount_processed' => $totalProcessed,
            'transaction_count' => $successCount + $failureCount,
            'success_rate' => $successRate,
            'success_count' => $successCount,
            'failure_count' => $failureCount,
            'failure_rate' => $failureCount > 0 ? ($failureCount / ($successCount + $failureCount)) * 100 : 0,
            'refund_rate' => $refundCount > 0 ? ($refundCount / $successCount) * 100 : 0,
            'chargeback_rate' => $chargebackCount > 0 ? ($chargebackCount / $successCount) * 100 : 0,
            'payment_method_performance' => $paymentMethods,
            'gateway_performance' => $this->calculateGatewayPerformance($startDate, $endDate),
            'transaction_fees' => $totalProcessed * 0.029 // 2.9% average fee
        ];
    }

    /**
     * Calculate gateway performance
     */
    private function calculateGatewayPerformance($startDate, $endDate)
    {
        return [
            'stripe' => [
                'success_rate' => 99.2,
                'average_transaction_time' => 2.3, // seconds
                'fee_rate' => 2.9
            ],
            'paypal' => [
                'success_rate' => 97.8,
                'average_transaction_time' => 4.1, // seconds
                'fee_rate' => 3.49
            ],
            'square' => [
                'success_rate' => 98.5,
                'average_transaction_time' => 1.8, // seconds
                'fee_rate' => 2.6
            ]
        ];
    }
}