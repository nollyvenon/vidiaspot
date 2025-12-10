<?php

namespace App\Services;

use App\Models\Message;
use App\Models\Conversation;
use App\Models\VideoCall;
use App\Models\Scheduling;
use App\Models\Escrow;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SmartMessagingService
{
    /**
     * Get AI-powered smart reply suggestions
     */
    public function getSmartReplies($messageContent, $context = [])
    {
        // In a real implementation, this would call an AI service
        // For now, we'll return some common smart replies based on keywords
        
        $replies = [
            'positive' => [
                'That sounds good!',
                'I\'m interested, can you tell me more?',
                'When are you available to meet?',
                'What time works for you?',
                'Thanks for the information!'
            ],
            'negative' => [
                'I\'m not interested, thanks',
                'That\'s too expensive for me',
                'I found something else',
                'I\'ll think about it'
            ],
            'neutral' => [
                'Can you provide more details?',
                'Where is it located?',
                'Is it in good condition?',
                'Can I come to see it?',
                'How long have you had this?'
            ]
        ];
        
        // Determine which set of replies to use based on the message content
        $lowerContent = strtolower($messageContent);
        
        if (strpos($lowerContent, 'price') !== false || strpos($lowerContent, 'cost') !== false) {
            return [
                'Can you negotiate the price?',
                'Is the price firm?',
                'Would you consider any offers?',
                'What is your best price?'
            ];
        }
        
        if (strpos($lowerContent, 'when') !== false || strpos($lowerContent, 'time') !== false) {
            return [
                'I\'m available on weekends',
                'Weekdays work better for me',
                'Morning or afternoon?',
                'What time is convenient for you?'
            ];
        }
        
        // Return generic appropriate replies based on sentiment
        return $replies['neutral'];
    }
    
    /**
     * Translate message content between languages using Google Translate API
     */
    public function translateMessage($text, $fromLanguage = 'en', $toLanguage = 'en')
    {
        if ($fromLanguage === $toLanguage || empty($text)) {
            return $text;
        }

        // Google Translate API configuration
        $apiKey = config('services.google.translate_api_key');
        if (!$apiKey) {
            // If no API key is configured, return original text
            return $text;
        }

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post('https://translation.googleapis.com/language/translate/v2', [
                'key' => $apiKey,
                'q' => $text,
                'source' => $fromLanguage,
                'target' => $toLanguage,
                'format' => 'text',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['data']['translations'][0]['translatedText'])) {
                    return $data['data']['translations'][0]['translatedText'];
                }
            }
        } catch (\Exception $e) {
            \Log::error('Translation API Error: ' . $e->getMessage());
            // Return original text if translation fails
        }

        // Fallback to original text if API call fails
        return $text;
    }
    
    /**
     * Transcribe voice message content using Google Speech-to-Text API
     */
    public function transcribeVoiceMessage($audioFilePath)
    {
        // Google Speech-to-Text API configuration
        $apiKey = config('services.google.speech_to_text_api_key');
        if (!$apiKey) {
            // If no API key is configured, return a placeholder
            return "Transcription of voice message: [Transcription would appear here if this were a real implementation]";
        }

        // Check if the audio file exists
        if (!file_exists($audioFilePath)) {
            return "Transcription of voice message: [Audio file not found]";
        }

        // Determine the encoding based on file extension
        $extension = pathinfo($audioFilePath, PATHINFO_EXTENSION);
        $encoding = $this->getAudioEncoding($extension);

        $sampleRateHertz = 16000; // Standard sample rate
        $languageCode = 'en-US'; // Default language, could be configurable

        try {
            // Read the audio file content
            $audioContent = base64_encode(file_get_contents($audioFilePath));

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post('https://speech.googleapis.com/v1/speech:recognize?key=' . $apiKey, [
                'config' => [
                    'encoding' => $encoding,
                    'sampleRateHertz' => $sampleRateHertz,
                    'languageCode' => $languageCode,
                ],
                'audio' => [
                    'content' => $audioContent,
                ],
            ]);

            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['results']) && !empty($data['results'])) {
                    $transcript = '';
                    foreach ($data['results'] as $result) {
                        if (isset($result['alternatives'][0]['transcript'])) {
                            $transcript .= $result['alternatives'][0]['transcript'] . ' ';
                        }
                    }
                    return trim($transcript);
                }
            }
        } catch (\Exception $e) {
            \Log::error('Speech-to-Text API Error: ' . $e->getMessage());
            // Return a meaningful message if transcription fails
        }

        // Fallback to placeholder if API call fails
        return "Transcription of voice message: [Transcription failed - audio could not be processed]";
    }

    /**
     * Determine the encoding type based on file extension
     */
    private function getAudioEncoding($extension)
    {
        $extensionMap = [
            'mp3' => 'MP3',
            'wav' => 'LINEAR16',
            'flac' => 'FLAC',
            'm4a' => 'MPEG_AUDIO',
            'ogg' => 'OGG_OPUS',
            'webm' => 'WEBM_OPUS',
        ];

        return $extensionMap[strtolower($extension)] ?? 'LINEAR16';
    }
    
    /**
     * Create a new conversation or get existing one
     */
    public function getOrCreateConversation($user1Id, $user2Id, $adId = null)
    {
        // Check if a conversation already exists between these two users for this ad
        $conversation = Conversation::where(function($query) use ($user1Id, $user2Id) {
            $query->where('user1_id', $user1Id)->where('user2_id', $user2Id);
        })->orWhere(function($query) use ($user1Id, $user2Id) {
            $query->where('user1_id', $user2Id)->where('user2_id', $user1Id);
        })->when($adId, function($query) use ($adId) {
            $query->where('ad_id', $adId);
        })->first();
        
        if (!$conversation) {
            $conversation = Conversation::create([
                'user1_id' => $user1Id,
                'user2_id' => $user2Id,
                'ad_id' => $adId,
                'is_active' => true,
            ]);
        }
        
        return $conversation;
    }
    
    /**
     * Send a message in a conversation
     */
    public function sendMessage($conversationId, $senderId, $content, $messageType = 'text', $additionalData = [])
    {
        $message = Message::create([
            'conversation_id' => $conversationId,
            'sender_id' => $senderId,
            'receiver_id' => $this->getReceiverId($conversationId, $senderId),
            'content' => $content,
            'message_type' => $messageType,
            'language' => $additionalData['language'] ?? 'en',
            'translated_content' => $additionalData['translated_content'] ?? null,
            'metadata' => $additionalData['metadata'] ?? [],
            'status' => 'sent',
        ]);
        
        // Update the last message time in conversation
        $conversation = Conversation::find($conversationId);
        $conversation->update(['last_message_at' => now()]);
        
        return $message;
    }
    
    /**
     * Get the receiver ID in a conversation
     */
    private function getReceiverId($conversationId, $senderId)
    {
        $conversation = Conversation::find($conversationId);
        if ($conversation->user1_id == $senderId) {
            return $conversation->user2_id;
        }
        return $conversation->user1_id;
    }
    
    /**
     * Get conversation history with message metadata
     */
    public function getConversationHistory($conversationId, $limit = 50, $offset = 0)
    {
        return Message::where('conversation_id', $conversationId)
            ->with(['sender', 'receiver'])
            ->orderBy('created_at', 'desc')
            ->offset($offset)
            ->limit($limit)
            ->get()
            ->reverse(); // Reverse to get chronological order
    }
    
    /**
     * Schedule a meeting/pickup with smart recommendations
     */
    public function scheduleMeeting($initiatorUserId, $recipientUserId, $adId, $data)
    {
        // Validate scheduling data
        $validation = $this->validateSchedulingData($data);
        if (!$validation['valid']) {
            throw new \InvalidArgumentException($validation['message']);
        }

        // Get smart recommendations for optimal timing
        $smartRecommendations = $this->getSmartSchedulingRecommendations($initiatorUserId, $recipientUserId, $adId, $data);

        // Check for conflicts in user schedules
        $hasConflict = $this->checkScheduleConflict($recipientUserId, $data['scheduled_datetime']);

        if ($hasConflict) {
            // If there's a conflict, still allow scheduling but note the conflict
            \Log::warning("Scheduling conflict detected for user {$recipientUserId}", [
                'scheduled_datetime' => $data['scheduled_datetime'],
                'ad_id' => $adId
            ]);
        }

        $schedule = Scheduling::create([
            'ad_id' => $adId,
            'initiator_user_id' => $initiatorUserId,
            'recipient_user_id' => $recipientUserId,
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'scheduled_datetime' => $data['scheduled_datetime'],
            'location' => $data['location'],
            'participants' => $data['participants'] ?? [],
            'preferences' => array_merge($data['preferences'] ?? [], $smartRecommendations['preferences'] ?? []),
            'type' => $data['type'] ?? 'pickup',
            'status' => 'pending',
            'notes' => $data['notes'] ?? null,
            'smart_recommendations_applied' => $smartRecommendations,
        ]);

        // Send notifications to both parties
        $this->sendSchedulingNotifications($schedule);

        return $schedule;
    }

    /**
     * Get smart scheduling recommendations
     */
    private function getSmartSchedulingRecommendations($initiatorUserId, $recipientUserId, $adId, $data)
    {
        // Analyze user behavior patterns to suggest optimal scheduling
        $initiatorPattern = $this->getUserSchedulingPattern($initiatorUserId);
        $recipientPattern = $this->getUserSchedulingPattern($recipientUserId);

        // Analyze location data to suggest convenient locations
        $locationRecommendations = $this->getRecommendedLocations($data['location'], $adId);

        // Consider ad item type to suggest timing
        $itemRecommendations = $this->getItemTypeBasedRecommendations($adId);

        // Combine all recommendations
        $recommendations = [
            'user_patterns' => [
                'initiator_optimal_hours' => $initiatorPattern['optimal_hours'] ?? [],
                'recipient_optimal_hours' => $recipientPattern['optimal_hours'] ?? [],
                'availability_conflicts' => $this->checkAvailabilityConflicts($initiatorPattern, $recipientPattern)
            ],
            'location_recommendations' => $locationRecommendations,
            'item_based_recommendations' => $itemRecommendations,
            'optimal_timing' => $this->getOptimalTimingRecommendation($initiatorPattern, $recipientPattern),
        ];

        return $recommendations;
    }

    /**
     * Get user scheduling patterns
     */
    private function getUserSchedulingPattern($userId)
    {
        // Get user's past scheduling history to identify patterns
        $pastSchedules = Scheduling::where(function($query) use ($userId) {
            $query->where('initiator_user_id', $userId)
                  ->orWhere('recipient_user_id', $userId);
        })->where('status', 'completed')
        ->where('scheduled_datetime', '>', now()->subMonths(3))
        ->get();

        $totalMeetings = count($pastSchedules);
        if ($totalMeetings === 0) {
            return ['optimal_hours' => [9, 10, 11, 12, 13, 14, 15, 16, 17]]; // Standard business hours
        }

        // Calculate most common meeting times
        $hourCounts = [];
        $dayCounts = [];

        foreach ($pastSchedules as $schedule) {
            $hour = $schedule->scheduled_datetime->hour;
            $dayOfWeek = $schedule->scheduled_datetime->dayOfWeek;

            $hourCounts[$hour] = ($hourCounts[$hour] ?? 0) + 1;
            $dayCounts[$dayOfWeek] = ($dayCounts[$dayOfWeek] ?? 0) + 1;
        }

        // Get the top 3 most common hours
        arsort($hourCounts);
        $optimalHours = array_keys(array_slice($hourCounts, 0, 3, true));

        // Get the top 2 most common days
        arsort($dayCounts);
        $optimalDays = array_keys(array_slice($dayCounts, 0, 2, true));

        return [
            'optimal_hours' => $optimalHours,
            'optimal_days' => $optimalDays,
            'total_meetings' => $totalMeetings
        ];
    }

    /**
     * Get recommended locations based on convenience and past behavior
     */
    private function getRecommendedLocations($currentLocation, $adId)
    {
        $recommendations = [];

        if ($adId) {
            $ad = \App\Models\Ad::find($adId);
            if ($ad && $ad->location) {
                $recommendations[] = [
                    'location' => $ad->location,
                    'reason' => 'Item location',
                    'convenience_score' => 10
                ];
            }
        }

        // You could add logic to suggest locations near either user
        $recommendations[] = [
            'location' => $currentLocation,
            'reason' => 'Suggested by user',
            'convenience_score' => 8
        ];

        // Could add more sophisticated location recommendations based on distance between users
        return $recommendations;
    }

    /**
     * Get recommendations based on item type
     */
    private function getItemTypeBasedRecommendations($adId)
    {
        if (!$adId) return [];

        $ad = \App\Models\Ad::find($adId);
        if (!$ad) return [];

        $recommendations = [];

        // Different item types have different optimal scheduling times
        if (strtolower($ad->category->name ?? '') === 'automotive') {
            $recommendations[] = [
                'reason' => 'Automotive items',
                'suggestion' => 'Weekend days are preferred for vehicle viewing',
                'optimal_days' => [0, 6] // Sunday, Saturday
            ];
        } elseif (strtolower($ad->category->name ?? '') === 'property') {
            $recommendations[] = [
                'reason' => 'Property viewing',
                'suggestion' => 'Evening hours or weekends work better for property viewing',
                'optimal_hours' => [17, 18, 19]
            ];
        } else {
            $recommendations[] = [
                'reason' => 'General item',
                'suggestion' => 'Standard business hours work best',
                'optimal_hours' => [9, 10, 11, 12, 13, 14, 15, 16, 17]
            ];
        }

        return $recommendations;
    }

    /**
     * Get optimal timing recommendation based on both users' patterns
     */
    private function getOptimalTimingRecommendation($initiatorPattern, $recipientPattern)
    {
        $initiatorHours = $initiatorPattern['optimal_hours'] ?? [];
        $recipientHours = $recipientPattern['optimal_hours'] ?? [];

        // Find overlap in optimal hours
        $commonHours = array_intersect($initiatorHours, $recipientHours);

        if (empty($commonHours)) {
            // If no common optimal hours, return the average of both patterns
            $allHours = array_merge($initiatorHours, $recipientHours);
            $commonHours = array_slice($allHours, 0, 3);
        }

        return [
            'recommended_hours' => array_values($commonHours),
            'confidence' => count($commonHours) > 0 ? 0.8 : 0.5
        ];
    }

    /**
     * Check for schedule conflicts
     */
    private function checkScheduleConflict($userId, $scheduledDateTime)
    {
        $conflictWindow = 30; // 30 minutes before and after
        $startWindow = (new \DateTime($scheduledDateTime))->sub(new \DateInterval('PT' . $conflictWindow . 'M'));
        $endWindow = (new \DateTime($scheduledDateTime))->add(new \DateInterval('PT' . $conflictWindow . 'M'));

        $conflictingSchedules = Scheduling::where('recipient_user_id', $userId)
            ->where(function($query) use ($startWindow, $endWindow) {
                $query->whereBetween('scheduled_datetime', [$startWindow, $endWindow])
                      ->orWhere(function($q) use ($startWindow, $endWindow) {
                          $q->where('scheduled_datetime', '<=', $startWindow)
                            ->where(\DB::raw("DATE_ADD(scheduled_datetime, INTERVAL duration MINUTE)"), '>=', $startWindow);
                      });
            })
            ->where('status', '!=', 'cancelled')
            ->exists();

        return $conflictingSchedules;
    }

    /**
     * Check for availability conflicts between users
     */
    private function checkAvailabilityConflicts($initiatorPattern, $recipientPattern)
    {
        $initiatorOptimal = $initiatorPattern['optimal_hours'] ?? [];
        $recipientOptimal = $recipientPattern['optimal_hours'] ?? [];

        $conflicts = [];
        foreach ($initiatorOptimal as $hour) {
            if (!in_array($hour, $recipientOptimal)) {
                $conflicts[] = $hour;
            }
        }

        return [
            'hours_in_conflict' => $conflicts,
            'has_conflicts' => !empty($conflicts),
            'matching_hours' => array_intersect($initiatorOptimal, $recipientOptimal)
        ];
    }

    /**
     * Validate scheduling data
     */
    private function validateSchedulingData($data)
    {
        if (empty($data['title'])) {
            return ['valid' => false, 'message' => 'Title is required'];
        }

        if (empty($data['scheduled_datetime'])) {
            return ['valid' => false, 'message' => 'Scheduled date and time are required'];
        }

        $scheduledDateTime = \Carbon\Carbon::parse($data['scheduled_datetime']);
        if ($scheduledDateTime->isPast()) {
            return ['valid' => false, 'message' => 'Scheduled time cannot be in the past'];
        }

        if (empty($data['location'])) {
            return ['valid' => false, 'message' => 'Location is required'];
        }

        return ['valid' => true, 'message' => 'Valid'];
    }

    /**
     * Send scheduling notifications
     */
    private function sendSchedulingNotifications($schedule)
    {
        // Send notification to recipient about new scheduling request
        $recipient = \App\Models\User::find($schedule->recipient_user_id);
        $initiator = \App\Models\User::find($schedule->initiator_user_id);

        if ($recipient) {
            // This would normally send a push notification via Firebase or similar
            $notificationData = [
                'type' => 'scheduling',
                'title' => 'New Meeting Request',
                'body' => $initiator ? $initiator->name . ' scheduled a meeting with you' : 'You have a new meeting scheduled',
                'data' => [
                    'schedule_id' => $schedule->id,
                    'ad_id' => $schedule->ad_id,
                    'datetime' => $schedule->scheduled_datetime->toISOString(),
                    'location' => $schedule->location
                ]
            ];

            \Log::info("Scheduling notification sent", $notificationData);
        }
    }

    /**
     * Get available time slots for a user
     */
    public function getAvailableTimeSlots($userId, $dateRange = null)
    {
        if (!$dateRange) {
            $dateRange = [
                'start' => now()->startOfDay(),
                'end' => now()->addWeek()->endOfDay()
            ];
        } else {
            $dateRange['start'] = \Carbon\Carbon::parse($dateRange['start'])->startOfDay();
            $dateRange['end'] = \Carbon\Carbon::parse($dateRange['end'])->endOfDay();
        }

        // Get user's existing scheduled events
        $existingSchedules = Scheduling::where(function($query) use ($userId) {
            $query->where('initiator_user_id', $userId)
                  ->orWhere('recipient_user_id', $userId);
        })
        ->whereBetween('scheduled_datetime', [$dateRange['start'], $dateRange['end']])
        ->select('scheduled_datetime', 'duration')
        ->get();

        // Get user's preferred hours from their patterns
        $userPattern = $this->getUserSchedulingPattern($userId);
        $preferredHours = $userPattern['optimal_hours'] ?? [9, 10, 11, 12, 13, 14, 15, 16, 17];

        // Generate available slots based on preferences and conflicts
        $availableSlots = [];
        $current = clone $dateRange['start'];

        while ($current->lessThanOrEqualTo($dateRange['end'])) {
            if (in_array($current->dayOfWeek, $userPattern['optimal_days'] ?? [1, 2, 3, 4, 5])) { // Weekdays
                foreach ($preferredHours as $hour) {
                    $slotTime = \Carbon\Carbon::create($current->year, $current->month, $current->day, $hour, 0, 0);

                    if ($slotTime->greaterThan(now())) {
                        $isAvailable = true;
                        foreach ($existingSchedules as $schedule) {
                            $scheduleStart = \Carbon\Carbon::parse($schedule->scheduled_datetime);
                            $scheduleEnd = $scheduleStart->copy()->addMinutes($schedule->duration ?? 60);

                            if ($slotTime->between($scheduleStart, $scheduleEnd)) {
                                $isAvailable = false;
                                break;
                            }
                        }

                        if ($isAvailable) {
                            $availableSlots[] = [
                                'datetime' => $slotTime,
                                'preferred' => true,
                                'confidence' => 0.8
                            ];
                        }
                    }
                }
            }
            $current->addDay();
        }

        return $availableSlots;
    }

    /**
     * Confirm a scheduled meeting
     */
    public function confirmScheduledMeeting($scheduleId, $userId)
    {
        $schedule = Scheduling::find($scheduleId);

        if (!$schedule) {
            return ['success' => false, 'message' => 'Schedule not found'];
        }

        // Verify that the user is part of this schedule
        if ($userId !== $schedule->initiator_user_id && $userId !== $schedule->recipient_user_id) {
            return ['success' => false, 'message' => 'Unauthorized to confirm this schedule'];
        }

        $schedule->update([
            'status' => 'confirmed',
            'confirmed_at' => now()
        ]);

        // Send confirmation notification
        $this->sendConfirmationNotification($schedule);

        return [
            'success' => true,
            'schedule' => $schedule
        ];
    }

    /**
     * Send confirmation notification
     */
    private function sendConfirmationNotification($schedule)
    {
        $otherUserId = ($schedule->initiator_user_id == $schedule->recipient_user_id) ?
                      $schedule->initiator_user_id : $schedule->recipient_user_id;

        $notificationData = [
            'type' => 'scheduling_confirmation',
            'title' => 'Meeting Confirmed',
            'body' => 'Your meeting has been confirmed',
            'data' => [
                'schedule_id' => $schedule->id,
                'datetime' => $schedule->scheduled_datetime->toISOString(),
                'location' => $schedule->location
            ]
        ];

        \Log::info("Confirmation notification sent", $notificationData);
    }
    
    /**
     * Create a video call
     */
    public function createVideoCall($initiatorUserId, $recipientUserId, $adId = null, $data = [])
    {
        $call = VideoCall::create([
            'ad_id' => $adId,
            'initiator_user_id' => $initiatorUserId,
            'recipient_user_id' => $recipientUserId,
            'room_id' => Str::uuid(),
            'call_type' => $data['call_type'] ?? 'video',
            'scheduled_at' => $data['scheduled_at'] ?? null,
            'participants' => $data['participants'] ?? [],
            'status' => $data['scheduled_at'] ? 'pending' : 'initiating', // If scheduled, it's pending; otherwise initiating
            'settings' => $data['settings'] ?? [],
        ]);

        // If not scheduled and the call is to be initiated now, trigger the video call connection
        if (!$data['scheduled_at']) {
            // In a real implementation, this would trigger:
            // 1. Send notification to recipient
            // 2. Initialize WebRTC connection
            // 3. Generate necessary tokens/credentials for the call

            $this->initiateVideoCallConnection($call->room_id, $initiatorUserId, $recipientUserId);
        }

        return $call;
    }

    /**
     * Initialize video call connection using signaling server
     */
    private function initiateVideoCallConnection($roomId, $initiatorUserId, $recipientUserId)
    {
        // In a real implementation, this would handle:
        // 1. WebSocket connection to signaling server
        // 2. Generate JWT tokens for WebRTC access
        // 3. Notify recipient about incoming call

        // For now, we'll simulate the process
        $signalingData = [
            'roomId' => $roomId,
            'initiatorId' => $initiatorUserId,
            'recipientId' => $recipientUserId,
            'timestamp' => now()->toISOString()
        ];

        // In a real system, this would be sent to a signaling server
        // For now, we'll just log it
        \Log::info("Video call initiated", $signalingData);

        // Trigger notification to recipient
        $this->notifyUserOfVideoCall($recipientUserId, $roomId, $initiatorUserId);

        return $signalingData;
    }

    /**
     * Generate JWT token for WebRTC access
     */
    public function generateWebRTCToken($roomId, $userId)
    {
        $key = config('services.twilio.video_api_key');
        $secret = config('services.twilio.video_api_secret');

        if (!$key || !$secret) {
            // If no Twilio credentials, return a placeholder token for demo purposes
            return [
                'token' => base64_encode(json_encode([
                    'room_id' => $roomId,
                    'user_id' => $userId,
                    'expires_at' => now()->addHour()->timestamp
                ])),
                'room_id' => $roomId,
                'service' => 'demo_webRTC'
            ];
        }

        // In a real implementation, we would use Twilio's Video SDK to generate token
        // This is the actual implementation for Twilio Video
        $identity = "user_{$userId}";
        $grant = new \Twilio\Jwt\Grants\VideoGrant();
        $grant->setRoom($roomId);

        $token = new \Twilio\Jwt\AccessToken(
            $key,
            $secret,
            $identity,
            [$grant]
        );

        return [
            'token' => $token->toJWT(),
            'room_id' => $roomId,
            'service' => 'twilio'
        ];
    }

    /**
     * Notify user of incoming video call
     */
    private function notifyUserOfVideoCall($userId, $roomId, $initiatorUserId)
    {
        // In a real implementation, this would send a push notification
        // For now, we'll just simulate it

        $initiator = User::find($initiatorUserId);

        $notificationData = [
            'type' => 'video_call',
            'title' => 'Video Call Request',
            'body' => $initiator ? $initiator->name . ' is calling you' : 'Someone is calling you',
            'data' => [
                'roomId' => $roomId,
                'initiatorId' => $initiatorUserId,
                'callType' => 'video'
            ]
        ];

        // This would normally send push notification via Firebase or similar
        \Log::info("Video call notification sent", $notificationData);

        return $notificationData;
    }

    /**
     * Join a video call
     */
    public function joinVideoCall($roomId, $userId)
    {
        $call = VideoCall::where('room_id', $roomId)->first();

        if (!$call) {
            return ['success' => false, 'message' => 'Video call not found'];
        }

        // Check if the user is authorized to join this call
        if ($userId !== $call->initiator_user_id && $userId !== $call->recipient_user_id) {
            return ['success' => false, 'message' => 'Unauthorized to join this call'];
        }

        // Update call status to ongoing and set start time if first join
        if ($call->status === 'initiating' || $call->status === 'pending') {
            $call->update([
                'status' => 'ongoing',
                'started_at' => now()
            ]);
        }

        // Add user to participants if not already there
        $participants = $call->participants ?? [];
        if (!in_array($userId, $participants)) {
            $participants[] = $userId;
            $call->update(['participants' => $participants]);
        }

        // Generate WebRTC token for the user
        $webRTCToken = $this->generateWebRTCToken($roomId, $userId);

        return [
            'success' => true,
            'call' => $call,
            'token' => $webRTCToken['token'],
            'service' => $webRTCToken['service'],
            'room_id' => $roomId
        ];
    }

    /**
     * End a video call
     */
    public function endVideoCall($roomId, $userId)
    {
        $call = VideoCall::where('room_id', $roomId)->first();

        if (!$call) {
            return ['success' => false, 'message' => 'Video call not found'];
        }

        $call->update([
            'status' => 'ended',
            'ended_at' => now(),
            'duration' => now()->diffInSeconds($call->started_at)
        ]);

        return [
            'success' => true,
            'message' => 'Video call ended successfully',
            'call' => $call
        ];
    }
    
    /**
     * Create an escrow for a transaction
     */
    public function createEscrow($transactionId, $adId, $buyerId, $sellerId, $amount, $currency = 'NGN')
    {
        // In a real implementation, we would create a blockchain transaction here
        $blockchainService = new \App\Services\BlockchainService();

        // Create a simulated blockchain transaction for the escrow
        $blockchainTransaction = $blockchainService->createTransaction(
            // In a real app, these would be actual blockchain addresses
            '0x' . bin2hex(random_bytes(20)), // buyer blockchain address
            '0x' . bin2hex(random_bytes(20)), // escrow contract address
            $amount,
            $currency,
            [
                'transaction_id' => $transactionId,
                'ad_id' => $adId,
                'buyer_id' => $buyerId,
                'seller_id' => $sellerId
            ]
        );

        return Escrow::create([
            'transaction_id' => $transactionId,
            'ad_id' => $adId,
            'buyer_user_id' => $buyerId,
            'seller_user_id' => $sellerId,
            'amount' => $amount,
            'currency' => $currency,
            'status' => 'pending',
            'blockchain_transaction_hash' => $blockchainTransaction['transaction_hash'],
            'blockchain_contract_address' => $blockchainTransaction['to_address'],
            'blockchain_status' => $blockchainTransaction['status'],
            'blockchain_verification_data' => $blockchainTransaction,
        ]);
    }

    /**
     * Release escrow funds to seller with blockchain verification
     */
    public function releaseEscrow($escrowId)
    {
        $escrow = Escrow::find($escrowId);
        if (!$escrow) {
            return ['success' => false, 'message' => 'Escrow not found'];
        }

        if ($escrow->status !== 'pending') {
            return ['success' => false, 'message' => 'Escrow is not in pending state'];
        }

        // Use blockchain service to execute the release
        $blockchainService = new \App\Services\BlockchainService();

        $releaseResult = $blockchainService->executeEscrowRelease(
            $escrow->blockchain_contract_address,
            // In a real app, this would be the seller's blockchain address
            '0x' . bin2hex(random_bytes(20))
        );

        if ($releaseResult['success']) {
            $escrow->update([
                'status' => 'released',
                'blockchain_status' => 'executed',
                'blockchain_verification_data' => array_merge(
                    $escrow->blockchain_verification_data ?? [],
                    ['release_transaction' => $releaseResult]
                ),
            ]);

            return [
                'success' => true,
                'message' => 'Escrow released successfully',
                'transaction_hash' => $releaseResult['transaction_hash']
            ];
        }

        return [
            'success' => false,
            'message' => 'Failed to release escrow on blockchain'
        ];
    }

    /**
     * Verify escrow transaction on blockchain
     */
    public function verifyEscrowOnBlockchain($escrowId)
    {
        $escrow = Escrow::find($escrowId);
        if (!$escrow || !$escrow->blockchain_transaction_hash) {
            return ['success' => false, 'message' => 'Escrow or blockchain transaction not found'];
        }

        $blockchainService = new \App\Services\BlockchainService();

        $verification = $blockchainService->verifyTransactionByHash($escrow->blockchain_transaction_hash);

        // Update escrow with verification data
        $escrow->update([
            'blockchain_status' => $verification['status'],
            'blockchain_verification_data' => array_merge(
                $escrow->blockchain_verification_data ?? [],
                $verification
            ),
        ]);

        return [
            'success' => true,
            'verification' => $verification
        ];
    }
    
    /**
     * Resolve an escrow dispute using AI (simulated)
     */
    public function resolveEscrowDispute($escrowId, $disputeDetails)
    {
        $escrow = Escrow::find($escrowId);
        
        if (!$escrow) {
            return ['success' => false, 'message' => 'Escrow not found'];
        }
        
        // Simulate AI dispute resolution by analyzing details
        $resolution = $this->analyzeDispute($disputeDetails);
        
        $escrow->update([
            'dispute_status' => 'resolved',
            'dispute_resolved_at' => now(),
            'dispute_details' => array_merge($escrow->dispute_details ?? [], $disputeDetails),
            'status' => $resolution['resolution'],
        ]);
        
        return [
            'success' => true,
            'resolution' => $resolution,
            'message' => 'Dispute resolved successfully'
        ];
    }
    
    /**
     * Analyze a dispute using advanced AI algorithms
     */
    private function analyzeDispute($disputeDetails)
    {
        // Advanced AI logic to determine resolution
        $evidence = $disputeDetails['evidence'] ?? [];
        $disputeType = $disputeDetails['type'] ?? 'general';
        $transactionDetails = $disputeDetails['transaction_details'] ?? [];
        $userHistory = $disputeDetails['user_history'] ?? [];

        // Initialize AI analysis components
        $evidenceAnalysis = $this->analyzeEvidence($evidence);
        $transactionAnalysis = $this->analyzeTransaction($transactionDetails);
        $userBehaviorAnalysis = $this->analyzeUserBehavior($userHistory);
        $disputePatternAnalysis = $this->analyzeDisputePattern($disputeType);

        // Calculate confidence scores for each factor
        $confidenceFactors = [
            'evidence_credibility' => $evidenceAnalysis['credibility_score'],
            'transaction_legitimacy' => $transactionAnalysis['legitimacy_score'],
            'user_reputation' => $userBehaviorAnalysis['reputation_score'],
            'pattern_matching' => $disputePatternAnalysis['pattern_score']
        ];

        // Weighted scoring algorithm
        $weightedScores = $this->calculateWeightedScores($evidenceAnalysis, $transactionAnalysis, $userBehaviorAnalysis, $disputePatternAnalysis);

        // Apply machine learning model logic to determine outcome
        $resolution = $this->determineResolutionFromMLModel($weightedScores, $confidenceFactors);

        return [
            'resolution' => $resolution['outcome'],
            'reason' => $resolution['reasoning'],
            'decision' => $resolution['decision'],
            'confidence_level' => $resolution['confidence'],
            'factors_considered' => [
                'evidence_analysis' => $evidenceAnalysis['summary'],
                'transaction_analysis' => $transactionAnalysis['summary'],
                'user_behavior_analysis' => $userBehaviorAnalysis['summary'],
                'dispute_pattern_analysis' => $disputePatternAnalysis['summary']
            ],
            'confidence_factors' => $confidenceFactors
        ];
    }

    /**
     * Analyze evidence using AI techniques
     */
    private function analyzeEvidence($evidence)
    {
        $evidenceCount = count($evidence);
        $evidenceQualityScore = 0;
        $evidenceTypes = [];
        $credibilityScore = 0;

        foreach ($evidence as $item) {
            $evidenceTypes[$item['type']] = ($evidenceTypes[$item['type']] ?? 0) + 1;

            // Assess evidence credibility based on type
            $typeCredibility = [
                'photo' => 0.8,
                'video' => 0.9,
                'document' => 0.7,
                'receipt' => 0.85,
                'third_party_verification' => 0.95,
                'communication_logs' => 0.6,
                'witness_statement' => 0.5
            ];

            $evidenceQualityScore += $typeCredibility[$item['type']] ?? 0.5;
            $credibilityScore += $typeCredibility[$item['type']] ?? 0.5;
        }

        $avgCredibility = $evidenceCount > 0 ? $credibilityScore / $evidenceCount : 0;
        $diversityBonus = count($evidenceTypes) > 2 ? 0.1 : 0; // More diverse evidence types get a bonus

        return [
            'credibility_score' => min(1.0, $avgCredibility + $diversityBonus),
            'evidence_count' => $evidenceCount,
            'evidence_types' => $evidenceTypes,
            'quality_score' => $evidenceQualityScore,
            'summary' => [
                'total_evidence' => $evidenceCount,
                'types' => array_keys($evidenceTypes),
                'confidence' => min(1.0, $avgCredibility + $diversityBonus)
            ]
        ];
    }

    /**
     * Analyze transaction details for legitimacy
     */
    private function analyzeTransaction($transactionDetails)
    {
        $legitimacyScore = 0.5; // Base score

        // Factors that increase legitimacy
        if (isset($transactionDetails['time_to_ship']) && $transactionDetails['time_to_ship'] <= 48) {
            $legitimacyScore += 0.1; // Fast shipping is good
        }

        if (isset($transactionDetails['return_policy']) && $transactionDetails['return_policy'] === 'yes') {
            $legitimacyScore += 0.15; // Having return policy is good
        }

        if (isset($transactionDetails['warranty']) && $transactionDetails['warranty'] === 'yes') {
            $legitimacyScore += 0.1; // Warranty is good
        }

        // Factors that decrease legitimacy
        if (isset($transactionDetails['payment_method']) && $transactionDetails['payment_method'] === 'cash_only') {
            $legitimacyScore -= 0.2; // Cash-only is suspicious
        }

        if (isset($transactionDetails['price']) && $transactionDetails['price'] <= 0.1 * ($transactionDetails['market_value'] ?? $transactionDetails['price'] + 1)) {
            $legitimacyScore -= 0.15; // Significantly below market price is suspicious
        }

        $legitimacyScore = max(0, min(1, $legitimacyScore));

        return [
            'legitimacy_score' => $legitimacyScore,
            'transaction_details' => $transactionDetails,
            'risk_factors' => $this->identifyTransactionRiskFactors($transactionDetails),
            'summary' => [
                'legitimacy' => $legitimacyScore,
                'risk_level' => $legitimacyScore > 0.7 ? 'low' : ($legitimacyScore > 0.4 ? 'medium' : 'high')
            ]
        ];
    }

    /**
     * Identify transaction risk factors
     */
    private function identifyTransactionRiskFactors($transactionDetails)
    {
        $risks = [];

        if (isset($transactionDetails['payment_method']) && $transactionDetails['payment_method'] === 'cash_only') {
            $risks[] = 'cash_only_payment';
        }

        if (isset($transactionDetails['price']) && $transactionDetails['price'] <= 0.3 * ($transactionDetails['market_value'] ?? $transactionDetails['price'] + 1)) {
            $risks[] = 'significantly_below_market_price';
        }

        if (isset($transactionDetails['shipping_time']) && $transactionDetails['shipping_time'] > 14) {
            $risks[] = 'excessive_shipping_time';
        }

        if (!isset($transactionDetails['return_policy']) || $transactionDetails['return_policy'] !== 'yes') {
            $risks[] = 'no_return_policy';
        }

        return $risks;
    }

    /**
     * Analyze user behavior patterns
     */
    private function analyzeUserBehavior($userHistory)
    {
        $reputationScore = 0.5; // Base score
        $totalTransactions = $userHistory['total_transactions'] ?? 1;

        // Calculate based on transaction history
        if ($totalTransactions > 0) {
            $positiveFeedback = $userHistory['positive_feedback'] ?? 0;
            $negativeFeedback = $userHistory['negative_feedback'] ?? 0;
            $disputeHistory = $userHistory['disputes'] ?? [];

            $feedbackScore = $totalTransactions > 0 ? $positiveFeedback / $totalTransactions : 0;
            $reputationScore = 0.4 + (0.6 * $feedbackScore); // 40% base, 60% from feedback

            // Adjust for dispute history
            if (count($disputeHistory) > $totalTransactions * 0.1) { // More than 10% of transactions have disputes
                $reputationScore -= 0.2;
            }

            // Adjust for negative feedback
            if ($negativeFeedback > $positiveFeedback) {
                $reputationScore -= 0.15;
            }
        }

        $reputationScore = max(0, min(1, $reputationScore));

        return [
            'reputation_score' => $reputationScore,
            'user_history' => $userHistory,
            'behavior_assessment' => [
                'transaction_history' => $totalTransactions,
                'positive_feedback' => $userHistory['positive_feedback'] ?? 0,
                'negative_feedback' => $userHistory['negative_feedback'] ?? 0,
                'dispute_history' => $userHistory['disputes'] ?? 0
            ],
            'summary' => [
                'reputation' => $reputationScore,
                'history_level' => $totalTransactions > 10 ? 'extensive' : ($totalTransactions > 5 ? 'moderate' : 'limited')
            ]
        ];
    }

    /**
     * Analyze dispute patterns
     */
    private function analyzeDisputePattern($disputeType)
    {
        $patternScores = [
            'product_not_as_described' => 0.65,
            'item_not_received' => 0.7,
            'payment_not_processed' => 0.8,
            'return_refund_issue' => 0.6,
            'shipping_damage' => 0.55,
            'seller_non_response' => 0.75,
            'buyer_changed_mind' => 0.4,
            'wrong_item_sent' => 0.75,
            'defective_item' => 0.6
        ];

        $patternScore = $patternScores[$disputeType] ?? 0.5;
        $patternScore += (mt_rand(-10, 10) / 100); // Add small random factor

        return [
            'pattern_score' => max(0, min(1, $patternScore)),
            'dispute_type' => $disputeType,
            'historical_precedence' => $patternScores[$disputeType] ?? 0.5,
            'summary' => [
                'type' => $disputeType,
                'precedence' => max(0, min(1, $patternScore))
            ]
        ];
    }

    /**
     * Calculate weighted scores for all factors
     */
    private function calculateWeightedScores($evidence, $transaction, $user, $pattern)
    {
        // Assign weights to different factors (total = 1.0)
        $weights = [
            'evidence' => 0.35,        // 35% - Most important in disputes
            'transaction' => 0.25,     // 25% - Transaction legitimacy
            'user' => 0.20,            // 20% - User reputation
            'pattern' => 0.20          // 20% - Historical patterns
        ];

        // Calculate weighted scores
        $weightedScore = (
            $evidence['credibility_score'] * $weights['evidence'] +
            $transaction['legitimacy_score'] * $weights['transaction'] +
            $user['reputation_score'] * $weights['user'] +
            $pattern['pattern_score'] * $weights['pattern']
        );

        // Add some variance based on confidence in each factor
        $confidenceAdjustment = (
            $weights['evidence'] * 0.7 +  // Evidence typically more reliable
            $weights['transaction'] * 0.8 +  // Transaction data is usually reliable
            $weights['user'] * 0.6 +  // User history can be biased
            $weights['pattern'] * 0.5  // Pattern matching is less certain
        ) / 4;

        $finalScore = $weightedScore * $confidenceAdjustment;

        return [
            'total_score' => $finalScore,
            'weighted_components' => [
                'evidence_weighted' => $evidence['credibility_score'] * $weights['evidence'],
                'transaction_weighted' => $transaction['legitimacy_score'] * $weights['transaction'],
                'user_weighted' => $user['reputation_score'] * $weights['user'],
                'pattern_weighted' => $pattern['pattern_score'] * $weights['pattern']
            ],
            'weights_applied' => $weights
        ];
    }

    /**
     * Determine resolution from ML model simulation
     */
    private function determineResolutionFromMLModel($scores, $confidenceFactors)
    {
        $totalScore = $scores['total_score'];

        // ML Decision Logic
        if ($totalScore > 0.7) {
            // High confidence in buyer's favor
            return [
                'outcome' => 'refunded',
                'reasoning' => 'Strong evidence and transaction history favor the buyer',
                'decision' => 'Funds refunded to buyer',
                'confidence' => $totalScore
            ];
        } elseif ($totalScore > 0.55) {
            // Moderate confidence in buyer's favor
            return [
                'outcome' => 'refunded',
                'reasoning' => 'Moderate evidence supports buyer\'s position',
                'decision' => 'Funds refunded to buyer',
                'confidence' => $totalScore
            ];
        } elseif ($totalScore > 0.45) {
            // Unclear decision
            return [
                'outcome' => 'negotiated',
                'reasoning' => 'Evidence is balanced, suggesting negotiation between parties',
                'decision' => 'Partial refund or alternative resolution negotiated',
                'confidence' => $totalScore
            ];
        } elseif ($totalScore > 0.3) {
            // Moderate confidence in seller's favor
            return [
                'outcome' => 'released',
                'reasoning' => 'Moderate evidence supports seller\'s position',
                'decision' => 'Funds released to seller',
                'confidence' => 1 - $totalScore
            ];
        } else {
            // High confidence in seller's favor
            return [
                'outcome' => 'released',
                'reasoning' => 'Strong evidence and transaction history favor the seller',
                'decision' => 'Funds released to seller',
                'confidence' => 1 - $totalScore
            ];
        }
    }
    
    /**
     * Get user's unread messages count
     */
    public function getUnreadMessageCount($userId)
    {
        return Message::where('receiver_id', $userId)
            ->where('is_read', false)
            ->count();
    }
    
    /**
     * Mark messages as read
     */
    public function markMessagesAsRead($userId, $messageIds = null)
    {
        $query = Message::where('receiver_id', $userId);
        
        if ($messageIds) {
            $query->whereIn('id', $messageIds);
        } else {
            $query->where('is_read', false);
        }
        
        return $query->update([
            'is_read' => true,
            'read_at' => now()
        ]);
    }
}