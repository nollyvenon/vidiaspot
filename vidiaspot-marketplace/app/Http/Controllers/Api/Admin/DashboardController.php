<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use App\Models\User;
use App\Models\Message;
use App\Models\Category;
use App\Services\RecommendationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    protected $recommendationService;

    public function __construct(RecommendationService $recommendationService)
    {
        $this->recommendationService = $recommendationService;
    }

    /**
     * Get admin dashboard statistics.
     */
    public function dashboard(): JsonResponse
    {
        $user = Auth::user();

        if (!$user || (!$user->hasRole('admin') && $user->email !== config('app.admin_email', 'admin@vidiaspot.com'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $stats = [
            'total_users' => User::count(),
            'total_ads' => Ad::count(),
            'active_ads' => Ad::where('status', 'active')->count(),
            'total_categories' => Category::count(),
            'total_messages' => Message::count(),
            'recent_ads' => Ad::with(['user', 'category'])->latest()->limit(10)->get(),
            'recent_users' => User::latest()->limit(10)->get(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
            'message' => 'Admin dashboard statistics'
        ]);
    }

    /**
     * Get system analytics and insights.
     */
    public function analytics(): JsonResponse
    {
        $user = Auth::user();

        if (!$user || (!$user->hasRole('admin') && $user->email !== config('app.admin_email', 'admin@vidiaspot.com'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Calculate various analytics
        $analytics = [
            'ads_by_category' => Ad::selectRaw('category_id, COUNT(*) as count')
                ->groupBy('category_id')
                ->with(['category:id,name'])
                ->get()
                ->map(function ($item) {
                    return [
                        'category' => $item->category ? $item->category->name : 'Unknown',
                        'count' => $item->count
                    ];
                }),
            'ads_by_status' => Ad::selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status'),
            'ads_by_currency' => Ad::selectRaw('currency_code, COUNT(*) as count')
                ->groupBy('currency_code')
                ->pluck('count', 'currency_code'),
            'users_by_month' => User::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
                ->groupBy('month')
                ->orderBy('month', 'desc')
                ->limit(12)
                ->pluck('count', 'month'),
        ];

        return response()->json([
            'success' => true,
            'data' => $analytics,
            'message' => 'System analytics and insights'
        ]);
    }
}
