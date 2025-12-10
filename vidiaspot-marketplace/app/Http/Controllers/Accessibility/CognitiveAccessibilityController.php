<?php

namespace App\Http\Controllers;

use App\Services\CognitiveAccessibilityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CognitiveAccessibilityController extends Controller
{
    private CognitiveAccessibilityService $cognitiveService;

    public function __construct()
    {
        $this->cognitiveService = new CognitiveAccessibilityService();
    }

    /**
     * Get cognitive accessibility settings for the current user.
     */
    public function getSettings()
    {
        $userId = Auth::id();
        $preferences = $this->cognitiveService->getUserPreferences($userId);

        return response()->json([
            'preferences' => $preferences,
            'message' => 'Cognitive accessibility preferences retrieved successfully'
        ]);
    }

    /**
     * Update cognitive accessibility settings for the current user.
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'reading_level' => 'string|in:elementary,middle,high_school',
            'high_contrast' => 'boolean',
            'large_text' => 'boolean',
            'simplified_navigation' => 'boolean',
            'visual_aids' => 'boolean',
            'step_by_step_guides' => 'boolean',
            'extra_time_prompts' => 'boolean',
            'distraction_free_mode' => 'boolean',
        ]);

        $userId = Auth::id();
        $this->cognitiveService->setUserPreferences($userId, $request->all());

        $updatedPreferences = $this->cognitiveService->getUserPreferences($userId);

        return response()->json([
            'preferences' => $updatedPreferences,
            'message' => 'Cognitive accessibility preferences updated successfully'
        ]);
    }

    /**
     * Simplify text content for cognitive accessibility.
     */
    public function simplifyText(Request $request)
    {
        $request->validate([
            'text' => 'required|string',
            'reading_level' => 'string|in:elementary,middle,high_school',
            'options' => 'array',
        ]);

        $simplified = $this->cognitiveService->simplifyText(
            $request->text,
            array_merge([
                'reading_level' => $request->reading_level ?? 'elementary'
            ], $request->options ?? [])
        );

        return response()->json($simplified);
    }

    /**
     * Get alternative content formats for cognitive accessibility.
     */
    public function getAlternativeFormats(Request $request)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $formats = $this->cognitiveService->generateAlternativeFormats($request->content);

        return response()->json($formats);
    }

    /**
     * Create a simplified interface structure.
     */
    public function createSimplifiedInterface(Request $request)
    {
        $request->validate([
            'components' => 'required|array',
            'options' => 'array',
        ]);

        $simplified = $this->cognitiveService->createSimplifiedInterface(
            $request->components,
            $request->options ?? []
        );

        return response()->json($simplified);
    }

    /**
     * Get a simplified version of a page or content area.
     */
    public function getSimplifiedPage(Request $request)
    {
        $request->validate([
            'page_url' => 'required|string',
            'content_elements' => 'required|array',
        ]);

        // This would typically take the page structure and simplify it
        $simplifiedStructure = $this->cognitiveService->createSimplifiedInterface(
            $request->content_elements
        );

        return response()->json([
            'simplified_structure' => $simplifiedStructure,
            'original_url' => $request->page_url,
            'message' => 'Page structure simplified for cognitive accessibility'
        ]);
    }

    /**
     * Get cognitive accessibility guidelines for content creators.
     */
    public function getCognitiveGuidelines()
    {
        $guidelines = [
            'text_complexity' => [
                'use_simple_words',
                'short_sentences',
                'clear_structure',
                'consistent_formatting',
            ],
            'navigation' => [
                'consistent_placement',
                'clear_labels',
                'limited_options',
                'visual_cues',
            ],
            'content' => [
                'white_space',
                'high_contrast',
                'simple_fonts',
                'clear_headings',
            ],
            'interaction' => [
                'extra_time',
                'clear_feedback',
                'simple_controls',
                'error_tolerance',
            ]
        ];

        return response()->json($guidelines);
    }

    /**
     * Get cognitive accessibility status for the current user.
     */
    public function getStatus()
    {
        $userId = Auth::id();
        $preferences = $this->cognitiveService->getUserPreferences($userId);

        return response()->json([
            'enabled' => $preferences['simplified_navigation'] || $preferences['step_by_step_guides'] || $preferences['visual_aids'],
            'preferences' => $preferences,
            'features_active' => [
                'simple_text' => true,
                'simplified_navigation' => $preferences['simplified_navigation'],
                'visual_aids' => $preferences['visual_aids'],
                'step_by_step' => $preferences['step_by_step_guides'],
            ]
        ]);
    }
}