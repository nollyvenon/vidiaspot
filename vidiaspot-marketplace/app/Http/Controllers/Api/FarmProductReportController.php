<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FarmProductReportsController extends Controller
{
    /**
     * Get farm product performance summary
     */
    public function getPerformanceSummary(Request $request): JsonResponse
    {
        $request->validate([
            'start_date' => 'date|nullable',
            'end_date' => 'date|nullable',
            'user_id' => 'nullable|exists:users,id',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->subDays(30);
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now();

        $query = Ad::where('direct_from_farm', true)
            ->whereBetween('created_at', [$startDate, $endDate]);

        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        $stats = $query->select([
            DB::raw('COUNT(*) as total_products'),
            DB::raw('AVG(price) as average_price'),
            DB::raw('SUM(view_count) as total_views'),
            DB::raw('COUNT(CASE WHEN is_organic = 1 THEN 1 END) as organic_products'),
            DB::raw('COUNT(CASE WHEN direct_from_farm = 1 THEN 1 END) as direct_farm_products'),
            DB::raw('AVG(quality_rating) as average_quality_rating'),
            DB::raw('AVG(sustainability_score) as average_sustainability_score'),
        ])->first();

        return response()->json([
            'success' => true,
            'data' => $stats,
            'date_range' => [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
            ],
        ]);
    }

    /**
     * Get farm analytics with detailed insights
     */
    public function getDetailedAnalytics(Request $request): JsonResponse
    {
        $request->validate([
            'start_date' => 'date|nullable',
            'end_date' => 'date|nullable',
            'user_id' => 'nullable|exists:users,id',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->subDays(30);
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now();

        // Overall analytics
        $overallStats = Ad::where('direct_from_farm', true)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select([
                DB::raw('COUNT(*) as total_products'),
                DB::raw('AVG(price) as average_price'),
                DB::raw('SUM(view_count) as total_views'),
                DB::raw('COUNT(CASE WHEN is_organic = 1 THEN 1 END) as organic_products'),
                DB::raw('AVG(quality_rating) as average_quality_rating'),
                DB::raw('AVG(sustainability_score) as average_sustainability_score'),
            ])
            ->first();

        // Category performance
        $categoryPerformance = Ad::where('direct_from_farm', true)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->join('categories', 'ads.category_id', '=', 'categories.id')
            ->select([
                'categories.name as category_name',
                DB::raw('COUNT(*) as product_count'),
                DB::raw('AVG(price) as average_price'),
                DB::raw('SUM(view_count) as total_views'),
                DB::raw('AVG(quality_rating) as average_rating'),
            ])
            ->groupBy('category_id', 'categories.name')
            ->orderByDesc('product_count')
            ->get();

        // Seasonal performance
        $seasonalPerformance = Ad::where('direct_from_farm', true)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select([
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month_year"),
                DB::raw('COUNT(*) as product_count'),
                DB::raw('AVG(price) as average_price'),
                DB::raw('SUM(view_count) as total_views'),
                DB::raw('AVG(quality_rating) as average_rating'),
            ])
            ->groupBy(DB::raw("DATE_FORMAT(created_at, '%Y-%m')"))
            ->orderBy('month_year')
            ->get();

        // Organic vs Conventional performance
        $organicVsConventional = Ad::where('direct_from_farm', true)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select([
                'is_organic',
                DB::raw('COUNT(*) as product_count'),
                DB::raw('AVG(price) as average_price'),
                DB::raw('SUM(view_count) as total_views'),
                DB::raw('AVG(quality_rating) as average_rating'),
            ])
            ->groupBy('is_organic')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'overall_stats' => $overallStats,
                'category_performance' => $categoryPerformance,
                'seasonal_performance' => $seasonalPerformance,
                'organic_vs_conventional' => $organicVsConventional,
            ],
        ]);
    }

    /**
     * Get seasonal report for farm products
     */
    public function getSeasonalReport(Request $request): JsonResponse
    {
        $request->validate([
            'start_date' => 'date|nullable',
            'end_date' => 'date|nullable',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->subYear();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now();

        $seasonalData = Ad::where('direct_from_farm', true)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->when($request->category_id, function ($query) use ($request) {
                $query->where('category_id', $request->category_id);
            })
            ->select([
                DB::raw("
                    CASE 
                        WHEN MONTH(created_at) IN (10, 11, 12, 1, 2) THEN 'dry_season'
                        WHEN MONTH(created_at) IN (3, 4, 5, 6, 7, 8, 9) THEN 'wet_season'
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
        ]);
    }

    /**
     * Get sustainability report
     */
    public function getSustainabilityReport(Request $request): JsonResponse
    {
        $request->validate([
            'start_date' => 'date|nullable',
            'end_date' => 'date|nullable',
            'user_id' => 'nullable|exists:users,id',
        ]);

        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->subDays(30);
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now();

        $query = Ad::where('direct_from_farm', true)
            ->whereBetween('created_at', [$startDate, $endDate]);

        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        $sustainabilityData = $query->select([
            DB::raw('AVG(sustainability_score) as average_sustainability_score'),
            DB::raw('AVG(carbon_footprint) as average_carbon_footprint'),
            DB::raw('COUNT(CASE WHEN is_organic = 1 THEN 1 END) as organic_products_count'),
            DB::raw('COUNT(CASE WHEN certification IS NOT NULL THEN 1 END) as certified_products_count'),
            DB::raw('COUNT(*) as total_farm_products'),
            DB::raw('COUNT(CASE WHEN pesticide_use = 0 THEN 1 END) as non_pesticide_products_count'),
            DB::raw('AVG(freshness_days) as average_freshness'),
            DB::raw('AVG(shelf_life) as average_shelf_life'),
            DB::raw('COUNT(CASE WHEN farm_tour_available = 1 THEN 1 END) as farm_tour_available_count'),
            DB::raw('COUNT(CASE WHEN farm_practices IS NOT NULL THEN 1 END) as sustainable_practices_count'),
        ])->first();

        $sustainabilityData->organic_percentage = $sustainabilityData->total_farm_products > 0
            ? round(($sustainabilityData->organic_products_count / $sustainabilityData->total_farm_products) * 100, 2)
            : 0;

        $sustainabilityData->farm_tour_availability_rate = $sustainabilityData->total_farm_products > 0
            ? round(($sustainabilityData->farm_tour_available_count / $sustainabilityData->total_farm_products) * 100, 2)
            : 0;

        return response()->json([
            'success' => true,
            'data' => $sustainabilityData,
        ]);
    }

    /**
     * Get farmer productivity report
     */
    public function getFarmerProductivityReport(Request $request): JsonResponse
    {
        $request->validate([
            'start_date' => 'date|nullable',
            'end_date' => 'date|nullable',
            'farmer_id' => 'nullable|exists:users,id',
        ]);

        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->subDays(30);
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now();

        $query = Ad::with(['user', 'category'])
            ->where('direct_from_farm', true)
            ->whereBetween('created_at', [$startDate, $endDate]);

        if ($request->farmer_id) {
            $query->where('user_id', $request->farmer_id);
            $farmers = $query->get();
        } else {
            $farmers = $query->get()->groupBy('user_id')->map(function ($products, $userId) {
                $user = $products->first()->user;
                return [
                    'user_id' => $userId,
                    'user_name' => $user->name,
                    'farm_name' => $products->first()->farm_name ?? $user->name . "'s Farm",
                    'total_products' => $products->count(),
                    'total_revenue_potential' => $products->sum('price'),
                    'total_views' => $products->sum('view_count'),
                    'average_rating' => $products->avg('quality_rating'),
                    'organic_percentage' => $products->where('is_organic', true)->count() / max(1, $products->count()) * 100,
                    'average_sustainability_score' => $products->avg('sustainability_score'),
                ];
            })->sortByDesc('total_products')->values();
        }

        return response()->json([
            'success' => true,
            'data' => $farmers,
        ]);
    }

    /**
     * Get product comparison report
     */
    public function getProductComparison(Request $request): JsonResponse
    {
        $request->validate([
            'start_date' => 'date|nullable',
            'end_date' => 'date|nullable',
            'category_ids' => 'array',
            'category_ids.*' => 'exists:categories,id',
        ]);

        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->subDays(30);
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now();

        $query = Ad::where('direct_from_farm', true)
            ->whereBetween('created_at', [$startDate, $endDate]);

        if ($request->category_ids) {
            $query->whereIn('category_id', $request->category_ids);
        }

        $comparisonData = $query->join('categories', 'ads.category_id', '=', 'categories.id')
            ->select([
                'categories.name as category_name',
                DB::raw('COUNT(*) as product_count'),
                DB::raw('AVG(price) as average_price'),
                DB::raw('AVG(quality_rating) as average_quality_rating'),
                DB::raw('AVG(sustainability_score) as average_sustainability_score'),
                DB::raw('SUM(view_count) as total_views'),
                DB::raw('COUNT(CASE WHEN is_organic = 1 THEN 1 END) as organic_products'),
                DB::raw('AVG(freshness_days) as average_freshness'),
                DB::raw('AVG(shelf_life) as average_shelf_life'),
            ])
            ->groupBy('category_id', 'categories.name')
            ->orderByDesc('product_count')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $comparisonData,
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
            'region' => 'string|nullable',
        ]);

        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->subDays(30);
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now();

        $query = Ad::where('direct_from_farm', true)
            ->whereBetween('created_at', [$startDate, $endDate]);

        if ($request->region) {
            $query->where('location', 'like', '%' . $request->region . '%');
        }

        $locationData = $query->select([
            'location',
            'farm_location',
            DB::raw('COUNT(*) as product_count'),
            DB::raw('AVG(price) as average_price'),
            DB::raw('SUM(view_count) as total_views'),
            DB::raw('AVG(quality_rating) as average_quality'),
            DB::raw('AVG(sustainability_score) as average_sustainability_score'),
        ])
        ->groupBy('location', 'farm_location')
        ->orderBy('product_count', 'desc')
        ->get();

        return response()->json([
            'success' => true,
            'data' => $locationData,
        ]);
    }

    /**
     * Get demand forecast for farm products
     */
    public function getDemandForecast(Request $request): JsonResponse
    {
        $request->validate([
            'start_date' => 'date|nullable',
            'end_date' => 'date|nullable',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->subMonths(6);
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now();

        // Get historical data
        $historicalData = Ad::where('direct_from_farm', true)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->when($request->category_id, function ($query) use ($request) {
                $query->where('category_id', $request->category_id);
            })
            ->select([
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as product_count'),
                DB::raw('AVG(price) as average_price'),
                DB::raw('SUM(view_count) as total_views'),
            ])
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();

        // Calculate trends for forecasting
        $avgGrowth = 0;
        $previousViews = null;
        foreach ($historicalData as $index => $dataPoint) {
            if ($index > 0 && $previousViews !== null && $previousViews > 0) {
                $growth = (($dataPoint->total_views - $previousViews) / $previousViews) * 100;
                $avgGrowth += $growth;
            }
            $previousViews = $dataPoint->total_views;
        }

        // If we have data points, calculate average growth rate
        $avgGrowth = $historicalData->count() > 1 ? $avgGrowth / ($historicalData->count() - 1) : 0;

        // Calculate forecast for next 3 months
        $forecast = [];
        for ($i = 1; $i <= 3; $i++) {
            $forecastDate = Carbon::now()->addMonth($i);
            $forecast[] = [
                'month' => $forecastDate->format('F Y'),
                'predicted_demand' => round($previousViews * (1 + ($avgGrowth / 100))),
                'confidence_level' => 'moderate', // Would be calculated in a real implementation
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'historical_data' => $historicalData,
                'forecast' => $forecast,
                'average_growth_rate' => round($avgGrowth, 2),
            ],
        ]);
    }

    /**
     * Get seasonal demand analysis
     */
    public function getSeasonalDemandAnalysis(Request $request): JsonResponse
    {
        $request->validate([
            'start_date' => 'date|nullable',
            'end_date' => 'date|nullable',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->subYears(2);
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now();

        // Group by month to analyze seasonal patterns
        $seasonalAnalysis = Ad::where('direct_from_farm', true)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->when($request->category_id, function ($query) use ($request) {
                $query->where('category_id', $request->category_id);
            })
            ->select([
                DB::raw('MONTH(created_at) as month_number'),
                DB::raw('MONTHNAME(created_at) as month_name'),
                DB::raw('COUNT(*) as product_count'),
                DB::raw('AVG(price) as average_price'),
                DB::raw('SUM(view_count) as total_views'),
                DB::raw('AVG(quality_rating) as average_quality'),
            ])
            ->groupBy('month_number', 'month_name')
            ->orderBy('month_number')
            ->get();

        // Calculate seasonal averages
        $seasonalAverages = [
            'dry_season' => [
                'months' => ['October', 'November', 'December', 'January', 'February'],
                'avg_products' => 0,
                'avg_views' => 0,
            ],
            'wet_season' => [
                'months' => ['March', 'April', 'May', 'June', 'July', 'August', 'September'],
                'avg_products' => 0,
                'avg_views' => 0,
            ],
        ];

        foreach ($seasonalAverages as &$season) {
            $seasonData = $seasonalAnalysis->whereIn('month_name', $season['months']);
            if ($seasonData->count() > 0) {
                $season['avg_products'] = $seasonData->avg('product_count');
                $season['avg_views'] = $seasonData->avg('total_views');
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'monthly_data' => $seasonalAnalysis,
                'seasonal_averages' => $seasonalAverages,
            ],
        ]);
    }

    /**
     * Get organic certification compliance report
     */
    public function getOrganicCertificationCompliance(Request $request): JsonResponse
    {
        $request->validate([
            'start_date' => 'date|nullable',
            'end_date' => 'date|nullable',
            'certification_type' => 'string|nullable',
        ]);

        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->subDays(30);
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now();

        $query = Ad::where('direct_from_farm', true)
            ->where('is_organic', true)
            ->whereBetween('created_at', [$startDate, $endDate]);

        if ($request->certification_type) {
            $query->where('certification_type', $request->certification_type);
        }

        $complianceData = $query->select([
            'certification_type',
            'certification_body',
            DB::raw('COUNT(*) as product_count'),
            DB::raw('AVG(quality_rating) as average_quality'),
            DB::raw('AVG(sustainability_score) as average_sustainability'),
        ])
        ->groupBy('certification_type', 'certification_body')
        ->get();

        // Total organic products
        $totalOrganicProducts = Ad::where('direct_from_farm', true)
            ->where('is_organic', true)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        return response()->json([
            'success' => true,
            'data' => [
                'certification_breakdown' => $complianceData,
                'total_organic_products' => $totalOrganicProducts,
                'certification_compliance_rate' => $totalOrganicProducts > 0 
                    ? round(($complianceData->sum('product_count') / $totalOrganicProducts) * 100, 2)
                    : 0,
            ],
        ]);
    }
}