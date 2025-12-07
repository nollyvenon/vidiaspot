<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContentPage;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ContentPagesController extends Controller
{
    /**
     * Get all content pages for admin management.
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user || (!$user->hasRole('admin') && $user->email !== config('app.admin_email', 'admin@vidiaspot.com'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $query = ContentPage::with(['updatedBy']);

        // Filter by page type
        if ($request->page_type) {
            $query->where('page_type', $request->page_type);
        }

        // Filter by active status
        if ($request->is_active !== null) {
            $query->where('is_active', $request->is_active);
        }

        // Search
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('content', 'like', '%' . $request->search . '%')
                  ->orWhere('slug', 'like', '%' . $request->search . '%');
            });
        }

        $pages = $query->orderBy('created_at', 'desc')->paginate($request->per_page ?? 15);

        return response()->json([
            'success' => true,
            'data' => $pages,
            'message' => 'Content pages list for admin management'
        ]);
    }

    /**
     * Store a new content page.
     */
    public function store(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user || (!$user->hasRole('admin') && $user->email !== config('app.admin_email', 'admin@vidiaspot.com'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'slug' => 'required|string|alpha_dash|unique:content_pages,slug',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'page_type' => 'required|in:static,legal,service,about,contact,terms,privacy,faq,help',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|array',
            'meta_keywords.*' => 'string|max:50',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'published_at' => 'nullable|date',
        ]);

        $contentPage = ContentPage::create([
            'slug' => $request->slug,
            'title' => $request->title,
            'content' => $request->content,
            'page_type' => $request->page_type,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'meta_keywords' => $request->meta_keywords,
            'is_active' => $request->is_active ?? false,
            'is_featured' => $request->is_featured ?? false,
            'updated_by' => $user->id,
            'published_at' => $request->published_at ?? now(),
        ]);

        return response()->json([
            'success' => true,
            'data' => $contentPage,
            'message' => 'Content page created successfully'
        ], 201);
    }

    /**
     * Update a content page.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $user = Auth::user();

        if (!$user || (!$user->hasRole('admin') && $user->email !== config('app.admin_email', 'admin@vidiaspot.com'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'slug' => 'sometimes|string|alpha_dash|unique:content_pages,slug,' . $id,
            'title' => 'sometimes|string|max:255',
            'content' => 'sometimes|string',
            'page_type' => 'sometimes|in:static,legal,service,about,contact,terms,privacy,faq,help',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|array',
            'meta_keywords.*' => 'string|max:50',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'published_at' => 'nullable|date',
        ]);

        $contentPage = ContentPage::findOrFail($id);
        $contentPage->update(array_merge($request->all(), ['updated_by' => $user->id]));

        return response()->json([
            'success' => true,
            'data' => $contentPage->refresh(),
            'message' => 'Content page updated successfully'
        ]);
    }

    /**
     * Toggle page active status.
     */
    public function toggleStatus(string $id): JsonResponse
    {
        $user = Auth::user();

        if (!$user || (!$user->hasRole('admin') && $user->email !== config('app.admin_email', 'admin@vidiaspot.com'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $contentPage = ContentPage::findOrFail($id);
        $contentPage->update(['is_active' => !$contentPage->is_active]);

        return response()->json([
            'success' => true,
            'data' => $contentPage->refresh(),
            'message' => 'Content page status updated successfully'
        ]);
    }

    /**
     * Show single content page details.
     */
    public function show(string $id): JsonResponse
    {
        $user = Auth::user();

        if (!$user || (!$user->hasRole('admin') && $user->email !== config('app.admin_email', 'admin@vidiaspot.com'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $contentPage = ContentPage::with(['updatedBy'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $contentPage,
            'message' => 'Content page details'
        ]);
    }

    /**
     * Delete a content page.
     */
    public function destroy(string $id): JsonResponse
    {
        $user = Auth::user();

        if (!$user || (!$user->hasRole('admin') && $user->email !== config('app.admin_email', 'admin@vidiaspot.com'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $contentPage = ContentPage::findOrFail($id);
        $contentPage->delete();

        return response()->json([
            'success' => true,
            'message' => 'Content page deleted successfully'
        ]);
    }
}
