<?php

namespace App\Http\Controllers\Admin;

use App\Models\Subscription;
use App\Models\User;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $this->checkAdminAccess();

        $query = Subscription::query();

        if ($request->filled('status')) {
            $isActive = $request->status === 'active';
            $query->where('is_active', $isActive);
        }

        if ($request->filled('featured')) {
            $isFeatured = $request->featured === 'yes';
            $query->where('is_featured', $isFeatured);
        }

        $subscriptions = $query->orderBy('order_column', 'asc')->orderBy('name', 'asc')->paginate(25);

        return $this->adminView('admin.subscriptions.index', [
            'subscriptions' => $subscriptions,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $this->checkAdminAccess();

        return $this->adminView('admin.subscriptions.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $this->checkAdminAccess();

        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:subscriptions',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'currency_code' => 'required|string|size:3',
            'billing_cycle' => 'required|in:monthly,yearly,quarterly',
            'duration_days' => 'required|integer|min:1',
            'ad_limit' => 'required|integer|min:0',
            'featured_ads_limit' => 'required|integer|min:0',
            'has_priority_support' => 'boolean',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'features' => 'nullable|array',
            'features.*' => 'string|max:500',
        ]);

        $subscription = Subscription::create([
            'name' => $request->name,
            'slug' => $request->slug,
            'description' => $request->description,
            'price' => $request->price,
            'currency_code' => $request->currency_code,
            'billing_cycle' => $request->billing_cycle,
            'duration_days' => $request->duration_days,
            'ad_limit' => $request->ad_limit,
            'featured_ads_limit' => $request->featured_ads_limit,
            'has_priority_support' => $request->has_priority_support ?? false,
            'is_active' => $request->is_active ?? true,
            'is_featured' => $request->is_featured ?? false,
            'features' => $request->features ?? [],
        ]);

        return response()->json([
            'message' => 'Subscription created successfully',
            'subscription' => $subscription,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Subscription $subscription): View
    {
        $this->checkAdminAccess();

        $subscription->load('payments');

        return $this->adminView('admin.subscriptions.show', [
            'subscription' => $subscription,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Subscription $subscription): View
    {
        $this->checkAdminAccess();

        return $this->adminView('admin.subscriptions.edit', [
            'subscription' => $subscription,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Subscription $subscription): JsonResponse
    {
        $this->checkAdminAccess();

        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:subscriptions,slug,' . $subscription->id,
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'currency_code' => 'required|string|size:3',
            'billing_cycle' => 'required|in:monthly,yearly,quarterly',
            'duration_days' => 'required|integer|min:1',
            'ad_limit' => 'required|integer|min:0',
            'featured_ads_limit' => 'required|integer|min:0',
            'has_priority_support' => 'boolean',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'features' => 'nullable|array',
            'features.*' => 'string|max:500',
        ]);

        $subscription->update([
            'name' => $request->name,
            'slug' => $request->slug,
            'description' => $request->description,
            'price' => $request->price,
            'currency_code' => $request->currency_code,
            'billing_cycle' => $request->billing_cycle,
            'duration_days' => $request->duration_days,
            'ad_limit' => $request->ad_limit,
            'featured_ads_limit' => $request->featured_ads_limit,
            'has_priority_support' => $request->has_priority_support ?? false,
            'is_active' => $request->is_active ?? true,
            'is_featured' => $request->is_featured ?? false,
            'features' => $request->features ?? [],
        ]);

        return response()->json([
            'message' => 'Subscription updated successfully',
            'subscription' => $subscription->refresh(),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Subscription $subscription): JsonResponse
    {
        $this->checkAdminAccess();

        // Check if this subscription is in use
        $paymentCount = Payment::where('subscription_id', $subscription->id)->count();
        if ($paymentCount > 0) {
            return response()->json([
                'error' => 'Cannot delete subscription with existing payments',
            ], 400);
        }

        $subscription->delete();

        return response()->json([
            'message' => 'Subscription deleted successfully',
        ]);
    }

    /**
     * Get subscription statistics for admin dashboard.
     */
    public function stats(): JsonResponse
    {
        $this->checkAdminAccess();

        $totalSubscriptions = Subscription::count();
        $activeSubscriptions = Subscription::active()->count();
        $featuredSubscriptions = Subscription::featured()->count();

        return response()->json([
            'total_subscriptions' => $totalSubscriptions,
            'active_subscriptions' => $activeSubscriptions,
            'featured_subscriptions' => $featuredSubscriptions,
        ]);
    }
}