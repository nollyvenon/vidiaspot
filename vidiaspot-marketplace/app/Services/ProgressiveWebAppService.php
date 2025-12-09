<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProgressiveWebAppService
{
    private array $pwaFeatures = [
        'offline_mode' => [
            'name' => 'Offline Mode',
            'description' => 'Allow users to access content when offline',
            'implementation' => [
                'service_worker' => true,
                'cache_strategy' => 'cache_first',
                'sync_when_online' => true,
                'background_sync' => true,
            ],
        ],
        'push_notifications' => [
            'name' => 'Push Notifications',
            'description' => 'Send notifications to users even when app is closed',
            'implementation' => [
                'web_push_api' => true,
                'notification_permission' => 'ask_when_useful',
                'customization_enabled' => true,
            ],
        ],
        'add_to_home_screen' => [
            'name' => 'Add to Home Screen',
            'description' => 'Allow users to install the app on their device',
            'implementation' => [
                'manifest_json' => true,
                'install_prompt' => 'smart',
                'custom_icons' => true,
            ],
        ],
        'fast_loading' => [
            'name' => 'Fast Loading',
            'description' => 'Ensure quick loading times even on slow connections',
            'implementation' => [
                'resource_caching' => true,
                'preloading_strategies' => 'smart_preload',
                'image_lazy_loading' => true,
            ],
        ],
        'responsive_design' => [
            'name' => 'Responsive Design',
            'description' => 'Optimize layout for all screen sizes',
            'implementation' => [
                'adaptive_layouts' => true,
                'touch_optimized' => true,
                'gesture_support' => true,
            ],
        ],
        'data_savings' => [
            'name' => 'Data Savings Mode',
            'description' => 'Minimize data usage for users with limited bandwidth',
            'implementation' => [
                'lite_mode' => true,
                'image_compression' => true,
                'video_quality_control' => true,
            ],
        ],
    ];

    /**
     * PWA manifest configuration
     */
    private array $pwaManifest = [
        'name' => 'VidiaSpot Marketplace',
        'short_name' => 'VidiaSpot',
        'description' => 'A universal design marketplace for all users',
        'start_url' => '/',
        'display' => 'standalone',
        'background_color' => '#ffffff',
        'theme_color' => '#4f46e5',
        'orientation' => 'any',
        'icons' => [
            [
                'src' => '/pwa-icons/icon-72x72.png',
                'sizes' => '72x72',
                'type' => 'image/png',
            ],
            [
                'src' => '/pwa-icons/icon-96x96.png',
                'sizes' => '96x96',
                'type' => 'image/png',
            ],
            [
                'src' => '/pwa-icons/icon-128x128.png',
                'sizes' => '128x128',
                'type' => 'image/png',
            ],
            [
                'src' => '/pwa-icons/icon-144x144.png',
                'sizes' => '144x144',
                'type' => 'image/png',
            ],
            [
                'src' => '/pwa-icons/icon-152x152.png',
                'sizes' => '152x152',
                'type' => 'image/png',
            ],
            [
                'src' => '/pwa-icons/icon-192x192.png',
                'sizes' => '192x192',
                'type' => 'image/png',
            ],
            [
                'src' => '/pwa-icons/icon-384x384.png',
                'sizes' => '384x384',
                'type' => 'image/png',
            ],
            [
                'src' => '/pwa-icons/icon-512x512.png',
                'sizes' => '512x512',
                'type' => 'image/png',
            ],
        ],
    ];

    /**
     * Service worker configuration
     */
    private array $serviceWorkerConfig = [
        'cache_name' => 'vidiaspot-v1',
        'offline_page' => '/offline',
        'pre_cache_urls' => [
            '/',
            '/manifest.json',
            '/css/app.css',
            '/js/app.js',
            '/fonts/main.woff2',
            '/images/logo.svg',
        ],
        'network_first_regex' => [
            '/\/api\//', // API calls should be network-first
            '/\/auth\//', // Authentication endpoints
        ],
        'cache_strategy' => 'cache_then_network',
    ];

    /**
     * Get PWA features
     */
    public function getPWAFeatures(): array
    {
        return $this->pwaFeatures;
    }

    /**
     * Generate PWA manifest JSON
     */
    public function generatePWAManifest(): array
    {
        return [
            'manifest' => array_merge($this->pwaManifest, [
                'id' => '/?source=pwa',
                'scope' => '/',
                'lang' => app()->getLocale(),
                'dir' => 'ltr',
            ]),
            'generated_at' => now()->toISOString(),
        ];
    }

    /**
     * Generate service worker JavaScript code
     */
    public function generateServiceWorker(): string
    {
        $serviceWorkerJs = "
// VidiaSpot Marketplace PWA Service Worker
const CACHE_NAME = '{$this->serviceWorkerConfig['cache_name']}';
const OFFLINE_PAGE = '{$this->serviceWorkerConfig['offline_page']}';
const PRE_CACHE_URLS = {$this->arrayToJs($this->serviceWorkerConfig['pre_cache_urls'])};

const NETWORK_FIRST_REGEX = [
    {$this->regexArrayToJs($this->serviceWorkerConfig['network_first_regex'])}
];

// Install event - cache initial assets
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then((cache) => {
                console.log('[ServiceWorker] Caching core assets');
                return cache.addAll(PRE_CACHE_URLS);
            })
    );
});

