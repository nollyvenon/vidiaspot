<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use App\Models\User;
use App\Http\Resources\AdResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AdsController extends Controller
{
    /**
     * Get all ads for admin management.
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user || (!$user->hasRole('admin') && $user->email !== config('app.admin_email', 'admin@vidiaspot.com'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $query = Ad::with(['user', 'category', 'images', 'currency']);

        // Filter by status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Filter by category
        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by user
        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        // Search
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', function($q) use ($request) {
                      $q->where('name', 'like', '%' . $request->search . '%');
                  });
            });
        }

        $ads = $query->orderBy('created_at', 'desc')->paginate($request->per_page ?? 15);

        return response()->json([
            'success' => true,
            'data' => AdResource::collection($ads),
            'message' => 'Ads list for admin management'
        ]);
    }

    /**
     * Update an ad status (approve, reject, activate, deactivate).
     */
    public function updateStatus(Request $request, string $id): JsonResponse
    {
        $user = Auth::user();

        if (!$user || (!$user->hasRole('admin') && $user->email !== config('app.admin_email', 'admin@vidiaspot.com'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'status' => 'required|in:active,inactive,sold,pending,approved,rejected'
        ]);

        $ad = Ad::findOrFail($id);
        $ad->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'data' => new AdResource($ad->refresh()),
            'message' => 'Ad status updated successfully'
        ]);
    }

    /**
     * Delete an ad permanently.
     */
    public function destroy(string $id): JsonResponse
    {
        $user = Auth::user();

        if (!$user || (!$user->hasRole('admin') && $user->email !== config('app.admin_email', 'admin@vidiaspot.com'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $ad = Ad::findOrFail($id);

        // Delete associated images and files
        foreach ($ad->images as $image) {
            if ($image->image_path) {
                \Storage::disk('public')->delete($image->image_path);
            }
            $image->delete();
        }

        $ad->delete();

        return response()->json([
            'success' => true,
            'message' => 'Ad deleted successfully'
        ]);
    }

    /**
     * Get pending ads that need moderation.
     */
    public function pending(): JsonResponse
    {
        $user = Auth::user();

        if (!$user || (!$user->hasRole('admin') && $user->email !== config('app.admin_email', 'admin@vidiaspot.com'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $ads = Ad::with(['user', 'category', 'images', 'currency'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => AdResource::collection($ads),
            'message' => 'Pending ads for moderation'
        ]);
    }
}
