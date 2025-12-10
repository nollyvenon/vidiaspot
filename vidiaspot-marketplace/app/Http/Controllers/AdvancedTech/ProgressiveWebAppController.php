<?php

namespace App\Http\Controllers;

use App\Services\ProgressiveWebAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProgressiveWebAppController extends Controller
{
    private ProgressiveWebAppService $pwaService;

    public function __construct()
    {
        $this->pwaService = new ProgressiveWebAppService();
    }

    /**
     * Get PWA manifest.
     */
    public function getManifest()
    {
        $manifest = $this->pwaService->generatePwaManifest();

        return response()->json($manifest['manifest'])
            ->header('Content-Type', 'application/manifest+json');
    }

    /**
     * Get service worker JavaScript.
     */
    public function getServiceWorker()
    {
        $serviceWorker = $this->pwaService->generateServiceWorker();

        return response($serviceWorker)
            ->header('Content-Type', 'application/javascript');
    }

    /**
     * Get PWA features.
     */
    public function getPwaFeatures()
    {
        $features = $this->pwaService->getPwaFeatures();

        return response()->json([
            'features' => $features,
            'message' => 'PWA features retrieved successfully'
        ]);
    }

    /**
     * Get PWA installation status for current user.
     */
    public function getInstallationStatus()
    {
        $userId = Auth::id();
        $status = $this->pwaService->getInstallationStatus($userId);

        return response()->json([
            'status' => $status,
            'message' => 'PWA installation status retrieved successfully'
        ]);
    }

    /**
     * Update PWA installation status.
     */
    public function updateInstallationStatus(Request $request)
    {
        $request->validate([
            'installed' => 'boolean',
            'homescreen_added' => 'boolean',
            'push_notifications_enabled' => 'boolean',
            'offline_mode_enabled' => 'boolean',
            'data_savings_enabled' => 'boolean',
        ]);

        $userId = Auth::id();
        $status = $request->all();

        $result = $this->pwaService->setInstallationStatus($userId, $status);

        return response()->json([
            'success' => $result,
            'message' => 'PWA installation status updated successfully'
        ]);
    }

    /**
     * Get offline content for the current user.
     */
    public function getOfflineContent(Request $request)
    {
        $request->validate([
            'context' => 'array',
            'context.page' => 'string',
            'context.section' => 'string',
        ]);

        $userId = Auth::id();
        $context = $request->context ?? [];

        $content = $this->pwaService->getOfflineContent($userId, $context);

        return response()->json([
            'offline_content' => $content,
            'message' => 'Offline content retrieved successfully'
        ]);
    }

    /**
     * Preload content for offline access.
     */
    public function preloadContent(Request $request)
    {
        $request->validate([
            'urls' => 'required|array',
            'urls.*' => 'required|url',
        ]);

        $userId = Auth::id();
        $result = $this->pwaService->preloadContentForOffline($userId, $request->urls);

        return response()->json($result);
    }

    /**
     * Sync offline data when back online.
     */
    public function syncOfflineData(Request $request)
    {
        $request->validate([
            'offline_data' => 'required|array',
        ]);

        $userId = Auth::id();
        $result = $this->pwaService->syncOfflineData($userId, $request->offline_data);

        return response()->json($result);
    }

    /**
     * Get offline data queue for user.
     */
    public function getOfflineDataQueue()
    {
        $userId = Auth::id();
        $queue = $this->pwaService->getOfflineDataQueue($userId);

        return response()->json([
            'queue' => $queue,
            'message' => 'Offline data queue retrieved successfully'
        ]);
    }

    /**
     * Add data to offline queue.
     */
    public function addToOfflineQueue(Request $request)
    {
        $request->validate([
            'data' => 'required|array',
            'action_type' => 'required|string',
        ]);

        $userId = Auth::id();
        $data = array_merge(['action' => $request->action_type], $request->data);
        $itemId = $this->pwaService->addToOfflineQueue($userId, $data);

        return response()->json([
            'item_id' => $itemId,
            'message' => 'Data added to offline queue successfully'
        ]);
    }

    /**
     * Get PWA performance metrics.
     */
    public function getPerformanceMetrics()
    {
        $userId = Auth::id();
        $metrics = $this->pwaService->getPerformanceMetrics($userId);

        return response()->json([
            'metrics' => $metrics,
            'message' => 'PWA performance metrics retrieved successfully'
        ]);
    }

    /**
     * Get PWA update information.
     */
    public function getUpdateInfo()
    {
        $updateInfo = $this->pwaService->getUpdateInfo();

        return response()->json([
            'update_info' => $updateInfo,
            'message' => 'PWA update information retrieved successfully'
        ]);
    }

    /**
     * Get user's PWA preferences.
     */
    public function getUserPwaPreferences()
    {
        $userId = Auth::id();
        $preferences = $this->pwaService->getUserPwaPreferences($userId);

        return response()->json([
            'preferences' => $preferences,
            'message' => 'User PWA preferences retrieved successfully'
        ]);
    }

    /**
     * Update user's PWA preferences.
     */
    public function updateUserPwaPreferences(Request $request)
    {
        $request->validate([
            'dark_mode' => 'boolean',
            'data_savings_mode' => 'boolean',
            'push_notifications' => 'boolean',
            'background_sync' => 'boolean',
            'offline_content_autoload' => 'boolean',
            'language_override' => 'string',
            'accessibility_features' => 'array',
            'accessibility_features.high_contrast' => 'boolean',
            'accessibility_features.large_text' => 'boolean',
            'accessibility_features.reduced_motion' => 'boolean',
        ]);

        $userId = Auth::id();
        $result = $this->pwaService->updateUserPwaPreferences($userId, $request->all());

        return response()->json([
            'success' => $result,
            'message' => 'User PWA preferences updated successfully'
        ]);
    }

    /**
     * Get troubleshooting information.
     */
    public function getTroubleshootingInfo()
    {
        $troubleshooting = $this->pwaService->getTroubleshootingInfo();

        return response()->json([
            'troubleshooting' => $troubleshooting,
            'message' => 'PWA troubleshooting information retrieved successfully'
        ]);
    }

    /**
     * Check if PWA is supported in user's browser.
     */
    public function checkPwaSupport(Request $request)
    {
        $userAgent = $request->userAgent();
        
        $supportInfo = $this->evaluateBrowserPwaSupport($userAgent);

        return response()->json([
            'support_info' => $supportInfo,
            'message' => 'PWA browser support evaluated'
        ]);
    }

    /**
     * Evaluate browser support for PWA features
     */
    private function evaluateBrowserPwaSupport(?string $userAgent): array
    {
        $isSupported = true;
        $unsupportedFeatures = [];
        $pwaCapabilities = [
            'service_workers' => false,
            'push_notifications' => false,
            'add_to_home_screen' => false,
            'offline_support' => false,
            'manifest_support' => false,
        ];

        if (!$userAgent) {
            return [
                'supported' => false,
                'reason' => 'No user agent provided',
                'capabilities' => $pwaCapabilities,
            ];
        }

        $userAgent = strtolower($userAgent);

        // Check for service worker support (most browsers since 2016)
        $pwaCapabilities['service_workers'] = (
            strpos($userAgent, 'chrome') !== false ||
            strpos($userAgent, 'firefox') !== false ||
            strpos($userAgent, 'safari') !== false ||
            strpos($userAgent, 'edge') !== false
        );

        // Check for manifest support
        $pwaCapabilities['manifest_support'] = (
            strpos($userAgent, 'chrome') !== false ||
            strpos($userAgent, 'firefox') !== false ||
            strpos($userAgent, 'edge') !== false ||
            (strpos($userAgent, 'safari') !== false && strpos($userAgent, 'mobile') !== false) // iOS Safari has limited support
        );

        // Check for add to home screen support
        $pwaCapabilities['add_to_home_screen'] = (
            strpos($userAgent, 'chrome') !== false ||
            (strpos($userAgent, 'safari') !== false && strpos($userAgent, 'mobile') !== false) // iOS Safari
        );

        // Check for push notification support
        $pwaCapabilities['push_notifications'] = (
            strpos($userAgent, 'chrome') !== false ||
            strpos($userAgent, 'firefox') !== false ||
            strpos($userAgent, 'edge') !== false
        );

        // Overall support determination
        $isSupported = $pwaCapabilities['service_workers'] && $pwaCapabilities['manifest_support'];

        return [
            'supported' => $isSupported,
            'user_agent' => $userAgent,
            'capabilities' => $pwaCapabilities,
            'unsupported_features' => $unsupportedFeatures,
            'recommendation' => $isSupported ? 'Install PWA' : 'Upgrade browser for full PWA experience',
        ];
    }
}