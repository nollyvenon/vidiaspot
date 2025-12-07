<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdPlacement;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AdPlacementsController extends Controller
{
    /**
     * Get all ad placements for admin management.
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user || (!$user->hasRole('admin') && $user->email !== config('app.admin_email', 'admin@vidiaspot.com'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $query = AdPlacement::with(['user']);

        // Filter by location
        if ($request->location) {
            $query->where('location', $request->location);
        }

        // Filter by type
        if ($request->type) {
            $query->where('type', $request->type);
        }

        // Filter by active status
        if ($request->is_active !== null) {
            $query->where('is_active', $request->is_active);
        }

        // Filter by target pages
        if ($request->target_pages) {
            $query->where('target_pages', $request->target_pages);
        }

        // Search
        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $placements = $query->orderBy('priority', 'asc')->orderBy('created_at', 'desc')->paginate($request->per_page ?? 15);

        return response()->json([
            'success' => true,
            'data' => $placements,
            'message' => 'Ad placements list for admin management'
        ]);
    }

    /**
     * Store a new ad placement.
     */
    public function store(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user || (!$user->hasRole('admin') && $user->email !== config('app.admin_email', 'admin@vidiaspot.com'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|in:top,side,bottom,between,header,footer,content,sidebar',
            'type' => 'required|in:banner,text,image,video,native,html',
            'size' => 'nullable|string|max:50', // e.g., 300x250, 728x90
            'priority' => 'integer|min:0',
            'is_active' => 'boolean',
            'settings' => 'nullable|array',
            'content' => 'nullable|array',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:starts_at',
            'target_pages' => 'nullable|string|max:255', // homepage, category, ad-detail
            'targeting_rules' => 'nullable|array',
        ]);

        $adPlacement = AdPlacement::create([
            'name' => $request->name,
            'slug' => \Str::slug($request->name),
            'location' => $request->location,
            'type' => $request->type,
            'size' => $request->size,
            'priority' => $request->priority ?? 0,
            'is_active' => $request->is_active ?? false,
            'settings' => $request->settings,
            'content' => $request->content,
            'user_id' => $user->id,
            'starts_at' => $request->starts_at,
            'expires_at' => $request->expires_at,
            'target_pages' => $request->target_pages,
            'targeting_rules' => $request->targeting_rules,
        ]);

        return response()->json([
            'success' => true,
            'data' => $adPlacement,
            'message' => 'Ad placement created successfully'
        ], 201);
    }

    /**
     * Update an ad placement.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $user = Auth::user();

        if (!$user || (!$user->hasRole('admin') && $user->email !== config('app.admin_email', 'admin@vidiaspot.com'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'location' => 'sometimes|in:top,side,bottom,between,header,footer,content,sidebar',
            'type' => 'sometimes|in:banner,text,image,video,native,html',
            'size' => 'nullable|string|max:50',
            'priority' => 'integer|min:0',
            'is_active' => 'boolean',
            'settings' => 'nullable|array',
            'content' => 'nullable|array',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:starts_at',
            'target_pages' => 'nullable|string|max:255',
            'targeting_rules' => 'nullable|array',
        ]);

        $adPlacement = AdPlacement::findOrFail($id);
        $updateData = $request->all();

        if ($request->filled('name')) {
            $updateData['slug'] = \Str::slug($request->name);
        }

        $adPlacement->update($updateData);

        return response()->json([
            'success' => true,
            'data' => $adPlacement->refresh(),
            'message' => 'Ad placement updated successfully'
        ]);
    }

    /**
     * Toggle ad placement active status.
     */
    public function toggleStatus(string $id): JsonResponse
    {
        $user = Auth::user();

        if (!$user || (!$user->hasRole('admin') && $user->email !== config('app.admin_email', 'admin@vidiaspot.com'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $adPlacement = AdPlacement::findOrFail($id);
        $adPlacement->update(['is_active' => !$adPlacement->is_active]);

        return response()->json([
            'success' => true,
            'data' => $adPlacement->refresh(),
            'message' => 'Ad placement status updated successfully'
        ]);
    }

    /**
     * Delete an ad placement.
     */
    public function destroy(string $id): JsonResponse
    {
        $user = Auth::user();

        if (!$user || (!$user->hasRole('admin') && $user->email !== config('app.admin_email', 'admin@vidiaspot.com'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $adPlacement = AdPlacement::findOrFail($id);
        $adPlacement->delete();

        return response()->json([
            'success' => true,
            'message' => 'Ad placement deleted successfully'
        ]);
    }

    /**
     * Get ad placement statistics.
     */
    public function stats(): JsonResponse
    {
        $user = Auth::user();

        if (!$user || (!$user->hasRole('admin') && $user->email !== config('app.admin_email', 'admin@vidiaspot.com'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $stats = [
            'total_placements' => AdPlacement::count(),
            'active_placements' => AdPlacement::where('is_active', true)->count(),
            'placements_by_location' => AdPlacement::selectRaw('location, COUNT(*) as count')
                ->groupBy('location')
                ->pluck('count', 'location'),
            'placements_by_type' => AdPlacement::selectRaw('type, COUNT(*) as count')
                ->groupBy('type')
                ->pluck('count', 'type'),
            'total_impressions' => AdPlacement::sum('view_count'),
            'total_clicks' => AdPlacement::sum('click_count'),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
            'message' => 'Ad placement statistics'
        ]);
    }
}
