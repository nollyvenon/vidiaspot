<?php

namespace App\Http\Controllers\AI;

use App\Http\Controllers\Controller;
use App\Services\AI\VoiceSearchService;
use App\Services\AI\VisualSearchService;
use App\Services\AI\ARViewService;
use App\Services\AI\SocialSearchService;
use App\Services\AI\TrendingRecommendationsService;
use App\Services\AI\PriceDropAlertService;
use App\Services\AI\GeographicHeatMapService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\UploadedFile;

class AdvancedSearchDiscoveryController extends Controller
{
    protected $voiceSearchService;
    protected $visualSearchService;
    protected $arService;
    protected $socialSearchService;
    protected $trendingService;
    protected $priceAlertService;
    protected $geoHeatMapService;
    
    public function __construct(
        VoiceSearchService $voiceSearchService,
        VisualSearchService $visualSearchService,
        ARViewService $arService,
        SocialSearchService $socialSearchService,
        TrendingRecommendationsService $trendingService,
        PriceDropAlertService $priceAlertService,
        GeographicHeatMapService $geoHeatMapService
    ) {
        $this->voiceSearchService = $voiceSearchService;
        $this->visualSearchService = $visualSearchService;
        $this->arService = $arService;
        $this->socialSearchService = $socialSearchService;
        $this->trendingService = $trendingService;
        $this->priceAlertService = $priceAlertService;
        $this->geoHeatMapService = $geoHeatMapService;
    }
    
