<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserPreference;
use Illuminate\Support\Facades\Mail;
use App\Mail\PersonalizedRecommendationNotification;

class NotificationPreferenceService
{
    /**
     * Get user's notification preferences
     */
    public function getNotificationPreferences(User $user)
    {
        $preferences = [
            'email' => [
                'enabled' => UserPreference::getPreference($user->id, 'email_notifications', true),
                'frequency' => UserPreference::getPreference($user->id, 'email_frequency', 'daily'),
                'types' => UserPreference::getPreference($user->id, 'email_notification_types', [
                    'recommendations' => true,
                    'new_ads_in_category' => true,
                    'price_alerts' => true,
                    'account_activity' => true,
                ])
            ],
            'push' => [
                'enabled' => UserPreference::getPreference($user->id, 'push_notifications', true),
                'types' => UserPreference::getPreference($user->id, 'push_notification_types', [
                    'recommendations' => true,
                    'messages' => true,
                    'account_activity' => true,
                ])
            ],
            'sms' => [
                'enabled' => UserPreference::getPreference($user->id, 'sms_notifications', false),
                'types' => UserPreference::getPreference($user->id, 'sms_notification_types', [
                    'important' => true,
                    'account_activity' => false,
                ])
            ]
        ];
        
        return $preferences;
    }
    
    /**
     * Update user's notification preferences
     */
    public function updateNotificationPreferences(User $user, array $preferences)
    {
        foreach ($preferences as $channel => $settings) {
            if (isset($settings['enabled'])) {
                UserPreference::setPreference($user->id, "{$channel}_notifications", $settings['enabled']);
            }
            
            if (isset($settings['frequency'])) {
                UserPreference::setPreference($user->id, "{$channel}_frequency", $settings['frequency']);
            }
            
            if (isset($settings['types'])) {
                UserPreference::setPreference($user->id, "{$channel}_notification_types", $settings['types']);
            }
        }
        
        return true;
    }
    
    /**
     * Send personalized recommendation notification
     */
    public function sendRecommendationNotification(User $user, $recommendations)
    {
        $prefs = $this->getNotificationPreferences($user);
        
        // Check if recommendations notification is enabled
        if ($prefs['email']['enabled'] && $prefs['email']['types']['recommendations'] ?? false) {
            // Send email notification
            Mail::to($user->email)->send(new PersonalizedRecommendationNotification($user, $recommendations));
        }
        
        // TODO: Add push notification and SMS sending logic here
    }
    
    /**
     * Set mood state for a user
     */
    public function setMoodState(User $user, $mood)
    {
        UserPreference::setPreference($user->id, 'mood_state', $mood);
    }
    
    /**
     * Get user's current mood state
     */
    public function getMoodState(User $user)
    {
        return UserPreference::getPreference($user->id, 'mood_state', 'normal');
    }
}