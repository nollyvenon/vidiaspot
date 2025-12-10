<?php

namespace App\Models\Logistics;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourierPartner extends BaseModel
{
    protected $fillable = [
        'name',
        'description',
        'logo_url',
        'website',
        'contact_phone',
        'contact_email',
        'address',
        'coverage_areas', // JSON array of coverage areas
        'service_types', // ['express', 'standard', 'eco', 'cold_chain']
        'delivery_timeframes', // {express: '24h', standard: '48h', economy: '72h'}
        'pricing_tiers', // {weight_tiers: [...], distance_tiers: [...]}
        'is_active',
        'is_verified',
        'rating',
        'total_shipments',
        'on_time_delivery_rate',
        'success_rate',
        'insurance_coverage_available',
        'insurance_max_value',
        'insurance_premium_rate', // % of package value
        'cold_chain_capabilities', // Can handle refrigerated shipments
        'fragile_handling',
        'specialized_vehicle_fleet',
        'real_time_tracking',
        'customer_support_available',
        'returns_management',
        'pickup_services',
        'delivery_windows', // {morning, afternoon, evening}
        'same_day_delivery_available',
        'next_day_delivery_available',
        'international_shipping',
        'warehousing_services',
        'integration_api_details',
        'api_endpoint',
        'api_key',
        'secret_key',
        'webhook_url',
        'commission_rate', // Platform commission from courier fees
        'preferred_partnership', // Preferred partner status
        'minimum_contract_period',
        'contract_terms',
        'sla_metrics', // Service Level Agreement metrics
        'performance_score',
        'last_performance_review',
        'next_review_date',
        'driver_verification_required',
        'driver_background_check',
        'vehicle_insurance_required',
        'driver_training_certified',
        'carbon_neutral_shipping',
        'green_fleet_percentage',
        'sustainability_certifications',
        'special_handling_certifications',
        'custom_fields',
        'metadata',
    ];

    protected $casts = [
        'coverage_areas' => 'array',
        'service_types' => 'array',
        'delivery_timeframes' => 'array',
        'pricing_tiers' => 'array',
        'is_active' => 'boolean',
        'is_verified' => 'boolean',
        'insurance_coverage_available' => 'boolean',
        'cold_chain_capabilities' => 'boolean',
        'fragile_handling' => 'boolean',
        'real_time_tracking' => 'boolean',
        'customer_support_available' => 'boolean',
        'returns_management' => 'boolean',
        'pickup_services' => 'boolean',
        'delivery_windows' => 'array',
        'same_day_delivery_available' => 'boolean',
        'next_day_delivery_available' => 'boolean',
        'international_shipping' => 'boolean',
        'warehousing_services' => 'boolean',
        'specialized_vehicle_fleet' => 'array',
        'custom_fields' => 'array',
        'metadata' => 'array',
        'rating' => 'decimal:2',
        'on_time_delivery_rate' => 'decimal:2',
        'success_rate' => 'decimal:2',
        'insurance_max_value' => 'decimal:2',
        'insurance_premium_rate' => 'decimal:2',
        'commission_rate' => 'decimal:2',
        'performance_score' => 'decimal:2',
        'last_performance_review' => 'datetime',
        'next_review_date' => 'datetime',
        'minimum_contract_period' => 'integer', // in months
        'driver_verification_required' => 'boolean',
        'driver_background_check' => 'boolean',
        'vehicle_insurance_required' => 'boolean',
        'driver_training_certified' => 'boolean',
        'carbon_neutral_shipping' => 'boolean',
        'green_fleet_percentage' => 'decimal:2', // Percentage of green vehicles
        'sustainability_certifications' => 'array',
        'special_handling_certifications' => 'array',
    ];

    /**
     * Get the delivery orders associated with this courier partner
     */
    public function deliveryOrders(): HasMany
    {
        return $this->hasMany(\App\Models\DeliveryOrder::class);
    }

    /**
     * Scope to get only active courier partners
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get by service type
     */
    public function scopeByServiceType($query, $serviceType)
    {
        return $query->whereJsonContains('service_types', $serviceType);
    }

