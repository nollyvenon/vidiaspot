<?php

namespace App\Services;

use App\Models\FoodVendor;
use App\Models\FoodOrder;
use App\Models\FoodMenuItem;
use App\Models\DeliveryOrder;
use Carbon\Carbon;

class FoodReportingService
{
    /**
     * Generate Sales & Revenue Report
     */
    public function generateSalesRevenueReport($startDate, $endDate, $vendorId = null)
    {
        $query = FoodOrder::whereBetween('created_at', [$startDate, $endDate])
                         ->where('status', 'delivered');

        if ($vendorId) {
            $query = $query->where('food_vendor_id', $vendorId);
        }

        $orders = $query->get();

        $dailySales = [];
        $totalRevenue = 0;
        $totalOrders = count($orders);

        foreach ($orders as $order) {
            $date = $order->created_at->format('Y-m-d');
            if (!isset($dailySales[$date])) {
                $dailySales[$date] = 0;
            }
            $dailySales[$date] += $order->total_amount;
            $totalRevenue += $order->total_amount;
        }

        // Calculate menu performance
        $menuPerformance = $this->calculateMenuPerformance($startDate, $endDate, $vendorId);

        // Calculate delivery performance
        $deliveryPerformance = $this->calculateDeliveryPerformance($startDate, $endDate, $vendorId);

        // Calculate customer spending patterns
        $customerPatterns = $this->calculateCustomerSpendingPatterns($startDate, $endDate, $vendorId);

        // Calculate revenue by payment method
        $paymentMethods = $this->calculateRevenueByPaymentMethod($startDate, $endDate, $vendorId);

        return [
            'daily_sales_summary' => [
                'revenue_by_hour' => $this->calculateRevenueByHour($startDate, $endDate, $vendorId),
                'revenue_by_day' => $dailySales,
                'revenue_by_location' => $this->calculateRevenueByLocation($startDate, $endDate, $vendorId),
                'revenue_by_vendor' => $this->calculateRevenueByVendor($startDate, $endDate, $vendorId),
                'total_revenue' => $totalRevenue,
                'total_orders' => $totalOrders,
                'average_order_value' => $totalOrders > 0 ? $totalRevenue / $totalOrders : 0
            ],
            'menu_performance' => $menuPerformance,
            'delivery_performance' => $deliveryPerformance,
            'customer_spending_patterns' => $customerPatterns,
            'revenue_by_payment_method' => $paymentMethods,
            'period_start' => $startDate,
            'period_end' => $endDate
        ];
    }

    /**
     * Calculate revenue by hour
     */
    private function calculateRevenueByHour($startDate, $endDate, $vendorId = null)
    {
        $query = FoodOrder::whereBetween('created_at', [$startDate, $endDate])
                         ->where('status', 'delivered');

        if ($vendorId) {
            $query = $query->where('food_vendor_id', $vendorId);
        }

        $orders = $query->get();

        $hourlyRevenue = array_fill(0, 24, 0);
        foreach ($orders as $order) {
            $hour = (int)$order->created_at->format('H');
            $hourlyRevenue[$hour] += $order->total_amount;
        }

        return $hourlyRevenue;
    }

    /**
     * Calculate revenue by location
     */
    private function calculateRevenueByLocation($startDate, $endDate, $vendorId = null)
    {
        $query = FoodOrder::whereBetween('created_at', [$startDate, $endDate])
                         ->where('status', 'delivered');

        if ($vendorId) {
            $query = $query->where('food_vendor_id', $vendorId);
        }

        $orders = $query->get();

        $locationRevenue = [];
        foreach ($orders as $order) {
            $location = $order->delivery_address['city'] ?? 'Unknown';
            if (!isset($locationRevenue[$location])) {
                $locationRevenue[$location] = 0;
            }
            $locationRevenue[$location] += $order->total_amount;
        }

        return $locationRevenue;
    }

    /**
     * Calculate revenue by vendor
     */
    private function calculateRevenueByVendor($startDate, $endDate, $vendorId = null)
    {
        if ($vendorId) {
            $vendor = FoodVendor::find($vendorId);
            return [
                $vendor->name => FoodOrder::where('food_vendor_id', $vendorId)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->where('status', 'delivered')
                    ->sum('total_amount')
            ];
        }

        $vendors = FoodOrder::whereBetween('created_at', [$startDate, $endDate])
                           ->where('status', 'delivered')
                           ->select('food_vendor_id')
                           ->distinct()
                           ->get();

        $vendorRevenue = [];
        foreach ($vendors as $order) {
            $vendor = FoodVendor::find($order->food_vendor_id);
            $vendorRevenue[$vendor->name] = FoodOrder::where('food_vendor_id', $order->food_vendor_id)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 'delivered')
                ->sum('total_amount');
        }

