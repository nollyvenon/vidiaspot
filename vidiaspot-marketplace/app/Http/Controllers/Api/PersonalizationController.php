<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\RecommendationService;
use App\Services\NotificationPreferenceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PersonalizationController extends Controller
{
    protected $recommendationService;
    protected $notificationService;

    public function __construct(RecommendationService $recommendationService, NotificationPreferenceService $notificationService)
    {
        $this->recommendationService = $recommendationService;
        $this->notificationService = $notificationService;
    }

    /**
     * Get personalized home feed
     */
    public function getPersonalizedFeed(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Get personalized recommendations
        $recommendations = $this->recommendationService->getPersonalizedRecommendations($user, 20);
        
        // Apply mood-based filtering if specified
        $mood = $request->get('mood');
        if ($mood) {
            $recommendations = $this->recommendationService->getMoodBasedRecommendations($user, $mood, 20);
        }

        return response()->json([
            'data' => [
                'recommendations' => $recommendations,
                'mood' => $this->notificationService->getMoodState($user),
            ]
        ]);
    }

    /**
     * Track user behavior
     */
    public function trackBehavior(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'behavior_type' => 'required|string|in:view,click,like,purchase,search,share,favorite,comment',
            'target_type' => 'required|string|in:ad,category,user',
            'target_id' => 'required|integer',
            'metadata' => 'array'
        ]);

        $this->recommendationService->trackBehavior(
            $user->id,
            $validated['behavior_type'],
            $validated['target_type'],
            $validated['target_id'],
            $validated['metadata'] ?? []
        );

        return response()->json([
            'message' => 'Behavior tracked successfully',
            'data' => [
                'behavior_type' => $validated['behavior_type'],
                'target_type' => $validated['target_type'],
                'target_id' => $validated['target_id'],
            ]
        ]);
    }

    /**
     * Get user preferences
     */
    public function getUserPreferences()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $preferences = [
            'theme' => \App\Models\UserPreference::getPreference($user->id, 'theme', 'light'),
            'layout' => \App\Models\UserPreference::getPreference($user->id, 'layout', 'default'),
            'preferred_categories' => \App\Models\UserPreference::getPreference($user->id, 'preferred_categories', []),
            'preferred_locations' => \App\Models\UserPreference::getPreference($user->id, 'preferred_locations', []),
            'price_range' => \App\Models\UserPreference::getPreference($user->id, 'price_range', []),
            'notification_preferences' => $this->notificationService->getNotificationPreferences($user),
            'mood_state' => \App\Models\UserPreference::getPreference($user->id, 'mood_state', 'normal'),
        ];

        return response()->json([
            'data' => $preferences
        ]);
    }

    /**
     * Update user preferences
     */
    public function updateUserPreferences(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'theme' => 'nullable|string|in:light,dark,auto',
            'layout' => 'nullable|string|in:default,compact,card',
            'preferred_categories' => 'nullable|array',
            'preferred_locations' => 'nullable|array',
            'price_range' => 'nullable|array',
            'mood_state' => 'nullable|string|in:normal,excited,home,luxury,practical',
            'notification_preferences' => 'nullable|array'
        ]);

        $preferences = $request->only([
            'theme', 
            'layout', 
            'preferred_categories', 
            'preferred_locations', 
            'price_range'
        ]);

        foreach ($preferences as $key => $value) {
            if ($value !== null) {
                \App\Models\UserPreference::setPreference($user->id, $key, $value);
            }
        }

        // Handle mood state
        if ($request->has('mood_state') && $request->mood_state) {
            $this->notificationService->setMoodState($user, $request->mood_state);
        }

        // Handle notification preferences
        if ($request->has('notification_preferences')) {
            $this->notificationService->updateNotificationPreferences($user, $request->notification_preferences);
        }

        return response()->json([
            'message' => 'Preferences updated successfully',
            'data' => $this->getUserPreferences()->getData()
        ]);
    }
}