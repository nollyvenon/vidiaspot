<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vendor extends Model
{
    protected $fillable = [
        'user_id',
        'business_name',
        'business_email',
        'business_phone',
        'business_description',
        'business_type',
        'business_registration_number',
        'logo_url',
        'banner_url',
        'country_id',
        'state_id',
        'city_id',
        'address',
        'status',
        'approved_at',
        'approved_by',
        'documents',
        'is_verified',
        'rating',
        'total_sales',
        'is_featured',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'country_id' => 'integer',
        'state_id' => 'integer',
        'city_id' => 'integer',
        'approved_by' => 'integer',
        'is_verified' => 'boolean',
        'is_featured' => 'boolean',
        'rating' => 'decimal:2',
        'total_sales' => 'integer',
        'documents' => 'array',
        'approved_at' => 'datetime',
    ];

    /**
     * Get the user associated with this vendor.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the country of this vendor.
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Get the state of this vendor.
     */
    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    /**
     * Get the city of this vendor.
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Get the ads associated with this vendor.
     */
    public function ads(): HasMany
    {
        return $this->hasMany(Ad::class, 'user_id', 'user_id'); // Ads by the vendor's user
    }

    /**
     * Get the featured ads for this vendor.
     */
    public function featuredAds(): HasMany
    {
        return $this->hasMany(FeaturedAd::class);
    }

    /**
     * Scope to get vendors by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to get approved vendors.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope to get featured vendors.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }
}
