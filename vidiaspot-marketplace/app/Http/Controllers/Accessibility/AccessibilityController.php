<?php

namespace App\Http\Controllers;

use App\Services\AccessibilityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class AccessibilityController extends Controller
{
    private AccessibilityService $accessibilityService;

    public function __construct()
    {
        $this->accessibilityService = new AccessibilityService();
    }

    /**
     * Get accessibility settings for the current user.
     */
    public function getSettings()
    {
        // Get user's accessibility preferences from session or database
        $preferences = session('accessibility_preferences', [
            'high_contrast' => false,
            'large_text' => false,
            'screen_reader_mode' => false,
            'reduced_motion' => false,
            'language' => App::getLocale(),
        ]);

        return response()->json([
            'preferences' => $preferences,
            'labels' => $this->accessibilityService->generateLocalizedAccessibilityContent($preferences['language'])
        ]);
    }

    /**
     * Update accessibility settings for the current user.
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'high_contrast' => 'boolean',
            'large_text' => 'boolean',
            'screen_reader_mode' => 'boolean',
            'reduced_motion' => 'boolean',
            'language' => 'string|in:en,es,fr,de,pt,ar,ja,zh,yo,ig,ha', // Common languages including Nigerian languages
        ]);

        $preferences = [
            'high_contrast' => $request->get('high_contrast', false),
            'large_text' => $request->get('large_text', false),
            'screen_reader_mode' => $request->get('screen_reader_mode', false),
            'reduced_motion' => $request->get('reduced_motion', false),
            'language' => $request->get('language', App::getLocale()),
        ];

        // Store preferences in session
        session(['accessibility_preferences' => $preferences]);

        return response()->json([
            'message' => 'Accessibility preferences updated successfully',
            'preferences' => $preferences,
            'labels' => $this->accessibilityService->generateLocalizedAccessibilityContent($preferences['language'])
        ]);
    }

    /**
     * Get ARIA attributes for a specific element.
     */
    public function getAriaAttributes(Request $request)
    {
        $request->validate([
            'role' => 'string',
            'label' => 'string',
            'describedby' => 'string',
            'live' => 'in:polite,assertive,off',
            'busy' => 'boolean',
            'hidden' => 'boolean',
            'expanded' => 'boolean',
            'controls' => 'string',
        ]);

        $ariaAttributes = $this->accessibilityService->generateAriaAttributes($request->all());

        return response()->json($ariaAttributes);
    }

    /**
     * Generate accessibility-compliant HTML attributes for an element type.
     */
    public function getAccessibilityAttributes(Request $request)
    {
        $request->validate([
            'element_type' => 'required|string|in:button,link,form,dialog,navigation,search,main,banner,contentinfo,complementary,region',
            'content' => 'array',
        ]);

        $attributes = $this->accessibilityService->generateAccessibilityAttributes(
            $request->element_type,
            $request->content ?? []
        );

        return response()->json($attributes);
    }

    /**
     * Get skip link HTML.
     */
    public function getSkipLink(Request $request)
    {
        $target = $request->get('target', '#main-content');
        $text = $request->get('text', 'Skip to main content');

        $skipLink = $this->accessibilityService->generateSkipLink($target, $text);

        return response()->json([
            'skip_link' => $skipLink
        ]);
    }

    /**
     * Get screen reader only text wrapper.
     */
    public function getScreenReaderOnly(Request $request)
    {
        $request->validate([
            'text' => 'required|string'
        ]);

        $srText = $this->accessibilityService->screenReaderOnly($request->text);

        return response()->json([
            'screen_reader_text' => $srText
        ]);
    }

    /**
     * Create accessible table structure.
     */
    public function createAccessibleTable(Request $request)
    {
        $request->validate([
            'headers' => 'required|array',
            'rows' => 'required|array',
            'caption' => 'string|nullable',
        ]);

        $tableData = $this->accessibilityService->createAccessibleTable(
            $request->headers,
            $request->rows,
            $request->caption
        );

        return response()->json($tableData);
    }
}