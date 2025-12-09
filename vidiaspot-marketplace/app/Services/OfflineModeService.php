<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class OfflineModeService
{
    private string $offlineStoragePath = 'offline-data';

    /**
     * Prepare content for offline use
     */
    public function prepareForOffline(string $userId, array $contentIds, string $contentType = 'content'): array
    {
        $offlineData = [
            'id' => 'offline_' . Str::uuid(),
            'user_id' => $userId,
            'content_type' => $contentType,
            'content_ids' => $contentIds,
            'content_data' => [],
            'created_at' => now(),
            'expires_at' => now()->addDays(30), // Expire after 30 days
            'size' => 0,
        ];

        // Fetch and store content data
        foreach ($contentIds as $contentId) {
            $content = $this->getContentById($contentId, $contentType);
            
            if ($content) {
                $offlineData['content_data'][$contentId] = $content;
                $offlineData['size'] += strlen(json_encode($content));
            }
        }

        // Store offline package
        $this->storeOfflinePackage($offlineData);

        return [
            'success' => true,
            'package_id' => $offlineData['id'],
            'content_count' => count($offlineData['content_data']),
            'size' => $offlineData['size'],
            'expires_at' => $offlineData['expires_at'],
            'message' => 'Content prepared for offline use successfully'
        ];
    }

    /**
     * Get content by ID based on type
     */
    private function getContentById(string $contentId, string $contentType): ?array
    {
        // This is a simplified implementation
        // In a real app, this would query different models based on content type
        switch ($contentType) {
            case 'ad':
                // Fetch ad data
                return [
                    'id' => $contentId,
                    'title' => 'Sample Ad Title',
                    'description' => 'Sample ad description would be fetched from database',
                    'price' => 1000,
                    'images' => [],
                    'user' => ['name' => 'Sample User'],
                ];
            case 'category':
                // Fetch category data
                return [
                    'id' => $contentId,
                    'name' => 'Sample Category',
                    'description' => 'Sample category description',
                ];
            case 'content-page':
                // Fetch content page data
                return [
                    'id' => $contentId,
                    'title' => 'Sample Content Page',
                    'content' => 'Sample content for offline reading',
                ];
            default:
                return [
                    'id' => $contentId,
                    'title' => 'Offline Content',
                    'data' => 'Content data would be fetched from database',
                ];
        }
    }

    /**
     * Store offline package
     */
    private function storeOfflinePackage(array $offlineData): void
    {
        // Store in cache with expiration
        $cacheKey = "offline_package_{$offlineData['id']}";
        Cache::put($cacheKey, $offlineData, $offlineData['expires_at']);
        
        // Store user's offline packages
        $userPackagesKey = "user_offline_packages_{$offlineData['user_id']}";
        $userPackages = Cache::get($userPackagesKey, []);
        $userPackages[] = $offlineData['id'];
        Cache::put($userPackagesKey, $userPackages, now()->addDays(31));
    }

    /**
     * Get offline content for a user
     */
    public function getOfflineContent(string $userId, string $packageId = null): array
    {
        if ($packageId) {
            // Get specific package
            $cacheKey = "offline_package_{$packageId}";
            $package = Cache::get($cacheKey);
            
            if ($package && $package['user_id'] === $userId) {
                return $this->formatOfflineResponse($package);
            }
        } else {
            // Get all packages for user
            $userPackagesKey = "user_offline_packages_{$userId}";
            $packageIds = Cache::get($userPackagesKey, []);
            
            $allPackages = [];
            foreach ($packageIds as $pkgId) {
                $cacheKey = "offline_package_{$pkgId}";
                $package = Cache::get($cacheKey);
                
                if ($package && $package['user_id'] === $userId) {
                    $allPackages[] = $this->formatOfflineResponse($package);
                }
            }
            
            return [
                'packages' => $allPackages,
                'total_count' => count($allPackages),
                'user_id' => $userId
            ];
        }

        return ['packages' => [], 'message' => 'No offline content found'];
    }

    /**
     * Format offline response
     */
    private function formatOfflineResponse(array $package): array
    {
        return [
            'id' => $package['id'],
            'content_type' => $package['content_type'],
            'content_data' => $package['content_data'],
            'content_count' => count($package['content_data']),
            'size' => $package['size'],
            'created_at' => $package['created_at'],
            'expires_at' => $package['expires_at'],
        ];
    }

    /**
     * Sync offline changes when back online
     */
    public function syncOfflineChanges(string $userId, array $offlineChanges): array
    {
        $syncResults = [
            'successful' => [],
            'failed' => [],
            'summary' => [
                'total_changes' => count($offlineChanges),
                'successful_syncs' => 0,
                'failed_syncs' => 0,
            ]
        ];

        foreach ($offlineChanges as $change) {
            $result = $this->processSyncChange($userId, $change);
            
            if ($result['success']) {
                $syncResults['successful'][] = $result;
                $syncResults['summary']['successful_syncs']++;
            } else {
                $syncResults['failed'][] = $result;
                $syncResults['summary']['failed_syncs']++;
            }
        }

        return $syncResults;
    }

    /**
     * Process individual sync change
     */
    private function processSyncChange(string $userId, array $change): array
    {
        // This would sync the change to the main database
        // In this implementation, we'll simulate the process
        
        $changeType = $change['type'] ?? 'update';
        $contentId = $change['id'] ?? null;
        
        switch ($changeType) {
            case 'create':
                // Create new content
                return [
                    'success' => true,
                    'change_id' => $contentId,
                    'action' => 'created',
                    'timestamp' => now()->toISOString(),
                    'message' => "Content {$contentId} created successfully"
                ];
            case 'update':
                // Update existing content
                return [
                    'success' => true,
                    'change_id' => $contentId,
                    'action' => 'updated',
                    'timestamp' => now()->toISOString(),
                    'message' => "Content {$contentId} updated successfully"
                ];
            case 'delete':
                // Delete content
                return [
                    'success' => true,
                    'change_id' => $contentId,
                    'action' => 'deleted',
                    'timestamp' => now()->toISOString(),
                    'message' => "Content {$contentId} deleted successfully"
                ];
            default:
                return [
                    'success' => false,
                    'change_id' => $contentId,
                    'action' => $changeType,
                    'timestamp' => now()->toISOString(),
                    'message' => "Unknown change type: {$changeType}"
                ];
        }
    }

    /**
     * Get available content for offline download
     */
    public function getAvailableOfflineContent(string $userId, string $contentType, int $limit = 10): array
    {
        // This would fetch content that's suitable for offline access
        // In this implementation, we'll return sample data
        
        $sampleContent = [];
        
        for ($i = 1; $i <= $limit; $i++) {
            $sampleContent[] = [
                'id' => "content_{$i}",
                'title' => "Offline Content {$i}",
                'description' => "Description for offline content {$i}",
                'type' => $contentType,
                'size' => rand(1000, 50000), // Size in bytes
                'created_at' => now()->subDays($i)->toISOString(),
            ];
        }
        
        return [
            'content' => $sampleContent,
            'count' => count($sampleContent),
            'content_type' => $contentType,
            'limit' => $limit,
            'user_id' => $userId
        ];
    }

    /**
     * Clean up expired offline packages
     */
    public function cleanupExpiredPackages(): int
    {
        // In a real implementation, this would iterate through all packages and remove expired ones
        // For this sample, we'll just return a count of potential cleanup
        
        // Get all user package lists
        $allCacheKeys = Cache::get('all_offline_package_keys', []);
        $expiredCount = 0;
        
        foreach ($allCacheKeys as $key) {
            $package = Cache::get($key);
            if ($package && isset($package['expires_at']) && now()->gt($package['expires_at'])) {
                Cache::forget($key);
                $expiredCount++;
            }
        }
        
        return $expiredCount;
    }

    /**
     * Check if offline mode is available for user
     */
    public function isOfflineModeAvailable(string $userId): bool
    {
        $userPackagesKey = "user_offline_packages_{$userId}";
        $packages = Cache::get($userPackagesKey, []);
        
        return !empty($packages);
    }

    /**
     * Get offline mode status for user
     */
    public function getOfflineStatus(string $userId): array
    {
        $userPackagesKey = "user_offline_packages_{$userId}";
        $packageIds = Cache::get($userPackagesKey, []);
        
        $totalSize = 0;
        $validPackages = 0;
        
        foreach ($packageIds as $pkgId) {
            $cacheKey = "offline_package_{$pkgId}";
            $package = Cache::get($cacheKey);
            
            if ($package) {
                if (now()->lt($package['expires_at'])) {
                    $totalSize += $package['size'];
                    $validPackages++;
                } else {
                    // Remove expired package from user's list
                    Cache::forget($cacheKey);
                }
            }
        }
        
        return [
            'available' => $validPackages > 0,
            'package_count' => $validPackages,
            'total_size_bytes' => $totalSize,
            'total_size_mb' => round($totalSize / (1024 * 1024), 2),
            'user_id' => $userId,
            'last_sync' => now()->toISOString(),
        ];
    }
}