        return $vendorRevenue;
    }

    /**
     * Calculate menu performance
     */
    private function calculateMenuPerformance($startDate, $endDate, $vendorId = null)
    {
        $orders = FoodOrder::whereBetween('created_at', [$startDate, $endDate])
                          ->where('status', 'delivered');

        if ($vendorId) {
            $orders = $orders->where('food_vendor_id', $vendorId);
        }

        $orders = $orders->with('orderItems')->get();

        $itemSales = [];
        foreach ($orders as $order) {
            foreach ($order->orderItems as $item) {
                $itemName = $item->name;
                if (!isset($itemSales[$itemName])) {
                    $itemSales[$itemName] = [
                        'quantity' => 0,
                        'revenue' => 0,
                        'orders_count' => 0
                    ];
                }
                $itemSales[$itemName]['quantity'] += $item->quantity;
                $itemSales[$itemName]['revenue'] += $item->total_price;
                $itemSales[$itemName]['orders_count']++;
            }
        }

        // Sort by revenue to find best/worst selling items
        uasort($itemSales, function($a, $b) {
            return $b['revenue'] <=> $a['revenue'];
        });

        return [
            'best_selling_items' => array_slice($itemSales, 0, 10, true),
            'worst_selling_items' => array_slice($itemSales, -10, 10, true),
            'seasonal_trends' => $this->calculateSeasonalTrends($startDate, $endDate, $vendorId),
            'item_performance' => $itemSales
        ];
    }

    /**
     * Calculate seasonal trends
     */
    private function calculateSeasonalTrends($startDate, $endDate, $vendorId = null)
    {
        $orders = FoodOrder::whereBetween('created_at', [$startDate, $endDate])
                          ->where('status', 'delivered');

        if ($vendorId) {
            $orders = $orders->where('food_vendor_id', $vendorId);
        }

        $orders = $orders->get();

        $monthlyTrends = [];
        foreach ($orders as $order) {
            $month = $order->created_at->format('Y-m');
            if (!isset($monthlyTrends[$month])) {
                $monthlyTrends[$month] = [
                    'revenue' => 0,
                    'orders' => 0
                ];
            }
            $monthlyTrends[$month]['revenue'] += $order->total_amount;
            $monthlyTrends[$month]['orders']++;
        }

        return $monthlyTrends;
    }

    /**
     * Calculate delivery performance
     */
    private function calculateDeliveryPerformance($startDate, $endDate, $vendorId = null)
    {
        $deliveryOrders = DeliveryOrder::whereBetween('created_at', [$startDate, $endDate])
                                      ->where('delivery_type', 'food_order');

        if ($vendorId) {
            // Need to link delivery orders to food vendors somehow
            // For now, we'll consider all delivery orders in the period
        }

        $deliveryOrders = $deliveryOrders->get();

        $totalDeliveries = count($deliveryOrders);
        $successfulDeliveries = 0;
        $failedDeliveries = 0;
        $totalDeliveryTime = 0;

        foreach ($deliveryOrders as $deliveryOrder) {
            if ($deliveryOrder->delivery_status === 'delivered') {
                $successfulDeliveries++;
                if ($deliveryOrder->estimated_delivery_time && $deliveryOrder->actual_delivery_time) {
                    $totalDeliveryTime += $deliveryOrder->actual_delivery_time->diffInMinutes($deliveryOrder->estimated_delivery_time);
                }
            } else {
                $failedDeliveries++;
            }
        }

        return [
            'total_deliveries' => $totalDeliveries,
            'successful_deliveries' => $successfulDeliveries,
            'failed_deliveries' => $failedDeliveries,
            'success_rate' => $totalDeliveries > 0 ? ($successfulDeliveries / $totalDeliveries) * 100 : 0,
            'average_delivery_time' => $successfulDeliveries > 0 ? $totalDeliveryTime / $successfulDeliveries : 0,
            'failure_analysis' => $this->calculateDeliveryFailureAnalysis($startDate, $endDate)
        ];
    }

    /**
     * Calculate delivery failure analysis
     */
    private function calculateDeliveryFailureAnalysis($startDate, $endDate)
    {
        $failedDeliveries = DeliveryOrder::whereBetween('created_at', [$startDate, $endDate])
                                        ->whereIn('delivery_status', ['failed', 'returned'])
                                        ->get();

        $failureReasons = [
            'no_answer' => 0,
            'address_not_found' => 0,
            'customer_refused' => 0,
            'other' => 0
        ];

        foreach ($failedDeliveries as $delivery) {
            if (strpos(strtolower($delivery->delivery_notes), 'no answer') !== false) {
                $failureReasons['no_answer']++;
            } elseif (strpos(strtolower($delivery->delivery_notes), 'address') !== false) {
                $failureReasons['address_not_found']++;
            } elseif (strpos(strtolower($delivery->delivery_notes), 'refused') !== false) {
                $failureReasons['customer_refused']++;
            } else {
                $failureReasons['other']++;
            }
        }

        return $failureReasons;
    }

    /**
     * Calculate customer spending patterns
     */
    private function calculateCustomerSpendingPatterns($startDate, $endDate, $vendorId = null)
    {
        $orders = FoodOrder::whereBetween('created_at', [$startDate, $endDate])
                          ->where('status', 'delivered');

        if ($vendorId) {
            $orders = $orders->where('food_vendor_id', $vendorId);
        }

        $orders = $orders->get();

        $customerData = [];
        $totalOrderValue = 0;
        $totalOrders = count($orders);

        foreach ($orders as $order) {
            $userId = $order->user_id;
            if (!isset($customerData[$userId])) {
                $customerData[$userId] = [
                    'total_spent' => 0,
                    'order_count' => 0,
                    'last_order' => $order->created_at
                ];
            }
            $customerData[$userId]['total_spent'] += $order->total_amount;
            $customerData[$userId]['order_count']++;
            if ($order->created_at > $customerData[$userId]['last_order']) {
                $customerData[$userId]['last_order'] = $order->created_at;
            }
            $totalOrderValue += $order->total_amount;
        }

        $avgOrderValue = $totalOrders > 0 ? $totalOrderValue / $totalOrders : 0;
        $avgCustomerFrequency = 0;
        if (count($customerData) > 0) {
            $totalFrequencies = 0;
            foreach ($customerData as $data) {
                $totalFrequencies += $data['order_count'];
            }
            $avgCustomerFrequency = $totalFrequencies / count($customerData);
        }

        return [
            'average_order_value' => $avgOrderValue,
            'average_frequency' => $avgCustomerFrequency,
            'loyalty_patterns' => $this->calculateLoyaltyPatterns($customerData),
            'customer_segments' => $this->segmentCustomers($customerData)
        ];
    }

    /**
     * Calculate loyalty patterns
     */
    private function calculateLoyaltyPatterns($customerData)
    {
        $highValue = 0;
        $frequent = 0;
        $occasional = 0;
        $inactive = 0;

        foreach ($customerData as $data) {
            if ($data['total_spent'] > 500) {
                $highValue++;
            }
            if ($data['order_count'] > 10) {
                $frequent++;
            } elseif ($data['order_count'] > 2) {
                $occasional++;
            } else {
                $inactive++;
            }
        }

        return [
            'high_value_customers' => $highValue,
            'frequent_customers' => $frequent,
            'occasional_customers' => $occasional,
            'inactive_customers' => $inactive
        ];
    }

    /**
     * Segment customers
     */
    private function segmentCustomers($customerData)
    {
        $segments = [
            'vip' => [],
            'regular' => [],
            'new' => [],
            'at_risk' => []
        ];

        foreach ($customerData as $userId => $data) {
            if ($data['total_spent'] > 500 && $data['order_count'] > 5) {
                $segments['vip'][$userId] = $data;
            } elseif ($data['order_count'] > 2) {
                $segments['regular'][$userId] = $data;
            } elseif ($data['order_count'] === 1) {
                $segments['new'][$userId] = $data;
            } else {
                $segments['at_risk'][$userId] = $data;
            }
        }

        return $segments;
    }

    /**
     * Calculate revenue by payment method
     */
    private function calculateRevenueByPaymentMethod($startDate, $endDate, $vendorId = null)
    {
        $orders = FoodOrder::whereBetween('created_at', [$startDate, $endDate])
                          ->where('status', 'delivered');

        if ($vendorId) {
            $orders = $orders->where('food_vendor_id', $vendorId);
        }

        $orders = $orders->get();

        $paymentMethods = [
            'cash' => 0,
            'card' => 0,
            'digital_wallet' => 0,
            'cryptocurrency' => 0
        ];

        foreach ($orders as $order) {
            $method = $order->payment_method ?? 'other';
            $amount = $order->total_amount;
            
            switch (strtolower($method)) {
                case 'cash':
                case 'cod':
                    $paymentMethods['cash'] += $amount;
                    break;
                case 'card':
                case 'credit_card':
                case 'debit_card':
                    $paymentMethods['card'] += $amount;
                    break;
                case 'digital_wallet':
                case 'paypal':
                case 'paystack':
                case 'flutterwave':
                    $paymentMethods['digital_wallet'] += $amount;
                    break;
                case 'cryptocurrency':
                case 'crypto':
                    $paymentMethods['cryptocurrency'] += $amount;
                    break;
                default:
                    $paymentMethods['digital_wallet'] += $amount; // Default to digital wallet
                    break;
            }
        }

        return $paymentMethods;
    }

    /**
     * Generate Operational Efficiency Report
     */
    public function generateOperationalEfficiencyReport($startDate, $endDate, $vendorId = null)
    {
        return [
            'kitchen_performance' => $this->calculateKitchenPerformance($startDate, $endDate, $vendorId),
            'driver_performance' => $this->calculateDriverPerformance($startDate, $endDate),
            'inventory_management' => $this->calculateInventoryManagement($startDate, $endDate, $vendorId),
            'location_analytics' => $this->calculateLocationAnalytics($startDate, $endDate),
            'equipment_utilization' => $this->calculateEquipmentUtilization($startDate, $endDate),
            'period_start' => $startDate,
            'period_end' => $endDate
        ];
    }

    /**
     * Calculate kitchen performance
     */
    private function calculateKitchenPerformance($startDate, $endDate, $vendorId = null)
    {
        $orders = FoodOrder::whereBetween('created_at', [$startDate, $endDate])
                          ->where('status', 'delivered');

        if ($vendorId) {
            $orders = $orders->where('food_vendor_id', $vendorId);
        }

        $orders = $orders->get();

        $totalPrepTime = 0;
        $orderCount = count($orders);
        $capacityUtilization = 0; // This would require additional data about kitchen capacity

        foreach ($orders as $order) {
            // Calculate prep time if available (would need to store when order was ready)
            if ($order->prepared_at && $order->created_at) {
                $totalPrepTime += $order->prepared_at->diffInMinutes($order->created_at);
            }
        }

        return [
            'average_preparation_time' => $orderCount > 0 ? $totalPrepTime / $orderCount : 0,
            'orders_processed' => $orderCount,
            'kitchen_capacity_utilization' => $capacityUtilization,
            'peak_hours_efficiency' => $this->calculatePeakHoursEfficiency($startDate, $endDate, $vendorId)
        ];
    }

    /**
     * Calculate peak hours efficiency
     */
    private function calculatePeakHoursEfficiency($startDate, $endDate, $vendorId = null)
    {
        $peakHours = [11, 12, 13, 17, 18, 19]; // Typical lunch and dinner hours
        $orders = FoodOrder::whereBetween('created_at', [$startDate, $endDate])
                          ->where('status', 'delivered');

        if ($vendorId) {
            $orders = $orders->where('food_vendor_id', $vendorId);
        }

        $orders = $orders->get();

        $peakOrders = 0;
        $offPeakOrders = 0;
        $peakPrepTime = 0;
        $offPeakPrepTime = 0;
        $peakOrderCount = 0;
        $offPeakOrderCount = 0;

        foreach ($orders as $order) {
            $hour = (int)$order->created_at->format('H');
            if (in_array($hour, $peakHours)) {
                $peakOrders++;
                if ($order->prepared_at) {
                    $peakPrepTime += $order->prepared_at->diffInMinutes($order->created_at);
                }
                $peakOrderCount++;
            } else {
                $offPeakOrders++;
                if ($order->prepared_at) {
                    $offPeakPrepTime += $order->prepared_at->diffInMinutes($order->created_at);
                }
                $offPeakOrderCount++;
            }
        }

        return [
            'peak_orders' => $peakOrders,
            'off_peak_orders' => $offPeakOrders,
            'avg_prep_time_peak' => $peakOrderCount > 0 ? $peakPrepTime / $peakOrderCount : 0,
            'avg_prep_time_off_peak' => $offPeakOrderCount > 0 ? $offPeakPrepTime / $offPeakOrderCount : 0
        ];
    }

    /**
     * Calculate driver performance
     */
    private function calculateDriverPerformance($startDate, $endDate)
    {
        $deliveryOrders = DeliveryOrder::whereBetween('created_at', [$startDate, $endDate])
                                      ->where('delivery_type', 'food_order')
                                      ->with('courierPartner')
                                      ->get();

        $driverPerformance = [];

        foreach ($deliveryOrders as $deliveryOrder) {
            $driverId = $deliveryOrder->courier_partner_id;
            if (!isset($driverPerformance[$driverId])) {
                $driverPerformance[$driverId] = [
                    'total_deliveries' => 0,
                    'successful_deliveries' => 0,
                    'failed_deliveries' => 0,
                    'total_earnings' => 0,
                    'total_ratings' => 0,
                    'rating_count' => 0,
                    'driver_name' => $deliveryOrder->courierPartner->name ?? 'Unknown'
                ];
            }

            $driverPerformance[$driverId]['total_deliveries']++;
            if ($deliveryOrder->delivery_status === 'delivered') {
                $driverPerformance[$driverId]['successful_deliveries']++;
            } else {
                $driverPerformance[$driverId]['failed_deliveries']++;
            }

            $driverPerformance[$driverId]['total_earnings'] += $deliveryOrder->delivery_cost ?? 0;
            if ($deliveryOrder->delivery_partner_rating) {
                $driverPerformance[$driverId]['total_ratings'] += $deliveryOrder->delivery_partner_rating;
                $driverPerformance[$driverId]['rating_count']++;
            }
        }

        // Calculate average ratings
        foreach ($driverPerformance as &$driver) {
            $driver['average_rating'] = $driver['rating_count'] > 0 ? 
                $driver['total_ratings'] / $driver['rating_count'] : 0;
        }

        return $driverPerformance;
    }

    /**
     * Calculate inventory management
     */
    private function calculateInventoryManagement($startDate, $endDate, $vendorId = null)
    {
        // This would require inventory tracking that isn't in the current models
        // For now, we'll return placeholder data based on order data
        $orders = FoodOrder::whereBetween('created_at', [$startDate, $endDate])
                          ->where('status', 'delivered');

        if ($vendorId) {
            $orders = $orders->where('food_vendor_id', $vendorId);
        }

        $orders = $orders->with('orderItems')->get();

        $itemQuantities = [];
        foreach ($orders as $order) {
            foreach ($order->orderItems as $item) {
                if (!isset($itemQuantities[$item->name])) {
                    $itemQuantities[$item->name] = 0;
                }
                $itemQuantities[$item->name] += $item->quantity;
            }
        }

        return [
            'stock_levels' => $itemQuantities, // This would be actual stock - sold items
            'reorder_alerts' => [], // Would be based on actual stock levels
            'waste_reports' => $this->calculateWasteReports($startDate, $endDate, $vendorId),
            'item_turnover_rates' => $this->calculateItemTurnoverRates($startDate, $endDate, $vendorId)
        ];
    }

    /**
     * Calculate waste reports
     */
    private function calculateWasteReports($startDate, $endDate, $vendorId = null)
    {
        // Placeholder implementation - would need actual waste tracking
        return [
            'total_waste' => 0,
            'waste_by_item' => [],
            'waste_percentage' => 0
        ];
    }

    /**
     * Calculate item turnover rates
     */
    private function calculateItemTurnoverRates($startDate, $endDate, $vendorId = null)
    {
        // Placeholder implementation
        return [
            'high_turnover_items' => [],
            'slow_moving_items' => [],
            'turnover_rate_by_category' => []
        ];
    }

    /**
     * Calculate location analytics
     */
    private function calculateLocationAnalytics($startDate, $endDate)
    {
        $orders = FoodOrder::whereBetween('created_at', [$startDate, $endDate])
                          ->where('status', 'delivered')
                          ->get();

        $locationAnalytics = [];
        foreach ($orders as $order) {
            $location = $order->delivery_address['city'] ?? $order->delivery_address['state'] ?? 'Unknown';
            if (!isset($locationAnalytics[$location])) {
                $locationAnalytics[$location] = [
                    'revenue' => 0,
                    'orders' => 0,
                    'avg_order_value' => 0
                ];
            }
            $locationAnalytics[$location]['revenue'] += $order->total_amount;
            $locationAnalytics[$location]['orders']++;
        }

        foreach ($locationAnalytics as &$location) {
            $location['avg_order_value'] = $location['orders'] > 0 ? 
                $location['revenue'] / $location['orders'] : 0;
        }

        // Sort by revenue to find highest performing locations
        uasort($locationAnalytics, function($a, $b) {
            return $b['revenue'] <=> $a['revenue'];
        });

        return $locationAnalytics;
    }

    /**
     * Calculate equipment utilization
     */
    private function calculateEquipmentUtilization($startDate, $endDate)
    {
        // This would require specific equipment tracking data
        // For now, returning placeholder data
        return [
            'vending_machine_usage' => [],
            'kitchen_equipment_utilization' => [],
            'maintenance_schedule_adherence' => []
        ];
    }

    /**
     * Generate Customer Experience Report
     */
    public function generateCustomerExperienceReport($startDate, $endDate, $vendorId = null)
    {
        return [
            'order_fulfillment' => $this->calculateOrderFulfillment($startDate, $endDate, $vendorId),
            'delivery_metrics' => $this->calculateDeliveryMetrics($startDate, $endDate),
            'customer_feedback' => $this->calculateCustomerFeedback($startDate, $endDate, $vendorId),
            'user_engagement' => $this->calculateUserEngagement($startDate, $endDate),
            'loyalty_program' => $this->calculateLoyaltyProgram($startDate, $endDate),
            'period_start' => $startDate,
            'period_end' => $endDate
        ];
    }

    /**
     * Calculate order fulfillment
     */
    private function calculateOrderFulfillment($startDate, $endDate, $vendorId = null)
    {
        $orders = FoodOrder::whereBetween('created_at', [$startDate, $endDate]);

        if ($vendorId) {
            $orders = $orders->where('food_vendor_id', $vendorId);
        }

        $orders = $orders->get();

        $totalOrders = count($orders);
        $fulfilledOrders = 0;
        $accuracyIssues = 0;
        $complaints = 0;

        foreach ($orders as $order) {
            if ($order->status === 'delivered') {
                $fulfilledOrders++;
            }
            if ($order->accuracy_issues) {
                $accuracyIssues++;
            }
            if ($order->complaints) {
                $complaints++;
            }
        }

        return [
            'total_orders' => $totalOrders,
            'fulfilled_orders' => $fulfilledOrders,
            'fulfillment_rate' => $totalOrders > 0 ? ($fulfilledOrders / $totalOrders) * 100 : 0,
            'accuracy_rate' => $totalOrders > 0 ? (($totalOrders - $accuracyIssues) / $totalOrders) * 100 : 0,
            'complaint_analysis' => $this->calculateComplaintAnalysis($startDate, $endDate, $vendorId),
            'satisfaction_scores' => $this->calculateSatisfactionScores($startDate, $endDate, $vendorId)
        ];
    }

    /**
     * Calculate complaint analysis
     */
    private function calculateComplaintAnalysis($startDate, $endDate, $vendorId = null)
    {
        $orders = FoodOrder::whereBetween('created_at', [$startDate, $endDate])
                          ->where('complaints', '!=', null);

        if ($vendorId) {
            $orders = $orders->where('food_vendor_id', $vendorId);
        }

        $orders = $orders->get();

        $complaintTypes = [
            'food_quality' => 0,
            'delivery_time' => 0,
            'order_accuracy' => 0,
            'customer_service' => 0,
            'other' => 0
        ];

        foreach ($orders as $order) {
            $complaint = strtolower($order->complaints);
            if (strpos($complaint, 'quality') !== false || strpos($complaint, 'taste') !== false) {
                $complaintTypes['food_quality']++;
            } elseif (strpos($complaint, 'time') !== false || strpos($complaint, 'late') !== false) {
                $complaintTypes['delivery_time']++;
            } elseif (strpos($complaint, 'wrong') !== false || strpos($complaint, 'accuracy') !== false) {
                $complaintTypes['order_accuracy']++;
            } elseif (strpos($complaint, 'service') !== false) {
                $complaintTypes['customer_service']++;
            } else {
                $complaintTypes['other']++;
            }
        }

        return $complaintTypes;
    }

    /**
     * Calculate satisfaction scores
     */
    private function calculateSatisfactionScores($startDate, $endDate, $vendorId = null)
    {
        $orders = FoodOrder::whereBetween('created_at', [$startDate, $endDate])
                          ->where('order_rating', '>', 0);

        if ($vendorId) {
            $orders = $orders->where('food_vendor_id', $vendorId);
        }

        $orders = $orders->get();

        $totalRating = 0;
        $ratingCount = 0;
        $fiveStar = 0;
        $fourStar = 0;
        $threeStar = 0;
        $twoStar = 0;
        $oneStar = 0;

        foreach ($orders as $order) {
            $totalRating += $order->order_rating;
            $ratingCount++;
            switch ($order->order_rating) {
                case 5: $fiveStar++; break;
                case 4: $fourStar++; break;
                case 3: $threeStar++; break;
                case 2: $twoStar++; break;
                case 1: $oneStar++; break;
            }
        }

        $averageRating = $ratingCount > 0 ? $totalRating / $ratingCount : 0;

        return [
            'average_rating' => $averageRating,
            'total_reviews' => $ratingCount,
            'rating_distribution' => [
                '5_star' => $fiveStar,
                '4_star' => $fourStar,
                '3_star' => $threeStar,
                '2_star' => $twoStar,
                '1_star' => $oneStar
            ],
            'satisfaction_rate' => $ratingCount > 0 ? ($fiveStar + $fourStar) / $ratingCount * 100 : 0
        ];
    }

    /**
     * Calculate delivery metrics
     */
    private function calculateDeliveryMetrics($startDate, $endDate)
    {
        $deliveryOrders = DeliveryOrder::whereBetween('created_at', [$startDate, $endDate])
                                      ->where('delivery_type', 'food_order')
                                      ->get();

        $totalDeliveries = count($deliveryOrders);
        $onTimeDeliveries = 0;
        $averageDeliveryTime = 0;

        foreach ($deliveryOrders as $delivery) {
            if ($delivery->estimated_delivery_time && $delivery->actual_delivery_time) {
                if ($delivery->actual_delivery_time->lte($delivery->estimated_delivery_time)) {
                    $onTimeDeliveries++;
                }
                
                $avgTime = $delivery->created_at->diffInMinutes($delivery->actual_delivery_time);
                $averageDeliveryTime += $avgTime;
            }
        }

        $averageDeliveryTime = $totalDeliveries > 0 ? $averageDeliveryTime / $totalDeliveries : 0;

        return [
            'total_deliveries' => $totalDeliveries,
            'on_time_deliveries' => $onTimeDeliveries,
            'on_time_rate' => $totalDeliveries > 0 ? ($onTimeDeliveries / $totalDeliveries) * 100 : 0,
            'average_delivery_time' => $averageDeliveryTime,
            'gps_tracking_analytics' => $this->calculateGpsTrackingAnalytics($startDate, $endDate)
        ];
    }

    /**
     * Calculate GPS tracking analytics
     */
    private function calculateGpsTrackingAnalytics($startDate, $endDate)
    {
        // Placeholder - would require actual GPS data
        return [
            'tracking_success_rate' => 0,
            'average_route_efficiency' => 0,
            'customer_tracking_engagement' => 0
        ];
    }

    /**
     * Calculate customer feedback
     */
    private function calculateCustomerFeedback($startDate, $endDate, $vendorId = null)
    {
        $orders = FoodOrder::whereBetween('created_at', [$startDate, $endDate])
                          ->where('order_feedback', '!=', null);

        if ($vendorId) {
            $orders = $orders->where('food_vendor_id', $vendorId);
        }

        $orders = $orders->get();

        $feedbackAnalysis = [
            'positive_feedback' => 0,
            'neutral_feedback' => 0,
            'negative_feedback' => 0,
            'common_themes' => [],
            'improvement_areas' => []
        ];

        foreach ($orders as $order) {
            if ($order->order_feedback) {
                $feedback = strtolower($order->order_feedback);
                
                if (strpos($feedback, 'good') !== false || strpos($feedback, 'great') !== false || 
                    strpos($feedback, 'excellent') !== false || strpos($feedback, 'love') !== false) {
                    $feedbackAnalysis['positive_feedback']++;
                } elseif (strpos($feedback, 'bad') !== false || strpos($feedback, 'terrible') !== false || 
                         strpos($feedback, 'hate') !== false || strpos($feedback, 'worst') !== false) {
                    $feedbackAnalysis['negative_feedback']++;
                } else {
                    $feedbackAnalysis['neutral_feedback']++;
                }
                
                // Extract common themes
                if (strpos($feedback, 'food') !== false || strpos($feedback, 'taste') !== false) {
                    $feedbackAnalysis['common_themes']['food_quality'] = 
                        ($feedbackAnalysis['common_themes']['food_quality'] ?? 0) + 1;
                }
                if (strpos($feedback, 'delivery') !== false || strpos($feedback, 'time') !== false) {
                    $feedbackAnalysis['common_themes']['delivery'] = 
                        ($feedbackAnalysis['common_themes']['delivery'] ?? 0) + 1;
                }
                if (strpos($feedback, 'service') !== false) {
                    $feedbackAnalysis['common_themes']['service'] = 
                        ($feedbackAnalysis['common_themes']['service'] ?? 0) + 1;
                }
            }
        }

        return $feedbackAnalysis;
    }

    /**
     * Calculate user engagement
     */
    private function calculateUserEngagement($startDate, $endDate)
    {
        $orders = FoodOrder::whereBetween('created_at', [$startDate, $endDate])
                          ->select('user_id')
                          ->distinct()
                          ->get();

        $totalUsers = count($orders);
        
        // Calculate retention metrics
        $returningUsers = 0;
        // This would require more complex analysis of user behavior over time
        
        return [
            'total_unique_users' => $totalUsers,
            'returning_users' => $returningUsers,
            'engagement_metrics' => [
                'avg_orders_per_user' => 0, // Would require user-specific order counts
                'session_duration' => 0, // Would require app usage data
                'feature_adoption' => 0, // Would require feature usage tracking
            ],
            'churn_analysis' => $this->calculateChurnAnalysis($startDate, $endDate)
        ];
    }

    /**
     * Calculate churn analysis
     */
    private function calculateChurnAnalysis($startDate, $endDate)
    {
        // Placeholder for churn analysis
        return [
            'churn_rate' => 0,
            'churn_reasons' => [],
            'retention_rate' => 0
        ];
    }

    /**
     * Calculate loyalty program performance
     */
    private function calculateLoyaltyProgram($startDate, $endDate)
    {
        // Placeholder for loyalty program analytics
        return [
            'active_members' => 0,
            'points_issued' => 0,
            'points_redeemed' => 0,
            'redemption_rate' => 0,
            'customer_retention_impact' => 0,
            'program_roi' => 0
        ];
    }

    /**
     * Generate Financial Reports for Food Operations
     */
    public function generateFoodFinancialReport($startDate, $endDate, $vendorId = null)
    {
        return [
            'cost_of_goods_sold' => $this->calculateFoodCostOfGoodsSold($startDate, $endDate, $vendorId),
            'profit_margins' => $this->calculateFoodProfitMargins($startDate, $endDate, $vendorId),
            'commission_reports' => $this->calculateFoodCommissionReports($startDate, $endDate),
            'waste_and_loss' => $this->calculateFoodWasteAndLoss($startDate, $endDate, $vendorId),
            'break_even_analysis' => $this->calculateFoodBreakEvenAnalysis($startDate, $endDate, $vendorId),
            'period_start' => $startDate,
            'period_end' => $endDate
        ];
    }

    /**
     * Calculate cost of goods sold for food
     */
    private function calculateFoodCostOfGoodsSold($startDate, $endDate, $vendorId = null)
    {
        // This would require actual food cost data which isn't in the current models
        // For now, using placeholder values based on revenue
        $revenue = FoodOrder::whereBetween('created_at', [$startDate, $endDate])
                           ->where('status', 'delivered');

        if ($vendorId) {
            $revenue = $revenue->where('food_vendor_id', $vendorId);
        }

        $totalRevenue = $revenue->sum('total_amount');
        
        // Assume 30% is food costs, 10% packaging, 15% delivery
        return [
            'food_costs' => $totalRevenue * 0.30,
            'packaging_costs' => $totalRevenue * 0.10,
            'delivery_costs' => $totalRevenue * 0.15,
            'total_cogs' => $totalRevenue * 0.55,
            'cogs_percentage' => 55
        ];
    }

    /**
     * Calculate profit margins for food
     */
    private function calculateFoodProfitMargins($startDate, $endDate, $vendorId = null)
    {
        $revenueQuery = FoodOrder::whereBetween('created_at', [$startDate, $endDate])
                                ->where('status', 'delivered');

        if ($vendorId) {
            $revenueQuery = $revenueQuery->where('food_vendor_id', $vendorId);
        }

        $totalRevenue = $revenueQuery->sum('total_amount');
        $cogsData = $this->calculateFoodCostOfGoodsSold($startDate, $endDate, $vendorId);
        $totalCogs = $cogsData['total_cogs'];

        $grossProfit = $totalRevenue - $totalCogs;
        $grossMargin = $totalRevenue > 0 ? ($grossProfit / $totalRevenue) * 100 : 0;

        return [
            'total_revenue' => $totalRevenue,
            'total_cogs' => $totalCogs,
            'gross_profit' => $grossProfit,
            'gross_margin_percentage' => $grossMargin,
            'by_item' => $this->calculateFoodProfitByItem($startDate, $endDate, $vendorId),
            'by_location' => $this->calculateFoodProfitByLocation($startDate, $endDate),
            'by_time_period' => $this->calculateFoodProfitByTime($startDate, $endDate)
        ];
    }

    /**
     * Calculate profit by item for food
     */
    private function calculateFoodProfitByItem($startDate, $endDate, $vendorId = null)
    {
        // Placeholder implementation
        return [
            'most_profitable' => [],
            'least_profitable' => [],
            'profit_by_category' => []
        ];
    }

    /**
     * Calculate profit by location for food
     */
    private function calculateFoodProfitByLocation($startDate, $endDate)
    {
        $orders = FoodOrder::whereBetween('created_at', [$startDate, $endDate])
                          ->where('status', 'delivered')
                          ->get();

        $locationProfit = [];
        foreach ($orders as $order) {
            $location = $order->delivery_address['city'] ?? $order->delivery_address['state'] ?? 'Unknown';
            if (!isset($locationProfit[$location])) {
                $locationProfit[$location] = 0;
            }
            $locationProfit[$location] += ($order->total_amount * 0.45); // Assuming 45% profit margin
        }

        return $locationProfit;
    }

    /**
     * Calculate profit by time for food
     */
    private function calculateFoodProfitByTime($startDate, $endDate)
    {
        $orders = FoodOrder::whereBetween('created_at', [$startDate, $endDate])
                          ->where('status', 'delivered')
                          ->get();

        $dailyProfit = [];
        foreach ($orders as $order) {
            $date = $order->created_at->format('Y-m-d');
            if (!isset($dailyProfit[$date])) {
                $dailyProfit[$date] = 0;
            }
            $dailyProfit[$date] += ($order->total_amount * 0.45);
        }

        return $dailyProfit;
    }

    /**
     * Calculate commission reports for food
     */
    private function calculateFoodCommissionReports($startDate, $endDate)
    {
        $deliveryOrders = DeliveryOrder::whereBetween('created_at', [$startDate, $endDate])
                                      ->where('delivery_type', 'food_order')
                                      ->get();

        $totalCommission = 0;
        $commissionByPartner = [];

        foreach ($deliveryOrders as $delivery) {
            $commission = $delivery->delivery_partner_commission ?? 0;
            $totalCommission += $commission;
            
            $partnerId = $delivery->courier_partner_id;
            if (!isset($commissionByPartner[$partnerId])) {
                $commissionByPartner[$partnerId] = 0;
            }
            $commissionByPartner[$partnerId] += $commission;
        }

        return [
            'total_commission_paid' => $totalCommission,
            'by_delivery_partners' => $commissionByPartner,
            'by_vendor' => [], // Would need to connect vendors to deliveries
            'by_geographic_region' => [] // Would require geographic data
        ];
    }

    /**
     * Calculate waste and loss for food
     */
    private function calculateFoodWasteAndLoss($startDate, $endDate, $vendorId = null)
    {
        // Placeholder implementation
        return [
            'food_waste' => 0,
            'equipment_damage' => 0,
            'theft_losses' => 0,
            'total_waste_loss' => 0,
            'waste_percentage' => 0
        ];
    }

    /**
     * Calculate break-even analysis for food
     */
    private function calculateFoodBreakEvenAnalysis($startDate, $endDate, $vendorId = null)
    {
        // Placeholder implementation
        return [
            'fixed_costs' => 0,
            'variable_costs' => 0,
            'revenue_needed_to_break_even' => 0,
            'current_margin_above_break_even' => 0,
            'by_location' => [],
            'by_vendor' => [],
            'by_item' => []
        ];
    }
}