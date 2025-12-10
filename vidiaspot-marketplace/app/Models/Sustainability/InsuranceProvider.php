<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InsuranceProvider extends Model
{
    protected $fillable = [
        'name',
        'description',
        'logo_url',
        'website',
        'phone',
        'email',
        'address',
        'license_number',
        'registration_number',
        'claim_settlement_ratio',
        'rating',
        'is_active',
        'categories', // json array of insurance categories they offer
        'features', // json array of features
        'coverage_areas', // json array of coverage areas
        'min_premium',
        'max_premium',
        'min_coverage',
        'max_coverage',
        'specializations', // json array of specializations
        'network_partners', // json array of network partners
    ];

    protected $casts = [
        'categories' => 'array',
        'features' => 'array',
        'coverage_areas' => 'array',
        'specializations' => 'array',
        'network_partners' => 'array',
        'claim_settlement_ratio' => 'decimal:2',
        'rating' => 'decimal:2',
        'min_premium' => 'decimal:2',
        'max_premium' => 'decimal:2',
        'min_coverage' => 'decimal:2',
        'max_coverage' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get policies associated with this provider
     */
    public function policies()
    {
        return $this->hasMany(InsurancePolicy::class, 'provider_id');
    }

    /**
     * Scope to get only active providers
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter by insurance category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->whereJsonContains('categories', $category);
    }

    /**
     * Scope to filter by coverage area
     */
    public function scopeByArea($query, $area)
    {
        return $query->whereJsonContains('coverage_areas', $area);
    }
}
