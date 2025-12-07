<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class TestimonialsController extends Controller
{
    /**
     * Get all testimonials for admin management.
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user || (!$user->hasRole('admin') && $user->email !== config('app.admin_email', 'admin@vidiaspot.com'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $query = Testimonial::with(['user']);

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
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('testimonial', 'like', '%' . $request->search . '%')
                  ->orWhere('company', 'like', '%' . $request->search . '%');
            });
        }

        $testimonials = $query->orderBy('created_at', 'desc')->paginate($request->per_page ?? 15);

        return response()->json([
            'success' => true,
            'data' => $testimonials,
            'message' => 'Testimonials list for admin management'
        ]);
    }

    /**
     * Store a new testimonial.
     */
    public function store(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user || (!$user->hasRole('admin') && $user->email !== config('app.admin_email', 'admin@vidiaspot.com'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
            'company' => 'nullable|string|max:255',
            'testimonial' => 'required|string',
            'avatar_url' => 'nullable|url',
            'source' => 'nullable|string|max:255',
            'rating' => 'nullable|array',
            'rating.*' => 'integer|min:1|max:5',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'published_at' => 'nullable|date',
        ]);

        $testimonial = Testimonial::create([
            'name' => $request->name,
            'position' => $request->position,
            'company' => $request->company,
            'testimonial' => $request->testimonial,
            'avatar_url' => $request->avatar_url,
            'source' => $request->source,
            'rating' => $request->rating,
            'is_featured' => $request->is_featured ?? false,
            'is_active' => $request->is_active ?? false,
            'user_id' => $user->id,
            'published_at' => $request->published_at,
        ]);

        return response()->json([
            'success' => true,
            'data' => $testimonial,
            'message' => 'Testimonial created successfully'
        ], 201);
    }

    /**
     * Update a testimonial.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $user = Auth::user();

        if (!$user || (!$user->hasRole('admin') && $user->email !== config('app.admin_email', 'admin@vidiaspot.com'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'position' => 'nullable|string|max:255',
            'company' => 'nullable|string|max:255',
            'testimonial' => 'sometimes|string',
            'avatar_url' => 'nullable|url',
            'source' => 'nullable|string|max:255',
            'rating' => 'nullable|array',
            'rating.*' => 'integer|min:1|max:5',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'published_at' => 'nullable|date',
        ]);

        $testimonial = Testimonial::findOrFail($id);
        $testimonial->update($request->all());

        return response()->json([
            'success' => true,
            'data' => $testimonial->refresh(),
            'message' => 'Testimonial updated successfully'
        ]);
    }

    /**
     * Toggle testimonial active status.
     */
    public function toggleStatus(string $id): JsonResponse
    {
        $user = Auth::user();

        if (!$user || (!$user->hasRole('admin') && $user->email !== config('app.admin_email', 'admin@vidiaspot.com'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $testimonial = Testimonial::findOrFail($id);
        $testimonial->update(['is_active' => !$testimonial->is_active]);

        return response()->json([
            'success' => true,
            'data' => $testimonial->refresh(),
            'message' => 'Testimonial status updated successfully'
        ]);
    }

    /**
     * Toggle testimonial featured status.
     */
    public function toggleFeatured(string $id): JsonResponse
    {
        $user = Auth::user();

        if (!$user || (!$user->hasRole('admin') && $user->email !== config('app.admin_email', 'admin@vidiaspot.com'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $testimonial = Testimonial::findOrFail($id);
        $testimonial->update(['is_featured' => !$testimonial->is_featured]);

        return response()->json([
            'success' => true,
            'data' => $testimonial->refresh(),
            'message' => 'Testimonial featured status updated successfully'
        ]);
    }

    /**
     * Delete a testimonial.
     */
    public function destroy(string $id): JsonResponse
    {
        $user = Auth::user();

        if (!$user || (!$user->hasRole('admin') && $user->email !== config('app.admin_email', 'admin@vidiaspot.com'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $testimonial = Testimonial::findOrFail($id);
        $testimonial->delete();

        return response()->json([
            'success' => true,
            'message' => 'Testimonial deleted successfully'
        ]);
    }
}