    /**
     * Scope to get by coverage area
     */
    public function scopeByCoverageArea($query, $area)
    {
        return $query->whereJsonContains('coverage_areas', $area);
    }

    /**
     * Scope to filter by same-day delivery availability
     */
    public function scopeWithSameDay($query)
    {
        return $query->where('same_day_delivery_available', true);
    }

    /**
     * Scope to filter by cold chain capability
     */
    public function scopeWithColdChain($query)
    {
        return $query->where('cold_chain_capabilities', true);
    }

    /**
     * Scope to filter by international shipping
     */
    public function scopeWithInternational($query)
    {
        return $query->where('international_shipping', true);
    }

    /**
     * Check if courier partner can deliver to a specific area
     */
    public function canDeliverTo($area): bool
    {
        $coverageAreas = $this->coverage_areas ?? [];
        return empty($coverageAreas) || in_array($area, $coverageAreas);
    }

    /**
     * Check if courier partner offers a specific service
     */
    public function offersService($serviceType): bool
    {
        $serviceTypes = $this->service_types ?? [];
        return in_array($serviceType, $serviceTypes);
    }

    /**
     * Get pricing for a specific shipment
     */
    public function calculatePricing($weight, $distance, $serviceType = 'standard', $isInternational = false)
    {
        $baseRate = 100; // Base rate in smallest currency unit (kobo for NGN)

        // Distance-based rate
        $distanceRate = $distance * 10; // 10 kobo per km

        // Weight-based rate
        $weightRate = $weight * 50; // 50 kobo per kg

        // Service type multiplier
        $serviceMultiplier = 1.0;
        if ($serviceType === 'express') {
            $serviceMultiplier = 1.8;
        } elseif ($serviceType === 'premium') {
            $serviceMultiplier = 2.0;
        } elseif ($serviceType === 'cold_chain') {
            $serviceMultiplier = 1.5;
        }

        // International shipping multiplier
        $internationalMultiplier = $isInternational ? 3.0 : 1.0;

        $total = ($baseRate + $distanceRate + $weightRate) * $serviceMultiplier * $internationalMultiplier;

        return [
            'base_rate' => $baseRate,
            'distance_charge' => $distanceRate,
            'weight_charge' => $weightRate,
            'service_multiplier' => $serviceMultiplier,
            'international_multiplier' => $internationalMultiplier,
            'subtotal' => $total / 100, // Convert back to currency units (NGN)
            'vat' => ($total * 0.075) / 100, // 7.5% VAT in Nigeria
            'total' => ($total * 1.075) / 100, // With VAT
        ];
    }

    /**
     * Check if this courier partner supports cold chain
     */
    public function supportsColdChain(): bool
    {
        return $this->cold_chain_capabilities ?? false;
    }

    /**
     * Get SLA metrics for this courier partner
     */
    public function getSLAMetrics()
    {
        return [
            'on_time_delivery_rate' => $this->on_time_delivery_rate,
            'success_rate' => $this->success_rate,
            'average_delivery_time' => $this->delivery_timeframes['standard'] ?? '48h',
            'customer_satisfaction' => $this->rating,
            'claims_rate' => $this->calculateClaimsRate(),
        ];
    }

    /**
     * Calculate claims rate
     */
    private function calculateClaimsRate()
    {
        if ($this->total_shipments == 0) {
            return 0;
        }

        // In a real implementation, this would come from claim records
        // For simulation, return a reasonable average
        return round((mt_rand(1, 5) / 100) * $this->total_shipments, 2);
    }

    /**
     * Calculate commission for this partner based on transaction value
     */
    public function calculateCommission($transactionValue)
    {
        $commissionRate = $this->commission_rate ?? 5; // Default 5%
        return ($transactionValue * $commissionRate) / 100;
    }

    /**
     * Check if supports special handling
     */
    public function supportsSpecialHandling()
    {
        return ($this->fragile_handling ?? false) || ($this->specialized_vehicle_fleet && count($this->specialized_vehicle_fleet) > 0);
    }
}
