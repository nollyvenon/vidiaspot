<?php

namespace App\Http\Controllers;

use App\Services\MultipleInputMethodsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MultipleInputMethodsController extends Controller
{
    private MultipleInputMethodsService $inputService;

    public function __construct()
    {
        $this->inputService = new MultipleInputMethodsService();
    }

    /**
     * Get user's input method preferences.
     */
    public function getPreferences()
    {
        $userId = Auth::id();
        $preferences = $this->inputService->getUserPreferredInputMethod($userId);

        return response()->json([
            'preferences' => $preferences,
            'user_id' => $userId,
            'message' => 'Input method preferences retrieved successfully'
        ]);
    }

    /**
     * Update user's input method preferences.
     */
    public function updatePreferences(Request $request)
    {
        $request->validate([
            'primary' => 'string|in:touch,voice,gesture,keyboard',
            'secondary' => 'string|in:touch,voice,gesture,keyboard',
            'voice_enabled' => 'boolean',
            'gesture_enabled' => 'boolean',
            'touch_enabled' => 'boolean',
            'haptic_feedback' => 'boolean',
        ]);

        $userId = Auth::id();
        $this->inputService->setUserInputPreferences($userId, $request->all());

        return response()->json([
            'success' => true,
            'message' => 'Input method preferences updated successfully'
        ]);
    }

    /**
     * Process voice input command.
     */
    public function processVoiceInput(Request $request)
    {
        $request->validate([
            'command' => 'required|string',
            'context' => 'array',
        ]);

        $userId = Auth::id();
        $result = $this->inputService->processVoiceInput(
            $userId,
            $request->command,
            $request->context ?? []
        );

        return response()->json($result);
    }

    /**
     * Process gesture input.
     */
    public function processGestureInput(Request $request)
    {
        $request->validate([
            'start' => 'required|array',
            'start.x' => 'required|numeric',
            'start.y' => 'required|numeric',
            'end' => 'required|array',
            'end.x' => 'required|numeric',
            'end.y' => 'required|numeric',
            'strength' => 'numeric',
            'duration' => 'numeric',
        ]);

        $userId = Auth::id();
        $result = $this->inputService->processGestureInput($userId, $request->all());

        return response()->json($result);
    }

    /**
     * Process touch input.
     */
    public function processTouchInput(Request $request)
    {
        $request->validate([
            'type' => 'required|string|in:tap,double_tap,long_press,swipe',
            'coordinates' => 'required|array',
            'coordinates.x' => 'required|numeric',
            'coordinates.y' => 'required|numeric',
        ]);

        $userId = Auth::id();
        $result = $this->inputService->processTouchInput($userId, $request->all());

        return response()->json($result);
    }

    /**
     * Adapt interface based on input method.
     */
    public function adaptInterface(Request $request)
    {
        $request->validate([
            'input_method' => 'required|string|in:touch,voice,gesture,keyboard',
            'page_elements' => 'required|array',
        ]);

        $adaptedElements = $this->inputService->adaptInterfaceForInputMethod(
            $request->input_method,
            $request->page_elements
        );

        return response()->json([
            'adapted_elements' => $adaptedElements,
            'input_method' => $request->input_method,
            'message' => 'Interface adapted for ' . $request->input_method . ' input method'
        ]);
    }

    /**
     * Get user's input method analytics.
     */
    public function getAnalytics()
    {
        $userId = Auth::id();
        $analytics = $this->inputService->getUserInputAnalytics($userId);

        return response()->json([
            'analytics' => $analytics,
            'user_id' => $userId,
            'message' => 'Input method analytics retrieved successfully'
        ]);
    }

    /**
     * Get supported input methods.
     */
    public function getSupportedMethods()
    {
        $methods = [
            'touch' => [
                'name' => 'Touch Input',
                'description' => 'Standard touch screen interactions',
                'supported_gestures' => ['tap', 'double-tap', 'long-press', 'swipe']
            ],
            'voice' => [
                'name' => 'Voice Commands',
                'description' => 'Control the interface using voice commands',
                'supported_commands' => ['click', 'navigate', 'search', 'select']
            ],
            'gesture' => [
                'name' => 'Gesture Control',
                'description' => 'Navigate using swipe and gesture patterns',
                'supported_gestures' => ['swipe-up', 'swipe-down', 'swipe-left', 'swipe-right']
            ],
            'keyboard' => [
                'name' => 'Keyboard Navigation',
                'description' => 'Full keyboard control with shortcuts',
                'supported_features' => ['tab-navigation', 'shortcuts', 'access-keys']
            ]
        ];

        return response()->json([
            'methods' => $methods,
            'message' => 'Supported input methods retrieved successfully'
        ]);
    }

    /**
     * Calibrate gesture recognition.
     */
    public function calibrateGestures(Request $request)
    {
        $request->validate([
            'calibration_data' => 'required|array',
            'calibration_data.*.gesture_type' => 'required|string',
            'calibration_data.*.coordinates' => 'required|array',
        ]);

        // In a real implementation, this would adjust gesture recognition thresholds
        // based on user's calibration data

        return response()->json([
            'success' => true,
            'message' => 'Gestures calibrated successfully',
            'calibration' => $request->calibration_data
        ]);
    }
}