<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserPreference;
use App\Services\RecommendationService;
use App\Services\NotificationPreferenceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    protected $recommendationService;
    protected $notificationService;

    public function __construct(RecommendationService $recommendationService, NotificationPreferenceService $notificationService)
    {
        $this->recommendationService = $recommendationService;
        $this->notificationService = $notificationService;
    }

    /**
     * Get personalized home feed for the user
     */
    public function getPersonalizedFeed(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Get personalized recommendations
        $recommendations = $this->recommendationService->getPersonalizedRecommendations($user, 20);
        
        // You can also get mood-based recommendations if mood is specified
        $mood = $request->get('mood');
        if ($mood) {
            $recommendations = $this->recommendationService->getMoodBasedRecommendations($user, $mood, 20);
        }

        return response()->json([
            'recommendations' => $recommendations,
            'mood' => $this->notificationService->getMoodState($user),
            'preferences' => $this->getUserPreferences($user)
        ]);
    }

    /**
     * Get user preferences
     */
    public function getUserPreferences(User $user)
    {
        $preferences = [
            'theme' => UserPreference::getPreference($user->id, 'theme', 'light'),
            'layout' => UserPreference::getPreference($user->id, 'layout', 'default'),
            'preferred_categories' => UserPreference::getPreference($user->id, 'preferred_categories', []),
            'preferred_locations' => UserPreference::getPreference($user->id, 'preferred_locations', []),
            'price_range' => UserPreference::getPreference($user->id, 'price_range', []),
            'notification_preferences' => $this->notificationService->getNotificationPreferences($user),
            'mood_state' => UserPreference::getPreference($user->id, 'mood_state', 'normal'),
        ];

        return $preferences;
    }

    /**
     * Update user preferences
     */
    public function updatePreferences(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $preferences = $request->only([
            'theme', 
            'layout', 
            'preferred_categories', 
            'preferred_locations', 
            'price_range',
            'mood_state'
        ]);

        foreach ($preferences as $key => $value) {
            if ($key === 'theme' || $key === 'layout') {
                UserPreference::setPreference($user->id, $key, $value);
            } elseif ($key === 'preferred_categories') {
                UserPreference::setPreference($user->id, $key, is_array($value) ? $value : []);
            } elseif ($key === 'preferred_locations') {
                UserPreference::setPreference($user->id, $key, is_array($value) ? $value : []);
            } elseif ($key === 'price_range') {
                UserPreference::setPreference($user->id, $key, is_array($value) ? $value : []);
            } elseif ($key === 'mood_state') {
                $this->notificationService->setMoodState($user, $value);
            }
        }

        // Update notification preferences if provided
        if ($request->has('notification_preferences')) {
            $this->notificationService->updateNotificationPreferences($user, $request->get('notification_preferences'));
        }

        return response()->json(['message' => 'Preferences updated successfully']);
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
            'behavior_type' => 'required|string',
            'target_type' => 'required|string',
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

        return response()->json(['message' => 'Behavior tracked successfully']);
    }

    /**
     * Update notification preferences
     */
    public function updateNotificationPreferences(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $this->notificationService->updateNotificationPreferences($user, $request->all());

        return response()->json(['message' => 'Notification preferences updated successfully']);
    }

    /**
     * Show personalization settings page
     */
    public function showPersonalizationSettings()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $moodState = $this->notificationService->getMoodState($user);
        $preferences = $this->getUserPreferences($user);

        return view('settings.personalization', compact('moodState', 'preferences'));
    }

    /**
     * Show notification settings page
     */
    public function showNotificationSettings()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $notificationPreferences = $this->notificationService->getNotificationPreferences($user);

        return view('settings.notifications', compact('notificationPreferences'));
    }
}