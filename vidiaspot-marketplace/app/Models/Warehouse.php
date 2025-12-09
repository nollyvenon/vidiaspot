<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Warehouse extends Model
{
    protected $fillable = [
        'user_id', // Owner of the warehouse (for multi-vendor support)
        'name',
        'description',
        'address_json', // {street, city, state, country, postal_code}
        'latitude',
        'longitude',
        'contact_person',
        'contact_phone',
        'contact_email',
        'warehouse_type', // 'private', 'public', 'fulfillment', 'storage', 'consolidation'
        'capacity_sqm', // Square meter capacity
        'capacity_utilization_percentage', // Current utilization percentage
        'max_item_types', // Maximum number of different item types
        'max_items_count', // Maximum number of items
        'operating_hours_json', // {monday: {open, close}, ...}
        'is_operational', // Whether warehouse is currently operational
        'is_public', // Whether other users can use this warehouse
        'service_types', // ['fulfillment', 'storage', 'pick_and_pack', 'shipping']
        'available_services', // Specific services offered ['cold_storage', 'fragile_handling', 'oversized_items', 'hazardous_materials']
        'supported_carriers', // ['fedex', 'ups', 'dhl', 'local_carriers']
        'specialized_equipment', // ['forklift', 'crane', 'refrigeration', 'security_cameras']
        'security_features', // ['24_7_security', 'cameras', 'alarms', 'guards']
        'access_restrictions', // ['appointment_required', 'business_hours_only', 'id_required']
        'fees_structure_json', // Fee structure for different services
        'minimum_contract_period', // Minimum period for contracts
        'contract_terms', // Terms and conditions
        'insurance_coverage_available',
        'insurance_max_coverage',
        'insurance_rate_percentage', // Rate as percentage of stored items value
        'insurance_providers', // List of approved insurance providers
        'environmental_conditions_json', // Temperature, humidity controls
        'loading_docks_count',
        'dock_doors_type', // ['ground_level', 'ramp', 'dock_high']
        'storage_options_json', // ['pallet_racks', 'floor_space', 'refrigerated', 'climate_controlled']
        'technology_integration', // ['wms', 'edi', 'api_connected', 'rfid_enabled']
        'inventory_sync_enabled', // Whether inventory sync with seller platforms is enabled
        'multi_vendor_support', // Whether warehouse supports multiple sellers
        'returns_processing_capability',
        'returns_processing_fee',
        'packaging_services_available',
        'packaging_service_fee',
        'value_added_services', // ['assembly', 'kitting', 'labeling', 'barcoding']
        'value_added_service_fees_json', // Fees for value-added services
        'quality_check_services',
        'quality_check_fee',
        'shipping_integration_enabled',
        'shipping_carriers_integration',
        'shipping_software_connected',
        'tracking_software_connected',
        'is_verified', // Verified by platform
        'verification_date',
        'verification_status', // 'pending', 'verified', 'flagged'
        'verification_details',
        'rating', // Average rating from users
        'total_reviews',
        'performance_metrics_json', // {on_time_delivery, accuracy_rate, damage_rate}
        'safety_ratings_json', // Safety compliance ratings
        'certifications', // ['iso_9001', 'iso_14001', 'oeko_tex', etc.]
        'compliance_standards', // ['fire_safety', 'environmental', 'labor', 'security']
        'emergency_procedures', // Emergency protocols
        'insurance_coverage_details', // Details about coverage
        'liability_limits', // Liability limitations
        'accessibility_features', // ADA compliance features
        'environmental_compliance', // Environmental compliance status
        'special_handling_capabilities', // ['hazmat', 'fragile', 'temperature_controlled', 'oversized', 'dangerous_goods']
        'custom_clearance_support', // Support for customs clearance
        'customs_broker_affiliation', // Affiliated customs broker
        'bonded_warehouse', // Whether it's a bonded warehouse
        'customs_license_number',
        'freight_forwarding_capability',
        'freight_forwarding_license',
        'cross_docking_capability',
        'picking_accuracy_rate',
        'inventory_accuracy_rate',
        'turnaround_time_hours',
        'warehouse_management_system', // Type of WMS used
        'integration_api_endpoint',
        'integration_api_key',
        'integration_webhook_url',
        'api_rate_limit', // API rate limit per minute
        'status', // 'active', 'maintenance', 'closed'
        'opening_date',
        'last_inspection_date',
        'next_inspection_date',
        'last_audit_date',
        'next_audit_date',
        'maintenance_schedule_json',
        'backup_power_available', // UPS/diesel generator availability
        'fire_suppression_system', // Type of fire suppression system
        'access_control_system', // Type of access control system
        'employee_count',
        'supervisor_count',
        'supervisor_name',
        'warehouse_manager_id',
        'manager_contact_phone',
        'manager_contact_email',
        'backup_manager_id',
        'backup_manager_name',
        'backup_manager_contact',
        'insurance_claims_history_json', // History of insurance claims
        'incident_report_history_json', // History of incidents
        'safety_incident_count',
        'safety_training_completed',
        'certification_expiry_dates_json', // Expiry dates for certifications
        'custom_fields',
        'metadata',
    ];

    protected $casts = [
        'address_json' => 'array',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'operating_hours_json' => 'array',
        'service_types' => 'array',
        'available_services' => 'array',
        'supported_carriers' => 'array',
        'specialized_equipment' => 'array',
        'security_features' => 'array',
        'access_restrictions' => 'array',
        'fees_structure_json' => 'array',
        'minimum_contract_period' => 'integer',
        'insurance_coverage_available' => 'boolean',
        'insurance_max_coverage' => 'decimal:2',
        'insurance_rate_percentage' => 'decimal:2',
        'insurance_providers' => 'array',
        'environmental_conditions_json' => 'array',
        'loading_docks_count' => 'integer',
        'storage_options_json' => 'array',
        'technology_integration' => 'array',
        'inventory_sync_enabled' => 'boolean',
        'multi_vendor_support' => 'boolean',
        'returns_processing_capability' => 'boolean',
        'returns_processing_fee' => 'decimal:2',
        'packaging_services_available' => 'boolean',
        'packaging_service_fee' => 'decimal:2',
        'value_added_services' => 'array',
        'value_added_service_fees_json' => 'array',
        'quality_check_services' => 'boolean',
        'quality_check_fee' => 'decimal:2',
        'shipping_integration_enabled' => 'boolean',
        'shipping_carriers_integration' => 'array',
        'shipping_software_connected' => 'array',
        'tracking_software_connected' => 'array',
        'is_verified' => 'boolean',
        'verification_date' => 'datetime',
        'verification_status' => 'string',
        'verification_details' => 'array',
        'rating' => 'decimal:2',
        'total_reviews' => 'integer',
        'performance_metrics_json' => 'array',
        'safety_ratings_json' => 'array',
        'certifications' => 'array',
        'compliance_standards' => 'array',
        'emergency_procedures' => 'array',
        'insurance_coverage_details' => 'array',
        'liability_limits' => 'decimal:2',
        'accessibility_features' => 'array',
        'environmental_compliance' => 'array',
        'special_handling_capabilities' => 'array',
        'custom_clearance_support' => 'boolean',
        'bonded_warehouse' => 'boolean',
        'freight_forwarding_capability' => 'boolean',
        'cross_docking_capability' => 'boolean',
        'picking_accuracy_rate' => 'decimal:2',
        'inventory_accuracy_rate' => 'decimal:2',
        'turnaround_time_hours' => 'integer',
        'api_rate_limit' => 'integer',
        'status' => 'string',
        'opening_date' => 'date',
        'last_inspection_date' => 'date',
        'next_inspection_date' => 'date',
        'last_audit_date' => 'date',
        'next_audit_date' => 'date',
        'maintenance_schedule_json' => 'array',
        'backup_power_available' => 'boolean',
        'employee_count' => 'integer',
        'supervisor_count' => 'integer',
        'safety_incident_count' => 'integer',
        'safety_training_completed' => 'boolean',
        'certification_expiry_dates_json' => 'array',
        'insurance_claims_history_json' => 'array',
        'incident_report_history_json' => 'array',
        'custom_fields' => 'array',
        'metadata' => 'array',
    ];

    /**
     * Get the user who owns this warehouse
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get inventory items stored in this warehouse
     */
    public function inventoryItems(): HasMany
    {
        return $this->hasMany(\App\Models\InventoryItem::class);
    }

    /**
     * Get orders processed by this warehouse
     */
    public function processedOrders(): HasMany
    {
        return $this->hasMany(\App\Models\DeliveryOrder::class, 'warehouse_id');
    }

    /**
     * Scope to get operational warehouses
     */
    public function scopeOperational($query)
    {
        return $query->where('is_operational', true)
                    ->where('status', 'active');
    }

    /**
     * Scope to get warehouses by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('warehouse_type', $type);
    }

    /**
     * Scope to get warehouses with specific services
     */
    public function scopeWithServices($query, $service)
    {
        return $query->whereJsonContains('service_types', $service);
    }

    /**
     * Scope to get warehouses with specific facilities
     */
    public function scopeWithFacilities($query, $facility)
    {
        return $query->whereJsonContains('available_services', $facility);
    }

    /**
     * Scope to get warehouses by location
     */
    public function scopeNearby($query, $latitude, $longitude, $radiusKm = 10)
    {
        $earthRadius = 6371; // Earth radius in km

        return $query->selectRaw("
            *,
            ({$earthRadius} * acos(
                cos(radians(?)) *
                cos(radians(latitude)) *
                cos(radians(longitude) - radians(?)) +
                sin(radians(?)) *
                sin(radians(latitude))
            )) AS distance
        ", [$latitude, $longitude, $latitude])
        ->havingRaw("distance < ?", [$radiusKm])
        ->orderBy('distance');
    }

    /**
     * Get warehouse utilization percentage
     */
    public function getUtilizationPercentageAttribute(): float
    {
        return $this->capacity_utilization_percentage ?? 0;
    }

    /**
     * Check if warehouse is available for new contracts
     */
    public function isAvailable(): bool
    {
        return $this->is_operational &&
               $this->status === 'active' &&
               $this->capacity_utilization_percentage < 90; // Available if less than 90% utilized
    }

    /**
     * Get available capacity
     */
    public function getAvailableCapacityAttribute(): float
    {
        if (!$this->capacity_sqm) {
            return 0;
        }

        $utilization = $this->capacity_utilization_percentage ?? 0;
        return $this->capacity_sqm * (1 - ($utilization / 100));
    }

    /**
     * Check if warehouse provides fulfillment services
     */
    public function providesFulfillmentServices(): bool
    {
        return in_array('fulfillment', $this->service_types ?? []);
    }

    /**
     * Check if warehouse has cold storage capabilities
     */
    public function hasColdStorage(): bool
    {
        return in_array('cold_storage', $this->available_services ?? []);
    }

    /**
     * Check if warehouse supports returns processing
     */
    public function supportsReturnsProcessing(): bool
    {
        return $this->returns_processing_capability && in_array('returns_processing', $this->service_types ?? []);
    }

    /**
     * Check if warehouse is ISO certified
     */
    public function isCertified(): bool
    {
        $certifications = $this->certifications ?? [];
        return !empty($certifications) && in_array('iso_9001', $certifications);
    }

    /**
     * Calculate storage cost for an item
     */
    public function calculateStorageCost($itemSizeSqM, $durationDays, $itemValue = 0)
    {
        $baseRate = 50; // Base rate per sqm per month
        $monthlyCost = $baseRate * $itemSizeSqM;

        // Calculate daily rate
        $dailyRate = $monthlyCost / 30;
        $totalCost = $dailyRate * $durationDays;

        // Add insurance based on item value
        if ($itemValue > 0) {
            $insuranceRate = $this->insurance_rate_percentage ?? 0.1; // Default 0.1%
            $insuranceCost = ($itemValue * $insuranceRate) / 100;
            $totalCost += $insuranceCost;
        }

        return $totalCost;
    }
}