    /**
     * Voice search with natural language processing
     */
    public function voiceSearch(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'audio_file' => 'required|file|mimes:wav,mp3,m4a|max:10240', // 10MB max
            'language' => 'sometimes|string|in:en,es,fr,de,pt,ru,ja,zh'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            $audioFile = $request->file('audio_file');
            $language = $request->get('language', 'en');
            
            // Convert voice to text
            $transcribedText = $this->voiceSearchService->processVoiceSearch($audioFile, $language);
            
            // Process natural language query
            $parsedQuery = $this->voiceSearchService->processNaturalLanguageQuery($transcribedText);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'transcribed_text' => $transcribedText,
                    'parsed_query' => $parsedQuery,
                    'search_results' => $parsedQuery // In a real app, this would call the search service
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error processing voice search',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Visual search using image recognition
     */
    public function visualSearch(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:10240', // 10MB max
            'min_confidence' => 'sometimes|numeric|min:0.1|max:1.0',
            'search_type' => 'sometimes|string|in:objects,categories,similar,reverse'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            $image = $request->file('image');
            $options = [
                'min_confidence' => $request->get('min_confidence', 0.7),
            ];
            
            $searchType = $request->get('search_type', 'objects');
            
            $results = match($searchType) {
                'reverse' => $this->visualSearchService->performReverseImageSearch($image),
                'objects', 'categories', 'similar' => $this->visualSearchService->performVisualSearch($image, $options),
                default => $this->visualSearchService->performVisualSearch($image, $options)
            };
            
            return response()->json([
                'success' => true,
                'data' => $results
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error processing visual search',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get AR view data for a product
     */
    public function getARViewData(Request $request, int $adId): JsonResponse
    {
        try {
            $arData = $this->arService->getARViewData($adId);
            
            return response()->json([
                'success' => true,
                'data' => $arData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error getting AR view data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get AR session data
     */
    public function getARSessionData(Request $request, int $adId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'session_id' => 'required|string|max:255'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            $sessionId = $request->get('session_id');
            $sessionData = $this->arService->getARSessionData($adId, $sessionId);
            
            return response()->json([
                'success' => true,
                'data' => $sessionData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error getting AR session data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Find listings from friends' networks
     */
    public function socialSearch(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'category_id' => 'sometimes|integer',
            'location' => 'sometimes|string|max:255',
            'max_price' => 'sometimes|numeric|min:0',
            'min_price' => 'sometimes|numeric|min:0|max:max_price',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            $userId = $request->get('user_id');
            $filters = $request->only(['category_id', 'location', 'max_price', 'min_price']);
            
            $listings = $this->socialSearchService->findListingsFromFriendsNetwork($userId, $filters);
            
            return response()->json([
                'success' => true,
                'data' => $listings
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error processing social search',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get friend recommendations
     */
    public function getFriendRecommendations(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'limit' => 'sometimes|integer|min:1|max:100'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            $userId = $request->get('user_id');
            $limit = $request->get('limit', 10);
            
            $recommendations = $this->socialSearchService->getFriendRecommendations($userId, $limit);
            
            return response()->json([
                'success' => true,
                'data' => $recommendations
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error getting friend recommendations',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get social activity feed
     */
    public function getSocialActivityFeed(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'limit' => 'sometimes|integer|min:1|max:50'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            $userId = $request->get('user_id');
            $limit = $request->get('limit', 20);
            
            $feed = $this->socialSearchService->getSocialActivityFeed($userId, $limit);
            
            return response()->json([
                'success' => true,
                'data' => $feed
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error getting social activity feed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get trending items
     */
    public function getTrendingItems(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'time_frame' => 'sometimes|string|in:day,week,month',
            'limit' => 'sometimes|integer|min:1|max:50',
            'category_id' => 'sometimes|integer'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            $options = $request->only(['time_frame', 'limit', 'category_id']);
            
            $trendingItems = $this->trendingService->getTrendingItems($options);
            
            return response()->json([
                'success' => true,
                'data' => $trendingItems
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error getting trending items',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get seasonal recommendations
     */
    public function getSeasonalRecommendations(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'sometimes|integer',
            'season' => 'sometimes|string|in:winter,summer,spring,fall'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            $categoryId = $request->get('category_id');
            $season = $request->get('season');
            
            $seasonalRecs = $this->trendingService->getSeasonalRecommendations($categoryId, $season);
            
            return response()->json([
                'success' => true,
                'data' => $seasonalRecs
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error getting seasonal recommendations',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get personalized seasonal recommendations
     */
    public function getPersonalizedSeasonalRecommendations(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'sometimes|integer',
            'categories' => 'sometimes|array',
            'categories.*' => 'string',
            'price_range.min' => 'sometimes|numeric|min:0',
            'price_range.max' => 'sometimes|numeric|min:0'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            $userId = $request->get('user_id');
            $preferences = $request->only(['categories', 'price_range']);
            
            $personalizedRecs = $this->trendingService->getPersonalizedSeasonalRecommendations($userId, $preferences);
            
            return response()->json([
                'success' => true,
                'data' => $personalizedRecs
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error getting personalized seasonal recommendations',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get trend forecast
     */
    public function getTrendForecast(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'days' => 'sometimes|integer|min:1|max:365',
            'category_id' => 'sometimes|integer'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            $days = $request->get('days', 7);
            $categoryId = $request->get('category_id');
            
            $forecast = $this->trendingService->getTrendForecast($days, $categoryId);
            
            return response()->json([
                'success' => true,
                'data' => $forecast
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error getting trend forecast',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Create a price drop alert
     */
    public function createPriceAlert(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'ad_id' => 'required|integer',
            'target_price' => 'required|numeric|min:0'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            $userId = $request->get('user_id');
            $adId = $request->get('ad_id');
            $targetPrice = $request->get('target_price');
            
            $result = $this->priceAlertService->createPriceAlert($userId, $adId, $targetPrice);
            
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating price alert',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get user's price alerts
     */
    public function getUserPriceAlerts(Request $request, int $userId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'active' => 'sometimes|boolean',
            'category_id' => 'sometimes|integer',
            'price_range.min' => 'sometimes|numeric|min:0',
            'price_range.max' => 'sometimes|numeric|min:0'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            $filters = $request->only(['active', 'category_id', 'price_range']);
            
            $alerts = $this->priceAlertService->getUserPriceAlerts($userId, $filters);
            
            return response()->json([
                'success' => true,
                'data' => $alerts
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error getting price alerts',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get geographic heat map
     */
    public function getGeographicHeatMap(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'sometimes|integer',
            'time_frame' => 'sometimes|string|in:day,week,month,quarter,year',
            'location' => 'sometimes|string|max:255',
            'min_demand' => 'sometimes|integer|min:1'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            $options = $request->only(['category_id', 'time_frame', 'location', 'min_demand']);
            
            $heatMap = $this->geoHeatMapService->generateHeatMap($options);
            
            return response()->json([
                'success' => true,
                'data' => $heatMap
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error getting geographic heat map',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get trending locations for a category
     */
    public function getTrendingLocationsForCategory(Request $request, int $categoryId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'time_frame' => 'sometimes|string|in:day,week,month',
            'limit' => 'sometimes|integer|min:1|max:50'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            $options = $request->only(['time_frame', 'limit']);
            
            $trendingLocations = $this->geoHeatMapService->getTrendingLocationsForCategory($categoryId, $options);
            
            return response()->json([
                'success' => true,
                'data' => $trendingLocations
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error getting trending locations',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get seasonal location patterns
     */
    public function getSeasonalLocationPatterns(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'sometimes|integer',
            'year' => 'sometimes|integer|min:2000|max:2030'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            $options = $request->only(['category_id', 'year']);
            
            $patterns = $this->geoHeatMapService->getSeasonalLocationPatterns($options);
            
            return response()->json([
                'success' => true,
                'data' => $patterns
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error getting seasonal location patterns',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get demand forecast for locations
     */
    public function getDemandForecastForLocations(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'days' => 'sometimes|integer|min:1|max:365',
            'category_id' => 'sometimes|integer'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            $options = $request->only(['days', 'category_id']);
            
            $forecast = $this->geoHeatMapService->getDemandForecastForLocations($options);
            
            return response()->json([
                'success' => true,
                'data' => $forecast
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error getting demand forecast for locations',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}