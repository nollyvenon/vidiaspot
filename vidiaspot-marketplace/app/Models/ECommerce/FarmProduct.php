<?php

namespace App\Models\ECommerce;

use App\Models\Ad;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FarmProduct extends Ad
{
    use HasFactory;

    protected $table = 'ads'; // Inherit from ads table

    protected $fillable = [
        // Inherited from Ad
        'user_id',
        'category_id',
        'title',
        'description',
        'price',
        'currency_code',
        'condition',
        'status',
        'location',
        'latitude',
        'longitude',
        'contact_phone',
        'negotiable',
        'view_count',
        'expires_at',
        
        // Farm-specific attributes
        'direct_from_farm',
        'farm_name',
        'is_organic',
        'harvest_date',
        'farm_location',
        'farm_latitude',
        'farm_longitude',
        'certification',
        'harvest_season',
        'farm_size',
        
        // Extended farm-specific attributes
        'freshness_days',
        'quality_rating',
        'seasonal_availability',
        'certification_type',
        'certification_body',
        'farm_practices',
        'delivery_options',
        'minimum_order',
        'packaging_type',
        'shelf_life',
        'storage_instructions',
        'farm_certifications',
        'pesticide_use',
        'irrigation_method',
        'soil_type',
        'sustainability_score',
        'carbon_footprint',
        'farm_tour_available',
        'farm_story',
        'farmer_name',
        'farmer_image',
        'farmer_bio',
        'harvest_method',
        'post_harvest_handling',
        'supply_capacity',
        'shipping_availability',
        'local_delivery_radius',
    ];

    protected $casts = [
        // Inherited from Ad
        'price' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'negotiable' => 'boolean',
        'view_count' => 'integer',
        'expires_at' => 'datetime',
        
        // Farm-specific casts
        'direct_from_farm' => 'boolean',
        'is_organic' => 'boolean',
        'harvest_date' => 'date',
        'farm_latitude' => 'decimal:8',
        'farm_longitude' => 'decimal:8',
        'farm_size' => 'decimal:2',
        
        // Extended farm-specific casts
        'freshness_days' => 'integer',
        'quality_rating' => 'decimal:2',
        'seasonal_availability' => 'array',
        'farm_practices' => 'array',
        'delivery_options' => 'array',
        'minimum_order' => 'decimal:2',
        'shelf_life' => 'integer', // in days
        'sustainability_score' => 'decimal:2', // 0-10 scale
        'carbon_footprint' => 'decimal:2', // in kg CO2 equivalent
        'farm_tour_available' => 'boolean',
        'farm_certifications' => 'array',
        'pesticide_use' => 'boolean',
        'supply_capacity' => 'integer', // units per day/week
        'local_delivery_radius' => 'decimal:2', // in km
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Scope to get only direct farm products
     */
    public function scopeDirectFarm($query)
    {
        return $query->where('direct_from_farm', true);
    }

    /**
     * Scope to get only organic products
     */
    public function scopeOrganic($query)
    {
        return $query->where('is_organic', true);
    }

    /**
     * Scope to filter by harvest season
     */
    public function scopeBySeason($query, $season)
    {
        return $query->where('harvest_season', $season);
    }

    /**
     * Scope to filter by farm location region
     */
    public function scopeByFarmLocation($query, $location)
    {
        return $query->where('farm_location', 'like', "%{$location}%");
    }

    /**
     * Scope to filter fresh products (within X days)
     */
    public function scopeFresh($query, $maxDays = 7)
    {
        return $query->where('freshness_days', '<=', $maxDays);
    }

    /**
     * Scope to filter by sustainability score
     */
    public function scopeHighSustainability($query, $minScore = 7.0)
    {
        return $query->where('sustainability_score', '>=', $minScore);
    }

    /**
     * Get the farm products near a given location
     */
    public function scopeNearLocation($query, $lat, $lng, $radius = 50)
    {
        return $query->selectRaw("
            *,
            (6371 * acos(
                cos(radians(?)) * 
                cos(radians(farm_latitude)) * 
                cos(radians(farm_longitude) - radians(?)) + 
                sin(radians(?)) * 
                sin(radians(farm_latitude))
            )) AS distance", [$lat, $lng, $lat])
            ->whereNotNull('farm_latitude')  // Only include products with farm location
            ->whereNotNull('farm_longitude')
            ->whereRaw("
                (6371 * acos(
                    cos(radians(?)) * 
                    cos(radians(farm_latitude)) * 
                    cos(radians(farm_longitude) - radians(?)) + 
                    sin(radians(?)) * 
                    sin(radians(farm_latitude))
                )) <= ?", [$lat, $lng, $lat, $radius]);
    }
}