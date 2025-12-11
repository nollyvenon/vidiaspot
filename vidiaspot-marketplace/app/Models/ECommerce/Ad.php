<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ad extends Model
{
    use HasFactory;

    protected $fillable = [
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
        // Additional farm-specific attributes
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
        'price' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'negotiable' => 'boolean',
        'view_count' => 'integer',
        'expires_at' => 'datetime',
        'direct_from_farm' => 'boolean',
        'is_organic' => 'boolean',
        'harvest_date' => 'date',
        'farm_latitude' => 'decimal:8',
        'farm_longitude' => 'decimal:8',
        'farm_size' => 'decimal:2',
        // Additional farm-specific casts
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
        'farmer_image' => 'string',
        'farm_certifications' => 'array',
        'pesticide_use' => 'boolean',
        'supply_capacity' => 'integer', // units per day/week
        'local_delivery_radius' => 'decimal:2', // in km
    ];

    // Relationship: Ad belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relationship: Ad belongs to a category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Relationship: Ad has many images (to be defined later)
    public function images()
    {
        return $this->hasMany(AdImage::class);
    }

    // Relationship: Ad belongs to a currency
    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_code', 'code');
    }

    // Accessor: Get the formatted price with currency symbol
    public function getFormattedPriceAttribute()
    {
        $currency = $this->currency;
        if ($currency) {
            return $currency->formatAmount($this->price);
        }

        // Fallback if no currency is set
        return 'NGN ' . number_format($this->price, 2);
    }

    /**
     * Convert the price to a different currency.
     *
     * @param string $toCurrencyCode
     * @return string
     */
    public function convertPriceTo($toCurrencyCode)
    {
        $currencyService = new \App\Services\CurrencyService();

        try {
            $convertedAmount = $currencyService->convert($this->price, $this->currency_code, $toCurrencyCode);
            return $currencyService->format($convertedAmount, $toCurrencyCode);
        } catch (\Exception $e) {
            // If conversion fails, return the original price with original currency
            return $this->formatted_price;
        }
    }

    // Relationship: Ad has many custom fields
    public function customFields()
    {
        return $this->hasMany(CustomAdField::class);
    }

    // Relationship: Ad may have one insurance policy
    public function insurancePolicy()
    {
        return $this->hasOne(InsurancePolicy::class);
    }
}
