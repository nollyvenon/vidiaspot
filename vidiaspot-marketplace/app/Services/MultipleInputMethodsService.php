<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class MultipleInputMethodsService
{
    /**
     * Process voice input commands
     */
    public function processVoiceInput(string $userId, string $voiceCommand, array $context = []): array
    {
        // In a real implementation, this would interface with a voice recognition service
        // For this implementation, we'll process a simplified command
        $normalizedCommand = strtolower(trim($voiceCommand));
        
        // Common voice commands mapping
        $commandMappings = [
            'search for' => 'search',
            'find' => 'search',
            'go to' => 'navigate',
            'click' => 'click',
            'select' => 'click',
            'post ad' => 'post_ad',
            'view profile' => 'view_profile',
            'settings' => 'settings',
            'help' => 'help',
            'quit' => 'quit',
        ];

        $action = 'unknown';
        $target = '';

        foreach ($commandMappings as $phrase => $actionType) {
            if (strpos($normalizedCommand, $phrase) === 0) {
                $action = $actionType;
                $target = trim(substr($normalizedCommand, strlen($phrase)));
                break;
            }
        }

        // Store voice command history
        $this->storeVoiceCommand($userId, $voiceCommand, $action, $target);

        return [
            'command' => $voiceCommand,
            'normalized' => $normalizedCommand,
            'action' => $action,
            'target' => $target,
            'context' => $context,
            'processed_at' => now()->toISOString(),
            'success' => $action !== 'unknown',
            'message' => $action !== 'unknown' ? "Voice command processed: {$action} {$target}" : "Command not recognized"
        ];
    }

    /**
     * Process gesture input
     */
    public function processGestureInput(string $userId, array $gestureData): array
    {
        // Gesture data typically contains start/end coordinates, duration, etc.
        $gestureType = $this->classifyGesture($gestureData);
        $action = $this->mapGestureToAction($gestureType);

        // Store gesture for user preference learning
        $this->storeGesture($userId, $gestureData, $gestureType, $action);

        return [
            'gesture_type' => $gestureType,
            'coordinates' => [
                'start' => $gestureData['start'] ?? null,
                'end' => $gestureData['end'] ?? null,
            ],
            'action' => $action,
            'strength' => $gestureData['strength'] ?? null,
            'duration' => $gestureData['duration'] ?? null,
            'processed_at' => now()->toISOString(),
            'success' => true,
            'message' => "Gesture processed: {$gestureType}"
        ];
    }

    /**
     * Classify gesture based on data points
     */
    private function classifyGesture(array $gestureData): string
    {
        if (!isset($gestureData['start']) || !isset($gestureData['end'])) {
            return 'invalid';
        }

        $start = $gestureData['start'];
        $end = $gestureData['end'];
        
        // Calculate direction and distance
        $dx = $end['x'] - $start['x'];
        $dy = $end['y'] - $start['y'];
        $distance = sqrt($dx * $dx + $dy * $dy);
        
        // Determine gesture type based on movement
        if ($distance < 30) { // Small movement = tap
            return 'tap';
        } elseif (abs($dx) > abs($dy)) { // Predominantly horizontal
            if ($dx > 0) {
                return 'swipe_right';
            } else {
                return 'swipe_left';
            }
        } else { // Predominantly vertical
            if ($dy > 0) {
                return 'swipe_down';
            } else {
                return 'swipe_up';
            }
        }
    }

    /**
     * Map gesture to action
     */
    private function mapGestureToAction(string $gestureType): string
    {
        $gestureActions = [
            'tap' => 'click',
            'swipe_right' => 'next',
            'swipe_left' => 'previous',
            'swipe_up' => 'scroll_up',
            'swipe_down' => 'scroll_down',
        ];

        return $gestureActions[$gestureType] ?? 'none';
    }

    /**
     * Process touch input
     */
    public function processTouchInput(string $userId, array $touchData): array
    {
        // Touch data typically contains coordinates and touch type
        $touchType = $touchData['type'] ?? 'tap';
        $coordinates = $touchData['coordinates'] ?? ['x' => 0, 'y' => 0];
        
        // Determine action based on touch type and location
        $action = $this->determineTouchAction($touchType, $coordinates);

        // Store touch interaction
        $this->storeTouch($userId, $touchData, $action);

        return [
            'touch_type' => $touchType,
            'coordinates' => $coordinates,
            'action' => $action,
            'processed_at' => now()->toISOString(),
            'success' => true,
            'message' => "Touch input processed: {$touchType}"
        ];
    }

    /**
     * Determine action from touch data
     */
    private function determineTouchAction(string $touchType, array $coordinates): string
    {
        switch ($touchType) {
            case 'tap':
                return 'click';
            case 'double_tap':
                return 'double_click';
            case 'long_press':
                return 'context_menu';
            default:
                return 'click';
        }
    }

    /**
     * Get user's preferred input method
     */
    public function getUserPreferredInputMethod(string $userId): string
    {
        $preferences = Cache::get("input_preferences_{$userId}", [
            'primary' => 'touch', // touch, voice, gesture, keyboard
            'secondary' => 'keyboard',
            'voice_enabled' => true,
            'gesture_enabled' => true,
        ]);

        return $preferences['primary'] ?? 'touch';
    }

    /**
     * Set user's input method preferences
     */
    public function setUserInputPreferences(string $userId, array $preferences): void
    {
        $validPreferences = [
            'primary' => in_array($preferences['primary'] ?? '', ['touch', 'voice', 'gesture', 'keyboard']) ? $preferences['primary'] : 'touch',
            'secondary' => in_array($preferences['secondary'] ?? '', ['touch', 'voice', 'gesture', 'keyboard']) ? $preferences['secondary'] : 'keyboard',
            'voice_enabled' => (bool)($preferences['voice_enabled'] ?? true),
            'gesture_enabled' => (bool)($preferences['gesture_enabled'] ?? true),
            'touch_enabled' => (bool)($preferences['touch_enabled'] ?? true),
            'haptic_feedback' => (bool)($preferences['haptic_feedback'] ?? true),
        ];

        Cache::put("input_preferences_{$userId}", $validPreferences, now()->addMonths(6));
    }

    /**
     * Adapt interface based on user's primary input method
     */
    public function adaptInterfaceForInputMethod(string $inputMethod, array $pageElements): array
    {
        $adaptedElements = $pageElements;

        switch ($inputMethod) {
            case 'voice':
                // Make elements more voice-command friendly
                foreach ($adaptedElements as &$element) {
                    if (isset($element['label'])) {
                        $element['voice_commands'] = $this->generateVoiceCommands($element);
                    }
                }
                break;

            case 'gesture':
                // Enhance gesture areas
                foreach ($adaptedElements as &$element) {
                    $element['gesture_target'] = true;
                    $element['gesture_size'] = 'large'; // Make targets bigger for gestures
                }
                break;

            case 'touch':
                // Optimize for touch screens
                foreach ($adaptedElements as &$element) {
                    $element['touch_target'] = true;
                    $element['touch_padding'] = 'large'; // Add more padding for touch
                }
                break;

            case 'keyboard':
                // Optimize for keyboard navigation
                $tabIndex = 1;
                foreach ($adaptedElements as &$element) {
                    $element['tabindex'] = $tabIndex++;
                    $element['keyboard_shortcut'] = $this->generateKeyboardShortcut($element);
                }
                break;
        }

        return $adaptedElements;
    }

    /**
     * Generate voice commands for an element
     */
    private function generateVoiceCommands(array $element): array
    {
        $commands = [];
        
        if (isset($element['label'])) {
            $label = strtolower($element['label']);
            $commands[] = "click {$label}";
            $commands[] = "select {$label}";
        }
        
        if (isset($element['type']) && $element['type'] === 'button') {
            $commands[] = "press button";
        }
        
        return $commands;
    }

    /**
     * Generate keyboard shortcut for an element
     */
    private function generateKeyboardShortcut(array $element): ?string
    {
        // Generate keyboard shortcuts based on element type and label
        if (isset($element['label'])) {
            $label = $element['label'];
            // Take the first letter as a potential shortcut
            return substr($label, 0, 1);
        }
        
        return null;
    }

    /**
     * Store voice command for analytics and improvement
     */
    private function storeVoiceCommand(string $userId, string $command, string $action, string $target): void
    {
        $commandRecord = [
            'id' => (string) Str::uuid(),
            'user_id' => $userId,
            'command' => $command,
            'action' => $action,
            'target' => $target,
            'timestamp' => now()->toISOString(),
        ];

        // Add to user's command history
        $historyKey = "voice_command_history_{$userId}";
        $history = Cache::get($historyKey, []);
        $history[] = $commandRecord;
        
        // Keep only last 50 commands to prevent cache bloat
        if (count($history) > 50) {
            $history = array_slice($history, -50);
        }
        
        Cache::put($historyKey, $history, now()->addWeeks(2));
    }

    /**
     * Store gesture for analytics and improvement
     */
    private function storeGesture(string $userId, array $gestureData, string $gestureType, string $action): void
    {
        $gestureRecord = [
            'id' => (string) Str::uuid(),
            'user_id' => $userId,
            'gesture_type' => $gestureType,
            'action' => $action,
            'coordinates' => [
                'start' => $gestureData['start'] ?? null,
                'end' => $gestureData['end'] ?? null,
            ],
            'timestamp' => now()->toISOString(),
        ];

        // Add to user's gesture history
        $historyKey = "gesture_history_{$userId}";
        $history = Cache::get($historyKey, []);
        $history[] = $gestureRecord;
        
        // Keep only last 50 gestures to prevent cache bloat
        if (count($history) > 50) {
            $history = array_slice($history, -50);
        }
        
        Cache::put($historyKey, $history, now()->addWeeks(2));
    }

    /**
     * Store touch interaction for analytics and improvement
     */
    private function storeTouch(string $userId, array $touchData, string $action): void
    {
        $touchRecord = [
            'id' => (string) Str::uuid(),
            'user_id' => $userId,
            'touch_type' => $touchData['type'] ?? 'tap',
            'action' => $action,
            'coordinates' => $touchData['coordinates'] ?? null,
            'timestamp' => now()->toISOString(),
        ];

        // Add to user's touch history
        $historyKey = "touch_history_{$userId}";
        $history = Cache::get($historyKey, []);
        $history[] = $touchRecord;
        
        // Keep only last 50 touches to prevent cache bloat
        if (count($history) > 50) {
            $history = array_slice($history, -50);
        }
        
        Cache::put($historyKey, $history, now()->addWeeks(2));
    }

    /**
     * Get user's input method analytics
     */
    public function getUserInputAnalytics(string $userId): array
    {
        $voiceHistory = Cache::get("voice_command_history_{$userId}", []);
        $gestureHistory = Cache::get("gesture_history_{$userId}", []);
        $touchHistory = Cache::get("touch_history_{$userId}", []);

        return [
            'voice' => [
                'total_commands' => count($voiceHistory),
                'most_used_commands' => $this->getMostUsedCommands($voiceHistory),
                'success_rate' => $this->calculateSuccessRate($voiceHistory),
            ],
            'gesture' => [
                'total_gestures' => count($gestureHistory),
                'most_used_gestures' => $this->getMostUsedGestures($gestureHistory),
            ],
            'touch' => [
                'total_touches' => count($touchHistory),
                'most_used_interactions' => $this->getMostUsedInteractions($touchHistory),
            ],
        ];
    }

    /**
     * Get most used commands from history
     */
    private function getMostUsedCommands(array $history): array
    {
        $commandCounts = [];
        foreach ($history as $record) {
            $action = $record['action'] ?? 'unknown';
            $commandCounts[$action] = ($commandCounts[$action] ?? 0) + 1;
        }
        
        arsort($commandCounts);
        return array_slice($commandCounts, 0, 5, true); // Top 5
    }

    /**
     * Get most used gestures from history
     */
    private function getMostUsedGestures(array $history): array
    {
        $gestureCounts = [];
        foreach ($history as $record) {
            $gesture = $record['gesture_type'] ?? 'unknown';
            $gestureCounts[$gesture] = ($gestureCounts[$gesture] ?? 0) + 1;
        }
        
        arsort($gestureCounts);
        return array_slice($gestureCounts, 0, 5, true); // Top 5
    }

    /**
     * Get most used interactions from history
     */
    private function getMostUsedInteractions(array $history): array
    {
        $interactionCounts = [];
        foreach ($history as $record) {
            $type = $record['touch_type'] ?? 'unknown';
            $interactionCounts[$type] = ($interactionCounts[$type] ?? 0) + 1;
        }
        
        arsort($interactionCounts);
        return array_slice($interactionCounts, 0, 5, true); // Top 5
    }

    /**
     * Calculate success rate for voice commands
     */
    private function calculateSuccessRate(array $history): float
    {
        if (empty($history)) {
            return 0.0;
        }

        $successful = 0;
        foreach ($history as $record) {
            if ($record['action'] !== 'unknown') {
                $successful++;
            }
        }

        return $successful / count($history);
    }
}