// Activate event - clean up old caches
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keyList) => {
            return Promise.all(keyList.map((key) => {
                if (key !== CACHE_NAME) {
                    console.log('[ServiceWorker] Removing old cache', key);
                    return caches.delete(key);
                }
            }));
        })
    );
    return self.clients.claim();
});

// Fetch event - handle network requests
self.addEventListener('fetch', (event) => {
    // Skip non-GET requests
    if (event.request.method !== 'GET') {
        return;
    }

    // Check if request matches network-first patterns
    const url = new URL(event.request.url);
    const isNetworkFirst = NETWORK_FIRST_REGEX.some(regex => new RegExp(regex).test(url.pathname));

    if (isNetworkFirst) {
        // Network-first strategy for API calls
        event.respondWith(networkFirstStrategy(event.request));
    } else {
        // Cache-first strategy for static assets
        event.respondWith(cacheFirstStrategy(event.request));
    }
});

// Network-first strategy
async function networkFirstStrategy(request) {
    try {
        const networkResponse = await fetch(request);
        
        // Update cache with fresh response (but don't wait for it)
        const cache = await caches.open(CACHE_NAME);
        cache.put(request, networkResponse.clone());
        
        return networkResponse;
    } catch (error) {
        // Fall back to cache if network fails
        const cachedResponse = await caches.match(request);
        if (cachedResponse) {
            return cachedResponse;
        }
        
        // Return offline page for navigation requests
        if (request.destination === 'document') {
            return caches.match(OFFLINE_PAGE) || caches.match('/');
        }
        
        throw error;
    }
}

// Cache-first strategy
async function cacheFirstStrategy(request) {
    // Try cache first
    const cachedResponse = await caches.match(request);
    if (cachedResponse) {
        return cachedResponse;
    }
    
    try {
        // Try network if cache miss
        const networkResponse = await fetch(request);
        
        // Update cache
        if (networkResponse.status === 200) {
            const cache = await caches.open(CACHE_NAME);
            cache.put(request, networkResponse.clone());
        }
        
        return networkResponse;
    } catch (error) {
        // Return offline page for navigation requests
        if (request.destination === 'document') {
            return caches.match(OFFLINE_PAGE) || caches.match('/');
        }
        
        throw error;
    }
}

// Background sync for offline actions
self.addEventListener('sync', (event) => {
    if (event.tag === 'sync-data') {
        event.waitUntil(syncData());
    }
});

async function syncData() {
    // This would sync any data that was captured while offline
    // In a real implementation, this would send queued requests to the server
    console.log('[ServiceWorker] Attempting to sync data...');
    
    // Example: sync offline forms, purchases, or user actions
    // const offlineActions = await getQueuedActions();
    // for (const action of offlineActions) {
    //     try {
    //         await sendAction(action);
    //         await removeQueuedAction(action.id);
    //     } catch (error) {
    //         console.error('Failed to sync action:', action.id, error);
    //     }
    // }
}

// Push notification handling
self.addEventListener('push', (event) => {
    const options = {
        body: event.data?.text() ?? 'New notification',
        icon: '/icon-192x192.png',
        badge: '/icon-72x72.png',
        vibrate: [100, 50, 100],
        data: {
            dateOfArrival: Date.now(),
            primaryKey: 1
        }
    };

    event.waitUntil(
        self.registration.showNotification('VidiaSpot Marketplace', options)
    );
});

self.addEventListener('notificationclick', (event) => {
    event.notification.close();
    event.waitUntil(
        clients.openWindow('/')
    );
});

// Background fetch for large downloads
self.addEventListener('backgroundfetch', (event) => {
    if (event.id.startsWith('download-')) {
        event.waitUntil(handleBackgroundFetch(event));
    }
});

