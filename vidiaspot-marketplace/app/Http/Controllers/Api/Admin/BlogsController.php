<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class BlogsController extends Controller
{
    /**
     * Get all blogs for admin management.
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user || (!$user->hasRole('admin') && $user->email !== config('app.admin_email', 'admin@vidiaspot.com'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $query = Blog::with(['author']);

        // Filter by status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Filter by published status
        if ($request->is_published !== null) {
            $query->where('is_published', $request->is_published);
        }

        // Filter by featured status
        if ($request->is_featured !== null) {
            $query->where('is_featured', $request->is_featured);
        }

        // Filter by author
        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        // Search
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('content', 'like', '%' . $request->search . '%')
                  ->orWhere('excerpt', 'like', '%' . $request->search . '%');
            });
        }

        $blogs = $query->orderBy('created_at', 'desc')->paginate($request->per_page ?? 15);

        return response()->json([
            'success' => true,
            'data' => $blogs,
            'message' => 'Blogs list for admin management'
        ]);
    }

    /**
     * Store a new blog post.
     */
    public function store(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user || (!$user->hasRole('admin') && $user->email !== config('app.admin_email', 'admin@vidiaspot.com'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string',
            'featured_image' => 'nullable|url',
            'status' => 'in:draft,published,archived',
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
            'meta' => 'nullable|array',
            'user_id' => 'required|exists:users,id',
        ]);

        $blog = Blog::create([
            'title' => $request->title,
            'slug' => \Str::slug($request->title),
            'content' => $request->content,
            'excerpt' => $request->excerpt,
            'featured_image' => $request->featured_image,
            'status' => $request->status ?? 'draft',
            'is_featured' => $request->is_featured ?? false,
            'is_published' => $request->is_published ?? false,
            'tags' => $request->tags,
            'meta' => $request->meta,
            'user_id' => $request->user_id,
            'published_at' => $request->is_published ? now() : null,
        ]);

        return response()->json([
            'success' => true,
            'data' => $blog->refresh(),
            'message' => 'Blog post created successfully'
        ], 201);
    }

    /**
     * Update a blog post.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $user = Auth::user();

        if (!$user || (!$user->hasRole('admin') && $user->email !== config('app.admin_email', 'admin@vidiaspot.com'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'title' => 'sometimes|string|max:255',
            'content' => 'sometimes|string',
            'excerpt' => 'nullable|string',
            'featured_image' => 'nullable|url',
            'status' => 'in:draft,published,archived',
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
            'meta' => 'nullable|array',
            'user_id' => 'sometimes|exists:users,id',
        ]);

        $blog = Blog::findOrFail($id);

        $updateData = $request->all();
        if ($request->filled('title')) {
            $updateData['slug'] = \Str::slug($request->title);
        }

        // Update published_at if is_published changes to true
        if ($request->has('is_published') && $request->is_published && !$blog->is_published) {
            $updateData['published_at'] = now();
        }

        $blog->update($updateData);

        return response()->json([
            'success' => true,
            'data' => $blog->refresh(),
            'message' => 'Blog post updated successfully'
        ]);
    }

    /**
     * Publish a blog post.
     */
    public function publish(string $id): JsonResponse
    {
        $user = Auth::user();

        if (!$user || (!$user->hasRole('admin') && $user->email !== config('app.admin_email', 'admin@vidiaspot.com'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $blog = Blog::findOrFail($id);
        $blog->update([
            'status' => 'published',
            'is_published' => true,
            'published_at' => $blog->published_at ?? now(),
        ]);

        return response()->json([
            'success' => true,
            'data' => $blog->refresh(),
            'message' => 'Blog post published successfully'
        ]);
    }

    /**
     * Unpublish a blog post.
     */
    public function unpublish(string $id): JsonResponse
    {
        $user = Auth::user();

        if (!$user || (!$user->hasRole('admin') && $user->email !== config('app.admin_email', 'admin@vidiaspot.com'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $blog = Blog::findOrFail($id);
        $blog->update([
            'status' => 'draft',
            'is_published' => false,
        ]);

        return response()->json([
            'success' => true,
            'data' => $blog->refresh(),
            'message' => 'Blog post unpublished successfully'
        ]);
    }

    /**
     * Toggle blog featured status.
     */
    public function toggleFeatured(string $id): JsonResponse
    {
        $user = Auth::user();

        if (!$user || (!$user->hasRole('admin') && $user->email !== config('app.admin_email', 'admin@vidiaspot.com'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $blog = Blog::findOrFail($id);
        $blog->update(['is_featured' => !$blog->is_featured]);

        return response()->json([
            'success' => true,
            'data' => $blog->refresh(),
            'message' => 'Blog featured status updated successfully'
        ]);
    }

    /**
     * Get blog statistics.
     */
    public function stats(): JsonResponse
    {
        $user = Auth::user();

        if (!$user || (!$user->hasRole('admin') && $user->email !== config('app.admin_email', 'admin@vidiaspot.com'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $stats = [
            'total_blogs' => Blog::count(),
            'published_blogs' => Blog::where('is_published', true)->count(),
            'draft_blogs' => Blog::where('status', 'draft')->count(),
            'featured_blogs' => Blog::where('is_featured', true)->count(),
            'total_views' => Blog::sum('view_count'),
            'blogs_by_author' => Blog::selectRaw('user_id, COUNT(*) as count')
                ->groupBy('user_id')
                ->with(['author:id,name'])
                ->get()
                ->map(function($item) {
                    return [
                        'author' => $item->author ? $item->author->name : 'Unknown',
                        'count' => $item->count
                    ];
                }),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
            'message' => 'Blog statistics'
        ]);
    }

    /**
     * Delete a blog post.
     */
    public function destroy(string $id): JsonResponse
    {
        $user = Auth::user();

        if (!$user || (!$user->hasRole('admin') && $user->email !== config('app.admin_email', 'admin@vidiaspot.com'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $blog = Blog::findOrFail($id);
        $blog->delete();

        return response()->json([
            'success' => true,
            'message' => 'Blog post deleted successfully'
        ]);
    }
}
