<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Ad;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FarmProductReportsController extends Controller
{
    /**
     * Get farm product performance summary
     */
    public function performanceSummary(Request $request): JsonResponse
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
    public function farmerProductivity(Request $request): JsonResponse
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
                'farm_name' => $farmProducts->first()?->farm_name ?: $user->name . "'s Farm",
                'total_products_listed' => $farmProducts->count(),
                'total_product_views' => $farmProducts->sum('view_count'),
                'average_price' => $farmProducts->avg('price'),
                'organic_ratio' => $farmProducts->count() > 0 ? round(($farmProducts->where('is_organic', true)->count() / $farmProducts->count()) * 100, 2) : 0,
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
     * Get seasonal performance report
     */
    public function seasonalPerformance(Request $request): JsonResponse
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
                DB::raw("CASE
                    WHEN MONTH(created_at) IN (10, 11, 12, 1, 2) THEN 'Dry Season'
                    WHEN MONTH(created_at) IN (3, 4, 5, 6, 7, 8, 9) THEN 'Wet Season'
                    ELSE 'Other'
                END as season"),
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
    public function sustainabilityReport(Request $request): JsonResponse
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

        $organicPercentage = $sustainabilityData->total_farm_products > 0
            ? round(($sustainabilityData->organic_products_count / $sustainabilityData->total_farm_products) * 100, 2)
            : 0;

        $sustainabilityData->organic_percentage = $organicPercentage;

        return response()->json([
            'success' => true,
            'data' => $sustainabilityData,
        ]);
    }

    /**
     * Get location-based farm report
     */
    public function locationReport(Request $request): JsonResponse
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
    public function topPerformingProducts(Request $request): JsonResponse
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
     * Get farm productivity trends over time
     */
    public function productivityTrends(Request $request): JsonResponse
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
     * Get monthly breakdown for farmer productivity
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

        // Format the data for the response
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
}