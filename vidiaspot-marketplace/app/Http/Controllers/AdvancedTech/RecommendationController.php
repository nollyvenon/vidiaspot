<?php

namespace App\Http\Controllers;

use App\Services\RecommendationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class RecommendationController extends Controller
{
    protected RecommendationService $recommendationService;

    public function __construct(RecommendationService $recommendationService)
    {
        $this->recommendationService = $recommendationService;
    }

    /**
     * Get personalized recommendations for a user
     */
    public function getPersonalized(Request $request): JsonResponse
    {
        $userId = $request->user()->id ?? $request->input('user_id');
        $limit = $request->input('limit', 10);

        if (!$userId) {
            return response()->json([
                'error' => 'User ID is required',
            ], 400);
        }

        $recommendations = $this->recommendationService->getPersonalizedRecommendations($userId, $limit);

        return response()->json([
            'data' => $recommendations,
            'total' => count($recommendations),
            'user_id' => $userId,
        ]);
    }

    /**
     * Get collaborative filtering recommendations
     */
    public function getCollaborative(Request $request): JsonResponse
    {
        $userId = $request->user()->id ?? $request->input('user_id');
        $limit = $request->input('limit', 10);

        if (!$userId) {
            return response()->json([
                'error' => 'User ID is required',
            ], 400);
        }

        $recommendations = $this->recommendationService->getCollaborativeRecommendations($userId, $limit);

        return response()->json([
            'data' => $recommendations,
            'total' => count($recommendations),
            'user_id' => $userId,
        ]);
    }

    /**
     * Get category-based recommendations
     */
    public function getByCategory(Request $request): JsonResponse
    {
        $category = $request->input('category');
        $limit = $request->input('limit', 10);

        if (!$category) {
            return response()->json([
                'error' => 'Category is required',
            ], 400);
        }

        $recommendations = $this->recommendationService->getCategoryRecommendations($category, $limit);

        return response()->json([
            'data' => $recommendations,
            'total' => count($recommendations),
            'category' => $category,
        ]);
    }

    /**
     * Get trending items
     */
    public function getTrending(Request $request): JsonResponse
    {
        $limit = $request->input('limit', 10);
        $timeFrame = $request->input('time_frame', 'week');

        $trendingItems = $this->recommendationService->getTrendingItems($limit, $timeFrame);

        return response()->json([
            'data' => $trendingItems,
            'total' => count($trendingItems),
            'time_frame' => $timeFrame,
        ]);
    }

    /**
     * Get seasonal recommendations
     */
    public function getSeasonal(Request $request): JsonResponse
    {
        $limit = $request->input('limit', 10);

        $seasonalRecommendations = $this->recommendationService->getSeasonalRecommendations($limit);

        return response()->json([
            'data' => $seasonalRecommendations,
            'total' => count($seasonalRecommendations),
        ]);
    }

    /**
     * Track user interaction with recommendation
     */
    public function trackInteraction(Request $request): JsonResponse
    {
        $request->validate([
            'ad_id' => 'required|integer',
            'interaction_type' => 'required|in:view,click,favorite,buy,share,comment',
        ]);

        $userId = $request->user()->id ?? $request->input('user_id');
        $adId = $request->input('ad_id');
        $interactionType = $request->input('interaction_type');

        if (!$userId) {
            return response()->json([
                'error' => 'User ID is required',
            ], 400);
        }

        $success = $this->recommendationService->trackRecommendationInteraction($userId, $adId, $interactionType);

        return response()->json([
            'success' => $success,
            'user_id' => $userId,
            'ad_id' => $adId,
            'interaction_type' => $interactionType,
        ]);
    }

    /**
     * Get cold start recommendations for new users
     */
    public function getColdStart(Request $request): JsonResponse
    {
        $limit = $request->input('limit', 10);

        $recommendations = $this->recommendationService->getColdStartRecommendations($limit);

        return response()->json([
            'data' => $recommendations,
            'total' => count($recommendations),
            'type' => 'cold_start',
        ]);
    }

    /**
     * Refresh user's recommendations
     */
    public function refreshRecommendations(Request $request): JsonResponse
    {
        $userId = $request->user()->id ?? $request->input('user_id');

        if (!$userId) {
            return response()->json([
                'error' => 'User ID is required',
            ], 400);
        }

        $success = $this->recommendationService->refreshRecommendations($userId);

        return response()->json([
            'success' => $success,
            'user_id' => $userId,
            'message' => 'Recommendations refreshed successfully',
        ]);
    }

    /**
     * Get recommendation analytics for admin
     */
    public function getAnalytics(Request $request): JsonResponse
    {
        // This would return analytics about recommendation performance
        // For now, return example data
        return response()->json([
            'total_recommendations_delivered' => 12345,
            'click_through_rate' => 0.15,
            'conversion_rate' => 0.08,
            'most_popular_category' => 'Electronics',
            'top_performing_algorithms' => [
                'collaborative_filtering' => 0.25,
                'content_based' => 0.20,
                'popularity_based' => 0.15,
            ],
        ]);
    }

    /**
     * Get combined recommendations
     */
    public function getCombined(Request $request): JsonResponse
    {
        $userId = $request->user()->id ?? $request->input('user_id');
        $limit = $request->input('limit', 20);

        $responses = [
            'personalized' => [],
            'collaborative' => [],
            'trending' => [],
            'category' => [],
        ];

        if ($userId) {
            // Get personalized recommendations
            $responses['personalized'] = array_slice(
                $this->recommendationService->getPersonalizedRecommendations($userId, $limit / 2),
                0,
                $limit / 4
            );

            // Get collaborative recommendations
            $responses['collaborative'] = array_slice(
                $this->recommendationService->getCollaborativeRecommendations($userId, $limit / 4),
                0,
                $limit / 4
            );
        } else {
            // For anonymous users, use trending and seasonal
            $responses['trending'] = array_slice(
                $this->recommendationService->getTrendingItems($limit / 4),
                0,
                $limit / 4
            );
            
            $responses['seasonal'] = array_slice(
                $this->recommendationService->getSeasonalRecommendations($limit / 4),
                0,
                $limit / 4
            );
        }

        // Combine all recommendations with some deduplication
        $allRecommendations = [];
        $seenIds = [];

        foreach ($responses as $type => $items) {
            foreach ($items as $item) {
                if (!isset($seenIds[$item['id']])) {
                    $allRecommendations[] = $item;
                    $seenIds[$item['id']] = true;
                }
            }
        }

        // Limit the final result
        $finalRecommendations = array_slice($allRecommendations, 0, $limit);

        return response()->json([
            'data' => $finalRecommendations,
            'total' => count($finalRecommendations),
            'breakdown' => array_map('count', $responses),
        ]);
    }
}