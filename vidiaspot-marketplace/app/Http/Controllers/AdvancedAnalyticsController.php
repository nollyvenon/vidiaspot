<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\AdvancedAnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class AdvancedAnalyticsController extends Controller
{
    protected AdvancedAnalyticsService $analyticsService;

    public function __construct(AdvancedAnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    /**
     * Get real-time market data and news
     */
    public function getRealTimeMarketData(Request $request): JsonResponse
    {
        $filters = $request->only(['category_id', 'location', 'days']);

        $data = $this->analyticsService->getRealTimeMarketData($filters);

        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => 'Real-time market data retrieved successfully'
        ]);
    }

    /**
     * Get technical analysis indicators for a category
     */
    public function getTechnicalIndicators(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|integer|exists:categories,id',
            'days' => 'nullable|integer|min:7|max:365',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $this->analyticsService->getTechnicalIndicators(
            $request->category_id,
            $request->days ?? 30
        );

        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => 'Technical indicators retrieved successfully'
        ]);
    }

    /**
     * Get market sentiment analysis
     */
    public function getMarketSentimentAnalysis(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'nullable|integer|exists:categories,id',
            'days' => 'nullable|integer|min=7|max=365',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $this->analyticsService->getMarketSentimentAnalysis(
            $request->category_id,
            $request->days ?? 30
        );

        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => 'Market sentiment analysis retrieved successfully'
        ]);
    }

    /**
     * Get price predictions
     */
    public function getPricePredictions(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|integer|exists:categories,id',
            'days_forward' => 'nullable|integer|min=1|max=30',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $this->analyticsService->getPricePredictions(
            $request->category_id,
            $request->days_forward ?? 7
        );

        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => 'Price predictions retrieved successfully'
        ]);
    }

    /**
     * Get portfolio analytics for a user
     */
    public function getPortfolioAnalytics(Request $request): JsonResponse
    {
        $data = $this->analyticsService->getPortfolioAnalytics(auth()->id());

        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => 'Portfolio analytics retrieved successfully'
        ]);
    }

    /**
     * Get tax reporting data
     */
    public function getTaxReportingData(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'year' => 'required|integer|min:2000|max:2030',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $this->analyticsService->getTaxReportingData(
            auth()->id(),
            $request->year
        );

        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => 'Tax reporting data retrieved successfully'
        ]);
    }

    /**
     * Get historical data for backtesting
     */
    public function getHistoricalData(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'nullable|integer|exists:categories,id',
            'days' => 'nullable|integer|min=7|max=365',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $this->analyticsService->getHistoricalData(
            $request->category_id,
            $request->days ?? 90
        );

        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => 'Historical data retrieved successfully'
        ]);
    }

    /**
     * Get correlation analysis between pairs
     */
    public function getCorrelationAnalysis(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'integer|exists:categories,id',
            'days' => 'nullable|integer|min=7|max=365',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $this->analyticsService->getCorrelationAnalysis(
            $request->category_ids ?? [],
            $request->days ?? 30
        );

        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => 'Correlation analysis retrieved successfully'
        ]);
    }
}