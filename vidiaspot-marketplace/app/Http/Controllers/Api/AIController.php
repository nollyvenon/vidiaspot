<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\AIService;
use App\Models\Ad;
use App\Models\Category;
use App\Models\City;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class AIController extends Controller
{
    protected $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Get pricing recommendation for an ad
     * POST /api/ai/pricing-recommendation
     */
    public function getPricingRecommendation(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'ad_id' => 'required|exists:ads,id',
            'category_id' => 'required|exists:categories,id',
            'location_id' => 'nullable|exists:cities,id',
            'condition' => 'sometimes|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::user();
        $ad = Ad::findOrFail($request->ad_id);

        if ($ad->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to get pricing recommendation for this ad'
            ], 403);
        }

        try {
            $recommendation = $this->aiService->generatePricingRecommendation(
                $ad,
                $request->category_id,
                $request->location_id
            );

            if (!$recommendation) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to generate pricing recommendation'
                ], 500);
            }

            return response()->json([
                'success' => true,
                'data' => $recommendation,
                'message' => 'Pricing recommendation generated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error generating pricing recommendation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get demand forecast for a category
     * GET /api/ai/demand-forecast
     */
    public function getDemandForecast(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:categories,id',
            'location_id' => 'nullable|exists:cities,id',
            'time_period' => 'sometimes|in:daily,weekly,monthly,quarterly,yearly'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $forecast = $this->aiService->generateDemandForecast(
                $request->category_id,
                $request->location_id,
                $request->time_period ?? 'monthly'
            );

            if (!$forecast) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to generate demand forecast'
                ], 500);
            }

            return response()->json([
                'success' => true,
                'data' => $forecast,
                'message' => 'Demand forecast generated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error generating demand forecast',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get success prediction for an ad
     * POST /api/ai/success-prediction
     */
    public function getSuccessPrediction(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'ad_id' => 'required|exists:ads,id',
            'category_id' => 'required|exists:categories,id',
            'location_id' => 'nullable|exists:cities,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::user();
        $ad = Ad::findOrFail($request->ad_id);

        if ($ad->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to get success prediction for this ad'
            ], 403);
        }

        try {
            $prediction = $this->aiService->generateSuccessPrediction(
                $ad,
                $request->category_id,
                $request->location_id
            );

            if (!$prediction) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to generate success prediction'
                ], 500);
            }

            return response()->json([
                'success' => true,
                'data' => $prediction,
                'message' => 'Success prediction generated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error generating success prediction',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check for duplicate ads
     * GET /api/ai/check-duplicates/{ad_id}
     */
    public function checkDuplicates(string $adId): JsonResponse
    {
        $user = Auth::user();
        $ad = Ad::findOrFail($adId);

        if ($ad->user_id !== $user->id && !$user->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to check duplicates for this ad'
            ], 403);
        }

        try {
            $duplicates = $this->aiService->detectDuplicates($ad);

            return response()->json([
                'success' => true,
                'data' => [
                    'ad_id' => $ad->id,
                    'duplicate_matches' => $duplicates,
                    'count' => count($duplicates)
                ],
                'message' => 'Duplicate check completed successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error checking for duplicates',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get fraud detection analysis
     * GET /api/ai/fraud-analysis
     */
    public function getFraudAnalysis(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'ad_id' => 'sometimes|exists:ads,id',
            'user_id' => 'sometimes|exists:users,id',
            'payment_transaction_id' => 'sometimes|exists:payment_transactions,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $ad = $request->ad_id ? Ad::findOrFail($request->ad_id) : null;
        $user = $request->user_id ? User::findOrFail($request->user_id) : Auth::user();
        $payment = $request->payment_transaction_id ? \App\Models\PaymentTransaction::findOrFail($request->payment_transaction_id) : null;

        if ($ad && $ad->user_id !== $user->id && !$user->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to analyze fraud for this ad'
            ], 403);
        }

        try {
            $fraudAnalysis = $this->aiService->detectFraud($ad, $user, $payment);

            return response()->json([
                'success' => true,
                'data' => $fraudAnalysis,
                'message' => 'Fraud analysis completed successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error performing fraud analysis',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get smart product recommendations (for ad personalization)
     * GET /api/ai/recommendations
     */
    public function getRecommendations(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'sometimes|exists:categories,id',
            'location_id' => 'sometimes|exists:cities,id',
            'user_id' => 'sometimes|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user_id ? User::findOrFail($request->user_id) : Auth::user();
        
        try {
            // In a real implementation, this would use AI to provide recommendations
            // For now, we'll return a sample response structure
            $recommendations = [];

            return response()->json([
                'success' => true,
                'data' => [
                    'recommendations' => $recommendations,
                    'user_id' => $user->id,
                    'personalized' => true
                ],
                'message' => 'Recommendations retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving recommendations',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get seasonal trend analysis
     * GET /api/ai/seasonal-trends/{category_id}
     */
    public function getSeasonalTrends(string $categoryId): JsonResponse
    {
        $category = Category::findOrFail($categoryId);

        try {
            // In a real implementation, this would analyze historical data for seasonal patterns
            // For now, we'll return a sample response
            $trends = [
                'category_name' => $category->name,
                'seasonal_factors' => [
                    'q1' => 75, // Jan-Mar
                    'q2' => 65, // Apr-Jun 
                    'q3' => 60, // Jul-Sep
                    'q4' => 90, // Oct-Dec (holiday season)
                ],
                'peak_months' => ['December', 'January', 'February'],
                'low_months' => ['August', 'September'],
                'year_over_year_growth' => 12.5,
                'demand_variability' => 'moderate',
            ];

            return response()->json([
                'success' => true,
                'data' => $trends,
                'message' => 'Seasonal trends retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving seasonal trends',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}