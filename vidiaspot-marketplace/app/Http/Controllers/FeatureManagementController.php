<?php

namespace App\Http\Controllers;

use App\Services\FeatureFlagService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeatureManagementController extends Controller
{
    protected $featureFlagService;

    public function __construct(FeatureFlagService $featureFlagService)
    {
        $this->featureFlagService = $featureFlagService;
    }

    /**
     * Get all available features with their status
     */
    public function getAllFeatures(Request $request)
    {
        $country = $request->input('country');
        $state = $request->input('state');
        $city = $request->input('city');
        $userId = Auth::id(); // Get authenticated user ID

        $features = $this->featureFlagService->getAllFeaturesWithStatus($country, $state, $city, $userId);

        return response()->json([
            'success' => true,
            'data' => $features
        ]);
    }

    /**
     * Get features by category
     */
    public function getFeaturesByCategory($category, Request $request)
    {
        $country = $request->input('country');
        $state = $request->input('state');
        $city = $request->input('city');
        $userId = Auth::id(); // Get authenticated user ID

        $features = $this->featureFlagService->getFeaturesByCategory($category, $country, $state, $city, $userId);

        return response()->json([
            'success' => true,
            'category' => $category,
            'data' => $features
        ]);
    }

    /**
     * Check if a specific feature is available
     */
    public function checkFeature(Request $request, string $featureKey)
    {
        $country = $request->input('country');
        $state = $request->input('state');
        $city = $request->input('city');
        $userId = Auth::id(); // Get authenticated user ID

        $isAvailable = $this->featureFlagService->isFeatureAvailable($featureKey, $country, $state, $city, $userId);

        return response()->json([
            'success' => true,
            'feature_key' => $featureKey,
            'is_available' => $isAvailable
        ]);
    }

    /**
     * Get all feature categories
     */
    public function getFeatureCategories()
    {
        $categories = $this->featureFlagService->getFeatureCategories();

        return response()->json([
            'success' => true,
            'categories' => $categories
        ]);
    }

    /**
     * Check multiple features at once
     */
    public function checkMultipleFeatures(Request $request)
    {
        $featureKeys = $request->input('features', []);
        $country = $request->input('country');
        $state = $request->input('state');
        $city = $request->input('city');
        $userId = Auth::id(); // Get authenticated user ID

        if (!is_array($featureKeys)) {
            return response()->json([
                'success' => false,
                'message' => 'Features must be provided as an array'
            ], 422);
        }

        $results = $this->featureFlagService->checkMultipleFeatures($featureKeys, $country, $state, $city, $userId);

        return response()->json([
            'success' => true,
            'data' => $results
        ]);
    }
}