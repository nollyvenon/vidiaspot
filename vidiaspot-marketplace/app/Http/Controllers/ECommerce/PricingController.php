<?php

namespace App\Http\Controllers;

use App\Services\PricingService;
use App\Models\Ad;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PricingController extends Controller
{
    protected PricingService $pricingService;

    public function __construct(PricingService $pricingService)
    {
        $this->pricingService = $pricingService;
    }

    /**
     * Get price suggestion for a product
     */
    public function suggestPrice(Request $request): JsonResponse
    {
        $request->validate([
            'category_id' => 'required|integer|exists:categories,id',
            'product_name' => 'nullable|string',
            'product_description' => 'nullable|string',
            'condition' => 'nullable|in:new,like_new,excellent,very_good,good,fair,poor',
            'brand' => 'nullable|string',
            'model' => 'nullable|string',
            'features' => 'nullable|array',
        ]);

        $productData = [
            'category_id' => $request->input('category_id'),
            'name' => $request->input('product_name'),
            'description' => $request->input('product_description'),
            'condition' => $request->input('condition', 'good'),
            'brand' => $request->input('brand'),
            'model' => $request->input('model'),
            'features' => $request->input('features', []),
        ];

        $suggestedPrice = $this->pricingService->suggestOptimalPrice($productData);

        return response()->json([
            'success' => true,
            'suggested_price' => $suggestedPrice,
        ]);
    }

    /**
     * Get competitor pricing analysis
     */
    public function competitorAnalysis(Request $request, int $categoryId): JsonResponse
    {
        $productName = $request->input('product_name', '');

        $analysis = $this->pricingService->getCompetitorPricing($categoryId, $productName);

        return response()->json([
            'success' => true,
            'competitor_analysis' => $analysis,
        ]);
    }

    /**
     * Get pricing trends for a category
     */
    public function pricingTrends(Request $request, int $categoryId): JsonResponse
    {
        $days = $request->input('days', 90);
        $days = min(365, max(7, $days)); // Between 7 and 365 days

        $trends = $this->pricingService->getPriceTrends($categoryId, $days);

        return response()->json([
            'success' => true,
            'trends' => $trends,
        ]);
    }

    /**
     * Validate a price against market standards
     */
    public function validatePrice(Request $request): JsonResponse
    {
        $request->validate([
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|integer|exists:categories,id',
        ]);

        $validation = $this->pricingService->validatePrice(
            $request->input('price'),
            $request->input('category_id')
        );

        return response()->json([
            'success' => true,
            'validation' => $validation,
        ]);
    }

    /**
     * Get dynamic pricing for events
     */
    public function dynamicPricing(Request $request, int $categoryId): JsonResponse
    {
        $request->validate([
            'event_type' => 'required|in:black_friday,cyber_monday,christmas,new_year,back_to_school,flash_sale,clearance,normal,premium,luxury',
        ]);

        $dynamicPricing = $this->pricingService->getDynamicPricing(
            $categoryId,
            $request->input('event_type')
        );

        return response()->json([
            'success' => true,
            'dynamic_pricing' => $dynamicPricing,
        ]);
    }

    /**
     * Get profit maximizing price
     */
    public function profitMaximizingPrice(Request $request): JsonResponse
    {
        $request->validate([
            'category_id' => 'required|integer|exists:categories,id',
            'cost_price' => 'required|numeric|min:0',
            'product_details' => 'nullable|array',
        ]);

        $productData = [
            'category_id' => $request->input('category_id'),
            'cost_price' => $request->input('cost_price'),
            'details' => $request->input('product_details', []),
        ];

        $optimization = $this->pricingService->getProfitMaximizingPrice($productData);

        return response()->json([
            'success' => true,
            'optimization' => $optimization,
        ]);
    }

    /**
     * Get pricing dashboard data for admin
     */
    public function dashboardData(Request $request): JsonResponse
    {
        $data = [
            'total_products_analyzed' => 1250, // This would come from actual data
            'average_price_accuracy' => 0.85, // 85% accuracy rate
            'most_competitive_categories' => [
                ['name' => 'Electronics', 'competition_index' => 0.92],
                ['name' => 'Fashion', 'competition_index' => 0.87],
                ['name' => 'Vehicles', 'competition_index' => 0.83],
            ],
            'price_volatility' => [
                'high_fluctuation_categories' => ['Cryptocurrency', 'Stocks'],
                'stable_categories' => ['Basic Groceries', 'Utilities'],
            ],
            'suggested_vs_actual_pricing' => [
                'adherence_rate' => 0.72, // 72% of users follow suggestions
                'average_adjustment' => 5.5, // On average, users adjust by 5.5%
            ],
            'market_trends' => $this->pricingService->getMarketTrends(),
        ];

        return response()->json([
            'success' => true,
            'dashboard_data' => $data,
        ]);
    }

    /**
     * Bulk price suggestions
     */
    public function bulkSuggestPrice(Request $request): JsonResponse
    {
        $request->validate([
            'products' => 'required|array|max:100',
            'products.*.category_id' => 'required|integer|exists:categories,id',
            'products.*.name' => 'nullable|string',
            'products.*.condition' => 'nullable|in:new,like_new,excellent,very_good,good,fair,poor',
        ]);

        $products = $request->input('products');
        $results = [];

        foreach ($products as $product) {
            $productData = [
                'category_id' => $product['category_id'],
                'name' => $product['name'] ?? '',
                'condition' => $product['condition'] ?? 'good',
            ];

            $suggestedPrice = $this->pricingService->suggestOptimalPrice($productData);
            $results[] = [
                'product' => $product,
                'suggested_price' => $suggestedPrice,
            ];
        }

        return response()->json([
            'success' => true,
            'results' => $results,
            'total_processed' => count($results),
        ]);
    }
}