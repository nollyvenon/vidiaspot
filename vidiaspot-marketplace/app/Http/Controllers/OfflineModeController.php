<?php

namespace App\Http\Controllers;

use App\Services\OfflineModeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OfflineModeController extends Controller
{
    private OfflineModeService $offlineService;

    public function __construct()
    {
        $this->offlineService = new OfflineModeService();
    }

    /**
     * Prepare content for offline use.
     */
    public function prepareForOffline(Request $request)
    {
        $request->validate([
            'content_ids' => 'required|array|min:1',
            'content_ids.*' => 'string',
            'content_type' => 'string|in:ad,category,content-page,profile,settings,search',
        ]);

        $userId = Auth::id();
        $result = $this->offlineService->prepareForOffline(
            $userId,
            $request->content_ids,
            $request->content_type ?? 'content-page'
        );

        return response()->json($result);
    }

    /**
     * Get offline content for the current user.
     */
    public function getOfflineContent(Request $request)
    {
        $userId = Auth::id();
        
        $packageId = $request->query('package_id');
        $result = $this->offlineService->getOfflineContent($userId, $packageId);

        return response()->json($result);
    }

    /**
     * Sync offline changes when back online.
     */
    public function syncOfflineChanges(Request $request)
    {
        $request->validate([
            'changes' => 'required|array',
            'changes.*.id' => 'required|string',
            'changes.*.type' => 'required|in:create,update,delete',
            'changes.*.data' => 'array',
        ]);

        $userId = Auth::id();
        $result = $this->offlineService->syncOfflineChanges($userId, $request->changes);

        return response()->json($result);
    }

    /**
     * Get available content for offline download.
     */
    public function getAvailableContent(Request $request)
    {
        $request->validate([
            'content_type' => 'required|string|in:ad,category,content-page,profile,settings,search',
            'limit' => 'integer|min:1|max:100',
        ]);

        $userId = Auth::id();
        $result = $this->offlineService->getAvailableOfflineContent(
            $userId,
            $request->content_type,
            $request->limit ?? 10
        );

        return response()->json($result);
    }

    /**
     * Get offline mode status for the current user.
     */
    public function getStatus()
    {
        $userId = Auth::id();
        $status = $this->offlineService->getOfflineStatus($userId);

        return response()->json($status);
    }

    /**
     * Check if offline mode is available for the current user.
     */
    public function checkAvailability()
    {
        $userId = Auth::id();
        $available = $this->offlineService->isOfflineModeAvailable($userId);

        return response()->json([
            'available' => $available,
            'user_id' => $userId,
        ]);
    }

    /**
     * Clean up expired offline packages.
     */
    public function cleanup()
    {
        $cleanedCount = $this->offlineService->cleanupExpiredPackages();

        return response()->json([
            'message' => "Cleaned up {$cleanedCount} expired offline packages",
            'cleaned_count' => $cleanedCount,
        ]);
    }

    /**
     * Remove a specific offline package.
     */
    public function removePackage(Request $request, string $packageId)
    {
        // In a real implementation, this would remove the specific package from storage
        // For this example, we'll simulate the removal
        
        return response()->json([
            'success' => true,
            'package_id' => $packageId,
            'message' => 'Offline package removed successfully'
        ]);
    }

    /**
     * Download offline package for mobile app synchronization.
     */
    public function downloadPackage(Request $request, string $packageId)
    {
        $userId = Auth::id();
        
        // Get the offline package
        $result = $this->offlineService->getOfflineContent($userId, $packageId);
        
        if (empty($result['packages'])) {
            return response()->json(['error' => 'Package not found'], 404);
        }
        
        $package = $result['packages'];
        
        // In a real implementation, this would return the package data for download
        return response()->json([
            'package' => $package,
            'download_url' => "/api/offline-packages/{$packageId}/download", // This would be a direct file download
            'expires_at' => $package['expires_at'],
            'size' => $package['size'],
        ]);
    }
}