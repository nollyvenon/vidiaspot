<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    protected $fillable = [
        'user_id',
        'location_name',
        'address_line1',
        'address_line2',
        'city',
        'state',
        'country',
        'postal_code',
        'latitude',
        'longitude',
        'location_type', // 'residential', 'commercial', 'warehouse', 'pickup_point', 'delivery_hub'
        'is_primary',
        'is_active',
        'geofence_radius', // In meters
        'delivery_zone',
        'operating_hours',
        'contact_person',
        'contact_phone',
        'contact_email',
        'indoor_map_data', // JSON for indoor mapping
        'floor_plan', // Floor plan information
        'aisle_positions', // Product aisle positions for indoor mapping
        'coordinates_precision', // GPS precision level
        'altitude', // Altitude in meters
        'timezone',
        'location_metadata', // Additional location-specific metadata
        'delivery_availability', // {same_day: true, next_day: true, time_slots: []}
        'cold_chain_supported', // Whether cold chain storage is available
        'max_package_size', // Maximum package dimensions
        'max_package_weight', // Maximum package weight
        'special_handling_available', // Special handling options
        'warehouse_capacity', // For warehouse locations
        'available_slot_times', // Available time slots for deliveries
        'last_updated',
        'custom_fields',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'altitude' => 'decimal:2',
        'is_primary' => 'boolean',
        'is_active' => 'boolean',
        'geofence_radius' => 'decimal:2',
        'operating_hours' => 'array',
        'indoor_map_data' => 'array',
        'floor_plan' => 'array',
        'aisle_positions' => 'array',
        'location_metadata' => 'array',
        'delivery_availability' => 'array',
        'cold_chain_supported' => 'boolean',
        'max_package_size' => 'array',
        'max_package_weight' => 'decimal:2',
        'special_handling_available' => 'array',
        'warehouse_capacity' => 'array',
        'available_slot_times' => 'array',
        'last_updated' => 'datetime',
        'custom_fields' => 'array',
        'coordinates_precision' => 'string',
        'timezone' => 'string',
    ];

    /**
     * Location types
     */
    const LOCATION_TYPES = [
        'residential' => 'Residential Address',
        'commercial' => 'Commercial Address',
        'warehouse' => 'Warehouse',
        'pickup_point' => 'Pickup Point',
        'delivery_hub' => 'Delivery Hub',
        'marketplace' => 'Marketplace',
        'retail_outlet' => 'Retail Outlet',
    ];

    /**
     * Get the user who owns this location
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get delivery orders associated with this location
     */
    public function deliveryOrders(): HasMany
    {
        return $this->hasMany(DeliveryOrder::class, 'delivery_address_id');
    }

    /**
     * Get pickup orders associated with this location
     */
    public function pickupOrders(): HasMany
    {
        return $this->hasMany(DeliveryOrder::class, 'pickup_address_id');
    }

    /**
     * Scope to get only active locations
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get by location type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('location_type', $type);
    }

    /**
     * Scope to get by geographic coordinates
     */
    public function scopeNearby($query, $latitude, $longitude, $radius = 10) // Radius in kilometers
    {
        // Haversine formula to calculate distance
        $earthRadius = 6371; // Earth radius in kilometers

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
        ->havingRaw("distance < ?", [$radius])
        ->orderBy('distance');
    }

    /**
     * Scope to get only primary locations
     */
    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    /**
     * Scope to get by country
     */
    public function scopeByCountry($query, $country)
    {
        return $query->where('country', $country);
    }

    /**
     * Scope to get by delivery zone
     */
    public function scopeByZone($query, $zone)
    {
        return $query->where('delivery_zone', $zone);
    }

    /**
     * Check if location is within cold chain support
     */
    public function supportsColdChain(): bool
    {
        return $this->cold_chain_supported ?? false;
    }

    /**
     * Check if location supports special handling
     */
    public function supportsSpecialHandling(): bool
    {
        return !empty($this->special_handling_available);
    }

    /**
     * Check if location supports same-day delivery
     */
    public function supportsSameDayDelivery(): bool
    {
        $availability = $this->delivery_availability ?? [];
        return $availability['same_day'] ?? false;
    }

    /**
     * Get available delivery time slots
     */
    public function getAvailableDeliverySlots(): array
    {
        $slots = $this->available_slot_times ?? [];
        $currentHour = now()->hour;

        // Filter out past time slots for today
        $filteredSlots = [];
        foreach ($slots as $slot) {
            $slotHour = substr($slot['start_time'], 0, 2);
            if ($slotHour >= $currentHour || date('Y-m-d') != now()->format('Y-m-d')) {
                $filteredSlots[] = $slot;
            }
        }

        return $filteredSlots;
    }

    /**
     * Check if location is currently open based on operating hours
     */
    public function isOpenNow(): bool
    {
        $operatingHours = $this->operating_hours ?? [];
        $currentDay = strtolower(now()->format('l')); // e.g., 'monday', 'tuesday'
        $currentTime = now()->format('H:i');

        if (!isset($operatingHours[$currentDay])) {
            return false; // Closed on this day
        }

        $hours = $operatingHours[$currentDay];
        if (!isset($hours['open']) || !isset($hours['close'])) {
            return false;
        }

        return $currentTime >= $hours['open'] && $currentTime <= $hours['close'];
    }

    /**
     * Calculate distance to another location in kilometers
     */
    public function calculateDistanceTo(Location $otherLocation): float
    {
        $earthRadius = 6371; // Earth radius in kilometers

        $latFrom = deg2rad($this->latitude);
        $lonFrom = deg2rad($this->longitude);
        $latTo = deg2rad($otherLocation->latitude);
        $lonTo = deg2rad($otherLocation->longitude);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

        return $angle * $earthRadius;
    }
}
