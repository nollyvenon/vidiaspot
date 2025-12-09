<?php

namespace App\Http\Controllers;

use App\Services\LowBandwidthOptimizationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LowBandwidthOptimizationController extends Controller
{
    private LowBandwidthOptimizationService $optimizationService;

    public function __construct()
    {
        $this->optimizationService = new LowBandwidthOptimizationService();
    }

    /**
     * Get low bandwidth mode status for the current user.
     */
    public function getStatus()
    {
        $userId = Auth::id();
        $isActive = $this->optimizationService->isLowBandwidthModeActive($userId);

        return response()->json([
            'active' => $isActive,
            'user_id' => $userId,
            'message' => $isActive ? 'Low bandwidth mode is active' : 'Low bandwidth mode is inactive'
        ]);
    }

    /**
     * Activate or deactivate low bandwidth mode for the current user.
     */
    public function toggleMode(Request $request)
    {
        $request->validate([
            'activate' => 'required|boolean'
        ]);

        $userId = Auth::id();
        $this->optimizationService->activateLowBandwidthMode($userId, $request->activate);

        return response()->json([
            'success' => true,
            'active' => $request->activate,
            'message' => $request->activate ? 'Low bandwidth mode activated' : 'Low bandwidth mode deactivated'
        ]);
    }

    /**
     * Optimize an image for low bandwidth.
     */
    public function optimizeImage(Request $request)
    {
        $request->validate([
            'image_path' => 'required|string',
            'max_width' => 'integer|min:100|max:2000',
            'max_height' => 'integer|min:100|max:2000',
            'quality' => 'integer|min:10|max:100',
            'format' => 'string|in:webp,jpeg,png',
        ]);

        $options = [
            'max_width' => $request->max_width ?? 800,
            'max_height' => $request->max_height ?? 600,
            'quality' => $request->quality ?? 60,
            'format' => $request->format ?? 'webp',
        ];

        $optimized = $this->optimizationService->optimizeImage($request->image_path, $options);

        return response()->json($optimized);
    }

    /**
     * Optimize text content for low bandwidth.
     */
    public function optimizeTextContent(Request $request)
    {
        $request->validate([
            'content' => 'required|string',
            'remove_extra_whitespace' => 'boolean',
            'minify_html' => 'boolean',
            'remove_comments' => 'boolean',
            'compress_content' => 'boolean',
        ]);

        $options = [
            'remove_extra_whitespace' => $request->remove_extra_whitespace ?? true,
            'minify_html' => $request->minify_html ?? true,
            'remove_comments' => $request->remove_comments ?? true,
            'compress_content' => $request->compress_content ?? true,
        ];

        $optimized = $this->optimizationService->optimizeTextContent($request->content, $options);

        return response()->json($optimized);
    }

    /**
     * Optimize a response for low bandwidth.
     */
    public function optimizeResponse(Request $request)
    {
        $request->validate([
            'data' => 'required|array',
            'remove_images' => 'boolean',
            'compress_data' => 'boolean',
            'reduce_precision' => 'boolean',
            'filter_optional_fields' => 'boolean',
        ]);

        $options = [
            'remove_images' => $request->remove_images ?? false,
            'compress_data' => $request->compress_data ?? true,
            'reduce_precision' => $request->reduce_precision ?? true,
            'filter_optional_fields' => $request->filter_optional_fields ?? true,
        ];

        $optimized = $this->optimizationService->optimizeResponse($request->data, $options);

        return response()->json($optimized);
    }

    /**
     * Generate low bandwidth version of a page.
     */
    public function generateLowBandwidthPage(Request $request)
    {
        $request->validate([
            'html_content' => 'required|string',
            'remove_images' => 'boolean',
            'remove_videos' => 'boolean',
            'simplify_css' => 'boolean',
            'remove_non_essential_elements' => 'boolean',
            'text_only_mode' => 'boolean',
        ]);

        $options = [
            'remove_images' => $request->remove_images ?? true,
            'remove_videos' => $request->remove_videos ?? true,
            'simplify_css' => $request->simplify_css ?? true,
            'remove_non_essential_elements' => $request->remove_non_essential_elements ?? true,
            'text_only_mode' => $request->text_only_mode ?? false,
        ];

        $optimizedContent = $this->optimizationService->generateLowBandwidthPage($request->html_content, $options);

        return response()->json([
            'optimized_content' => $optimizedContent,
            'original_size' => strlen($request->html_content),
            'optimized_size' => strlen($optimizedContent),
        ]);
    }

    /**
     * Get optimization recommendations.
     */
    public function getRecommendations(Request $request)
    {
        $request->validate([
            'page_data' => 'required|array',
        ]);

        $recommendations = $this->optimizationService->getOptimizationRecommendations($request->page_data);

        return response()->json($recommendations);
    }

    /**
     * Check if low bandwidth optimization should be activated based on request.
     */
    public function shouldActivate(Request $request)
    {
        $shouldActivate = $this->optimizationService->shouldActivateLowBandwidthMode($request);

        return response()->json([
            'should_activate' => $shouldActivate,
            'reasoning' => $shouldActivate ? 'User preferences or network conditions indicate low bandwidth' : 'No need for bandwidth optimization',
        ]);
    }
}