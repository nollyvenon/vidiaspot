<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\PremiumAd;
use App\Models\Ad;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class PremiumAdsController extends Controller
{
    /**
     * Get all premium ads for admin management.
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user || (!$user->hasRole('admin') && $user->email !== config('app.admin_email', 'admin@vidiaspot.com'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $query = PremiumAd::with(['ad', 'user', 'payment']);

        // Filter by status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Filter by ad type
        if ($request->ad_type) {
            $query->where('ad_type', $request->ad_type);
        }

        // Filter by ad
        if ($request->ad_id) {
            $query->where('ad_id', $request->ad_id);
        }

        // Filter by user
        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by date range
        if ($request->date_from) {
            $query->where('start_date', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->where('end_date', '<=', $request->date_to);
        }

        // Search
        if ($request->search) {
            $query->whereHas('ad', function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $premiumAds = $query->orderBy('created_at', 'desc')->paginate($request->per_page ?? 15);

        return response()->json([
            'success' => true,
            'data' => $premiumAds,
            'message' => 'Premium ads list for admin management'
        ]);
    }

    /**
     * Store a new premium ad campaign.
     */
    public function store(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user || (!$user->hasRole('admin') && $user->email !== config('app.admin_email', 'admin@vidiaspot.com'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'ad_id' => 'required|exists:ads,id',
            'ad_type' => 'required|in:featured,promoted,top,urgent,premium',
            'campaign_name' => 'required|string|max:255',
            'budget' => 'required|numeric|min:0',
            'currency_code' => 'required|string|size:3|exists:currencies,code',
            'targeting_settings' => 'nullable|array',
            'impressions_goal' => 'nullable|integer',
            'clicks_goal' => 'nullable|integer',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'daily_budget' => 'nullable|numeric|min:0',
            'placement_settings' => 'nullable|array',
        ]);

        $ad = Ad::findOrFail($request->ad_id);

        $premiumAd = PremiumAd::create([
            'ad_id' => $request->ad_id,
            'user_id' => $ad->user_id, // Use the ad owner's user ID
            'campaign_name' => $request->campaign_name,
            'ad_type' => $request->ad_type,
            'budget' => $request->budget,
            'currency_code' => $request->currency_code,
            'targeting_settings' => $request->targeting_settings,
            'impressions_goal' => $request->impressions_goal,
            'clicks_goal' => $request->clicks_goal,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'daily_budget' => $request->daily_budget,
            'placement_settings' => $request->placement_settings,
            'status' => 'pending',
            'payment_id' => null, // Will be linked when payment is processed
        ]);

        return response()->json([
            'success' => true,
            'data' => $premiumAd->refresh(),
            'message' => 'Premium ad campaign created successfully'
        ], 201);
    }

    /**
     * Update a premium ad campaign.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $user = Auth::user();

        if (!$user || (!$user->hasRole('admin') && $user->email !== config('app.admin_email', 'admin@vidiaspot.com'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'campaign_name' => 'sometimes|string|max:255',
            'ad_type' => 'sometimes|in:featured,promoted,top,urgent,premium',
            'budget' => 'sometimes|numeric|min:0',
            'currency_code' => 'sometimes|string|size:3|exists:currencies,code',
            'targeting_settings' => 'nullable|array',
            'impressions_goal' => 'nullable|integer',
            'clicks_goal' => 'nullable|integer',
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date|after:start_date',
            'daily_budget' => 'nullable|numeric|min:0',
            'placement_settings' => 'nullable|array',
            'status' => 'in:pending,active,completed,pending,paused,cancelled',
        ]);

        $premiumAd = PremiumAd::findOrFail($id);
        $premiumAd->update($request->all());

        return response()->json([
            'success' => true,
            'data' => $premiumAd->refresh(),
            'message' => 'Premium ad campaign updated successfully'
        ]);
    }

    /**
     * Activate a premium ad campaign.
     */
    public function activate(string $id): JsonResponse
    {
        $user = Auth::user();

        if (!$user || (!$user->hasRole('admin') && $user->email !== config('app.admin_email', 'admin@vidiaspot.com'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $premiumAd = PremiumAd::findOrFail($id);
        $premiumAd->update(['status' => 'active']);

        return response()->json([
            'success' => true,
            'data' => $premiumAd->refresh(),
            'message' => 'Premium ad campaign activated successfully'
        ]);
    }

    /**
     * Pause a premium ad campaign.
     */
    public function pause(string $id): JsonResponse
    {
        $user = Auth::user();

        if (!$user || (!$user->hasRole('admin') && $user->email !== config('app.admin_email', 'admin@vidiaspot.com'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $premiumAd = PremiumAd::findOrFail($id);
        $premiumAd->update(['status' => 'paused']);

        return response()->json([
            'success' => true,
            'data' => $premiumAd->refresh(),
            'message' => 'Premium ad campaign paused successfully'
        ]);
    }

    /**
     * Cancel a premium ad campaign.
     */
    public function cancel(string $id): JsonResponse
    {
        $user = Auth::user();

        if (!$user || (!$user->hasRole('admin') && $user->email !== config('app.admin_email', 'admin@vidiaspot.com'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $premiumAd = PremiumAd::findOrFail($id);
        $premiumAd->update(['status' => 'cancelled']);

        return response()->json([
            'success' => true,
            'data' => $premiumAd->refresh(),
            'message' => 'Premium ad campaign cancelled successfully'
        ]);
    }

    /**
     * Get premium ads statistics.
     */
    public function stats(): JsonResponse
    {
        $user = Auth::user();

        if (!$user || (!$user->hasRole('admin') && $user->email !== config('app.admin_email', 'admin@vidiaspot.com'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $stats = [
            'total_premium_ads' => PremiumAd::count(),
            'active_campaigns' => PremiumAd::where('status', 'active')
                ->where('start_date', '<=', now())
                ->where('end_date', '>', now())
                ->count(),
            'completed_campaigns' => PremiumAd::where('status', 'completed')->count(),
            'pending_campaigns' => PremiumAd::where('status', 'pending')->count(),
            'total_spent' => PremiumAd::sum('spent_amount'),
            'total_budget' => PremiumAd::sum('budget'),
            'premium_ads_by_type' => PremiumAd::selectRaw('ad_type, COUNT(*) as count')
                ->groupBy('ad_type')
                ->pluck('count', 'ad_type'),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
            'message' => 'Premium ads statistics'
        ]);
    }

    /**
     * Delete a premium ad campaign.
     */
    public function destroy(string $id): JsonResponse
    {
        $user = Auth::user();

        if (!$user || (!$user->hasRole('admin') && $user->email !== config('app.admin_email', 'admin@vidiaspot.com'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $premiumAd = PremiumAd::findOrFail($id);
        $premiumAd->delete();

        return response()->json([
            'success' => true,
            'message' => 'Premium ad campaign deleted successfully'
        ]);
    }
}
