<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlogController extends Controller
{
    /**
     * Display the blog index page
     */
    public function index(Request $request): View
    {
        $query = Blog::published()->with('user');

        // Apply filters
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('excerpt', 'like', '%' . $request->search . '%')
                  ->orWhere('content', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->category) {
            $query->byCategory($request->category);
        }

        if ($request->author) {
            $query->where('author', 'like', '%' . $request->author . '%');
        }

        $blogs = $query->orderBy('published_at', 'desc')->paginate(12);
        $blogs->appends($request->query());

        $categories = Blog::select('category')
            ->where('status', 'published')
            ->distinct()
            ->pluck('category');

        $featuredPosts = Blog::published()
            ->where('is_featured', true)
            ->limit(3)
            ->get();

        return view('web.pages.farm_blog_index', compact('blogs', 'categories', 'featuredPosts'));
    }

    /**
     * Display a specific blog post
     */
    public function show(string $slug): View
    {
        $blog = Blog::with('user')
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        // Increment view count
        $blog->increment('view_count');

        $relatedPosts = Blog::where('category', $blog->category)
            ->where('id', '!=', $blog->id)
            ->where('status', 'published')
            ->orderBy('published_at', 'desc')
            ->limit(4)
            ->get();

        return view('web.pages.farm_blog_show', compact('blog', 'relatedPosts'));
    }

    /**
     * Display blog posts by category
     */
    public function showByCategory(string $category): View
    {
        $blogs = Blog::published()
            ->byCategory($category)
            ->with('user')
            ->orderBy('published_at', 'desc')
            ->paginate(12);

        $categories = Blog::select('category')
            ->where('status', 'published')
            ->distinct()
            ->pluck('category');

        return view('web.pages.farm_blog_index', compact('blogs', 'categories', 'category'));
    }

    /**
     * Display featured blog posts
     */
    public function featured(): View
    {
        $featuredPosts = Blog::published()
            ->where('is_featured', true)
            ->with('user')
            ->orderBy('published_at', 'desc')
            ->paginate(12);

        $categories = Blog::select('category')
            ->where('status', 'published')
            ->distinct()
            ->pluck('category');

        return view('web.pages.farm_blog_featured', compact('featuredPosts', 'categories'));
    }

    /**
     * Display trending blog posts
     */
    public function trending(): View
    {
        $trendingPosts = Blog::published()
            ->with('user')
            ->orderBy('view_count', 'desc')
            ->orderBy('published_at', 'desc')
            ->limit(12)
            ->get();

        $categories = Blog::select('category')
            ->where('status', 'published')
            ->distinct()
            ->pluck('category');

        return view('web.pages.farm_blog_trending', compact('trendingPosts', 'categories'));
    }
}