<?php

namespace App\Http\Controllers;

use App\Services\IntelligentBandwidthManagementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IntelligentBandwidthManagementController extends Controller
{
    private IntelligentBandwidthManagementService $bandwidthService;

    public function __construct()
    {
        $this->bandwidthService = new IntelligentBandwidthManagementService();
    }

    /**
     * Get available bandwidth optimization strategies.
     */
    public function getOptimizationStrategies()
    {
        $strategies = $this->bandwidthService->getOptimizationStrategies();

        return response()->json([
            'strategies' => $strategies,
            'message' => 'Bandwidth optimization strategies retrieved successfully'
        ]);
    }

    /**
     * Get connection quality definitions.
     */
    public function getConnectionQualities()
    {
        $qualitites = $this->bandwidthService->getConnectionQualities();

        return response()->json([
            'qualities' => $qualities,
            'message' => 'Connection quality definitions retrieved successfully'
        ]);
    }

    /**
     * Get bandwidth profiles.
     */
    public function getBandwidthProfiles()
    {
        $profiles = $this->bandwidthService->getBandwidthProfiles();

        return response()->json([
            'profiles' => $profiles,
            'message' => 'Bandwidth profiles retrieved successfully'
        ]);
    }

    /**
     * Get content optimization options.
     */
    public function getContentOptimizationOptions()
    {
        $options = $this->bandwidthService->getContentOptimizationOptions();

        return response()->json([
            'options' => $options,
            'message' => 'Content optimization options retrieved successfully'
        ]);
    }

    /**
     * Assess connection quality for a user.
     */
    public function assessConnectionQuality(Request $request)
    {
        $request->validate([
            'connection_data' => 'array',
            'connection_data.network_type' => 'string|in:2g,3g,4g,5g,wifi,ethernet,offline',
            'connection_data.download_speed_mbps' => 'numeric|min:0',
            'connection_data.signal_strength' => 'integer|min:0|max:100',
            'connection_data.latency_ms' => 'integer|min:0',
        ]);

        $userId = Auth::id();
        $connectionData = $request->connection_data ?? [];

        $assessment = $this->bandwidthService->assessConnectionQuality($userId, $connectionData);

        return response()->json($assessment);
    }

    /**
     * Get bandwidth optimization settings for the user.
     */
    public function getBandwidthOptimizationSettings(Request $request)
    {
        $request->validate([
            'mode' => 'string|in:auto,emergency_mode,data_saver,balanced,performance,unrestricted',
        ]);

        $userId = Auth::id();
        $mode = $request->mode ?? 'auto';

        $settings = $this->bandwidthService->getBandwidthOptimizationSettings($userId, $mode);

        return response()->json($settings);
    }

    /**
     * Optimize content for bandwidth.
     */
    public function optimizeContentForBandwidth(Request $request)
    {
        $request->validate([
            'content_id' => 'required|string',
            'content_type' => 'required|string|in:images,videos,documents,javascript,css',
            'bandwidth_mode' => 'string|in:auto,emergency_mode,data_saver,balanced,performance,unrestricted',
        ]);

        $userId = Auth::id();
        $bandwidthMode = $request->bandwidth_mode ?? 'auto';

        $result = $this->bandwidthService->optimizeContentForBandwidth(
            $request->content_id,
            $request->content_type,
            $userId,
            $bandwidthMode
        );

        return response()->json($result);
    }

    /**
     * Calculate potential bandwidth savings.
     */
    public function calculateBandwidthSavings(Request $request)
    {
        $request->validate([
            'content_items' => 'required|array|min:1',
            'content_items.*.id' => 'required|string',
            'content_items.*.type' => 'required|string|in:images,videos,documents,javascript,css',
            'content_items.*.size_bytes' => 'required|integer|min:1',
            'bandwidth_mode' => 'string|in:emergency_mode,data_saver,balanced,performance,unrestricted',
        ]);

        $bandwidthMode = $request->bandwidth_mode ?? 'balanced';

        $savings = $this->bandwidthService->calculateBandwidthSavings($request->content_items, $bandwidthMode);

        return response()->json([
            'savings' => $savings,
            'message' => 'Bandwidth savings calculated successfully'
        ]);
    }

    /**
     * Get optimization recommendations for content.
     */
    public function getOptimizationRecommendations(Request $request)
    {
        $request->validate([
            'content_items' => 'required|array|min:1',
            'content_items.*.id' => 'required|string',
            'content_items.*.type' => 'required|string|in:images,videos,documents,javascript,css',
            'content_items.*.size_bytes' => 'integer',
            'bandwidth_mode' => 'string|in:auto,emergency_mode,data_saver,balanced,performance,unrestricted',
        ]);

        $userId = Auth::id();
        $bandwidthMode = $request->bandwidth_mode ?? 'auto';

        $recommendations = $this->bandwidthService->getOptimizationRecommendations(
            $request->content_items,
            $userId,
            $bandwidthMode
        );

        return response()->json([
            'recommendations' => $recommendations,
            'message' => 'Optimization recommendations retrieved successfully'
        ]);
    }

    /**
     * Get dynamic quality settings for media content.
     */
    public function getDynamicQualitySettings(Request $request)
    {
        $request->validate([
            'content_type' => 'required|string|in:images,videos,audio',
            'context' => 'array',
        ]);

        $userId = Auth::id();

        $settings = $this->bandwidthService->getDynamicQualitySettings(
            $userId,
            $request->content_type,
            $request->context ?? []
        );

        return response()->json($settings);
    }

    /**
     * Optimize an API response based on bandwidth.
     */
    public function optimizeApiResponse(Request $request)
    {
        $request->validate([
            'response_data' => 'required|array',
        ]);

        $userId = Auth::id();

        $optimizedResponse = $this->bandwidthService->optimizeApiResponse(
            $request->response_data,
            $userId
        );

        return response()->json($optimizedResponse);
    }

    /**
     * Get bandwidth usage report for the user.
     */
    public function getBandwidthUsageReport(Request $request)
    {
        $request->validate([
            'period' => 'string|in:daily,weekly,monthly,quarterly',
        ]);

        $userId = Auth::id();
        $period = $request->period ?? 'monthly';

        $report = $this->bandwidthService->getBandwidthUsageReport($userId, $period);

        return response()->json($report);
    }

    /**
     * Get network-aware content recommendations.
     */
    public function getNetworkAwareRecommendations(Request $request)
    {
        $request->validate([
            'content_preferences' => 'array',
        ]);

        $userId = Auth::id();
        $preferences = $request->content_preferences ?? [];

        $recommendations = $this->bandwidthService->getNetworkAwareRecommendations($userId, $preferences);

        return response()->json([
            'recommendations' => $recommendations,
            'message' => 'Network-aware content recommendations retrieved successfully'
        ]);
    }

    /**
     * Get user's current bandwidth settings.
     */
    public function getUserBandwidthSettings()
    {
        $userId = Auth::id();
        $settingsKey = "bandwidth_settings_{$userId}";
        
        $settings = \Cache::get($settingsKey, [
            'mode' => 'balanced',
            'profile' => $this->bandwidthService->getBandwidthProfiles()['balanced'],
            'content_options' => $this->bandwidthService->getContentOptimizationOptions(),
            'last_updated' => now()->toISOString(),
        ]);

        return response()->json([
            'settings' => $settings,
            'message' => 'User bandwidth settings retrieved successfully'
        ]);
    }

    /**
     * Update user's bandwidth settings.
     */
    public function updateUserBandwidthSettings(Request $request)
    {
        $request->validate([
            'mode' => 'required|string|in:emergency_mode,data_saver,balanced,performance,unrestricted',
            'content_preferences' => 'array',
            'content_preferences.images' => 'string|in:high,medium,low,none',
            'content_preferences.videos' => 'string|in:high,medium,low,none',
            'content_preferences.preload' => 'boolean',
            'content_preferences.lazy_load' => 'boolean',
        ]);

        $userId = Auth::id();
        $settingsKey = "bandwidth_settings_{$userId}";

        $newSettings = [
            'mode' => $request->mode,
            'content_preferences' => $request->content_preferences ?? [],
            'updated_at' => now()->toISOString(),
        ];

        \Cache::put($settingsKey, $newSettings, now()->addMonths(6));

        return response()->json([
            'settings' => $newSettings,
            'message' => 'User bandwidth settings updated successfully'
        ]);
    }
}