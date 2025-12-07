<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\RecommendationService;
use App\Services\AIRecommendationService;
use App\Http\Resources\AdResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class RecommendationController extends Controller
{
    protected $recommendationService;
    protected $aiRecommendationService;

    public function __construct(
        RecommendationService $recommendationService,
        AIRecommendationService $aiRecommendationService
    ) {
        $this->recommendationService = $recommendationService;
        $this->aiRecommendationService = $aiRecommendationService;
    }

    /**
     * Get recommended ads for the authenticated user.
     */
    public function getRecommendedAds(Request $request): JsonResponse
    {
        $limit = $request->get('limit', 10);
        $limit = min($limit, 50); // Max 50 recommendations

        if (!Auth::check()) {
            // For unauthenticated users, return trending ads
            $ads = $this->recommendationService->getTrendingAds($limit);
            return response()->json([
                'success' => true,
                'data' => AdResource::collection($ads),
                'message' => 'Trending ads for unauthenticated users'
            ]);
        }

        $userId = Auth::id();
        $ads = $this->aiRecommendationService->getAIRecommendations($userId, $limit);

        return response()->json([
            'success' => true,
            'data' => AdResource::collection($ads),
            'message' => 'AI-powered recommended ads based on your preferences'
        ]);
    }

    /**
     * Get trending ads.
     */
    public function getTrendingAds(Request $request): JsonResponse
    {
        $limit = $request->get('limit', 10);
        $limit = min($limit, 50); // Max 50 recommendations

        $ads = $this->recommendationService->getTrendingAds($limit);

        return response()->json([
            'success' => true,
            'data' => AdResource::collection($ads),
            'message' => 'Trending ads'
        ]);
    }

    /**
     * Get ads similar to a specified ad.
     */
    public function getSimilarAds(string $id, Request $request): JsonResponse
    {
        $limit = $request->get('limit', 10);
        $limit = min($limit, 50); // Max 50 recommendations

        $ads = $this->aiRecommendationService->getAISimilarAds($id, $limit);

        return response()->json([
            'success' => true,
            'data' => AdResource::collection($ads),
            'message' => 'AI-powered similar ads'
        ]);
    }
}
