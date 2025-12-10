<?php

namespace App\Http\Controllers;

use App\Services\PushNotificationService;
use App\Models\PushToken;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PushNotificationController extends Controller
{
    protected PushNotificationService $pushNotificationService;

    public function __construct(PushNotificationService $pushNotificationService)
    {
        $this->pushNotificationService = $pushNotificationService;
    }

    /**
     * Subscribe a device to push notifications
     */
    public function subscribe(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required|string',
            'device_type' => 'required|in:web,android,ios',
            'browser' => 'nullable|string',
            'platform' => 'nullable|string',
            'os_version' => 'nullable|string',
        ]);

        $userId = $request->user()->id;
        $deviceToken = $request->input('token');
        $deviceType = $request->input('device_type');

        // Validate the token
        if (!$this->pushNotificationService->validateToken($deviceToken, $deviceType)) {
            return response()->json([
                'success' => false,
                'error' => 'Invalid push notification token',
            ], 400);
        }

        $success = $this->pushNotificationService->subscribeDevice($userId, $deviceToken, $deviceType);

        if ($success) {
            return response()->json([
                'success' => true,
                'message' => 'Device subscribed to push notifications successfully',
            ]);
        } else {
            return response()->json([
                'success' => false,
                'error' => 'Failed to subscribe device',
            ], 500);
        }
    }

    /**
     * Unsubscribe a device from push notifications
     */
    public function unsubscribe(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        $userId = $request->user()->id;
        $deviceToken = $request->input('token');

        $success = $this->pushNotificationService->unsubscribeDevice($userId, $deviceToken);

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Device unsubscribed successfully' : 'Failed to unsubscribe device',
        ]);
    }

    /**
     * Send a test notification to the authenticated user
     */
    public function sendTestNotification(Request $request): JsonResponse
    {
        $user = $request->user();
        $title = $request->input('title', 'Test Notification');
        $body = $request->input('body', 'This is a test notification from VidiaSpot.');

        $success = $this->pushNotificationService->sendNotificationToUser($user->id, $title, $body, [
            'type' => 'test',
            'timestamp' => now()->toISOString(),
        ]);

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Test notification sent successfully' : 'Failed to send notification',
        ]);
    }

    /**
     * Send notification to a specific user
     */
    public function sendToUser(Request $request, int $userId): JsonResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string|max:500',
            'data' => 'array',
        ]);

        $success = $this->pushNotificationService->sendNotificationToUser(
            $userId,
            $request->input('title'),
            $request->input('body'),
            $request->input('data', [])
        );

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Notification sent successfully' : 'Failed to send notification',
        ]);
    }

    /**
     * Get user's notification preferences
     */
    public function getPreferences(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $preferences = $this->pushNotificationService->getUserNotificationPreferences($userId);

        return response()->json([
            'success' => true,
            'preferences' => $preferences,
        ]);
    }

    /**
     * Update user notification preferences
     */
    public function updatePreferences(Request $request): JsonResponse
    {
        $request->validate([
            'email_notifications' => 'boolean',
            'push_notifications' => 'boolean',
            'sms_notifications' => 'boolean',
            'in_app_notifications' => 'boolean',
            'notification_categories' => 'array',
            'notification_categories.*' => 'boolean',
        ]);

        $userId = $request->user()->id;
        $preferences = $request->all();

        $success = $this->pushNotificationService->updateUserNotificationPreferences($userId, $preferences);

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Preferences updated successfully' : 'Failed to update preferences',
        ]);
    }

    /**
     * Get user's push tokens
     */
    public function getTokens(Request $request): JsonResponse
    {
        $user = $request->user();

        $tokens = $user->pushTokens()->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'tokens' => $tokens->map(function ($token) {
                return [
                    'id' => $token->id,
                    'token_preview' => substr($token->token, 0, 10) . '...',
                    'device_type' => $token->device_type,
                    'browser' => $token->browser,
                    'platform' => $token->platform,
                    'is_active' => $token->is_active,
                    'last_used_at' => $token->last_used_at,
                    'created_at' => $token->created_at->toISOString(),
                ];
            }),
        ]);
    }

    /**
     * Get notification statistics for admin
     */
    public function getStats(): JsonResponse
    {
        $stats = $this->pushNotificationService->getNotificationStats();

        return response()->json([
            'success' => true,
            'stats' => $stats,
        ]);
    }

    /**
     * Send bulk notification to multiple users
     */
    public function sendBulk(Request $request): JsonResponse
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'integer|exists:users,id',
            'title' => 'required|string|max:255',
            'body' => 'required|string|max:500',
            'data' => 'array',
        ]);

        $results = $this->pushNotificationService->sendBulkNotification(
            $request->input('user_ids'),
            $request->input('title'),
            $request->input('body'),
            $request->input('data', [])
        );

        return response()->json([
            'success' => true,
            'results' => $results,
            'message' => "Notifications sent: {$results['success_count']} successful, {$results['failure_count']} failed",
        ]);
    }

    /**
     * Send targeted notification based on criteria
     */
    public function sendTargeted(Request $request): JsonResponse
    {
        $request->validate([
            'criteria' => 'required|array',
            'title' => 'required|string|max:255',
            'body' => 'required|string|max:500',
            'data' => 'array',
        ]);

        $results = $this->pushNotificationService->sendTargetedNotification(
            $request->input('criteria'),
            $request->input('title'),
            $request->input('body'),
            $request->input('data', [])
        );

        return response()->json([
            'success' => true,
            'results' => $results,
        ]);
    }

    /**
     * Send notification for ad activity
     */
    public function sendAdNotification(Request $request, int $adId): JsonResponse
    {
        $request->validate([
            'event' => 'required|in:view,favorite,message,offer,buy',
            'custom_data' => 'array',
        ]);

        $success = $this->pushNotificationService->sendAdActivityNotification(
            $adId,
            $request->input('event'),
            $request->input('custom_data', [])
        );

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Ad notification sent successfully' : 'Failed to send notification',
        ]);
    }

    /**
     * Notify seller of interest in their ad
     */
    public function notifySellerOfInterest(Request $request, int $adId): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'interest_type' => 'required|in:view,favorite,message,offer,buy',
        ]);

        $success = $this->pushNotificationService->notifySellerOfInterest(
            $adId,
            $request->input('user_id'),
            $request->input('interest_type')
        );

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Seller notified successfully' : 'Failed to notify seller',
        ]);
    }
}