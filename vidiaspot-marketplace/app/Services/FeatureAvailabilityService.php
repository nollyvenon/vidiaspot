<?php

namespace App\Services;

use App\Models\FeatureFlag;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class FeatureAvailabilityService
{
    /**
     * Check if insurance policy feature is available for the user
     */
    public function isInsurancePolicyAvailable($country = null, $state = null, $city = null)
    {
        $userLocation = $this->getUserLocation($country, $state, $city);
        return FeatureFlag::isFeatureAvailable('insurance_policy', $userLocation['country'], $userLocation['state'], $userLocation['city']);
    }

    /**
     * Check if insurance aggregator feature is available for the user
     */
    public function isInsuranceAggregatorAvailable($country = null, $state = null, $city = null)
    {
        $userLocation = $this->getUserLocation($country, $state, $city);
        return FeatureFlag::isFeatureAvailable('insurance_aggregator', $userLocation['country'], $userLocation['state'], $userLocation['city']);
    }

    /**
     * Check if online store feature is available for the user
     */
    public function isOnlineStoreAvailable($country = null, $state = null, $city = null)
    {
        $userLocation = $this->getUserLocation($country, $state, $city);
        return FeatureFlag::isFeatureAvailable('online_store', $userLocation['country'], $userLocation['state'], $userLocation['city']);
    }

    /**
     * Check if pay later feature is available for the user
     */
    public function isPayLaterAvailable($country = null, $state = null, $city = null)
    {
        $userLocation = $this->getUserLocation($country, $state, $city);
        return FeatureFlag::isFeatureAvailable('pay_later', $userLocation['country'], $userLocation['state'], $userLocation['city']);
    }

    /**
     * Check if crypto payments feature is available for the user
     */
    public function isCryptoPaymentsAvailable($country = null, $state = null, $city = null)
    {
        $userLocation = $this->getUserLocation($country, $state, $city);
        return FeatureFlag::isFeatureAvailable('crypto_payments', $userLocation['country'], $userLocation['state'], $userLocation['city']);
    }

    /**
     * Check if split payments feature is available for the user
     */
    public function isSplitPaymentsAvailable($country = null, $state = null, $city = null)
    {
        $userLocation = $this->getUserLocation($country, $state, $city);
        return FeatureFlag::isFeatureAvailable('split_payments', $userLocation['country'], $userLocation['state'], $userLocation['city']);
    }

    /**
     * Get user's location information
     */
    private function getUserLocation($country = null, $state = null, $city = null)
    {
        // Try to get location from request first
        if ($country) {
            return [
                'country' => $country,
                'state' => $state,
                'city' => $city
            ];
        }

        // Fallback to user's profile location if logged in
        if (Auth::check()) {
            $user = Auth::user();
            return [
                'country' => $user->country,
                'state' => $user->state,
                'city' => $user->city
            ];
        }

        // Fallback to IP-based location or default to India
        return [
            'country' => 'India',
            'state' => null,
            'city' => null
        ];
    }

    /**
     * Get available insurance providers based on user location and feature availability
     */
    public function getAvailableInsuranceProviders($category = null, $country = null, $state = null, $city = null)
    {
        if (!$this->isInsurancePolicyAvailable($country, $state, $city)) {
            return collect(); // Return empty collection if feature is not available
        }

        $userLocation = $this->getUserLocation($country, $state, $city);
        
        $query = \App\Models\InsuranceProvider::where('is_active', true);
        
        // Filter by category if specified
        if ($category) {
            $query = $query->whereJsonContains('categories', $category);
        }

        // Filter by coverage area
        if ($userLocation['country']) {
            $query = $query->where(function($q) use ($userLocation) {
                $q->whereNull('coverage_areas')
                  ->orWhereJsonContains('coverage_areas', $userLocation['country']);
            });
        }

        return $query->orderBy('rating', 'desc')->get();
    }

    /**
     * Get feature configuration
     */
    public function getFeatureConfig($featureKey)
    {
        $feature = FeatureFlag::where('feature_key', $featureKey)->first();
        return $feature ? $feature->config : [];
    }

    /**
     * Check if user can access a specific feature
     */
    public function canAccessFeature($featureKey, $country = null, $state = null, $city = null)
    {
        return FeatureFlag::isFeatureAvailable($featureKey, $country, $state, $city);
    }
}