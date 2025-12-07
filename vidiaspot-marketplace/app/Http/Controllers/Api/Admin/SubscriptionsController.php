<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class SubscriptionsController extends Controller
{
    /**
     * Get all subscriptions for admin management.
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user || (!$user->hasRole('admin') && $user->email !== config('app.admin_email', 'admin@vidiaspot.com'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $query = Subscription::query();

        // Filter by active status
        if ($request->is_active !== null) {
            $query->where('is_active', $request->is_active);
        }

        // Filter by featured status
        if ($request->is_featured !== null) {
            $query->where('is_featured', $request->is_featured);
        }

        // Search
        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
        }

        $subscriptions = $query->orderBy('price', 'asc')->paginate($request->per_page ?? 15);

        return response()->json([
            'success' => true,
            'data' => $subscriptions,
            'message' => 'Subscriptions list for admin management'
        ]);
    }

    /**
     * Store a new subscription plan.
     */
    public function store(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user || (!$user->hasRole('admin') && $user->email !== config('app.admin_email', 'admin@vidiaspot.com'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:subscriptions,name',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'currency_code' => 'required|string|size:3|exists:currencies,code',
            'billing_cycle' => 'required|in:monthly,yearly,quarterly',
            'duration_days' => 'required|integer|min:1',
            'features' => 'nullable|array',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'ad_limit' => 'required|integer|min:0',
            'featured_ads_limit' => 'required|integer|min:0',
            'has_priority_support' => 'boolean',
        ]);

        $subscription = Subscription::create($request->all());

        return response()->json([
            'success' => true,
            'data' => $subscription,
            'message' => 'Subscription plan created successfully'
        ], 201);
    }

    /**
     * Update a subscription plan.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $user = Auth::user();

        if (!$user || (!$user->hasRole('admin') && $user->email !== config('app.admin_email', 'admin@vidiaspot.com'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'name' => 'sometimes|string|max:255|unique:subscriptions,name,' . $id,
            'description' => 'nullable|string',
            'price' => 'sometimes|numeric|min:0',
            'currency_code' => 'sometimes|string|size:3|exists:currencies,code',
            'billing_cycle' => 'sometimes|in:monthly,yearly,quarterly',
            'duration_days' => 'sometimes|integer|min:1',
            'features' => 'nullable|array',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'ad_limit' => 'sometimes|integer|min:0',
            'featured_ads_limit' => 'sometimes|integer|min:0',
            'has_priority_support' => 'boolean',
        ]);

        $subscription = Subscription::findOrFail($id);
        $subscription->update($request->all());

        return response()->json([
            'success' => true,
            'data' => $subscription->refresh(),
            'message' => 'Subscription plan updated successfully'
        ]);
    }

    /**
     * Delete a subscription plan.
     */
    public function destroy(string $id): JsonResponse
    {
        $user = Auth::user();

        if (!$user || (!$user->hasRole('admin') && $user->email !== config('app.admin_email', 'admin@vidiaspot.com'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $subscription = Subscription::findOrFail($id);

        // Check if subscription is in use
        if ($subscription->payments()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete subscription that has active payments'
            ], 422);
        }

        $subscription->delete();

        return response()->json([
            'success' => true,
            'message' => 'Subscription plan deleted successfully'
        ]);
    }

    /**
     * Toggle subscription status.
     */
    public function toggleStatus(string $id): JsonResponse
    {
        $user = Auth::user();

        if (!$user || (!$user->hasRole('admin') && $user->email !== config('app.admin_email', 'admin@vidiaspot.com'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $subscription = Subscription::findOrFail($id);
        $subscription->update(['is_active' => !$subscription->is_active]);

        return response()->json([
            'success' => true,
            'data' => $subscription->refresh(),
            'message' => 'Subscription status updated successfully'
        ]);
    }

    /**
     * Get subscription statistics.
     */
    public function stats(): JsonResponse
    {
        $user = Auth::user();

        if (!$user || (!$user->hasRole('admin') && $user->email !== config('app.admin_email', 'admin@vidiaspot.com'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $stats = [
            'total_subscriptions' => Subscription::count(),
            'active_subscriptions' => Subscription::where('is_active', true)->count(),
            'featured_subscriptions' => Subscription::where('is_featured', true)->count(),
            'subscriptions_by_cycle' => Subscription::selectRaw('billing_cycle, COUNT(*) as count')
                ->groupBy('billing_cycle')
                ->pluck('count', 'billing_cycle'),
            'revenue_by_subscriptions' => Subscription::withSum('payments as revenue', 'amount')
                ->get()
                ->map(function($subscription) {
                    return [
                        'name' => $subscription->name,
                        'revenue' => $subscription->revenue ?? 0
                    ];
                }),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
            'message' => 'Subscription statistics'
        ]);
    }
}
