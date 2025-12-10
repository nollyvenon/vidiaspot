<?php

namespace App\Services;

use App\Models\Logistics\ShippingLabel;
use App\Models\Logistics\CourierPartner;
use App\Models\Logistics\ReturnRequest;
use App\Models\BuyerProtection;
use App\Models\InventoryTracking;
use App\Models\Logistics\Warehouse;
use App\Models\Ad;
use App\Models\User;
use App\Models\TrustScore;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LogisticsService
{
    /**
     * Get available third-party logistics partners
     */
    public function getLogisticsPartners($filters = [])
    {
        $query = CourierPartner::where('is_active', true)
                              ->where('accepting_orders', true);

        if (isset($filters['service_type'])) {
            $query = $query->whereJsonContains('service_types', $filters['service_type']);
        }

        if (isset($filters['coverage_area'])) {
            $query = $query->whereJsonContains('coverage_areas', $filters['coverage_area']);
        }

        if (isset($filters['is_same_day'])) {
            $query = $query->where('same_day_delivery_available', $filters['is_same_day']);
        }

        if (isset($filters['delivery_timeframe'])) {
            $query = $query->whereJsonContains('delivery_timeframes', $filters['delivery_timeframe']);
        }

        $partners = $query->orderBy('rating', 'desc')
                         ->orderBy('success_rate', 'desc')
                         ->get();

        $results = [];
        foreach ($partners as $partner) {
            $results[] = [
                'id' => $partner->id,
                'name' => $partner->name,
                'description' => $partner->description,
                'logo_url' => $partner->logo_url,
                'rating' => $partner->rating,
                'success_rate' => $partner->success_rate,
                'on_time_delivery_rate' => $partner->on_time_delivery_rate,
                'coverage_areas' => $partner->coverage_areas,
                'service_types' => $partner->service_types,
                'delivery_timeframes' => $partner->delivery_timeframes,
                'pricing_tiers' => $this->calculatePricing($partner, $filters),
                'features' => [
                    'real_time_tracking' => $partner->real_time_tracking,
                    'customer_support' => $partner->customer_support_available,
                    'returns_management' => $partner->returns_management,
                    'cold_chain' => $partner->cold_chain_capabilities,
                    'fragile_handling' => $partner->fragile_handling,
                    'express_delivery' => $partner->same_day_delivery_available,
                ],
                'is_available' => $this->isPartnerAvailable($partner, $filters),
                'estimated_cost' => $this->estimateCost($partner, $filters),
                'estimated_delivery' => $this->estimateDeliveryTime($partner, $filters),
                'commission_rate' => $partner->commission_rate,
            ];
        }

        return $results;
    }

    /**
     * Generate automated shipping label
     */
    public function generateShippingLabel($userId, $orderData)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($orderData, [
            'ad_id' => 'required|integer|exists:ads,id',
            'from_address' => 'required|array',
            'to_address' => 'required|array',
            'package_weight' => 'required|numeric|min:0.1',
            'package_dimensions' => 'required|array',
            'package_value' => 'required|numeric|min:100',
            'carrier_code' => 'required|in:fedex,ups,dhl,poslaju,local_carrier',
            'service_type' => 'required|in:standard,express,overnight,freight',
        ]);

        if ($validator->fails()) {
            throw new \Exception('Shipping data validation failed: ' . $validator->errors()->first());
        }

        // Generate unique shipment ID and tracking number
        $shipmentId = 'SH_' . date('Ymd') . '_' . strtoupper(Str::random(8));
        $trackingNumber = $this->generateTrackingNumber($orderData['carrier_code']);

        // Calculate shipping cost
        $shippingCost = $this->calculateShippingCost($orderData);

        // Create shipping label
        $label = ShippingLabel::create([
            'user_id' => $userId,
            'order_id' => $orderData['ad_id'], // Using ad_id as order_id for this implementation
            'shipment_id' => $shipmentId,
            'tracking_number' => $trackingNumber,
            'carrier_code' => $orderData['carrier_code'],
            'carrier_name' => $this->getCarrierName($orderData['carrier_code']),
            'shipping_cost' => $shippingCost,
            'currency_code' => 'NGN',
            'package_weight_kg' => $orderData['package_weight'],
            'package_length_cm' => $orderData['package_dimensions']['length'] ?? 10,
            'package_width_cm' => $orderData['package_dimensions']['width'] ?? 10,
            'package_height_cm' => $orderData['package_dimensions']['height'] ?? 10,
            'package_value' => $orderData['package_value'],
            'shipping_address_json' => $orderData['from_address'],
            'delivery_address_json' => $orderData['to_address'],
            'service_type' => $orderData['service_type'],
            'insurance_covered' => $orderData['insure_package'] ?? false,
            'insurance_amount' => $orderData['insure_package'] ? $orderData['package_value'] : 0,
            'signature_required' => $orderData['signature_required'] ?? false,
            'adult_signature_required' => $orderData['adult_signature_required'] ?? false,
            'status' => 'pending',
            'estimated_delivery_date' => now()->addDays($this->getEstimatedDeliveryDays($orderData['service_type'])),
            'delivery_instructions' => $orderData['delivery_instructions'] ?? '',
            'special_services' => $orderData['special_services'] ?? [],
            'label_file_path' => 'shipping_labels/' . $shipmentId . '.pdf',
            'label_url' => null, // Would be generated by shipping carrier API
            'packages_count' => $orderData['packages_count'] ?? 1,
            'origin_country' => $orderData['from_address']['country'] ?? 'Nigeria',
            'destination_country' => $orderData['to_address']['country'] ?? 'Nigeria',
            'delivery_confirmation' => 'signature_required',
            'delivery_time_preference' => $orderData['delivery_time_preference'] ?? 'any_time',
            'billing_option' => $orderData['billing_option'] ?? 'shipper_pay',
        ]);

        // In a real implementation, this would connect to carrier APIs to generate actual labels
        $this->connectToCarrierAPI($label);

        return $label;
    }

    /**
     * Process return request
     */
    public function processReturn($userId, $returnData)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($returnData, [
            'order_id' => 'required|integer',
            'ad_id' => 'nullable|integer|exists:ads,id',
            'vendor_id' => 'required|integer|exists:users,id',
            'return_reason' => 'required|string',
            'return_type' => 'required|in:refund,exchange,repair,replacement',
            'return_method' => 'required|in:pickup,drop_off,courier_collection,self_delivery',
            'refund_amount' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            throw new \Exception('Return data validation failed: ' . $validator->errors()->first());
        }

        $return = ReturnRequest::create([
            'user_id' => $userId,
            'order_id' => $returnData['order_id'],
            'ad_id' => $returnData['ad_id'] ?? null,
            'vendor_id' => $returnData['vendor_id'],
            'return_reason' => $returnData['return_reason'],
            'return_description' => $returnData['return_description'] ?? '',
            'return_images' => $returnData['return_images'] ?? [],
            'return_reason_category' => $this->categorizeReturnReason($returnData['return_reason']),
            'return_type' => $returnData['return_type'],
            'return_method' => $returnData['return_method'],
            'return_status' => 'pending',
            'refund_amount' => $returnData['refund_amount'],
            'exchange_item_id' => $returnData['exchange_item_id'] ?? null,
            'return_address_json' => $returnData['return_address'] ?? [],
            'original_delivery_address_json' => $returnData['original_delivery_address'] ?? [],
            'is_return_insured' => true, // Auto-insure returns
            'return_insurance_amount' => $returnData['refund_amount'] * 0.02, // 2% insurance for returns
            'return_insurance_status' => 'active',
            'delivery_cost' => $returnData['return_method'] === 'pickup' ? 0 : 500, // Return delivery cost if needed
            'restocking_fee' => $returnData['restocking_fee'] ?? 0,
            'return_fee' => $returnData['return_fee'] ?? 0,
            'item_verification_status' => 'pending',
            'quality_check_status' => 'not_started',
            'return_deadline' => now()->addDays(30),
            'is_return_eligible' => true,
            'return_label_generated' => false,
            'resolution_type' => $returnData['return_type'] === 'refund' ? 'full_refund' : 
                               ($returnData['return_type'] === 'exchange' ? 'exchange' : 
                               ($returnData['return_type'] === 'repair' ? 'replacement' : 'refund')),
            'quality_check_notes' => $returnData['quality_check_notes'] ?? null,
            'resolution_details' => $returnData['resolution_details'] ?? [],
            'customer_satisfaction_score' => null, // Will be set after resolution
        ]);

        // Generate return shipping label
        $this->generateReturnLabel($return);

        // Update related trust scores
        $this->updateTrustScoresAfterReturn($return);

        return $return;
    }

    /**
     * Generate package insurance for shipments
     */
    public function generatePackageInsurance($userId, $packageValue, $providerId = null)
    {
        // Find appropriate insurance provider
        $provider = $providerId ? 
            \App\Models\InsuranceProvider::find($providerId) :
            \App\Models\InsuranceProvider::whereJsonContains('categories', 'shipping')
                                         ->where('is_active', true)
                                         ->orderBy('rating', 'desc')
                                         ->first();

        if (!$provider) {
            $provider = \App\Models\InsuranceProvider::firstOrCreate([
                'name' => 'VidiAspot Shipping Insurance'
            ], [
                'description' => 'Platform-provided shipping insurance for all deliveries',
                'rating' => 4.5,
                'is_active' => true,
                'categories' => ['shipping', 'logistics', 'parcel'],
                'coverage_areas' => ['Nigeria'],
                'min_coverage' => 10000,
                'max_coverage' => 10000000,
                'claim_settlement_ratio' => 95.00,
            ]);
        }

        $coverageAmount = $packageValue * 1.1; // 110% of package value
        $premiumAmount = $this->calculateInsurancePremium($packageValue, $provider);

        $policy = BuyerProtection::create([
            'user_id' => $userId,
            'transaction_id' => null, // For shipping insurance
            'transaction_type' => 'shipping_insurance',
            'provider_id' => $provider->id,
            'provider' => $provider->name,
            'policy_number' => 'SHI_' . date('Y') . '_' . strtoupper(Str::random(8)),
            'coverage_amount' => $coverageAmount,
            'premium_amount' => $premiumAmount,
            'status' => 'active',
            'coverage_terms' => [
                'covered_perils' => ['damage', 'theft', 'loss', 'delay', 'non-delivery'],
                'exclusions' => ['intentional_damage', 'acts_of_war', 'natural_disasters'],
                'claim_process' => 'submit_within_48_hours',
                'processing_time' => '7_business_days',
            ],
            'exclusions' => ['intentional_damage', 'acts_of_war', 'natural_disasters'],
            'protection_type' => 'full_coverage',
            'claim_status' => 'no_claim',
            'purchase_date' => now(),
            'expiry_date' => now()->addYears(1),
            'metadata' => [
                'original_package_value' => $packageValue,
                'insurance_provider_type' => $providerId ? 'external' : 'platform_default',
                'coverage_type' => 'shipping_protection',
            ],
            'custom_fields' => [
                'shipping_related' => true,
                'coverage_purpose' => 'parcel_protection',
                'claim_limit_frequency' => 'once_per_shipment',
            ],
        ]);

        return $policy;
    }

    /**
     * Integrate with warehouse for large sellers
     */
    public function integrateWithWarehouse($userId, $integrationData)
    {
        $user = User::find($userId);
        
        // Validate that user meets criteria for warehouse integration
        $trustScore = $user->trustScore ?? $this->getDefaultTrustScore($userId);
        if ($trustScore->trust_score < 60) {
            throw new \Exception('User does not meet minimum trust score (' . $trustScore->trust_score . ') for warehouse integration. Minimum required: 60');
        }

        $warehouse = isset($integrationData['warehouse_id']) ? 
            Warehouse::find($integrationData['warehouse_id']) :
            $this->findBestWarehouse(
                $integrationData['location_preference'] ?? null,
                $integrationData['allocation_size'] ?? null
            );

        if (!$warehouse) {
            throw new \Exception('No suitable warehouse found for integration');
        }

        if ($warehouse->hasReachedCapacity($integrationData['allocation_size'])) {
            throw new \Exception('Selected warehouse does not have sufficient capacity');
        }

        // Create warehouse integration record
        $integration = [
            'user_id' => $userId,
            'warehouse_id' => $warehouse->id,
            'integration_type' => $integrationData['integration_type'] ?? 'fulfillment',
            'status' => 'pending_approval',
            'contract_start_date' => now(),
            'contract_end_date' => now()->addMonths($integrationData['contract_months'] ?? 12),
            'allocated_square_meters' => $integrationData['allocation_size'],
            'monthly_fee' => $warehouse->calculateStorageCost(
                $integrationData['allocation_size'],
                30, // 30 days
                $integrationData['estimated_inventory_value'] ?? 0
            ),
            'services_enabled' => $integrationData['services'] ?? ['storage', 'fulfillment'],
            'api_access_enabled' => true,
            'inventory_sync_enabled' => $integrationData['auto_sync'] ?? true,
            'fulfillment_auto_enabled' => $integrationData['auto_fulfillment'] ?? false,
            'return_processing_enabled' => $warehouse->returns_processing_capability ?? false,
            'shipping_integration_enabled' => $warehouse->shipping_integration_enabled ?? true,
            'supported_carriers' => $warehouse->shipping_carriers_integration ?? [],
            'integration_settings' => [
                'auto_order_fulfillment' => $integrationData['auto_fulfillment'] ?? false,
                'auto_inventory_sync' => $integrationData['auto_sync'] ?? true,
                'notify_low_stock' => $integrationData['alert_on_low_stock'] ?? true,
                'multi_channel_sync' => $integrationData['enable_multi_sync'] ?? true,
                'fulfillment_priority' => $integrationData['fulfillment_priority'] ?? 'first_available',
            ],
            'credentials' => [
                'api_key' => 'WH_' . Str::random(32),
                'api_secret' => hash('sha256', Str::random(64)),
                'webhook_url' => $integrationData['webhook_url'] ?? url('/webhooks/warehouse/' . $userId),
            ],
            'setup_complete' => false,
            'setup_progress' => 0,
            'last_sync' => null,
            'next_sync' => now()->addMinutes(30),
            'sync_frequencies' => [
                'inventory' => $integrationData['inventory_sync_freq'] ?? 'every_30_minutes',
                'orders' => $integrationData['order_sync_freq'] ?? 'real_time',
                'returns' => $integrationData['return_sync_freq'] ?? 'every_1_hour',
            ],
            'warehouse_info' => [
                'name' => $warehouse->name,
                'address' => $warehouse->address_json,
                'capacity' => $warehouse->capacity_sqm,
                'utilization' => $warehouse->capacity_utilization_percentage,
                'supported_services' => $warehouse->service_types,
            ],
            'seller_info' => [
                'name' => $user->name,
                'email' => $user->email,
                'trust_score' => $trustScore->trust_score,
                'verification_level' => $trustScore->verification_level,
            ],
        ];

        // In a real implementation, this would create a warehouse_integration record
        // For this demo, we'll return the integration details

        // Create the inventory tracking records for the integration
        $this->setupWarehouseInventoryTracking($userId, $warehouse->id, $integrationData);

        return $integration;
    }

    /**
     * Synchronize inventory across multiple platforms and warehouses
     */
    public function synchronizeInventoryAcrossPlatforms($userId, $platforms = 'all')
    {
        $user = User::find($userId);
        $ads = Ad::where('user_id', $userId)->get();
        $inventoryItems = $this->getUserInventoryItems($userId);

        $synchronizationResults = [];

        foreach ($inventoryItems as $item) {
            $syncResult = [
                'item_id' => $item->id,
                'item_name' => $item->ad->title ?? 'Unknown Item',
                'current_inventory' => $item->current_quantity ?? $item->quantity_available ?? 0,
                'platform_sync_results' => [],
                'sync_timestamp' => now(),
                'status' => 'success',
            ];

            // Simulate synchronization with different platforms
            $platformList = $platforms === 'all' ? ['vidiaspot', 'jumia', 'konga', 'amazon'] : $platforms;

            foreach ($platformList as $platform) {
                $quantity = $item->current_quantity ?? $item->quantity_available ?? 0;
                
                $syncResult['platform_sync_results'][$platform] = [
                    'status' => 'synced',
                    'quantity_synced' => $quantity,
                    'timestamp' => now(),
                    'error' => null,
                    'platform_inventory_id' => $platform . '_' . $item->id,
                ];
            }

            $synchronizationResults[] = $syncResult;
        }

        return [
            'user_id' => $userId,
            'platforms_synced' => $platforms,
            'items_synced' => count($synchronizationResults),
            'results' => $synchronizationResults,
            'sync_summary' => [
                'successful_syncs' => count($synchronizationResults),
                'failed_syncs' => 0,
                'total_items' => count($inventoryItems),
                'out_of_sync_items' => 0,
                'next_sync_scheduled' => now()->addMinutes(30),
            ],
            'sync_timestamp' => now(),
        ];
    }

    /**
     * Get return management dashboard for sellers
     */
    public function getReturnManagementDashboard($userId)
    {
        $returns = ReturnRequest::where('vendor_id', $userId)
                               ->selectRaw('
                                   COUNT(*) as total_returns,
                                   COUNT(CASE WHEN return_status = "pending" THEN 1 END) as pending_returns,
                                   COUNT(CASE WHEN return_status = "approved" THEN 1 END) as approved_returns,
                                   COUNT(CASE WHEN return_status = "rejected" THEN 1 END) as rejected_returns,
                                   COUNT(CASE WHEN return_status = "resolved" THEN 1 END) as resolved_returns,
                                   AVG(TIMESTAMPDIFF(DAY, created_at, resolution_date)) as avg_resolution_time,
                                   SUM(refund_amount) as total_refund_amount
                               ')
                               ->first();

        $trustScore = $this->getTrustScoreWithReturns($userId);

        $dashboard = [
            'summary' => [
                'total_returns' => $returns->total_returns ?? 0,
                'pending_returns' => $returns->pending_returns ?? 0,
                'approved_returns' => $returns->approved_returns ?? 0,
                'rejected_returns' => $returns->rejected_returns ?? 0,
                'resolved_returns' => $returns->resolved_returns ?? 0,
                'total_refund_amount' => $returns->total_refund_amount ?? 0,
                'average_resolution_time_days' => $returns->avg_resolution_time ?? 0,
            ],
            'return_rate_analysis' => [
                'return_rate_percentage' => $this->calculateReturnRate($userId),
                'industry_average' => 7,
                'performance_rating' => $this->getReturnRatePerformance($userId),
            ],
            'trust_and_safety_metrics' => [
                'current_trust_score' => $trustScore->trust_score ?? 50,
                'verification_level' => $trustScore->verification_level ?? 'basic',
                'positive_return_responses' => $this->getPositiveReturnHandlingRate($userId),
                'complaints_vs_returns' => $this->getComplaintsVsReturns($userId),
            ],
            'top_return_reasons' => $this->getTopReturnReasons($userId),
            'return_trends' => $this->getReturnTrends($userId),
        ];

        return $dashboard;
    }

    /**
     * Process return for warehouse fulfillment
     */
    public function processReturnThroughWarehouse($returnId)
    {
        $return = ReturnRequest::findOrFail($returnId);

        // Find the warehouse responsible for this return if the item was fulfilled from warehouse
        $warehouse = null;
        if ($return->fulfilled_from_warehouse_id) {
            $warehouse = Warehouse::find($return->fulfilled_from_warehouse_id);
        } else {
            // Find warehouse based on delivery location
            $warehouse = $this->findNearestWarehouseForReturn($return->original_delivery_address_json);
        }

        if ($warehouse) {
            // Process return at warehouse
            $returnProcessingResult = $warehouse->processReturn(
                $return->return_items ?? [$return->ad_id],
                $return->return_reason,
                $return->return_images ?? []
            );

            $return->update([
                'return_processing_status' => 'warehouse_processing',
                'return_processing_details' => $returnProcessingResult,
                'return_processed_at_warehouse' => now(),
                'return_warehouse_id' => $warehouse->id,
            ]);
        }

        return $return;
    }

    /**
     * Calculate shipping cost based on various factors
     */
    private function calculateShippingCost($orderData)
    {
        // Base calculation considering distance, weight, and service type
        $weight = $orderData['package_weight'] ?? 1;
        $distance = $this->calculateDistanceEstimate(
            $orderData['from_address']['city'] ?? 'Lagos',
            $orderData['to_address']['city'] ?? 'Abuja'
        );
        
        $baseRate = 500; // Base rate in NGN
        $weightRate = $weight * 100; // 100 NGN per kg
        $distanceRate = $distance * 15; // 15 NGN per km
        
        $total = $baseRate + $weightRate + $distanceRate;
        
        // Apply service type multiplier
        $serviceType = $orderData['service_type'] ?? 'standard';
        $multiplier = $this->getServiceMultiplier($serviceType);
        
        $total *= $multiplier;

        // Add insurance if requested
        if ($orderData['insure_package'] ?? false) {
            $insuranceCost = $orderData['package_value'] * 0.02; // 2% insurance fee
            $total += $insuranceCost;
        }

        return round($total, 2);
    }

    /**
     * Get service multiplier for shipping
     */
    private function getServiceMultiplier($serviceType)
    {
        $multipliers = [
            'overnight' => 3.0,
            'express' => 2.0,
            'standard' => 1.0,
            'economy' => 0.8,
            'freight' => 0.6, // Bulk freight is cheaper
        ];
        
        return $multipliers[$serviceType] ?? 1.0;
    }

    /**
     * Calculate distance estimate between locations
     */
    private function calculateDistanceEstimate($origin, $destination)
    {
        // In a real implementation, this would use a distance matrix API
        // For demo, return a distance based on major Nigerian cities
        $distances = [
            ['Lagos', 'Abuja'] => 539,
            ['Lagos', 'Port Harcourt'] => 667,
            ['Lagos', 'Kano'] => 1057,
            ['Lagos', 'Ibadan'] => 135,
            ['Abuja', 'Kano'] => 475,
            ['Abuja', 'Port Harcourt'] => 580,
        ];
        
        $routeKey = [strtolower($origin), strtolower($destination)];
        sort($routeKey);
        $routeKeyStr = implode('-', $routeKey);
        
        $routeMap = [
            'lagos-abuja' => 539,
            'abuja-lagos' => 539,
            'lagos-port harcourt' => 667,
            'port harcourt-lagos' => 667,
            'lagos-kano' => 1057,
            'kano-lagos' => 1057,
            'lagos-ibadan' => 135,
            'ibadan-lagos' => 135,
            'abuja-kano' => 475,
            'kano-abuja' => 475,
            'abuja-port harcourt' => 580,
            'port harcourt-abuja' => 580,
        ];
        
        return $routeMap[$routeKeyStr] ?? mt_rand(50, 1000); // Default random distance if not in map
    }

    /**
     * Generate tracking number for carrier
     */
    private function generateTrackingNumber($carrierCode)
    {
        switch ($carrierCode) {
            case 'fedex':
                return '123' . mt_rand(100000000, 999999999) . 'FE';
            case 'ups':
                return '1Z' . strtoupper(Str::random(14));
            case 'dhl':
                return 'JV' . mt_rand(1000000000, 9999999999) . 'GL';
            case 'poslaju':
                return mt_rand(1000000000, 9999999999) . 'MY';
            default:
                return 'VID' . date('Y') . mt_rand(100000000, 999999999);
        }
    }

    /**
     * Get carrier name from code
     */
    private function getCarrierName($carrierCode)
    {
        $carriers = [
            'fedex' => 'FedEx',
            'ups' => 'United Parcel Service',
            'dhl' => 'DHL Express',
            'poslaju' => 'Pos Laju',
            'local_carrier' => 'Local Courier Partner',
        ];

        return $carriers[$carrierCode] ?? $carrierCode;
    }

    /**
     * Get estimated delivery days based on service type
     */
    private function getEstimatedDeliveryDays($serviceType)
    {
        $days = [
            'overnight' => 1,
            'express' => 2,
            'standard' => 3,
            'economy' => 5,
            'freight' => 7,
        ];
        
        return $days[$serviceType] ?? 3;
    }

    /**
     * Connect to carrier API to generate actual label
     */
    private function connectToCarrierAPI($label)
    {
        // This would connect to actual shipping carrier APIs in production
        // For demo purposes, we'll simulate the process
        $carrier = $label->carrier_code;
        
        // Simulate API response
        $success = mt_rand(0, 100) > 10; // 90% success rate for demo

        if ($success) {
            $label->update([
                'label_url' => 'https://api.' . $carrier . '.com/label/' . $label->tracking_number,
                'status' => 'label_generated',
                'label_generated_at' => now(),
            ]);
        } else {
            $label->update([
                'status' => 'label_generation_failed',
                'notes' => 'Carrier API not responding for ' . $carrier,
            ]);
        }
    }

    /**
     * Calculate pricing for a partner
     */
    private function calculatePricing($partner, $filters)
    {
        $weight = $filters['weight'] ?? 1;
        $distance = $filters['distance'] ?? 10;
        $value = $filters['value'] ?? 5000;
        
        // Base calculation
        $baseRate = 500; // Base rate in NGN
        $weightRate = $weight * 100;
        $distanceRate = $distance * 15;
        
        $total = $baseRate + $weightRate + $distanceRate;
        
        // Apply partner-specific multipliers
        $serviceType = $filters['service_type'] ?? 'standard';
        $multiplier = $this->getServiceMultiplier($serviceType);
        
        $total *= $multiplier;
        
        // Apply partner commission
        $commission = $partner->commission_rate ?? 0;
        $finalTotal = $total + ($total * $commission / 100);

        return [
            'base_rate' => $baseRate,
            'weight_rate' => $weightRate,
            'distance_rate' => $distanceRate,
            'service_multiplier' => $multiplier,
            'commission_rate' => $commission,
            'total_cost' => round($finalTotal, 2),
            'estimated_delivery_days' => $this->getEstimatedDeliveryDays($serviceType),
        ];
    }

    /**
     * Check if partner is available for specific requirements
     */
    private function isPartnerAvailable($partner, $filters)
    {
        $origin = $filters['from_address']['city'] ?? 'Lagos';
        $destination = $filters['to_address']['city'] ?? 'Abuja';
        $coverages = $partner->coverage_areas ?? [];

        // Check if partner covers both origin and destination
        $originCovered = $this->locationInCoverage($origin, $coverages);
        $destinationCovered = $this->locationInCoverage($destination, $coverages);

        if (!$originCovered && !$destinationCovered) {
            return false;
        }

        // Check service type availability
        $serviceType = $filters['service_type'] ?? 'standard';
        $serviceAvailable = in_array($serviceType, $partner->service_types ?? []);

        return $serviceAvailable;
    }

    /**
     * Check if location is in coverage areas
     */
    private function locationInCoverage($location, $coverageAreas)
    {
        if (!is_array($coverageAreas)) {
            return false;
        }
        
        foreach ($coverageAreas as $area) {
            if (is_string($area) && stripos($area, $location) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Categorize return reason
     */
    private function categorizeReturnReason($reason)
    {
        $defectiveKeywords = ['broken', 'defective', 'faulty', 'not_working', 'damaged', 'bad_quality'];
        $wrongKeywords = ['wrong', 'incorrect', 'different', 'not_expected'];
        $descriptionKeywords = ['not_as_described', 'misleading', 'false_ad', 'different_from_picture', 'false_description'];
        $changeKeywords = ['changed_mind', 'don\'t_want', 'order_error', 'buying_mistake', 'no_longer_needed'];

        $reasonLower = strtolower($reason);

        foreach ($defectiveKeywords as $keyword) {
            if (strpos($reasonLower, $keyword) !== false) {
                return 'defective';
            }
        }

        foreach ($wrongKeywords as $keyword) {
            if (strpos($reasonLower, $keyword) !== false) {
                return 'wrong_item';
            }
        }

        foreach ($descriptionKeywords as $keyword) {
            if (strpos($reasonLower, $keyword) !== false) {
                return 'not_as_described';
            }
        }

        foreach ($changeKeywords as $keyword) {
            if (strpos($reasonLower, $keyword) !== false) {
                return 'changed_mind';
            }
        }

        return 'other';
    }

    /**
     * Generate return shipping label
     */
    private function generateReturnLabel($return)
    {
        $returnLabel = ShippingLabel::create([
            'user_id' => $return->user_id,
            'order_id' => $return->order_id,
            'shipment_id' => 'RTN_' . date('Ymd') . '_' . strtoupper(Str::random(8)),
            'tracking_number' => $this->generateReturnTrackingNumber($return->id),
            'carrier_code' => 'return_carrier',
            'carrier_name' => 'Platform Return Service',
            'shipping_cost' => 0, // Return shipping often free for good sellers
            'currency_code' => 'NGN',
            'package_weight_kg' => $this->getOriginalPackageWeight($return->order_id),
            'package_length_cm' => $this->getOriginalPackageLength($return->order_id),
            'package_width_cm' => $this->getOriginalPackageWidth($return->order_id),
            'package_height_cm' => $this->getOriginalPackageHeight($return->order_id),
            'package_value' => $return->refund_amount,
            'shipping_address_json' => $return->return_address_json,
            'delivery_address_json' => $return->original_delivery_address_json,
            'service_type' => 'standard',
            'insurance_covered' => true,
            'insurance_amount' => $return->return_insurance_amount,
            'signature_required' => false,
            'status' => 'pending',
            'estimated_delivery_date' => now()->addDays(7),
            'special_services' => ['return_processing'],
            'label_file_path' => 'return_labels/' . $return->id . '.pdf',
            'return_label' => true,
        ]);

        $return->update([
            'return_shipping_label_id' => $returnLabel->id,
            'return_label_generated' => true,
            'return_label_url' => $returnLabel->label_url,
            'return_tracking_number' => $returnLabel->tracking_number,
        ]);
    }

    /**
     * Generate return tracking number
     */
    private function generateReturnTrackingNumber($returnId)
    {
        return 'RTN' . date('Ymd') . str_pad($returnId, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Get original package weight
     */
    private function getOriginalPackageWeight($orderId)
    {
        // In a real implementation, this would fetch from the original order
        return mt_rand(100, 2000) / 1000; // Random weight between 0.1kg and 2kg
    }

    /**
     * Get original package dimensions
     */
    private function getOriginalPackageLength($orderId) { return mt_rand(10, 60); }
    private function getOriginalPackageWidth($orderId) { return mt_rand(10, 40); }
    private function getOriginalPackageHeight($orderId) { return mt_rand(5, 30); }

    /**
     * Update trust scores after return
     */
    private function updateTrustScoresAfterReturn($return)
    {
        // Update vendor trust score based on return
        $vendorTrustScore = TrustScore::firstOrCreate(['user_id' => $return->vendor_id]);
        $customerTrustScore = TrustScore::firstOrCreate(['user_id' => $return->user_id]);

        $returnImpact = -2; // Small negative impact for returns

        if ($return->return_reason_category === 'defective' || $return->return_reason_category === 'not_as_described') {
            // More significant impact if it's the vendor's fault
            $returnImpact = -5;
        } elseif ($return->return_reason_category === 'changed_mind') {
            // Less impact if it's just change of mind
            $returnImpact = -1;
        }

        // Update vendor trust score
        $vendorTrustScore->update([
            'trust_score' => max(0, min(100, $vendorTrustScore->trust_score + $returnImpact)),
            'dispute_count' => $vendorTrustScore->dispute_count + 1,
            'complaint_count' => $vendorTrustScore->complaint_count + 1,
            'last_updated' => now(),
        ]);

        // Customer trust score impact (usually minimal unless fraudulent returns)
        $customerTrustScore->update([
            'trust_score' => max(0, min(100, $customerTrustScore->trust_score)),
            'last_updated' => now(),
        ]);
    }

    /**
     * Calculate insurance premium
     */
    private function calculateInsurancePremium($packageValue, $provider)
    {
        $rate = 0.02; // Default 2% rate
        
        // Adjust based on provider rating and package value
        if ($provider && $provider->rating) {
            $rate = max(0.01, min(0.05, 0.02 - (($provider->rating - 4.0) * 0.005))); // Better rated providers charge less
        }

        $premium = $packageValue * $rate;

        // Minimum premium of 100 NGN
        return max(100, $premium);
    }

    /**
     * Find best warehouse based on location and capacity
     */
    private function findBestWarehouse($location = null, $size = null)
    {
        $query = Warehouse::where('is_active', true)
                         ->where('is_operational', true);

        if ($location) {
            $query = $query->where(function($q) use ($location) {
                $q->where('city', 'LIKE', '%' . $location . '%')
                  ->orWhere('state', 'LIKE', '%' . $location . '%')
                  ->orWhere('country', 'LIKE', '%' . $location . '%');
            });
        }

        if ($size) {
            $query = $query->where('capacity_sqm', '>=', $size)
                          ->whereRaw('(capacity_sqm * (1 - (capacity_utilization_percentage / 100))) >= ?', [$size]);
        }

        return $query->orderBy('rating', 'desc')
                    ->first();
    }

    /**
     * Setup warehouse inventory tracking for user
     */
    private function setupWarehouseInventoryTracking($userId, $warehouseId, $integrationData)
    {
        // Link user's inventory items to warehouse
        $inventoryItems = InventoryTracking::where('user_id', $userId)->get();
        
        foreach ($inventoryItems as $item) {
            $item->update([
                'warehouses_integration_enabled' => true,
                'warehouses_connected' => array_merge($item->warehouses_connected ?? [], [$warehouseId]),
            ]);
        }
    }

    /**
     * Get user's inventory items
     */
    private function getUserInventoryItems($userId)
    {
        return InventoryTracking::where('user_id', $userId)
                               ->with(['ad'])
                               ->get();
    }

    /**
     * Get default trust score for user
     */
    private function getDefaultTrustScore($userId)
    {
        return TrustScore::firstOrCreate([
            'user_id' => $userId
        ], [
            'trust_score' => 50.00,
            'verification_level' => 'basic',
            'background_check_status' => 'pending',
        ]);
    }

    /**
     * Calculate return rate for a user
     */
    private function calculateReturnRate($userId)
    {
        $totalTransactions = \App\Models\Order::where('user_id', $userId)->count();
        $totalReturns = ReturnRequest::where('vendor_id', $userId)->count();

        if ($totalTransactions == 0) {
            return 0;
        }

        return ($totalReturns / $totalTransactions) * 100;
    }

    /**
     * Get return rate performance rating
     */
    private function getReturnRatePerformance($userId)
    {
        $returnRate = $this->calculateReturnRate($userId);
        
        if ($returnRate < 2) {
            return 'excellent'; // Less than 2% return rate
        } elseif ($returnRate < 5) {
            return 'good'; // 2-5% return rate
        } elseif ($returnRate < 10) {
            return 'average'; // 5-10% return rate
        } else {
            return 'needs_improvement'; // More than 10% return rate
        }
    }

    /**
     * Get trust score with return data
     */
    private function getTrustScoreWithReturns($userId)
    {
        $trustScore = TrustScore::firstOrCreate(['user_id' => $userId]);
        
        // Additional return-specific metrics
        $trustScore->return_rate = $this->calculateReturnRate($userId);
        $trustScore->return_performance = $this->getReturnRatePerformance($userId);
        
        return $trustScore;
    }

    /**
     * Get positive return handling rate
     */
    private function getPositiveReturnHandlingRate($userId)
    {
        $totalReturns = ReturnRequest::where('vendor_id', $userId)->count();
        $resolvedReturns = ReturnRequest::where('vendor_id', $userId)
                                       ->where('return_status', 'resolved')
                                       ->count();

        if ($totalReturns === 0) {
            return 0;
        }

        return ($resolvedReturns / $totalReturns) * 100;
    }

    /**
     * Get complaints vs returns ratio
     */
    private function getComplaintsVsReturns($userId)
    {
        $totalReturns = ReturnRequest::where('vendor_id', $userId)->count();
        $trustScore = TrustScore::where('user_id', $userId)->first();
        $totalComplaints = $trustScore->complaint_count ?? 0;

        return [
            'returns' => $totalReturns,
            'complaints' => $totalComplaints,
            'ratio' => $totalReturns > 0 ? round($totalComplaints / $totalReturns, 2) : 0,
        ];
    }

    /**
     * Get top return reasons
     */
    private function getTopReturnReasons($userId)
    {
        $reasons = ReturnRequest::where('vendor_id', $userId)
                               ->select('return_reason_category', \DB::raw('COUNT(*) as count'))
                               ->groupBy('return_reason_category')
                               ->orderBy('count', 'desc')
                               ->limit(5)
                               ->get();

        return $reasons->toArray();
    }

    /**
     * Get return trends
     */
    private function getReturnTrends($userId)
    {
        $trends = ReturnRequest::where('vendor_id', $userId)
                              ->selectRaw("
                                  DATE(created_at) as date,
                                  COUNT(*) as returns_count,
                                  SUM(refund_amount) as total_refunds
                              ")
                              ->where('created_at', '>', now()->subDays(30))
                              ->groupBy('date')
                              ->orderBy('date')
                              ->get();

        return $trends->toArray();
    }

    /**
     * Find nearest warehouse for return processing
     */
    private function findNearestWarehouseForReturn($deliveryAddress)
    {
        if (!isset($deliveryAddress['latitude'], $deliveryAddress['longitude'])) {
            return null;
        }

        $latitude = $deliveryAddress['latitude'];
        $longitude = $deliveryAddress['longitude'];

        return Warehouse::where('is_active', true)
                        ->where('is_operational', true)
                        ->nearby($latitude, $longitude, 50) // Within 50km
                        ->first();
    }
}