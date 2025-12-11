<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Ad;
use App\Models\ECommerce\Category;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FarmProductReportController extends Controller
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
     * Get farmer productivity report
     */
    public function getFarmerProductivityReport(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'start_date' => 'date|nullable',
            'end_date' => 'date|nullable',
        ]);

        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->subDays(30);
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now();

        $user = User::findOrFail($request->user_id);

        $farmProducts = Ad::where('user_id', $request->user_id)
            ->where('direct_from_farm', true)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with('category')
            ->get();

        $productivityData = [
            'farmer_info' => [
                'name' => $user->name,
                'farm_name' => $farmProducts->first()?->farm_name ?? $user->name . "'s Farm",
                'total_products_listed' => $farmProducts->count(),
                'total_product_views' => $farmProducts->sum('view_count'),
                'average_price' => $farmProducts->avg('price'),
                'organic_ratio' => $farmProducts->count() > 0
                    ? round(($farmProducts->where('is_organic', true)->count() / $farmProducts->count()) * 100, 2)
                    : 0,
            ],
            'products_by_category' => $farmProducts->groupBy('category.name')->map(function($products, $category) {
                return [
                    'category' => $category,
                    'count' => $products->count(),
                    'total_revenue_potential' => $products->sum('price'),
                    'average_rating' => $products->avg('quality_rating'),
                ];
            })->values(),
            'monthly_breakdown' => $this->getMonthlyBreakdown($request->user_id, $startDate, $endDate),
            'sustainability_metrics' => [
                'average_sustainability_score' => $farmProducts->avg('sustainability_score'),
                'carbon_footprint_total' => $farmProducts->sum('carbon_footprint'),
                'organic_certification_rate' => $farmProducts->where('is_organic', true)->count() / max(1, $farmProducts->count()) * 100,
            ],
        ];

        return response()->json([
            'success' => true,
            'data' => $productivityData,
        ]);
    }

    /**
     * Get monthly breakdown helper method
     */
    private function getMonthlyBreakdown($userId, $startDate, $endDate)
    {
        $monthlyData = Ad::where('user_id', $userId)
            ->where('direct_from_farm', true)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select([
                DB::raw('MONTH(created_at) as month'),
                DB::raw('YEAR(created_at) as year'),
                DB::raw('COUNT(*) as products_count'),
                DB::raw('SUM(view_count) as total_views'),
                DB::raw('AVG(price) as average_price'),
                DB::raw('AVG(quality_rating) as average_rating'),
            ])
            ->groupBy(DB::raw('YEAR(created_at)'), DB::raw('MONTH(created_at)'))
            ->orderBy(DB::raw('year DESC'), DB::raw('month DESC'))
            ->get();

        return $monthlyData->map(function($data) {
            $monthName = Carbon::createFromDate($data->year, $data->month, 1)->format('F Y');
            return [
                'month' => $monthName,
                'products_count' => $data->products_count,
                'total_views' => $data->total_views,
                'average_price' => $data->average_price,
                'average_rating' => $data->average_rating,
            ];
        })->toArray();
    }

    /**
     * Get seasonal performance report
     */
    public function getSeasonalReport(Request $request): JsonResponse
    {
        $request->validate([
            'start_date' => 'date|nullable',
            'end_date' => 'date|nullable',
        ]);

        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->subYear();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now();

        $seasonalData = Ad::where('direct_from_farm', true)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select([
                DB::raw("
                    CASE 
                        WHEN MONTH(created_at) IN (10, 11, 12, 1, 2) THEN 'Dry Season'
                        WHEN MONTH(created_at) IN (3, 4, 5, 6, 7, 8, 9) THEN 'Wet Season'
                        ELSE 'Other'
                    END as season
                "),
                DB::raw('COUNT(*) as product_count'),
                DB::raw('AVG(price) as average_price'),
                DB::raw('SUM(view_count) as total_views'),
                DB::raw('AVG(quality_rating) as average_quality'),
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
        ])->first();

        $sustainabilityData->organic_percentage = $sustainabilityData->total_farm_products > 0 
            ? round(($sustainabilityData->organic_products_count / $sustainabilityData->total_farm_products) * 100, 2)
            : 0;

        return response()->json([
            'success' => true,
            'data' => $sustainabilityData,
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
            'category_ids' => 'array|nullable',
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
                DB::raw('SUM(view_count) as total_views'),
                DB::raw('AVG(quality_rating) as average_rating'),
                DB::raw('AVG(sustainability_score) as average_sustainability_score'),
                DB::raw('COUNT(CASE WHEN is_organic = 1 THEN 1 END) as organic_products'),
                DB::raw('AVG(freshness_days) as average_freshness'),
            ])
            ->groupBy('category_id', 'categories.name')
            ->orderBy('product_count', 'desc')
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
        ]);

        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->subDays(30);
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now();

        $locationData = Ad::where('direct_from_farm', true)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select([
                'location',
                'farm_location',
                DB::raw('COUNT(*) as product_count'),
                DB::raw('AVG(price) as average_price'),
                DB::raw('SUM(view_count) as total_views'),
                DB::raw('AVG(quality_rating) as average_quality'),
            ])
            ->groupBy('location', 'farm_location')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $locationData,
        ]);
    }

    /**
     * Get top performing farm products
     */
    public function getTopPerformingProducts(Request $request): JsonResponse
    {
        $request->validate([
            'start_date' => 'date|nullable',
            'end_date' => 'date|nullable',
            'limit' => 'integer|min:1|max:100|nullable',
        ]);

        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->subDays(30);
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now();
        $limit = $request->limit ?? 10;

        $topProducts = Ad::where('direct_from_farm', true)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'active')
            ->select([
                'id',
                'title',
                'price',
                'location',
                'view_count',
                'user_id',
                'category_id',
                'quality_rating',
                'is_organic',
                DB::raw('(view_count * price) as popularity_score'),
            ])
            ->orderByDesc('popularity_score')
            ->limit($limit)
            ->with(['user:id,name', 'category:id,name'])
            ->get();

        return response()->json([
            'success' => true,
            'data' => $topProducts,
        ]);
    }

    /**
     * Get farm productivity trends
     */
    public function getProductivityTrends(Request $request): JsonResponse
    {
        $request->validate([
            'start_date' => 'date|nullable',
            'end_date' => 'date|nullable',
        ]);

        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->subDays(90);
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now();

        // Get daily data for the past month
        $dailyStats = Ad::where('direct_from_farm', true)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select([
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as products_added'),
                DB::raw('AVG(price) as average_price'),
                DB::raw('SUM(view_count) as total_views'),
                DB::raw('AVG(quality_rating) as average_quality'),
            ])
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'daily_stats' => $dailyStats,
                'summary' => [
                    'total_products_added' => $dailyStats->sum('products_added'),
                    'average_daily_additions' => $dailyStats->avg('products_added'),
                    'total_views' => $dailyStats->sum('total_views'),
                    'average_price' => $dailyStats->avg('average_price'),
                ],
            ],
        ]);
    }

    /**
     * Helper method to get monthly breakdown data
     */
    private function getMonthlyBreakdown($userId, $startDate, $endDate)
    {
        $monthlyData = Ad::where('user_id', $userId)
            ->where('direct_from_farm', true)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select([
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as products_count'),
                DB::raw('SUM(view_count) as total_views'),
                DB::raw('AVG(price) as average_price'),
                DB::raw('AVG(quality_rating) as average_rating'),
            ])
            ->groupBy(DB::raw('YEAR(created_at)'), DB::raw('MONTH(created_at)'))
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        $formattedData = [];
        foreach ($monthlyData as $data) {
            $formattedData[] = [
                'period' => Carbon::createFromDate($data->year, $data->month, 1)->format('F Y'),
                'products_count' => $data->products_count,
                'total_views' => $data->total_views,
                'average_price' => $data->average_price,
                'average_rating' => $data->average_rating,
            ];
        }

        return $formattedData;
    }

    /**
     * Get product comparison report
     */
    public function getProductComparison(Request $request): JsonResponse
    {
        $request->validate([
            'start_date' => 'date|nullable',
            'end_date' => 'date|nullable',
            'category_ids' => 'array|nullable',
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
            $query->where('farm_location', 'like', '%' . $request->region . '%');
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
        ->limit(10)
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

        $query = Ad::where('direct_from_farm', true)
            ->whereBetween('created_at', [$startDate, $endDate]);

        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        // Get historical data
        $historicalData = $query->select([
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as product_count'),
            DB::raw('AVG(price) as average_price'),
            DB::raw('SUM(view_count) as total_views'),
        ])
        ->groupBy(DB::raw('DATE(created_at)'))
        ->orderBy('date')
        ->get();

        // Simple forecasting algorithm based on historical trends
        $totalViews = $historicalData->sum('total_views');
        $totalDays = $historicalData->count();
        $avgDailyViews = $totalDays > 0 ? $totalViews / $totalDays : 0;

        // Calculate trend direction (simplified)
        $trendDirection = 'stable';
        if ($totalDays >= 2) {
            $recentViews = $historicalData->slice(-7)->sum('total_views'); // Last 7 days
            $previousViews = $historicalData->slice(-14, 7)->sum('total_views'); // Previous 7 days
            $weeklyGrowth = $previousViews > 0 ? (($recentViews - $previousViews) / $previousViews) * 100 : 0;

            if ($weeklyGrowth > 5) {
                $trendDirection = 'increasing';
            } elseif ($weeklyGrowth < -5) {
                $trendDirection = 'decreasing';
            }
        }

        // Forecast next 7 days
        $forecast = [];
        for ($i = 1; $i <= 7; $i++) {
            $futureDate = Carbon::now()->addDays($i)->format('Y-m-d');

            // Adjust forecast based on trend
            $adjustment = 1.0;
            if ($trendDirection === 'increasing') {
                $adjustment = 1.05; // 5% growth
            } elseif ($trendDirection === 'decreasing') {
                $adjustment = 0.95; // 5% decline
            }

            $forecast[] = [
                'date' => $futureDate,
                'predicted_views' => round($avgDailyViews * $adjustment),
                'confidence_level' => 'medium',
                'growth_trend' => $trendDirection,
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'historical_data' => $historicalData,
                'forecast' => $forecast,
                'average_daily_views' => round($avgDailyViews, 2),
                'trend_direction' => $trendDirection,
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

        // Group by month and season to analyze seasonal patterns
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
                DB::raw('AVG(quality_rating) as average_rating'),
            ])
            ->groupBy(DB::raw('MONTH(created_at)'), DB::raw('MONTHNAME(created_at)'))
            ->orderBy('month_number')
            ->get();

        // Calculate seasonal averages (based on Nigerian seasons)
        $seasonalAverages = [
            'rainy_season' => [  // April - October
                'months' => [4, 5, 6, 7, 8, 9, 10],
                'month_names' => ['April', 'May', 'June', 'July', 'August', 'September', 'October'],
                'avg_products' => 0,
                'avg_views' => 0,
            ],
            'dry_season' => [   // November - March
                'months' => [11, 12, 1, 2, 3],
                'month_names' => ['November', 'December', 'January', 'February', 'March'],
                'avg_products' => 0,
                'avg_views' => 0,
            ],
        ];

        foreach ($seasonalAverages as &$season) {
            $seasonData = $seasonalAnalysis->whereIn('month_number', $season['months']);
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
            'certification',
        ])
        ->groupBy('certification_type', 'certification_body', 'certification')
        ->get();

        // Total organic products
        $totalOrganicProducts = Ad::where('direct_from_farm', true)
            ->where('is_organic', true)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        // Certified organic products (with certification info)
        $certifiedProducts = Ad::where('direct_from_farm', true)
            ->where('is_organic', true)
            ->whereNotNull('certification')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        return response()->json([
            'success' => true,
            'data' => [
                'certification_breakdown' => $complianceData,
                'total_organic_products' => $totalOrganicProducts,
                'certified_organic_products' => $certifiedProducts,
                'certification_compliance_rate' => $totalOrganicProducts > 0
                    ? round(($certifiedProducts / $totalOrganicProducts) * 100, 2)
                    : 0,
            ],
        ]);
    }
}