<?php

namespace App\Http\Controllers\Admin;

use App\Models\Blog;
use App\Models\Category;
use App\Models\Ad;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class ContentController extends Controller
{
    /**
     * Display blogs management page.
     */
    public function blogs(Request $request): View
    {
        $this->checkAdminAccess();

        $query = Blog::with(['user'])->select('blogs.*');

        if ($request->filled('search')) {
            $query->where('title', 'LIKE', '%' . $request->search . '%')
                  ->orWhere('content', 'LIKE', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $blogs = $query->latest()->paginate(25);

        $users = User::all();

        return $this->adminView('admin.content.blogs', [
            'blogs' => $blogs,
            'users' => $users,
        ]);
    }

    /**
     * Store a new blog.
     */
    public function storeBlog(Request $request): JsonResponse
    {
        $this->checkAdminAccess();

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string',
            'status' => 'required|in:draft,published,pending',
            'featured_image' => 'nullable|string|max:500',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
        ]);

        $blog = Blog::create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'content' => $request->content,
            'excerpt' => $request->excerpt,
            'status' => $request->status,
            'featured_image' => $request->featured_image,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
        ]);

        return response()->json([
            'message' => 'Blog created successfully',
            'blog' => $blog,
        ], 201);
    }

    /**
     * Update a blog.
     */
    public function updateBlog(Request $request, Blog $blog): JsonResponse
    {
        $this->checkAdminAccess();

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string',
            'status' => 'required|in:draft,published,pending',
            'featured_image' => 'nullable|string|max:500',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
        ]);

        $blog->update([
            'title' => $request->title,
            'content' => $request->content,
            'excerpt' => $request->excerpt,
            'status' => $request->status,
            'featured_image' => $request->featured_image,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
        ]);

        return response()->json([
            'message' => 'Blog updated successfully',
            'blog' => $blog->refresh(),
        ]);
    }

    /**
     * Delete a blog.
     */
    public function destroyBlog(Blog $blog): JsonResponse
    {
        $this->checkAdminAccess();

        $blog->delete();

        return response()->json([
            'message' => 'Blog deleted successfully',
        ]);
    }

    /**
     * Display categories management page.
     */
    public function categories(Request $request): View
    {
        $this->checkAdminAccess();

        $query = Category::query();

        if ($request->filled('search')) {
            $query->where('name', 'LIKE', '%' . $request->search . '%')
                  ->orWhere('description', 'LIKE', '%' . $request->search . '%');
        }

        if ($request->filled('active')) {
            $isActive = $request->active === 'yes';
            $query->where('is_active', $isActive);
        }

        $categories = $query->orderBy('order')->orderBy('name')->get();

        return $this->adminView('admin.content.categories', [
            'categories' => $categories,
        ]);
    }

    /**
     * Store a new category.
     */
    public function storeCategory(Request $request): JsonResponse
    {
        $this->checkAdminAccess();

        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:categories,slug',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $category = Category::create([
            'name' => $request->name,
            'slug' => $request->slug,
            'description' => $request->description,
            'icon' => $request->icon,
            'parent_id' => $request->parent_id,
            'order' => $request->order ?? 0,
            'is_active' => $request->is_active ?? true,
        ]);

        return response()->json([
            'message' => 'Category created successfully',
            'category' => $category,
        ], 201);
    }

    /**
     * Update a category.
     */
    public function updateCategory(Request $request, Category $category): JsonResponse
    {
        $this->checkAdminAccess();

        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:categories,slug,' . $category->id,
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $category->update([
            'name' => $request->name,
            'slug' => $request->slug,
            'description' => $request->description,
            'icon' => $request->icon,
            'parent_id' => $request->parent_id,
            'order' => $request->order ?? 0,
            'is_active' => $request->is_active ?? true,
        ]);

        return response()->json([
            'message' => 'Category updated successfully',
            'category' => $category->refresh(),
        ]);
    }

    /**
     * Delete a category.
     */
    public function destroyCategory(Category $category): JsonResponse
    {
        $this->checkAdminAccess();

        // Check if category has subcategories or ads
        if ($category->children()->count() > 0) {
            return response()->json([
                'error' => 'Cannot delete category with subcategories',
            ], 400);
        }

        if ($category->ads()->count() > 0) {
            return response()->json([
                'error' => 'Cannot delete category with associated ads',
            ], 400);
        }

        $category->delete();

        return response()->json([
            'message' => 'Category deleted successfully',
        ]);
    }

    /**
     * Display pending ads for approval.
     */
    public function pendingAds(Request $request): View
    {
        $this->checkAdminAccess();

        $query = Ad::with(['user', 'category'])->where('status', 'pending');

        if ($request->filled('search')) {
            $query->where('title', 'LIKE', '%' . $request->search . '%')
                  ->orWhere('description', 'LIKE', '%' . $request->search . '%');
        }

        $ads = $query->latest()->paginate(25);

        return $this->adminView('admin.content.pending-ads', [
            'ads' => $ads,
        ]);
    }

    /**
     * Approve an ad.
     */
    public function approveAd(Ad $ad): JsonResponse
    {
        $this->checkAdminAccess();

        $ad->update([
            'status' => 'active',
        ]);

        return response()->json([
            'message' => 'Ad approved successfully',
            'ad' => $ad->refresh(),
        ]);
    }

    /**
     * Reject an ad.
     */
    public function rejectAd(Request $request, Ad $ad): JsonResponse
    {
        $this->checkAdminAccess();

        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $ad->update([
            'status' => 'rejected',
        ]);

        return response()->json([
            'message' => 'Ad rejected successfully',
            'ad' => $ad->refresh(),
        ]);
    }
}