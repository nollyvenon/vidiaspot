<?php

namespace App\Http\Controllers;

use App\Services\EnvironmentalImpactRatingsService;
use Illuminate\Http\Request;

class EnvironmentalImpactRatingsController extends Controller
{
    private EnvironmentalImpactRatingsService $ratingsService;

    public function __construct()
    {
        $this->ratingsService = new EnvironmentalImpactRatingsService();
    }

    /**
     * Calculate environmental impact rating for a product.
     */
    public function calculateProductRating(Request $request)
    {
        $request->validate([
            'id' => 'string',
            'category' => 'required|string',
            'weight_kg' => 'required|numeric|min:0',
            'production_location' => 'required|string',
            'shipping_distance_km' => 'required|numeric|min:0',
            'materials' => 'required|array',
            'packaging_type' => 'required|string',
            'lifespan_years' => 'required|numeric|min:0',
            'water_usage_liters' => 'numeric|min:0',
            'energy_usage_kwh' => 'numeric|min:0',
            'waste_generated_kg' => 'numeric|min:0',
            'energy_efficient' => 'boolean',
            'water_efficient' => 'boolean',
            'minimal_packaging' => 'boolean',
            'recyclable' => 'boolean',
            'biodegradable' => 'boolean',
            'take_back_program' => 'boolean',
            'fair_trade_certified' => 'boolean',
            'local_production' => 'boolean',
            'living_wage' => 'boolean',
            'ethical_sourcing' => 'boolean',
        ]);

        try {
            $rating = $this->ratingsService->calculateProductRating($request->all());

            return response()->json([
                'rating' => $rating,
                'message' => 'Environmental impact rating calculated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get impact criteria.
     */
    public function getImpactCriteria()
    {
        $criteria = $this->ratingsService->getImpactCriteria();

        return response()->json([
            'criteria' => $criteria,
            'message' => 'Impact criteria retrieved successfully'
        ]);
    }

    /**
     * Get product comparison data.
     */
    public function getProductComparison(Request $request)
    {
        $request->validate([
            'product_ids' => 'required|array|min:2',
            'product_ids.*' => 'required|string',
        ]);

        $comparison = $this->ratingsService->getComparisonData($request->product_ids);

        return response()->json([
            'comparison' => $comparison,
            'message' => 'Product comparison data retrieved successfully'
        ]);
    }

    /**
     * Get industry benchmarks.
     */
    public function getIndustryBenchmarks(Request $request)
    {
        $request->validate([
            'category' => 'required|string',
        ]);

        $benchmarks = $this->ratingsService->getIndustryBenchmarks($request->category);

        return response()->json([
            'benchmarks' => $benchmarks,
            'message' => 'Industry benchmarks retrieved successfully'
        ]);
    }

    /**
     * Calculate vendor sustainability score.
     */
    public function calculateVendorSustainabilityScore(Request $request)
    {
        $request->validate([
            'id' => 'required|string',
            'name' => 'required|string',
            'products' => 'array',
            'certifications' => 'array',
            'sustainability_practices' => 'array',
        ]);

        $score = $this->ratingsService->calculateVendorSustainabilityScore($request->all());

        return response()->json([
            'score' => $score,
            'message' => 'Vendor sustainability score calculated successfully'
        ]);
    }

    /**
     * Get rating history for a product.
     */
    public function getRatingHistory(Request $request, string $productId)
    {
        $history = $this->ratingsService->getRatingHistory($productId);

        return response()->json([
            'history' => $history,
            'message' => 'Rating history retrieved successfully'
        ]);
    }

    /**
     * Get environmental impact leaderboard.
     */
    public function getLeaderboard(Request $request)
    {
        $request->validate([
            'category' => 'string',
            'limit' => 'integer|min:1|max:100',
        ]);

        $leaderboard = $this->ratingsService->getLeaderboard(
            $request->category,
            $request->limit ?? 10
        );

        return response()->json([
            'leaderboard' => $leaderboard,
            'message' => 'Environmental impact leaderboard retrieved successfully'
        ]);
    }
}