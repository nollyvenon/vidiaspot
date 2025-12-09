<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentSetting extends Model
{
    protected $fillable = [
        'feature_key',
        'feature_name',
        'feature_type',
        'is_enabled',
        'available_countries',
        'configuration',
        'description',
        'sort_order',
    ];

    protected $casts = [
        'available_countries' => 'array',
        'configuration' => 'array',
        'is_enabled' => 'boolean',
        'sort_order' => 'integer',
    ];

    public static function getFeatureStatus(string $featureKey): bool
    {
        $setting = self::where('feature_key', $featureKey)->first();
        return $setting ? $setting->is_enabled : true; // Default to enabled if not found
    }

    public static function isAvailableInCountry(string $featureKey, string $countryCode): bool
    {
        $setting = self::where('feature_key', $featureKey)->first();

        if (!$setting) {
            return true; // Default to available if not configured
        }

        // If no countries are specified, assume feature is available globally
        if (empty($setting->available_countries)) {
            return $setting->is_enabled;
        }

        return $setting->is_enabled && in_array($countryCode, $setting->available_countries);
    }

    public static function getEnabledFeatures(): array
    {
        return self::where('is_enabled', true)->get()->toArray();
    }

    public static function getEnabledFeaturesForCountry(string $countryCode): array
    {
        $settings = self::where('is_enabled', true)->get();

        return $settings->filter(function ($setting) use ($countryCode) {
            // If no countries specified, it's available globally
            if (empty($setting->available_countries)) {
                return true;
            }

            return in_array($countryCode, $setting->available_countries);
        })->toArray();
    }
}
