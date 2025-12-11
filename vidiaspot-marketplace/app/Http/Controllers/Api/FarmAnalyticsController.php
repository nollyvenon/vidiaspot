<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FarmAnalyticsController extends Controller
{
    /**
     * Get comprehensive farm product analytics
     */
    public function getFarmAnalytics(Request $request): JsonResponse
    {
        $request->validate([
            'start_date' => 'date|nullable',
            'end_date' => 'date|nullable',
            'user_id' => 'nullable|exists:users,id',
            'farm_location' => 'nullable|string',
            'is_organic' => 'nullable|boolean',
            'harvest_season' => 'nullable|string',
        ]);

        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->subDays(30);
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now();

        $query = Ad::where('direct_from_farm', true)
            ->whereBetween('created_at', [$startDate, $endDate]);

        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->farm_location) {
            $query->where('farm_location', 'like', '%' . $request->farm_location . '%');
        }

        if ($request->is_organic !== null) {
            $query->where('is_organic', $request->is_organic);
        }

        if ($request->harvest_season) {
            $query->where('harvest_season', $request->harvest_season);
        }

        // Get main analytics
        $analytics = $query->select([
            DB::raw('COUNT(*) as total_farm_products'),
            DB::raw('AVG(price) as average_price'),
            DB::raw('SUM(view_count) as total_views'),
            DB::raw('AVG(quality_rating) as average_quality_rating'),
            DB::raw('AVG(sustainability_score) as average_sustainability_score'),
            DB::raw('AVG(carbon_footprint) as average_carbon_footprint'),
            DB::raw('COUNT(CASE WHEN is_organic = 1 THEN 1 END) as organic_products_count'),
            DB::raw('COUNT(CASE WHEN pesticide_use = 0 THEN 1 END) as non_pesticide_products_count'),
            DB::raw('AVG(freshness_days) as average_freshness_days'),
            DB::raw('COUNT(CASE WHEN farm_tour_available = 1 THEN 1 END) as farm_tour_available_count'),
        ])->first();

        // Calculate additional metrics
        $totalProducts = $analytics->total_farm_products;
        $analytics->organic_percentage = $totalProducts > 0 ? round(($analytics->organic_products_count / $totalProducts) * 100, 2) : 0;
        $analytics->non_pesticide_percentage = $totalProducts > 0 ? round(($analytics->non_pesticide_products_count / $totalProducts) * 100, 2) : 0;
        $analytics->farm_tour_availability_rate = $totalProducts > 0 ? round(($analytics->farm_tour_available_count / $totalProducts) * 100, 2) : 0;

        return response()->json([
            'success' => true,
            'data' => $analytics,
            'date_range' => [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
            ]
        ]);
    }

    /**
     * Get seasonal performance analysis
     */
    public function getSeasonalPerformance(Request $request): JsonResponse
    {
        $request->validate([
            'year' => 'integer|nullable',
        ]);

        $year = $request->year ?? Carbon::now()->year;

        $seasonalData = Ad::where('direct_from_farm', true)
            ->whereYear('created_at', $year)
            ->select([
                DB::raw("
                    CASE 
                        WHEN MONTH(created_at) IN (3, 4, 5) THEN 'spring'
                        WHEN MONTH(created_at) IN (6, 7, 8) THEN 'summer'
                        WHEN MONTH(created_at) IN (9, 10, 11) THEN 'fall'
                        WHEN MONTH(created_at) IN (12, 1, 2) THEN 'winter'
                        ELSE 'other'
                    END as season
                "),
                DB::raw('COUNT(*) as product_count'),
                DB::raw('AVG(price) as average_price'),
                DB::raw('SUM(view_count) as total_views'),
                DB::raw('AVG(quality_rating) as average_quality'),
                DB::raw('AVG(sustainability_score) as average_sustainability'),
            ])
            ->groupBy('season')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $seasonalData,
            'year' => $year,
        ]);
    }

    /**
     * Get farmer performance report
     */
    public function getFarmerPerformance(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'start_date' => 'date|nullable',
            'end_date' => 'date|nullable',
        ]);

        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->subDays(30);
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now();

        $farmer = User::find($request->user_id);

        $farmProducts = Ad::where('user_id', $request->user_id)
            ->where('direct_from_farm', true)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with(['category'])
            ->get();

        if ($farmProducts->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No farm products found for this farmer in the specified period'
            ], 404);
        }

        $performanceData = [
            'farmer_info' => [
                'name' => $farmer->name,
                'email' => $farmer->email,
                'registration_date' => $farmer->created_at->format('Y-m-d'),
            ],
            'period_summary' => [
                'total_products' => $farmProducts->count(),
                'total_views' => $farmProducts->sum('view_count'),
                'average_price' => $farmProducts->avg('price'),
                'average_quality_rating' => $farmProducts->avg('quality_rating'),
                'organic_percentage' => round(($farmProducts->where('is_organic', true)->count() / $farmProducts->count()) * 100, 2),
                'sustainability_average' => $farmProducts->avg('sustainability_score'),
            ],
            'products_by_category' => $farmProducts->groupBy('category.name')->map(function($products, $category) {
                return [
                    'category' => $category,
                    'count' => $products->count(),
                    'average_price' => $products->avg('price'),
                    'average_rating' => $products->avg('quality_rating'),
                    'organic_percentage' => $products->count() > 0 ? round(($products->where('is_organic', true)->count() / $products->count()) * 100, 2) : 0,
                ];
            })->values(),
            'daily_performance' => $this->getDailyPerformance($request->user_id, $startDate, $endDate),
        ];

        return response()->json([
            'success' => true,
            'data' => $performanceData,
        ]);
    }

    /**
     * Get sustainability and environmental impact report
     */
    public function getSustainabilityReport(Request $request): JsonResponse
    {
        $request->validate([
            'start_date' => 'date|nullable',
            'end_date' => 'date|nullable',
            'farm_location' => 'nullable|string',
        ]);

        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->subDays(30);
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now();

        $query = Ad::where('direct_from_farm', true)
            ->whereBetween('created_at', [$startDate, $endDate]);

        if ($request->farm_location) {
            $query->where('farm_location', 'like', '%' . $request->farm_location . '%');
        }

        $sustainabilityData = $query->select([
            DB::raw('AVG(sustainability_score) as average_sustainability_score'),
            DB::raw('AVG(carbon_footprint) as average_carbon_footprint'),
            DB::raw('COUNT(CASE WHEN is_organic = 1 THEN 1 END) as organic_products_count'),
            DB::raw('COUNT(CASE WHEN pesticide_use = 0 THEN 1 END) as non_pesticide_products_count'),
            DB::raw('COUNT(*) as total_farm_products'),
            DB::raw('COUNT(CASE WHEN farm_practices IS NOT NULL AND JSON_LENGTH(farm_practices) > 0 THEN 1 END) as sustainable_practice_products_count'),
            DB::raw('AVG(freshness_days) as average_freshness'),
            DB::raw('COUNT(CASE WHEN certification IS NOT NULL THEN 1 END) as certified_products_count'),
        ])->first();

        // Calculate additional sustainability metrics
        $sustainabilityData->organic_percentage = $sustainabilityData->total_farm_products > 0 
            ? round(($sustainabilityData->organic_products_count / $sustainabilityData->total_farm_products) * 100, 2)
            : 0;

        $sustainabilityData->non_pesticide_percentage = $sustainabilityData->total_farm_products > 0
            ? round(($sustainabilityData->non_pesticide_products_count / $sustainabilityData->total_farm_products) * 100, 2)
            : 0;

        $sustainabilityData->sustainable_practice_percentage = $sustainabilityData->total_farm_products > 0
            ? round(($sustainabilityData->sustainable_practice_products_count / $sustainabilityData->total_farm_products) * 100, 2)
            : 0;

        $sustainabilityData->certified_percentage = $sustainabilityData->total_farm_products > 0
            ? round(($sustainabilityData->certified_products_count / $sustainabilityData->total_farm_products) * 100, 2)
            : 0;

        return response()->json([
            'success' => true,
            'data' => $sustainabilityData,
            'date_range' => [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
            ]
        ]);
    }

    /**
     * Get location-based farm report
     */
    public function getLocationReport(Request $request): JsonResponse
    {
        $request->validate([
            'start_date' => 'date|nullable',
            'end_date' => 'date|nullable',
        ]);

        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->subDays(30);
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now();

        $locationData = Ad::where('direct_from_farm', true)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select([
                'farm_location',
                DB::raw('COUNT(*) as product_count'),
                DB::raw('AVG(price) as average_price'),
                DB::raw('SUM(view_count) as total_views'),
                DB::raw('AVG(quality_rating) as average_quality'),
                DB::raw('AVG(sustainability_score) as average_sustainability'),
            ])
            ->groupBy('farm_location')
            ->orderByDesc('product_count')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $locationData,
            'date_range' => [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
            ]
        ]);
    }

    /**
     * Get freshness and quality metrics
     */
    public function getFreshnessQualityMetrics(Request $request): JsonResponse
    {
        $request->validate([
            'start_date' => 'date|nullable',
            'end_date' => 'date|nullable',
        ]);

        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->subDays(30);
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now();

        $metrics = Ad::where('direct_from_farm', true)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select([
                DB::raw('AVG(freshness_days) as average_freshness_days'),
                DB::raw('AVG(quality_rating) as average_quality_rating'),
                DB::raw('COUNT(CASE WHEN freshness_days <= 1 THEN 1 END) as products_less_than_1_day_old'),
                DB::raw('COUNT(CASE WHEN freshness_days <= 3 THEN 1 END) as products_less_than_3_days_old'),
                DB::raw('COUNT(CASE WHEN freshness_days <= 7 THEN 1 END) as products_less_than_7_days_old'),
                DB::raw('COUNT(*) as total_farm_products'),
                DB::raw('AVG(shelf_life) as average_shelf_life'),
                DB::raw('AVG(supply_capacity) as average_supply_capacity'),
            ])
            ->first();

        // Calculate freshness percentages
        $metrics->percentage_less_than_1_day_old = $metrics->total_farm_products > 0 
            ? round(($metrics->products_less_than_1_day_old / $metrics->total_farm_products) * 100, 2)
            : 0;

        $metrics->percentage_less_than_3_days_old = $metrics->total_farm_products > 0
            ? round(($metrics->products_less_than_3_days_old / $metrics->total_farm_products) * 100, 2)
            : 0;

        $metrics->percentage_less_than_7_days_old = $metrics->total_farm_products > 0
            ? round(($metrics->products_less_than_7_days_old / $metrics->total_farm_products) * 100, 2)
            : 0;

        return response()->json([
            'success' => true,
            'data' => $metrics,
            'date_range' => [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
            ]
        ]);
    }

    /**
     * Get demand forecasting for farm products
     */
    public function getDemandForecast(Request $request): JsonResponse
    {
        $request->validate([
            'start_date' => 'date|nullable',
            'end_date' => 'date|nullable',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->subDays(90);
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now();

        $query = Ad::where('direct_from_farm', true)
            ->whereBetween('created_at', [$startDate, $endDate]);

        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        $historicalData = $query->select([
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as product_count'),
            DB::raw('SUM(view_count) as total_views'),
            DB::raw('AVG(price) as average_price'),
        ])
        ->groupBy('date')
        ->orderBy('date')
        ->get();

        // Simple forecasting based on historical trends
        $totalViews = $historicalData->sum('total_views');
        $totalDays = $historicalData->count();
        $avgDailyViews = $totalDays > 0 ? $totalViews / $totalDays : 0;

        // Forecast next 7 days
        $forecast = [];
        for ($i = 1; $i <= 7; $i++) {
            $futureDate = Carbon::now()->addDays($i)->format('Y-m-d');
            $forecast[] = [
                'date' => $futureDate,
                'predicted_views' => round($avgDailyViews),
                'confidence_level' => 'medium',
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'historical_data' => $historicalData,
                'forecast' => $forecast,
                'average_daily_views' => round($avgDailyViews, 2),
            ],
            'date_range' => [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
            ]
        ]);
    }

    /**
     * Helper method to get daily performance
     */
    private function getDailyPerformance($userId, $startDate, $endDate)
    {
        return Ad::where('user_id', $userId)
            ->where('direct_from_farm', true)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select([
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as products_added'),
                DB::raw('SUM(view_count) as total_views'),
                DB::raw('AVG(price) as average_price'),
                DB::raw('AVG(quality_rating) as average_rating'),
            ])
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }
}