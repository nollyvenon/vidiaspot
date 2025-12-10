<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\MobileDevice;
use App\Models\NotificationPreference;

class MobileService
{
    protected $firebaseApiUrl = 'https://fcm.googleapis.com/fcm/send';
    
    public function __construct()
    {
        $this->firebaseApiUrl = config('services.firebase.api_url', 'https://fcm.googleapis.com/fcm/send');
    }

    /**
     * Register a mobile device for push notifications
     */
    public function registerMobileDevice($userId, $deviceId, $deviceToken, $deviceInfo = [])
    {
        $device = MobileDevice::updateOrCreate(
            [
                'user_id' => $userId,
                'device_id' => $deviceId,
            ],
            [
                'device_token' => $deviceToken,
                'platform' => $deviceInfo['platform'] ?? null,
                'os_version' => $deviceInfo['os_version'] ?? null,
                'app_version' => $deviceInfo['app_version'] ?? null,
                'manufacturer' => $deviceInfo['manufacturer'] ?? null,
                'model' => $deviceInfo['model'] ?? null,
                'last_active_at' => now(),
            ]
        );

        return $device;
    }

    /**
     * Send push notification to mobile device
     */
    public function sendPushNotification($deviceToken, $title, $body, $data = [])
    {
        $apiKey = config('services.firebase.server_key');
        if (!$apiKey) {
            \Log::warning('Firebase server key not configured');
            return ['success' => false, 'message' => 'Firebase server key not configured'];
        }

        $payload = [
            'to' => $deviceToken,
            'notification' => [
                'title' => $title,
                'body' => $body,
                'sound' => 'default',
                'badge' => 1,
            ],
            'data' => $data,
            'android' => [
                'notification' => [
                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK'
                ]
            ],
            'apns' => [
                'payload' => [
                    'aps' => [
                        'badge' => 1,
                        'sound' => 'default'
                    ]
                ]
            ]
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => 'key=' . $apiKey,
                'Content-Type' => 'application/json'
            ])->post($this->firebaseApiUrl, $payload);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message_id' => $response->json()['message_id'] ?? null,
                    'response' => $response->json()
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to send notification',
                    'error' => $response->body()
                ];
            }
        } catch (\Exception $e) {
            \Log::error('Push notification error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Exception occurred while sending notification',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Send price alert notification
     */
    public function sendPriceAlertNotification($userId, $adId, $targetPrice, $currentPrice)
    {
        $user = User::find($userId);
        $deviceTokens = $user->mobileDevices()->pluck('device_token')->toArray();

        if (empty($deviceTokens)) {
            return ['success' => false, 'message' => 'No registered mobile devices found'];
        }

        $title = 'Price Alert Triggered!';
        $body = "The item you're watching has dropped to ₦" . number_format($currentPrice) . ", which is at or below your target of ₦" . number_format($targetPrice);

        $notificationData = [
            'type' => 'price_alert',
            'ad_id' => $adId,
            'target_price' => $targetPrice,
            'current_price' => $currentPrice,
            'timestamp' => now()->toISOString()
        ];

        $results = [];
        foreach ($deviceTokens as $token) {
            $result = $this->sendPushNotification($token, $title, $body, $notificationData);
            $results[] = $result;
        }

        return [
            'success' => true,
            'results' => $results,
            'message' => 'Price alert notifications sent'
        ];
    }

    /**
     * Handle biometric authentication request
     */
    public function handleBiometricAuth($userId, $biometricData)
    {
        // In a real implementation, this would verify biometric data against stored templates
        // For now, we'll simulate the process
        
        $user = User::find($userId);
        if (!$user) {
            return ['success' => false, 'message' => 'User not found'];
        }

        // Check if user has biometric data stored
        if (!$user->biometric_data) {
            return [
                'success' => false,
                'message' => 'No biometric data enrolled',
                'enroll_required' => true
            ];
        }

        // Simulate biometric verification (in real app, use actual biometric verification)
        $simulatedMatch = $this->simulateBiometricVerification($biometricData, $user->biometric_data);
        
        if ($simulatedMatch['confidence'] > 0.8) {
            // Log successful biometric authentication
            activity()
                ->performedOn($user)
                ->withProperties(['biometric_auth' => true, 'device' => $biometricData['device'] ?? null])
                ->log('biometric_auth_success');

            return [
                'success' => true,
                'message' => 'Biometric authentication successful',
                'confidence' => $simulatedMatch['confidence']
            ];
        } else {
            // Log failed biometric authentication
            activity()
                ->performedOn($user)
                ->withProperties(['biometric_auth' => true, 'device' => $biometricData['device'] ?? null])
                ->log('biometric_auth_failed');

            return [
                'success' => false,
                'message' => 'Biometric verification failed',
                'confidence' => $simulatedMatch['confidence'],
                'retry' => true
            ];
        }
    }

    /**
     * Simulate biometric verification (placeholder for actual biometric service)
     */
    private function simulateBiometricVerification($inputData, $storedData)
    {
        // In a real implementation, this would use actual biometric verification algorithms
        // For simulation, we'll return a confidence score based on some logic
        
        $confidence = 0.5; // Base confidence
        
        // Add factors that might increase confidence
        if (isset($inputData['device']) && isset($storedData['device'])) {
            $confidence += 0.2; // Device matches
        }
        
        if (isset($inputData['timestamp'])) {
            $timeDiff = abs(time() - strtotime($inputData['timestamp']));
            if ($timeDiff < 300) { // Within 5 minutes
                $confidence += 0.1;
            }
        }
        
        // Add some randomness
        $confidence += (mt_rand(-10, 10) / 100);
        
        return [
            'confidence' => min(1.0, max(0, $confidence)),
            'verified' => $confidence > 0.8
        ];
    }

    /**
     * Process QR code scan for transaction
     */
    public function processQRCodeTransaction($userId, $qrData)
    {
        // Validate QR data structure
        if (!isset($qrData['transaction_type']) || !isset($qrData['data'])) {
            return ['success' => false, 'message' => 'Invalid QR code data'];
        }

        $transactionType = $qrData['transaction_type'];
        $transactionData = $qrData['data'];

        switch ($transactionType) {
            case 'payment_request':
                return $this->processPaymentQR($userId, $transactionData);
            case 'product_info':
                return $this->getProductInfoFromQR($transactionData);
            case 'user_transfer':
                return $this->processUserTransferQR($userId, $transactionData);
            case 'location_checkin':
                return $this->processLocationCheckin($userId, $transactionData);
            default:
                return ['success' => false, 'message' => 'Unsupported QR code type'];
        }
    }

    /**
     * Process payment QR code
     */
    private function processPaymentQR($userId, $paymentData)
    {
        // Validate payment data
        if (!isset($paymentData['amount']) || !isset($paymentData['recipient_id'])) {
            return ['success' => false, 'message' => 'Invalid payment data in QR code'];
        }

        // Create a payment intent/transaction record
        $transaction = [
            'user_id' => $userId,
            'recipient_id' => $paymentData['recipient_id'],
            'amount' => $paymentData['amount'],
            'currency' => $paymentData['currency'] ?? 'NGN',
            'description' => $paymentData['description'] ?? 'Payment via QR code',
            'payment_method' => 'qr_code',
            'status' => 'pending',
            'qr_session_id' => $paymentData['session_id'] ?? Str::uuid(),
        ];

        // In a real implementation, this would create a transaction record in the database
        // For now, we'll just return the transaction data
        return [
            'success' => true,
            'transaction' => $transaction,
            'message' => 'Payment QR code processed successfully'
        ];
    }

    /**
     * Get product information from QR code
     */
    private function getProductInfoFromQR($productData)
    {
        if (!isset($productData['product_id'])) {
            return ['success' => false, 'message' => 'Product ID not found in QR code'];
        }

        // In a real implementation, this would fetch product details from the database
        // For now, we'll return mock data
        $product = [
            'id' => $productData['product_id'],
            'name' => 'Mock Product Name',
            'price' => 50000,
            'description' => 'Product details from QR code',
            'images' => [],
            'seller_info' => [
                'name' => 'Mock Seller',
                'rating' => 4.5,
                'verification_status' => 'verified'
            ]
        ];

        return [
            'success' => true,
            'product' => $product,
            'message' => 'Product information retrieved'
        ];
    }

    /**
     * Process user transfer QR code
     */
    private function processUserTransferQR($userId, $transferData)
    {
        if (!isset($transferData['recipient_identifier'])) {
            return ['success' => false, 'message' => 'Recipient identifier not found in QR code'];
        }

        // In a real implementation, this would process the transfer
        return [
            'success' => true,
            'recipient_info' => [
                'identifier' => $transferData['recipient_identifier'],
                'verified' => true
            ],
            'message' => 'User transfer QR processed successfully'
        ];
    }

    /**
     * Process location check-in QR code
     */
    private function processLocationCheckin($userId, $locationData)
    {
        if (!isset($locationData['location_id'])) {
            return ['success' => false, 'message' => 'Location ID not found in QR code'];
        }

        // Log the check-in
        activity()
            ->performedOn(User::find($userId))
            ->causedBy(User::find($userId))
            ->withProperties([
                'location_id' => $locationData['location_id'],
                'checkin_type' => 'qr_checkin',
                'timestamp' => now()
            ])
            ->log('location_checkin');

        return [
            'success' => true,
            'message' => 'Location check-in recorded successfully'
        ];
    }

    /**
     * Enable offline mode with sync capability
     */
    public function enableOfflineMode($userId, $options = [])
    {
        // The offline synchronization is already handled by the RealTimeDataSynchronizationService
        // Here we'll just prepare the offline package for the mobile app
        
        $user = User::find($userId);
        if (!$user) {
            return ['success' => false, 'message' => 'User not found'];
        }

        // Prepare offline data package
        $offlinePackage = [
            'user_profile' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'verification_status' => $user->is_verified,
            ],
            'user_ads' => $user->ads()->limit(50)->get()->toArray(),
            'favorites' => $user->favorites()->limit(50)->get()->map(function($fav) {
                return [
                    'id' => $fav->id,
                    'title' => $fav->title,
                    'price' => $fav->price,
                    'location' => $fav->location,
                    'images' => $fav->images
                ];
            })->toArray(),
            'watchlist' => $user->watchlist()->limit(50)->get()->map(function($item) {
                return [
                    'id' => $item->id,
                    'target_price' => $item->target_price,
                    'current_price' => $item->current_price ?? null,
                    'status' => $item->status
                ];
            })->toArray(),
            'recent_searches' => $user->searchHistory()->limit(20)->pluck('query')->toArray(),
            'notification_preferences' => $user->notificationPreferences ?? [],
            'last_sync_timestamp' => now()->toISOString(),
        ];

        return [
            'success' => true,
            'offline_package' => $offlinePackage,
            'size' => strlen(json_encode($offlinePackage)),
            'expires_at' => now()->addHours(24),
            'message' => 'Offline package prepared successfully'
        ];
    }

    /**
     * Sync offline data when back online
     */
    public function syncOfflineData($userId, $offlineData)
    {
        $user = User::find($userId);
        if (!$user) {
            return ['success' => false, 'message' => 'User not found'];
        }

        $syncResults = [
            'updated_ads' => 0,
            'new_ads' => 0,
            'notifications' => 0,
            'profile_updates' => 0,
            'favorites' => 0,
            'errors' => []
        ];

        try {
            // Sync ads if any exist in offline data
            if (isset($offlineData['ads'])) {
                foreach ($offlineData['ads'] as $ad) {
                    if (isset($ad['id'])) {
                        // Update existing ad
                        $existingAd = $user->ads()->find($ad['id']);
                        if ($existingAd) {
                            $existingAd->update($ad);
                            $syncResults['updated_ads']++;
                        }
                    } else {
                        // Create new ad
                        $newAd = $user->ads()->create($ad);
                        $syncResults['new_ads']++;
                    }
                }
            }

            // Sync profile updates
            if (isset($offlineData['profile'])) {
                $user->update($offlineData['profile']);
                $syncResults['profile_updates']++;
            }

            // Log the sync event
            activity()
                ->performedOn($user)
                ->withProperties([
                    'sync_data' => $offlineData,
                    'results' => $syncResults
                ])
                ->log('offline_sync');

        } catch (\Exception $e) {
            $syncResults['errors'][] = $e->getMessage();
        }

        return [
            'success' => empty($syncResults['errors']),
            'sync_results' => $syncResults,
            'message' => 'Offline data sync completed'
        ];
    }

    /**
     * Get mobile app configuration
     */
    public function getMobileAppConfig($platform = 'flutter')
    {
        return [
            'app_version' => config('app.mobile_app_version', '1.0.0'),
            'api_endpoint' => config('app.url') . '/api',
            'websocket_url' => config('app.websocket_url', null),
            'features' => [
                'push_notifications' => true,
                'biometric_auth' => true,
                'qr_scanning' => true,
                'offline_mode' => true,
                'location_services' => true,
                'camera_access' => true,
                'gallery_access' => true,
            ],
            'settings' => [
                'image_compression' => [
                    'quality' => config('mobile.image_compression_quality', 80),
                    'max_size' => config('mobile.image_max_size', 5000000), // 5MB
                ],
                'offline_sync' => [
                    'enabled' => true,
                    'max_items' => 100,
                    'sync_interval' => 300, // 5 minutes
                ],
                'push_notifications' => [
                    'enabled' => true,
                    'default_topics' => ['general', 'promotions', 'price_alerts'],
                ]
            ],
            'permissions' => [
                'camera' => 'For capturing product photos and scanning QR codes',
                'location' => 'For location-based services and ads',
                'storage' => 'For saving images and documents',
                'notifications' => 'For important updates and alerts',
            ],
            'platform_specific' => [
                'ios' => [
                    'use_face_id' => true,
                    'use_touch_id' => true,
                ],
                'android' => [
                    'use_fingerprint' => true,
                    'use_face_unlock' => true,
                ]
            ]
        ];
    }

    /**
     * Check mobile app version compatibility
     */
    public function checkVersionCompatibility($currentVersion, $platform = 'flutter')
    {
        $latestVersion = config('app.mobile_app_version', '1.0.0');
        $minSupportedVersion = config('app.min_supported_version', '1.0.0');

        return [
            'current_version' => $currentVersion,
            'latest_version' => $latestVersion,
            'min_supported_version' => $minSupportedVersion,
            'update_required' => version_compare($currentVersion, $minSupportedVersion, '<'),
            'is_latest' => version_compare($currentVersion, $latestVersion, '>='),
            'upgrade_available' => version_compare($currentVersion, $latestVersion, '<'),
            'compatibility_score' => $this->calculateCompatibilityScore($currentVersion, $minSupportedVersion)
        ];
    }

    /**
     * Calculate compatibility score based on version
     */
    private function calculateCompatibilityScore($currentVersion, $minSupportedVersion)
    {
        if (version_compare($currentVersion, $minSupportedVersion, '>=')) {
            // Versions are compatible, score based on how close to latest
            $latestVersion = config('app.mobile_app_version', '1.0.0');
            $versionDiff = version_compare($latestVersion, $currentVersion, '-');
            
            if (version_compare($currentVersion, $latestVersion, '>=')) {
                return 100; // Latest version
            } else {
                // Calculate score based on version difference
                $diff = abs($versionDiff);
                return max(50, 100 - ($diff * 10)); // Decrease score as versions differ
            }
        } else {
            // Version is below minimum supported
            return 20; // Low compatibility
        }
    }
}