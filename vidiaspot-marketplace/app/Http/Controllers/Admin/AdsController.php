<?php

namespace App\Http\Controllers\Admin;

use App\Models\Ad;
use App\Models\User;
use App\Models\Category;
use App\Models\FeaturedAd;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class AdsController extends Controller
{
    /**
     * Display ads management page.
     */
    public function index(Request $request): View
    {
        $this->checkAdminAccess();

        $query = Ad::with(['user', 'category']);

        if ($request->filled('search')) {
            $query->where('title', 'LIKE', '%' . $request->search . '%')
                  ->orWhere('description', 'LIKE', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $ads = $query->latest()->paginate(25);

        $categories = Category::all();
        $users = User::all();

        return $this->adminView('admin.ads.index', [
            'ads' => $ads,
            'categories' => $categories,
            'users' => $users,
        ]);
    }

    /**
     * Display the specified ad.
     */
    public function show(Ad $ad): View
    {
        $this->checkAdminAccess();

        $ad->load(['user', 'category', 'images']);

        return $this->adminView('admin.ads.show', [
            'ad' => $ad,
        ]);
    }

    /**
     * Update ad status.
     */
    public function updateStatus(Request $request, Ad $ad): JsonResponse
    {
        $this->checkAdminAccess();

        $request->validate([
            'status' => 'required|in:active,inactive,sold,pending,rejected',
        ]);

        $ad->update([
            'status' => $request->status,
        ]);

        return response()->json([
            'message' => 'Ad status updated successfully',
            'ad' => $ad->refresh(),
        ]);
    }

    /**
     * Display featured ads management page.
     */
    public function featuredAds(Request $request): View
    {
        $this->checkAdminAccess();

        $query = FeaturedAd::with(['ad.user', 'ad.category']);

        if ($request->filled('search')) {
            $query->whereHas('ad', function($q) use ($request) {
                $q->where('title', 'LIKE', '%' . $request->search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $featuredAds = $query->latest()->paginate(25);

        return $this->adminView('admin.ads.featured', [
            'featuredAds' => $featuredAds,
        ]);
    }

    /**
     * Make an ad featured.
     */
    public function makeFeatured(Request $request, Ad $ad): JsonResponse
    {
        $this->checkAdminAccess();

        $request->validate([
            'duration_days' => 'required|integer|min:1',
            'start_date' => 'required|date',
        ]);

        // Check if there's already a featured ad for this ad
        $existing = FeaturedAd::where('ad_id', $ad->id)->first();
        if ($existing) {
            return response()->json([
                'error' => 'This ad is already featured',
            ], 400);
        }

        $featuredAd = FeaturedAd::create([
            'ad_id' => $ad->id,
            'start_date' => $request->start_date,
            'end_date' => now()->addDays($request->duration_days),
            'status' => 'active',
            'payment_id' => null, // Will be set when payment is confirmed
        ]);

        return response()->json([
            'message' => 'Ad featured successfully',
            'featured_ad' => $featuredAd,
        ]);
    }

    /**
     * Remove featured status from an ad.
     */
    public function removeFeatured(FeaturedAd $featuredAd): JsonResponse
    {
        $this->checkAdminAccess();

        $featuredAd->delete();

        return response()->json([
            'message' => 'Featured ad removed successfully',
        ]);
    }

    /**
     * Display premium ads management page.
     */
    public function premiumAds(Request $request): View
    {
        $this->checkAdminAccess();

        $query = \App\Models\PremiumAd::with(['ad.user', 'ad.category']);

        if ($request->filled('search')) {
            $query->whereHas('ad', function($q) use ($request) {
                $q->where('title', 'LIKE', '%' . $request->search . '%');
            });
        }

        $premiumAds = $query->latest()->paginate(25);

        return $this->adminView('admin.ads.premium', [
            'premiumAds' => $premiumAds,
        ]);
    }
}