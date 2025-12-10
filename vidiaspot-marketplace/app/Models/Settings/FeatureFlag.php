<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeatureFlag extends Model
{
    protected $fillable = [
        'feature_key',
        'name',
        'description',
        'is_enabled',
        'allowed_countries',
        'allowed_states',
        'allowed_cities',
        'config',
        'starts_at',
        'expires_at',
    ];

    protected $casts = [
        'allowed_countries' => 'array',
        'allowed_states' => 'array',
        'allowed_cities' => 'array',
        'config' => 'array',
        'is_enabled' => 'boolean',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * Check if a feature is available for the given location
     */
    public static function isFeatureAvailable($featureKey, $country = null, $state = null, $city = null)
    {
        $feature = self::where('feature_key', $featureKey)->first();

        if (!$feature) {
            return false;
        }

        if (!$feature->is_enabled) {
            return false;
        }

        // Check activation dates
        if ($feature->starts_at && now()->lt($feature->starts_at)) {
            return false;
        }

        if ($feature->expires_at && now()->gt($feature->expires_at)) {
            return false;
        }

        // Check regional restrictions
        if ($feature->allowed_countries && $country && !in_array($country, $feature->allowed_countries)) {
            return false;
        }

        if ($feature->allowed_states && $state && !in_array($state, $feature->allowed_states)) {
            return false;
        }

        if ($feature->allowed_cities && $city && !in_array($city, $feature->allowed_cities)) {
            return false;
        }

        return true;
    }

    /**
     * Scope to get only enabled features
     */
    public function scopeEnabled($query)
    {
        return $query->where('is_enabled', true);
    }

    /**
     * Scope to filter by location
     */
    public function scopeByLocation($query, $country = null, $state = null, $city = null)
    {
        if ($country) {
            $query = $query->where(function($q) use ($country) {
                $q->whereNull('allowed_countries')
                  ->orWhereJsonContains('allowed_countries', $country);
            });
        }

        if ($state) {
            $query = $query->where(function($q) use ($state) {
                $q->whereNull('allowed_states')
                  ->orWhereJsonContains('allowed_states', $state);
            });
        }

        if ($city) {
            $query = $query->where(function($q) use ($city) {
                $q->whereNull('allowed_cities')
                  ->orWhereJsonContains('allowed_cities', $city);
            });
        }

        return $query;
    }
}
