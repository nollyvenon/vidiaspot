<?php

namespace App\Http\Controllers;

use App\Services\EdgeComputingOptimizationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EdgeComputingOptimizationController extends Controller
{
    private EdgeComputingOptimizationService $edgeService;

    public function __construct()
    {
        $this->edgeService = new EdgeComputingOptimizationService();
    }

    /**
     * Get optimal edge server for user.
     */
    public function getOptimalEdgeServer(Request $request)
    {
        $request->validate([
            'latitude' => 'numeric|between:-90,90',
            'longitude' => 'numeric|between:-180,180',
            'country' => 'string',
            'region' => 'string',
        ]);

        $location = [
            'lat' => $request->latitude,
            'lng' => $request->longitude,
            'country' => $request->country,
            'region' => $request->region,
        ];

        $server = $this->edgeService->getOptimalEdgeServer($location);

        return response()->json([
            'edge_server' => $server,
            'message' => 'Optimal edge server determined successfully'
        ]);
    }

    /**
     * Pre-cache content at edge locations.
     */
    public function preCacheContent(Request $request)
    {
        $request->validate([
            'content_items' => 'required|array',
            'content_items.*.id' => 'required|string',
            'content_items.*.url' => 'required|url',
            'content_items.*.type' => 'required|in:image,javascript,css,font,api_response,generic',
            'content_items.*.content' => 'required|string',
            'user_region' => 'string',
        ]);

        $result = $this->edgeService->preCacheContent($request->content_items, $request->user_region ?? 'US');

        return response()->json([
            'result' => $result,
            'message' => 'Content pre-cached at edge locations successfully'
        ]);
    }

    /**
     * Get content from edge cache.
     */
    public function getContentFromEdge(Request $request, string $contentId)
    {
        $request->validate([
            'user_region' => 'string',
        ]);

        $content = $this->edgeService->getContentFromEdge($contentId, $request->user_region ?? 'US');

        return response()->json([
            'content' => $content,
            'message' => 'Content retrieved from edge cache'
        ]);
    }

    /**
     * Warm edge cache for popular content.
     */
    public function warmEdgeCache(Request $request)
    {
        $request->validate([
            'popular_urls' => 'required|array',
            'popular_urls.*' => 'required|url',
            'user_region' => 'string',
        ]);

        $result = $this->edgeService->warmEdgeCache($request->popular_urls, $request->user_region ?? 'US');

        return response()->json([
            'result' => $result,
            'message' => 'Edge cache warmed successfully'
        ]);
    }

    /**
     * Get performance metrics for edge computing.
     */
    public function getPerformanceMetrics(Request $request)
    {
        $request->validate([
            'user_region' => 'string',
        ]);

        $metrics = $this->edgeService->getPerformanceMetrics($request->user_region ?? 'US');

        return response()->json([
            'metrics' => $metrics,
            'message' => 'Edge computing performance metrics retrieved successfully'
        ]);
    }

    /**
     * Get content optimization recommendations.
     */
    public function getContentOptimizationRecommendations(Request $request)
    {
        $request->validate([
            'page_urls' => 'required|array',
            'page_urls.*' => 'required|url',
        ]);

        $recommendations = $this->edgeService->getContentOptimizationRecommendations($request->page_urls);

        return response()->json([
            'recommendations' => $recommendations,
            'message' => 'Content optimization recommendations retrieved successfully'
        ]);
    }

    /**
     * Invalidate edge cache for specific content.
     */
    public function invalidateEdgeCache(Request $request)
    {
        $request->validate([
            'content_ids' => 'required|array',
            'content_ids.*' => 'required|string',
            'user_region' => 'string',
        ]);

        $result = $this->edgeService->invalidateEdgeCache($request->content_ids, $request->user_region ?? 'US');

        return response()->json([
            'result' => $result,
            'message' => 'Edge cache invalidated successfully'
        ]);
    }

    /**
     * Get edge server status.
     */
    public function getEdgeServerStatus(Request $request)
    {
        $request->validate([
            'region_code' => 'string',
        ]);

        $status = $this->edgeService->getEdgeServerStatus($request->region_code);

        return response()->json([
            'status' => $status,
            'message' => 'Edge server status retrieved successfully'
        ]);
    }

    /**
     * Get edge location options.
     */
    public function getEdgeLocationOptions()
    {
        $locations = $this->edgeService->getEdgeLocations();

        return response()->json([
            'locations' => $locations,
            'message' => 'Available edge location options retrieved successfully'
        ]);
    }

    /**
     * Get cache configuration options.
     */
    public function getCacheConfigurationOptions()
    {
        $configOptions = $this->edgeService->getCacheableContentTypes();

        return response()->json([
            'cache_options' => $configOptions,
            'message' => 'Cache configuration options retrieved successfully'
        ]);
    }
}