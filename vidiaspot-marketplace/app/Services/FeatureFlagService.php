<?php

namespace App\Services;

use App\Models\FeatureFlag;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class FeatureFlagService
{
    private const CACHE_TTL = 300; // 5 minutes

    /**
     * Check if a feature is available for the current user/location
     *
     * @param string $featureKey The feature key to check
     * @param string|null $country Optional country code
     * @param string|null $state Optional state/province
     * @param string|null $city Optional city
     * @param int|null $userId Optional user ID to check against
     * @return bool Whether the feature is available
     */
    public function isFeatureAvailable(string $featureKey, string $country = null, string $state = null, string $city = null, int $userId = null): bool
    {
        // Use cache to improve performance
        $cacheKey = "feature_flag_{$featureKey}_{$country}_{$state}_{$city}_{$userId}";
        $cacheKey = md5($cacheKey); // To ensure valid cache key

        return Cache::remember($cacheKey, self::CACHE_TTL, function() use ($featureKey, $country, $state, $city, $userId) {
            $isAvailable = FeatureFlag::isFeatureAvailable($featureKey, $country, $state, $city);

            // If the feature is available at the location/region level, check user access
            if ($isAvailable) {
                $isAvailable = $this->userHasAccessToFeature($featureKey, $userId);
            }

            return $isAvailable;
        });
    }

    /**
     * Get all enabled features for a specific location
     *
     * @param string|null $country Optional country code
     * @param string|null $state Optional state/province
     * @param string|null $city Optional city
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getEnabledFeatures(string $country = null, string $state = null, string $city = null)
    {
        $cacheKey = "enabled_features_{$country}_{$state}_{$city}";
        $cacheKey = md5($cacheKey);

        return Cache::remember($cacheKey, self::CACHE_TTL, function() use ($country, $state, $city) {
            return FeatureFlag::enabled()
                ->byLocation($country, $state, $city)
                ->get();
        });
    }

    /**
     * Check multiple features at once
     *
     * @param array $featureKeys Array of feature keys to check
     * @param string|null $country Optional country code
     * @param string|null $state Optional state/province
     * @param string|null $city Optional city
     * @param int|null $userId Optional user ID to check against
     * @return array Associative array of feature key => availability
     */
    public function checkMultipleFeatures(array $featureKeys, string $country = null, string $state = null, string $city = null, int $userId = null): array
    {
        $results = [];

        foreach ($featureKeys as $featureKey) {
            $results[$featureKey] = $this->isFeatureAvailable($featureKey, $country, $state, $city, $userId);
        }

        return $results;
    }

    /**
     * Get feature flag configuration
     *
     * @param string $featureKey The feature key to retrieve
     * @return FeatureFlag|null The feature flag model or null if not found
     */
    public function getFeatureConfig(string $featureKey): ?FeatureFlag
    {
        $cacheKey = "feature_config_{$featureKey}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function() use ($featureKey) {
            return FeatureFlag::where('feature_key', $featureKey)->first();
        });
    }

    /**
     * Get all features with their availability status
     *
     * @param string|null $country Optional country code
     * @param string|null $state Optional state/province
     * @param string|null $city Optional city
     * @param int|null $userId Optional user ID to check against
     * @return array Array of features with availability information
     */
    public function getAllFeaturesWithStatus(string $country = null, string $state = null, string $city = null, int $userId = null): array
    {
        $cacheKey = "all_features_with_status_{$country}_{$state}_{$city}_{$userId}";
        $cacheKey = md5($cacheKey);

        return Cache::remember($cacheKey, self::CACHE_TTL, function() use ($country, $state, $city, $userId) {
            $allFeatures = FeatureFlag::all();
            $results = [];

            foreach ($allFeatures as $feature) {
                $results[] = [
                    'feature_key' => $feature->feature_key,
                    'name' => $feature->name,
                    'description' => $feature->description,
                    'is_enabled' => $feature->is_enabled,
                    'is_available' => FeatureFlag::isFeatureAvailable($feature->feature_key, $country, $state, $city),
                    'starts_at' => $feature->starts_at,
                    'expires_at' => $feature->expires_at,
                    'config' => $feature->config,
                    'allowed_countries' => $feature->allowed_countries,
                    'allowed_states' => $feature->allowed_states,
                    'allowed_cities' => $feature->allowed_cities,
                ];
            }

            return $results;
        });
    }

    /**
     * Get features by category
     * Group features based on their naming convention (e.g., social_*, mobile_*, etc.)
     *
     * @param string $category The category to filter by (e.g., 'social', 'mobile', 'analytics', etc.)
     * @param string|null $country Optional country code
     * @param string|null $state Optional state/province
     * @param string|null $city Optional city
     * @param int|null $userId Optional user ID to check against
     * @return array Array of features in the specified category with availability
     */
    public function getFeaturesByCategory(string $category, string $country = null, string $state = null, string $city = null, int $userId = null): array
    {
        $cacheKey = "features_by_category_{$category}_{$country}_{$state}_{$city}_{$userId}";
        $cacheKey = md5($cacheKey);

        return Cache::remember($cacheKey, self::CACHE_TTL, function() use ($category, $country, $state, $city, $userId) {
            $pattern = $category . '_%';

            $features = FeatureFlag::where('feature_key', 'LIKE', $pattern)->get();
            $results = [];

            foreach ($features as $feature) {
                $results[] = [
                    'feature_key' => $feature->feature_key,
                    'name' => $feature->name,
                    'description' => $feature->description,
                    'is_enabled' => $feature->is_enabled,
                    'is_available' => FeatureFlag::isFeatureAvailable($feature->feature_key, $country, $state, $city, $userId),
                    'config' => $feature->config,
                ];
            }

            return $results;
        });
    }

    /**
     * Get all available categories of features
     *
     * @return array List of feature categories
     */
    public function getFeatureCategories(): array
    {
        $cacheKey = 'feature_categories';

        return Cache::remember($cacheKey, self::CACHE_TTL, function() {
            $features = FeatureFlag::all();
            $categories = [];

            foreach ($features as $feature) {
                $parts = explode('_', $feature->feature_key);
                if (count($parts) > 0) {
                    $category = $parts[0];
                    if (!in_array($category, $categories)) {
                        $categories[] = $category;
                    }
                }
            }

            return $categories;
        });
    }

    /**
     * Check if user has access to a premium feature
     * This could be extended to check user subscription/plan level
     *
     * @param string $featureKey The feature key to check
     * @param int|null $userId Optional user ID to check against
     * @return bool Whether the user has access to the feature
     */
    public function userHasAccessToFeature(string $featureKey, int $userId = null): bool
    {
        // If no user ID provided, use the authenticated user
        if (!$userId && Auth::check()) {
            $userId = Auth::id();
        }

        // First check basic feature availability at the feature flag level
        // (This is handled in the main isFeatureAvailable method)

        // Additional checks could include:
        // - User role permissions
        // - User subscription level
        // - Feature-specific user limits
        // - User verification status
        // - etc.

        // For now, we'll return true if the location-based availability passes
        // In a real implementation, you would add more sophisticated checks here
        return true;
    }

    /**
     * Invalidate feature flag cache
     *
     * @param string|null $featureKey Optional specific feature to clear cache for
     * @return bool
     */
    public function clearCache(string $featureKey = null): bool
    {
        if ($featureKey) {
            // Clear specific feature cache
            Cache::forget("feature_config_{$featureKey}");

            // This is a simplified approach - in a real implementation you'd need to
            // clear all related cache keys
        } else {
            // Clear all feature-related caches
            // In a real implementation, you'd flush more specifically
            // For now, we'll just return true and assume cache will expire
        }

        return true;
    }
}