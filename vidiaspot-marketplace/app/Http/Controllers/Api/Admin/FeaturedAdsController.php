<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeaturedAd;
use App\Models\Ad;
use App\Models\User;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class FeaturedAdsController extends Controller
{
    /**
     * Get all featured ads for admin management.
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user || (!$user->hasRole('admin') && $user->email !== config('app.admin_email', 'admin@vidiaspot.com'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $query = FeaturedAd::with(['ad', 'user', 'payment']);

        // Filter by status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Filter by type
        if ($request->type) {
            $query->where('type', $request->type);
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
            $query->where('starts_at', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->where('expires_at', '<=', $request->date_to);
        }

        // Search
        if ($request->search) {
            $query->whereHas('ad', function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $featuredAds = $query->orderBy('created_at', 'desc')->paginate($request->per_page ?? 15);

        return response()->json([
            'success' => true,
            'data' => $featuredAds,
            'message' => 'Featured ads list for admin management'
        ]);
    }

    /**
     * Store a new featured ad (admin can feature ads manually).
     */
    public function store(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user || (!$user->hasRole('admin') && $user->email !== config('app.admin_email', 'admin@vidiaspot.com'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'ad_id' => 'required|exists:ads,id',
            'type' => 'required|in:premium,highlighted,top,urgent',
            'cost' => 'required|numeric|min:0',
            'currency_code' => 'required|string|size:3|exists:currencies,code',
            'starts_at' => 'required|date',
            'expires_at' => 'required|date|after:starts_at',
            'status' => 'in:active,pending,expired,cancelled',
        ]);

        $ad = Ad::findOrFail($request->ad_id);

        $featuredAd = FeaturedAd::create([
            'ad_id' => $request->ad_id,
            'user_id' => $ad->user_id, // Use the ad owner's user ID
            'type' => $request->type,
            'cost' => $request->cost,
            'currency_code' => $request->currency_code,
            'starts_at' => $request->starts_at,
            'expires_at' => $request->expires_at,
            'status' => $request->status ?? 'active',
        ]);

        return response()->json([
            'success' => true,
            'data' => $featuredAd->refresh(),
            'message' => 'Featured ad created successfully'
        ], 201);
    }

    /**
     * Update a featured ad.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $user = Auth::user();

        if (!$user || (!$user->hasRole('admin') && $user->email !== config('app.admin_email', 'admin@vidiaspot.com'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'type' => 'sometimes|in:premium,highlighted,top,urgent',
            'cost' => 'sometimes|numeric|min:0',
            'currency_code' => 'sometimes|string|size:3|exists:currencies,code',
            'starts_at' => 'sometimes|date',
            'expires_at' => 'sometimes|date|after:starts_at',
            'status' => 'in:active,pending,expired,cancelled',
        ]);

        $featuredAd = FeaturedAd::findOrFail($id);
        $featuredAd->update($request->all());

        return response()->json([
            'success' => true,
            'data' => $featuredAd->refresh(),
            'message' => 'Featured ad updated successfully'
        ]);
    }

    /**
     * Cancel a featured ad.
     */
    public function cancel(string $id): JsonResponse
    {
        $user = Auth::user();

        if (!$user || (!$user->hasRole('admin') && $user->email !== config('app.admin_email', 'admin@vidiaspot.com'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $featuredAd = FeaturedAd::findOrFail($id);
        $featuredAd->update(['status' => 'cancelled']);

        return response()->json([
            'success' => true,
            'data' => $featuredAd->refresh(),
            'message' => 'Featured ad cancelled successfully'
        ]);
    }

    /**
     * Extend a featured ad duration.
     */
    public function extend(Request $request, string $id): JsonResponse
    {
        $user = Auth::user();

        if (!$user || (!$user->hasRole('admin') && $user->email !== config('app.admin_email', 'admin@vidiaspot.com'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'additional_days' => 'required|integer|min:1',
        ]);

        $featuredAd = FeaturedAd::findOrFail($id);
        $newExpiry = $featuredAd->expires_at->addDays($request->additional_days);

        $featuredAd->update(['expires_at' => $newExpiry]);

        return response()->json([
            'success' => true,
            'data' => $featuredAd->refresh(),
            'message' => 'Featured ad duration extended successfully'
        ]);
    }

    /**
     * Get featured ads statistics.
     */
    public function stats(): JsonResponse
    {
        $user = Auth::user();

        if (!$user || (!$user->hasRole('admin') && $user->email !== config('app.admin_email', 'admin@vidiaspot.com'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $stats = [
            'total_featured_ads' => FeaturedAd::count(),
            'active_featured_ads' => FeaturedAd::where('status', 'active')
                ->where('expires_at', '>', now())->count(),
            'pending_featured_ads' => FeaturedAd::where('status', 'pending')->count(),
            'expired_featured_ads' => FeaturedAd::where('expires_at', '<', now())->count(),
            'featured_ads_by_type' => FeaturedAd::selectRaw('type, COUNT(*) as count')
                ->groupBy('type')
                ->pluck('count', 'type'),
            'revenue_from_featured' => FeaturedAd::where('status', 'active')->sum('cost'),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
            'message' => 'Featured ads statistics'
        ]);
    }

    /**
     * Delete a featured ad.
     */
    public function destroy(string $id): JsonResponse
    {
        $user = Auth::user();

        if (!$user || (!$user->hasRole('admin') && $user->email !== config('app.admin_email', 'admin@vidiaspot.com'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $featuredAd = FeaturedAd::findOrFail($id);
        $featuredAd->delete();

        return response()->json([
            'success' => true,
            'message' => 'Featured ad deleted successfully'
        ]);
    }
}
