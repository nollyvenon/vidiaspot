<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\PushToken;

class PushNotificationService
{
    protected $webhookUrl;
    protected $apiKey;
    protected $redisService;

    public function __construct(RedisService $redisService)
    {
        $this->redisService = $redisService;
        $this->webhookUrl = env('PUSH_NOTIFICATION_WEBHOOK_URL');
        $this->apiKey = env('PUSH_NOTIFICATION_API_KEY');
    }

    /**
     * Send push notification to a user
     */
    public function sendNotificationToUser(int $userId, string $title, string $body, array $data = []): bool
    {
        $user = User::find($userId);
        if (!$user) {
            return false;
        }

        // Get all devices for the user
        $tokens = $user->pushTokens()->where('is_active', true)->get();

        $successCount = 0;
        foreach ($tokens as $token) {
            if ($this->sendNotificationToDevice($token->token, $title, $body, $data)) {
                $successCount++;
            }
        }

        return $successCount > 0;
    }

    /**
     * Send push notification to a specific device token
     */
    public function sendNotificationToDevice(string $deviceToken, string $title, string $body, array $data = []): bool
    {
        try {
            // Check if using Firebase Cloud Messaging (FCM)
            if (env('PUSH_SERVICE') === 'fcm') {
                return $this->sendWithFcm($deviceToken, $title, $body, $data);
            } 
            // Check if using Apple Push Notification Service (APNs)
            elseif (env('PUSH_SERVICE') === 'apns') {
                return $this->sendWithApns($deviceToken, $title, $body, $data);
            } 
            // For other services or for demo purposes
            else {
                return $this->sendWithGeneric($deviceToken, $title, $body, $data);
            }
        } catch (\Exception $e) {
            Log::error('Push notification failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send notification using Firebase Cloud Messaging
     */
    protected function sendWithFcm(string $deviceToken, string $title, string $body, array $data = []): bool
    {
        $fcmUrl = 'https://fcm.googleapis.com/fcm/send';
        $serverKey = env('FCM_SERVER_KEY');

        $payload = [
            'to' => $deviceToken,
            'notification' => [
                'title' => $title,
                'body' => $body,
                'sound' => 'default',
                'badge' => 1,
            ],
            'data' => $data,
        ];

        $response = Http::withHeaders([
            'Authorization' => 'key=' . $serverKey,
            'Content-Type' => 'application/json',
        ])->post($fcmUrl, $payload);

        return $response->successful();
    }

    /**
     * Send notification using Apple Push Notification Service
     */
    protected function sendWithApns(string $deviceToken, string $title, string $body, array $data = []): bool
    {
        // This would use Apple's APNs service
        // Implementation would require APNs certificate or token-based authentication
        // For this demo, we'll return true
        return true;
    }

    /**
     * Send notification using a generic service
     */
    protected function sendWithGeneric(string $deviceToken, string $title, string $body, array $data = []): bool
    {
        // For demonstration, we'll just return true
        // In a real implementation, you would integrate with your chosen push notification service
        
        // Log the notification for debugging
        Log::info('Push notification sent', [
            'device_token' => $deviceToken,
            'title' => $title,
            'body' => $body,
            'data' => $data,
        ]);

        return true;
    }

    /**
     * Subscribe a device to receive push notifications
     */
    public function subscribeDevice(int $userId, string $deviceToken, string $deviceType = 'web'): bool
    {
        try {
            $user = User::find($userId);
            if (!$user) {
                return false;
            }

            // Check if token already exists for this user
            $existingToken = $user->pushTokens()->where('token', $deviceToken)->first();

            if ($existingToken) {
                // Update the existing token
                $existingToken->update([
                    'device_type' => $deviceType,
                    'is_active' => true,
                    'last_used_at' => now(),
                ]);
            } else {
                // Create a new token record
                $user->pushTokens()->create([
                    'token' => $deviceToken,
                    'device_type' => $deviceType,
                    'is_active' => true,
                    'last_used_at' => now(),
                ]);
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to subscribe device: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Unsubscribe a device from push notifications
     */
    public function unsubscribeDevice(int $userId, string $deviceToken): bool
    {
        try {
            $user = User::find($userId);
            if (!$user) {
                return false;
            }

            $tokenRecord = $user->pushTokens()->where('token', $deviceToken)->first();
            if ($tokenRecord) {
                $tokenRecord->update(['is_active' => false]);
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to unsubscribe device: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send bulk notification to multiple users
     */
    public function sendBulkNotification(array $userIds, string $title, string $body, array $data = []): array
    {
        $results = [
            'success_count' => 0,
            'failure_count' => 0,
        ];

        foreach ($userIds as $userId) {
            if ($this->sendNotificationToUser($userId, $title, $body, $data)) {
                $results['success_count']++;
            } else {
                $results['failure_count']++;
            }
        }

        return $results;
    }

    /**
     * Send targeted notification to users by criteria
     */
    public function sendTargetedNotification(array $criteria, string $title, string $body, array $data = []): array
    {
        $query = User::where('is_active', true);

        // Apply criteria filters
        if (isset($criteria['role'])) {
            $query->whereHas('roles', function($q) use ($criteria) {
                $q->where('name', $criteria['role']);
            });
        }

        if (isset($criteria['location'])) {
            $query->where('location', 'LIKE', '%' . $criteria['location'] . '%');
        }

        if (isset($criteria['last_login_after'])) {
            $query->where('last_login_at', '>', $criteria['last_login_after']);
        }

        $users = $query->get();
        $userIds = $users->pluck('id')->toArray();

        return $this->sendBulkNotification($userIds, $title, $body, $data);
    }

    /**
     * Send notification to users based on ad activity
     */
    public function sendAdActivityNotification(int $adId, string $event, array $customData = []): bool
    {
        // This would notify users who might be interested in this ad
        // For example: users who saved similar ads, users in the same location, etc.
        
        $notificationData = array_merge([
            'ad_id' => $adId,
            'event' => $event,
            'timestamp' => now()->toISOString(),
        ], $customData);

        // In a real implementation, we would identify interested users
        // For now, this is a placeholder
        return true;
    }

    /**
     * Send notification to seller about interest in their ad
     */
    public function notifySellerOfInterest(int $adId, int $interestedUserId, string $interestType = 'view'): bool
    {
        // Get the ad and its owner
        $ad = \App\Models\Ad::find($adId);
        if (!$ad) {
            return false;
        }

        $notificationTitle = 'New Interest in Your Ad';
        $notificationBody = '';
        
        switch ($interestType) {
            case 'view':
                $notificationBody = "Someone viewed your ad: {$ad->title}";
                break;
            case 'favorite':
                $notificationBody = "Someone saved your ad to favorites: {$ad->title}";
                break;
            case 'message':
                $notificationBody = "Someone messaged you about your ad: {$ad->title}";
                break;
            case 'offer':
                $notificationBody = "Someone made an offer on your ad: {$ad->title}";
                break;
            default:
                $notificationBody = "There's new activity on your ad: {$ad->title}";
                break;
        }

        $notificationData = [
            'ad_id' => $adId,
            'interested_user_id' => $interestedUserId,
            'interest_type' => $interestType,
            'notification_type' => 'ad_interest',
        ];

        return $this->sendNotificationToUser($ad->user_id, $notificationTitle, $notificationBody, $notificationData);
    }

    /**
     * Send system-wide notification
     */
    public function sendSystemNotification(string $title, string $body, array $targetUsers = null, array $data = []): array
    {
        if (!$targetUsers) {
            // Target all active users
            $targetUsers = User::where('is_active', true)->pluck('id')->toArray();
        }

        return $this->sendBulkNotification($targetUsers, $title, $body, $data);
    }

    /**
     * Get notification preferences for a user
     */
    public function getUserNotificationPreferences(int $userId): array
    {
        // In a real implementation, this would get from a user preferences table
        // For now, return default preferences
        return [
            'email_notifications' => true,
            'push_notifications' => true,
            'sms_notifications' => false,
            'in_app_notifications' => true,
            'notification_categories' => [
                'marketing' => true,
                'promotions' => true,
                'orders' => true,
                'security' => true,
                'social' => true,
            ],
        ];
    }

    /**
     * Update notification preferences for a user
     */
    public function updateUserNotificationPreferences(int $userId, array $preferences): bool
    {
        // In a real implementation, this would update a user preferences table
        // For now, just return true
        return true;
    }

    /**
     * Get notification stats for admin dashboard
     */
    public function getNotificationStats(): array
    {
        // This would typically aggregate from a notification logs table
        // For demo purposes, returning mock data
        return [
            'total_sent' => 1250,
            'delivered' => 1200,
            'open_rate' => 0.68, // 68%
            'click_rate' => 0.12, // 12%
            'top_notification_types' => [
                ['type' => 'orders', 'count' => 450],
                ['type' => 'ad_interest', 'count' => 320],
                ['type' => 'promotions', 'count' => 180],
                ['type' => 'system', 'count' => 150],
                ['type' => 'social', 'count' => 100],
            ],
            'device_distribution' => [
                'mobile' => 78,
                'web' => 15,
                'tablet' => 7,
            ],
        ];
    }

    /**
     * Validate a push notification token
     */
    public function validateToken(string $deviceToken, string $deviceType = 'web'): bool
    {
        // This would typically call the service's validation endpoint
        // For now, just check if it's a valid format
        if ($deviceType === 'fcm') {
            // Check if it looks like a valid FCM token (163 characters, alphanumeric + dashes/underscores)
            return preg_match('/^[A-Za-z0-9_-]{163}$/', $deviceToken);
        } elseif ($deviceType === 'apns') {
            // Check if it looks like a valid APNs token (64 hex characters)
            return preg_match('/^[A-Fa-f0-9]{64}$/', $deviceToken);
        } else {
            // For other types, just check it's not empty
            return !empty($deviceToken);
        }
    }
}