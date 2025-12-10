<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FoodVendor extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'cuisine_type',
        'address',
        'latitude',
        'longitude',
        'contact_phone',
        'contact_email',
        'opening_time',
        'closing_time',
        'delivery_radius',
        'min_order_amount',
        'delivery_fee',
        'estimated_delivery_time',
        'image_url',
        'is_active',
        'is_verified',
        'rating',
        'total_reviews',
        'payment_methods',
        'dietary_options',
        'special_offers',
        'operating_days',
        'tax_percentage',
        'commission_rate',
        'accepting_orders',
        'vendor_settings',
    ];

    protected $casts = [
        'address' => 'array',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'opening_time' => 'datetime:H:i',
        'closing_time' => 'datetime:H:i',
        'estimated_delivery_time' => 'integer', // in minutes
        'delivery_radius' => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'is_active' => 'boolean',
        'is_verified' => 'boolean',
        'accepting_orders' => 'boolean',
        'rating' => 'decimal:2',
        'total_reviews' => 'integer',
        'payment_methods' => 'array',
        'dietary_options' => 'array',
        'special_offers' => 'array',
        'operating_days' => 'array',
        'tax_percentage' => 'decimal:2',
        'commission_rate' => 'decimal:2',
        'vendor_settings' => 'array',
    ];

    /**
     * Get the user who owns this food vendor
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get menu items for this vendor
     */
    public function menuItems(): HasMany
    {
        return $this->hasMany(FoodMenuItem::class);
    }

    /**
     * Get orders for this vendor
     */
    public function orders(): HasMany
    {
        return $this->hasMany(FoodOrder::class);
    }

    /**
     * Scope to get only active vendors
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter by cuisine type
     */
    public function scopeByCuisine($query, $cuisineType)
    {
        return $query->where('cuisine_type', $cuisineType);
    }

    /**
     * Scope to filter by location/radius
     */
    public function scopeByLocation($query, $latitude, $longitude, $radiusKm = 10)
    {
        // Calculate the bounding box for the radius
        $latRange = $radiusKm / 111; // Approximate km per degree latitude
        $lngRange = $radiusKm / (111 * cos(deg2rad($latitude))); // Adjust for longitude

        return $query->whereBetween('latitude', [$latitude - $latRange, $latitude + $latRange])
                     ->whereBetween('longitude', [$longitude - $lngRange, $longitude + $lngRange]);
    }
}
