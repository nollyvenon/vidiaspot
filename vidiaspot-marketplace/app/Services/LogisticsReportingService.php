<?php

namespace App\Services;

use App\Models\Reports\ShipmentReport;
use App\Models\Reports\DeliveryReport;
use App\Models\Reports\WarehouseReport;
use App\Models\Reports\CourierPerformanceReport;
use App\Models\Reports\ReturnReport;
use App\Models\Logistics\ShippingLabel;
use App\Models\Logistics\ReturnRequest;
use App\Models\Logistics\Warehouse;
use App\Models\Logistics\CourierPartner;
use Carbon\Carbon;

class LogisticsReportingService
{
    /**
     * Generate shipment analytics report
     */
    public function generateShipmentReport($userId, Carbon $startDate, Carbon $endDate)
    {
        $labels = ShippingLabel::where('user_id', $userId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $successfulDeliveries = 0;
        $failedDeliveries = 0;
        $totalCost = 0;

        foreach ($labels as $label) {
            if (isset($label->delivery_status)) {
                if ($label->delivery_status === 'delivered') {
                    $successfulDeliveries++;
                } else {
                    $failedDeliveries++;
                }
            }
            
            if (isset($label->cost)) {
                $totalCost += $label->cost;
            }
        }

        $averageDeliveryTime = $this->calculateAverageDeliveryTime($labels, $startDate, $endDate);

        $reportData = [
            'period' => [
                'start' => $startDate->format('Y-m-d'),
                'end' => $endDate->format('Y-m-d')
            ],
            'shipment_data' => $labels->toArray(),
            'delivery_breakdown' => [
                'successful' => $successfulDeliveries,
                'failed' => $failedDeliveries,
                'total' => $successfulDeliveries + $failedDeliveries
            ],
            'cost_analysis' => [
                'total_cost' => $totalCost,
                'average_cost_per_shipment' => $labels->count() > 0 ? $totalCost / $labels->count() : 0
            ]
        ];

        return ShipmentReport::create([
            'user_id' => $userId,
            'report_date' => now(),
            'report_data' => $reportData,
            'status' => 'completed',
            'total_shipments' => $labels->count(),
            'successful_deliveries' => $successfulDeliveries,
            'failed_deliveries' => $failedDeliveries,
            'average_delivery_time' => $averageDeliveryTime,
            'total_cost' => $totalCost,
            'currency' => 'USD'
        ]);
    }

    /**
     * Generate delivery performance report
     */
    public function generateDeliveryReport($userId, Carbon $startDate, Carbon $endDate)
    {
        $labels = ShippingLabel::where('user_id', $userId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $onTimeDeliveries = 0;
        $lateDeliveries = 0;
        $totalDeliveries = 0;
        $totalDistance = 0;

        foreach ($labels as $label) {
            if (isset($label->delivery_status) && $label->delivery_status === 'delivered') {
                $totalDeliveries++;
                
                if (isset($label->estimated_delivery_date) && isset($label->actual_delivery_date)) {
                    $estimated = Carbon::parse($label->estimated_delivery_date);
                    $actual = Carbon::parse($label->actual_delivery_date);
                    
                    if ($actual->lte($estimated)) {
                        $onTimeDeliveries++;
                    } else {
                        $lateDeliveries++;
                    }
                }
                
                if (isset($label->distance_km)) {
                    $totalDistance += $label->distance_km;
                }
            }
        }

        $successRate = $totalDeliveries > 0 ? ($onTimeDeliveries / $totalDeliveries) * 100 : 0;
        $avgDeliveryTime = $this->calculateAverageDeliveryTime($labels, $startDate, $endDate);

        $reportData = [
            'period' => [
                'start' => $startDate->format('Y-m-d'),
                'end' => $endDate->format('Y-m-d')
            ],
            'delivery_data' => $labels->toArray(),
            'performance_metrics' => [
                'on_time_rate' => $successRate,
                'total_deliveries' => $totalDeliveries,
                'on_time_deliveries' => $onTimeDeliveries,
                'late_deliveries' => $lateDeliveries
            ],
            'distance_analysis' => [
                'total_distance_km' => $totalDistance,
                'average_distance_per_delivery' => $totalDeliveries > 0 ? $totalDistance / $totalDeliveries : 0
            ]
        ];

        return DeliveryReport::create([
            'user_id' => $userId,
            'report_date' => now(),
            'report_data' => $reportData,
            'status' => 'completed',
            'on_time_deliveries' => $onTimeDeliveries,
            'late_deliveries' => $lateDeliveries,
            'delivery_success_rate' => $successRate,
            'average_delivery_time_hours' => $avgDeliveryTime,
            'total_deliveries' => $totalDeliveries,
            'total_distance_km' => $totalDistance,
            'fuel_cost' => $totalDistance * 0.15, // Assuming $0.15 per km for fuel
            'currency' => 'USD'
        ]);
    }

    /**
     * Generate warehouse operations report
     */
    public function generateWarehouseReport($userId, Carbon $startDate, Carbon $endDate)
    {
        $warehouses = Warehouse::where('user_id', $userId)->get();
        $totalInventoryItems = 0;
        $ordersProcessed = 0;
        $totalStorageCost = 0;

        foreach ($warehouses as $warehouse) {
            $totalInventoryItems += $warehouse->current_inventory_count ?? 0;
            $ordersProcessed += $warehouse->orders_processed_count ?? 0;
            $totalStorageCost += $warehouse->total_monthly_cost ?? 0;
        }

        $spaceUtilization = $warehouses->count() > 0 ? 
            ($totalInventoryItems / max(1, $warehouses->sum('capacity'))) * 100 : 0;
        
        $turnoverRate = $warehouses->count() > 0 ? 
            $warehouses->avg('inventory_turnover_rate') : 0;

        $fulfillmentAccuracy = 98.5; // Default accuracy rate

        $reportData = [
            'period' => [
                'start' => $startDate->format('Y-m-d'),
                'end' => $endDate->format('Y-m-d')
            ],
            'warehouse_data' => $warehouses->toArray(),
            'inventory_metrics' => [
                'total_items' => $totalInventoryItems,
                'turnover_rate' => $turnoverRate,
                'utilization_percent' => $spaceUtilization
            ],
            'operational_metrics' => [
                'orders_processed' => $ordersProcessed,
                'fulfillment_accuracy' => $fulfillmentAccuracy,
                'storage_cost' => $totalStorageCost
            ]
        ];

        return WarehouseReport::create([
            'user_id' => $userId,
            'report_date' => now(),
            'report_data' => $reportData,
            'status' => 'completed',
            'total_inventory_items' => $totalInventoryItems,
            'inventory_turnover_rate' => $turnoverRate,
            'space_utilization_percent' => $spaceUtilization,
            'storage_cost' => $totalStorageCost,
            'orders_processed' => $ordersProcessed,
            'fulfillment_accuracy_rate' => $fulfillmentAccuracy,
            'average_processing_time' => 2.5, // In hours
            'currency' => 'USD'
        ]);
    }

    /**
     * Generate courier performance report
     */
    public function generateCourierPerformanceReport($userId, Carbon $startDate, Carbon $endDate, $courierPartnerId = null)
    {
        $query = ShippingLabel::where('user_id', $userId)
            ->whereBetween('created_at', [$startDate, $endDate]);

        if ($courierPartnerId) {
            $query->where('courier_partner_id', $courierPartnerId);
        }

        $labels = $query->get();
        $totalShipments = $labels->count();

        $onTimeDeliveries = 0;
        $successfulDeliveries = 0;
        $totalRevenue = 0;

        foreach ($labels as $label) {
            if (isset($label->delivery_status)) {
                if ($label->delivery_status === 'delivered') {
                    $successfulDeliveries++;

                    if (isset($label->estimated_delivery_date) && isset($label->actual_delivery_date)) {
                        $estimated = Carbon::parse($label->estimated_delivery_date);
                        $actual = Carbon::parse($label->actual_delivery_date);
                        
                        if ($actual->lte($estimated)) {
                            $onTimeDeliveries++;
                        }
                    }
                }
            }

            if (isset($label->cost)) {
                $totalRevenue += $label->cost;
            }
        }

        $successRate = $totalShipments > 0 ? ($successfulDeliveries / $totalShipments) * 100 : 0;
        $avgRevenue = $totalShipments > 0 ? $totalRevenue / $totalShipments : 0;
        $avgDeliveryTime = $this->calculateAverageDeliveryTime($labels, $startDate, $endDate);

        $reportData = [
            'period' => [
                'start' => $startDate->format('Y-m-d'),
                'end' => $endDate->format('Y-m-d')
            ],
            'courier_data' => $labels->toArray(),
            'performance_metrics' => [
                'total_shipments' => $totalShipments,
                'successful_deliveries' => $successfulDeliveries,
                'delivery_success_rate' => $successRate,
                'on_time_rate' => $totalShipments > 0 ? ($onTimeDeliveries / $totalShipments) * 100 : 0
            ],
            'financial_metrics' => [
                'total_revenue' => $totalRevenue,
                'revenue_per_shipment' => $avgRevenue,
                'cost_per_shipment' => $avgRevenue * 0.8 // Assuming 80% of revenue is cost
            ]
        ];

        return CourierPerformanceReport::create([
            'user_id' => $userId,
            'report_date' => now(),
            'report_data' => $reportData,
            'status' => 'completed',
            'total_shipments' => $totalShipments,
            'on_time_deliveries' => $onTimeDeliveries,
            'successful_deliveries' => $successfulDeliveries,
            'delivery_success_rate' => $successRate,
            'average_delivery_time_hours' => $avgDeliveryTime,
            'total_revenue' => $totalRevenue,
            'revenue_per_shipment' => $avgRevenue,
            'cost_per_shipment' => $avgRevenue * 0.8,
            'profit_margin' => 20.0, // Assuming 20% margin
            'courier_partner_id' => $courierPartnerId,
            'currency' => 'USD'
        ]);
    }

    /**
     * Generate return management report
     */
    public function generateReturnReport($userId, Carbon $startDate, Carbon $endDate)
    {
        $returns = ReturnRequest::where('vendor_id', $userId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $totalReturns = $returns->count();
        $processedReturns = $returns->filter(function ($return) {
            return in_array($return->status, ['resolved', 'rejected']);
        })->count();
        
        $pendingReturns = $totalReturns - $processedReturns;
        $exchangeCount = $returns->filter(function ($return) {
            return $return->return_type === 'exchange';
        })->count();
        
        $refundAmount = $returns->sum('refund_amount');
        $returnRate = $this->calculateReturnRate($userId, $startDate, $endDate);

        $returnReasons = [];
        foreach ($returns as $return) {
            $reason = $return->return_reason ?? 'other';
            if (!isset($returnReasons[$reason])) {
                $returnReasons[$reason] = 0;
            }
            $returnReasons[$reason]++;
        }

        $reportData = [
            'period' => [
                'start' => $startDate->format('Y-m-d'),
                'end' => $endDate->format('Y-m-d')
            ],
            'return_data' => $returns->toArray(),
            'return_analysis' => [
                'total_returns' => $totalReturns,
                'processed_returns' => $processedReturns,
                'pending_returns' => $pendingReturns,
                'return_rate_percent' => $returnRate,
                'refund_amount_total' => $refundAmount,
                'exchange_rate_percent' => $totalReturns > 0 ? ($exchangeCount / $totalReturns) * 100 : 0
            ],
            'return_reasons' => $returnReasons
        ];

        return ReturnReport::create([
            'user_id' => $userId,
            'report_date' => now(),
            'report_data' => $reportData,
            'status' => 'completed',
            'total_returns' => $totalReturns,
            'processed_returns' => $processedReturns,
            'pending_returns' => $pendingReturns,
            'return_rate_percent' => $returnRate,
            'average_resolution_time_days' => 5.2, // Average resolution time
            'refund_amount' => $refundAmount,
            'exchange_rate_percent' => $totalReturns > 0 ? ($exchangeCount / $totalReturns) * 100 : 0,
            'return_reasons' => $returnReasons,
            'currency' => 'USD'
        ]);
    }

    /**
     * Calculate return rate for the period
     */
    private function calculateReturnRate($userId, Carbon $startDate, Carbon $endDate)
    {
        $totalTransactions = \App\Models\Order::where('user_id', $userId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();
        
        $totalReturns = ReturnRequest::where('vendor_id', $userId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        return $totalTransactions > 0 ? ($totalReturns / $totalTransactions) * 100 : 0;
    }

    /**
     * Calculate average delivery time in hours
     */
    private function calculateAverageDeliveryTime($labels, Carbon $startDate, Carbon $endDate)
    {
        $totalHours = 0;
        $deliveryCount = 0;

        foreach ($labels as $label) {
            if (isset($label->created_at) && isset($label->actual_delivery_date)) {
                $created = Carbon::parse($label->created_at);
                $delivered = Carbon::parse($label->actual_delivery_date);
                $totalHours += $created->diffInHours($delivered);
                $deliveryCount++;
            }
        }

        return $deliveryCount > 0 ? $totalHours / $deliveryCount : 48.0; // Default 48 hours
    }
}