async function handleBackgroundFetch(event) {
    const registration = event.registration;
    try {
        for await (const download of registration.records) {
            // Process each download item
            console.log('Processing download:', download.id);
            // In a real implementation, this would save the file or process it
        }
    } catch (error) {
        console.error('Background fetch failed:', error);
    }
}
";

        return trim($serviceWorkerJs);
    }

    /**
     * Convert PHP array to JavaScript array string
     */
    private function arrayToJs(array $array): string
    {
        $items = array_map(function($item) {
            return is_string($item) ? "'{$item}'" : $item;
        }, $array);
        
        return '[' . implode(', ', $items) . ']';
    }

    /**
     * Convert regex array to JavaScript regex strings
     */
    private function regexArrayToJs(array $array): string
    {
        $items = array_map(function($item) {
            return "'{$item}'";
        }, $array);
        
        return implode(",\n    ", $items);
    }

    /**
     * Get PWA installation status for a user
     */
    public function getInstallationStatus(string $userId): array
    {
        $cacheKey = "pwa_installation_status_{$userId}";
        $status = \Cache::get($cacheKey, [
            'installed' => false,
            'installation_date' => null,
            'homescreen_added' => false,
            'push_notifications_enabled' => false,
            'offline_mode_enabled' => true,
            'data_savings_enabled' => false,
        ]);

        return [
            'status' => $status,
            'user_id' => $userId,
            'features_available' => array_keys($this->pwaFeatures),
        ];
    }

    /**
     * Set PWA installation status for a user
     */
    public function setInstallationStatus(string $userId, array $status): bool
    {
        $cacheKey = "pwa_installation_status_{$userId}";
        \Cache::put($cacheKey, $status, now()->addMonths(12));
        
        return true;
    }

    /**
     * Get offline content for a user
     */
    public function getOfflineContent(string $userId, array $context = []): array
    {
        // In a real implementation, this would get content based on user's recent behavior
        // For this example, we'll return sample offline content
        
        $offlineContent = [
            [
                'id' => 'offline-home',
                'type' => 'page',
                'title' => 'Welcome to VidiaSpot Marketplace',
                'url' => '/',
                'content_preview' => 'Browse, buy, and sell items in your local marketplace',
                'last_synced' => now()->subMinutes(5)->toISOString(),
                'size_kb' => 150,
            ],
            [
                'id' => 'offline-categories',
                'type' => 'page',
                'title' => 'Categories',
                'url' => '/categories',
                'content_preview' => 'Explore all available categories for browsing or selling',
                'last_synced' => now()->subMinutes(10)->toISOString(),
                'size_kb' => 200,
            ],
            [
                'id' => 'offline-my-ads',
                'type' => 'page',
                'title' => 'My Ads',
                'url' => '/my-ads',
                'content_preview' => 'Manage your active listings and drafts',
                'last_synced' => now()->subMinutes(15)->toISOString(),
                'size_kb' => 180,
            ],
            [
                'id' => 'offline-profile',
                'type' => 'page',
                'title' => 'Profile',
                'url' => '/profile',
                'content_preview' => 'Manage your account, settings, and preferences',
                'last_synced' => now()->subMinutes(30)->toISOString(),
                'size_kb' => 120,
            ],
            [
                'id' => 'offline-search',
                'type' => 'page',
                'title' => 'Search',
                'url' => '/search',
                'content_preview' => 'Find items based on your preferences',
                'last_synced' => now()->subMinutes(20)->toISOString(),
                'size_kb' => 250,
            ],
        ];

        // Add recently viewed items if available
        $recentlyViewed = $this->getRecentlyViewedContent($userId);
        
        foreach ($recentlyViewed as $item) {
            $offlineContent[] = [
                'id' => 'offline-viewed-' . $item['id'],
                'type' => 'content',
                'title' => $item['title'],
                'url' => $item['url'],
                'content_preview' => substr($item['description'] ?? '', 0, 100) . '...',
                'last_synced' => $item['viewed_at'],
                'size_kb' => 80, // Estimated size for individual content
            ];
        }

        return [
            'content' => $offlineContent,
            'total_size_kb' => array_sum(array_column($offlineContent, 'size_kb')),
            'calculated_at' => now()->toISOString(),
            'user_id' => $userId,
            'context' => $context,
        ];
    }

    /**
     * Get recently viewed content for offline use
     */
    private function getRecentlyViewedContent(string $userId): array
    {
        // In a real implementation, this would query a database for recently viewed content
        // For this example, we'll return sample data
        $recentViews = \Cache::get("user_recent_views_{$userId}", []);
        
        // Sort by most recent and limit to 5
        usort($recentViews, function($a, $b) {
            return strtotime($b['viewed_at']) <=> strtotime($a['viewed_at']);
        });
        
        return array_slice($recentViews, 0, 5);
    }

    /**
     * Preload content for offline use
     */
    public function preloadContentForOffline(string $userId, array $urls): array
    {
        $preloaded = [];
        
        foreach ($urls as $url) {
            // In a real implementation, this would actually fetch and cache the content
            // For this example, we'll simulate the process
            
            $contentId = 'preload-' . Str::slug($url) . '-' . time();
            
            $preloaded[] = [
                'content_id' => $contentId,
                'url' => $url,
                'size_estimation_kb' => mt_rand(50, 500),
                'preloaded_at' => now()->toISOString(),
                'status' => 'completed',
            ];
        }

        // Store preload history
        $preloadKey = "offline_preload_history_{$userId}";
        $history = \Cache::get($preloadKey, []);
        
        $history = array_merge($history, $preloaded);
        
        // Keep only last 50 preload items to prevent cache bloat
        if (count($history) > 50) {
            $history = array_slice($history, -50);
        }
        
        \Cache::put($preloadKey, $history, now()->addMonths(1));

        return [
            'preloaded' => $preloaded,
            'total_items' => count($preloaded),
            'total_size_estimate_kb' => array_sum(array_column($preloaded, 'size_estimation_kb')),
            'user_id' => $userId,
            'preload_completed_at' => now()->toISOString(),
        ];
    }

    /**
     * Sync offline data when user comes back online
     */
    public function syncOfflineData(string $userId, array $offlineData): array
    {
        $syncedItems = [];
        $failedItems = [];
        
        foreach ($offlineData as $item) {
            $itemId = $item['id'] ?? 'offline-item-' . Str::random(8);
            
            try {
                // In a real implementation, this would sync the data to the server
                // For this example, we'll simulate a successful sync
                
                $syncedItems[] = [
                    'item_id' => $itemId,
                    'original_action' => $item['action'] ?? 'unknown',
                    'synced_at' => now()->toISOString(),
                    'status' => 'success',
                ];
                
                // Remove from offline queue
                $this->removeFromOfflineQueue($userId, $itemId);
                
            } catch (\Exception $e) {
                $failedItems[] = [
                    'item_id' => $itemId,
                    'error' => $e->getMessage(),
                    'retry_possible' => true,
                ];
            }
        }

        return [
            'synced_items' => $syncedItems,
            'failed_items' => $failedItems,
            'user_id' => $userId,
            'sync_started_at' => now()->toISOString(),
            'sync_completed_at' => now()->toISOString(),
            'success_rate' => count($syncedItems) / max(1, count($offlineData)) * 100 . '%',
        ];
    }

    /**
     * Remove item from offline queue
     */
    private function removeFromOfflineQueue(string $userId, string $itemId): void
    {
        $queueKey = "offline_queue_{$userId}";
        $queue = \Cache::get($queueKey, []);
        
        $queue = array_filter($queue, function($item) use ($itemId) {
            return $item['id'] !== $itemId;
        });
        
        \Cache::put($queueKey, $queue, now()->addHours(24));
    }

    /**
     * Get offline data queue for a user
     */
    public function getOfflineDataQueue(string $userId): array
    {
        $queueKey = "offline_queue_{$userId}";
        $queue = \Cache::get($queueKey, []);
        
        return [
            'queue' => $queue,
            'item_count' => count($queue),
            'user_id' => $userId,
            'last_updated' => now()->toISOString(),
        ];
    }

    /**
     * Add data to offline queue
     */
    public function addToOfflineQueue(string $userId, array $data): string
    {
        $queueKey = "offline_queue_{$userId}";
        $queue = \Cache::get($queueKey, []);
        
        $itemId = 'offline-action-' . Str::uuid();
        
        $queueItem = [
            'id' => $itemId,
            'data' => $data,
            'added_at' => now()->toISOString(),
            'attempts' => 0,
            'last_attempt' => null,
        ];
        
        $queue[] = $queueItem;
        
        // Limit queue size to prevent bloat
        if (count($queue) > 100) {
            $queue = array_slice($queue, -100);
        }
        
        \Cache::put($queueKey, $queue, now()->addHours(24));
        
        return $itemId;
    }

    /**
     * Get PWA performance metrics
     */
    public function getPerformanceMetrics(string $userId = null): array
    {
        // In a real implementation, this would gather actual PWA performance data
        // For this example, we'll return simulated metrics
        
        $metrics = [
            'install_rate' => mt_rand(25, 45) . '%',
            'offline_usage' => mt_rand(15, 35) . '%',
            'push_notification_opt_in' => mt_rand(40, 65) . '%',
            'session_length_improvement' => mt_rand(20, 45) . '%',
            'return_visitors' => mt_rand(45, 75) . '%',
            'load_time_improvement' => mt_rand(30, 60) . '%',
            'data_usage_reduction' => mt_rand(25, 50) . '%',
            'user_satisfaction_score' => mt_rand(4, 5) . '/5',
            'reliability_score' => mt_rand(95, 99) . '%',
        ];

        if ($userId) {
            // Add user-specific metrics
            $userMetrics = [
                'pwa_features_used' => mt_rand(3, 6),
                'offline_sessions' => mt_rand(5, 50),
                'push_notifications_received' => mt_rand(10, 100),
                'data_saved_mb' => mt_rand(50, 500),
                'offline_time_savings' => mt_rand(10, 60) . ' minutes',
            ];
            
            $metrics = array_merge($metrics, $userMetrics);
        }

        return [
            'metrics' => $metrics,
            'user_id' => $userId,
            'calculated_at' => now()->toISOString(),
            'pwa_installed' => $userId ? $this->getInstallationStatus($userId)['status']['installed'] : null,
        ];
    }

    /**
     * Get PWA update information
     */
    public function getUpdateInfo(): array
    {
        return [
            'current_version' => '1.0.0',
            'update_available' => false,
            'last_updated' => now()->subDays(30)->toISOString(),
            'new_features' => [
                'improved_offline_experience',
                'better_performance',
                'enhanced_push_notifications',
            ],
            'update_size_mb' => 2.5,
            'estimated_installation_time' => '2-5 minutes',
        ];
    }

    /**
     * Get user's PWA preferences
     */
    public function getUserPwaPreferences(string $userId): array
    {
        $preferencesKey = "user_pwa_preferences_{$userId}";
        $preferences = \Cache::get($preferencesKey, [
            'dark_mode' => false,
            'data_savings_mode' => false,
            'push_notifications' => true,
            'background_sync' => true,
            'offline_content_autoload' => true,
            'language_override' => null,
            'accessibility_features' => [
                'high_contrast' => false,
                'large_text' => false,
                'reduced_motion' => false,
            ],
        ]);

        return [
            'preferences' => $preferences,
            'user_id' => $userId,
            'last_updated' => now()->toISOString(),
        ];
    }

    /**
     * Update user's PWA preferences
     */
    public function updateUserPwaPreferences(string $userId, array $preferences): bool
    {
        $preferencesKey = "user_pwa_preferences_{$userId}";
        $currentPrefs = \Cache::get($preferencesKey, []);

        $updatedPrefs = array_merge($currentPrefs, $preferences);
        $updatedPrefs['updated_at'] = now()->toISOString();

        \Cache::put($preferencesKey, $updatedPrefs, now()->addMonths(6));

        return true;
    }

    /**
     * Get PWA troubleshooting information
     */
    public function getTroubleshootingInfo(): array
    {
        return [
            'common_issues' => [
                [
                    'issue' => 'PWA not installing',
                    'solution' => 'Try refreshing the page and look for the install prompt in the browser toolbar',
                    'frequency' => 'common',
                ],
                [
                    'issue' => 'Offline mode not working',
                    'solution' => 'Check if service worker is enabled in browser settings',
                    'frequency' => 'moderate',
                ],
                [
                    'issue' => 'Push notifications not appearing',
                    'solution' => 'Ensure notifications are enabled in browser and app settings',
                    'frequency' => 'moderate',
                ],
                [
                    'issue' => 'Slow loading despite PWA',
                    'solution' => 'Try clearing browser cache and reinstalling the PWA',
                    'frequency' => 'rare',
                ],
            ],
            'browser_support' => [
                'chrome' => ['supported' => true, 'minimum_version' => '76'],
                'firefox' => ['supported' => true, 'minimum_version' => '72'],
                'safari' => ['supported' => true, 'minimum_version' => '11.1'],
                'edge' => ['supported' => true, 'minimum_version' => '79'],
                'opera' => ['supported' => true, 'minimum_version' => '63'],
            ],
            'last_updated' => now()->toISOString(),
            'contact_support' => [
                'email' => 'pwa-support@vidiaspot-marketplace.com',
                'documentation_url' => '/help/pwa-setup',
            ],
        ];
    }
}