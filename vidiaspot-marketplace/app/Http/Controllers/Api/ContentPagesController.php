<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContentPage;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ContentPagesController extends Controller
{
    /**
     * Get all active content pages (public API).
     */
    public function index(Request $request): JsonResponse
    {
        $query = ContentPage::active();

        // Filter by page type
        if ($request->page_type) {
            $query->where('page_type', $request->page_type);
        }

        // Filter by featured
        if ($request->is_featured !== null) {
            $query->where('is_featured', $request->is_featured);
        }

        // Search
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('content', 'like', '%' . $request->search . '%')
                  ->orWhere('slug', 'like', '%' . $request->search . '%');
            });
        }

        $pages = $query->orderBy('title')->get();

        return response()->json([
            'success' => true,
            'data' => $pages,
            'message' => 'Available content pages'
        ]);
    }

    /**
     * Get a specific content page by slug (public API).
     */
    public function show(string $slug): JsonResponse
    {
        $contentPage = ContentPage::active()->bySlug($slug)->firstOrFail();

        // Increment view count
        $contentPage->increment('view_count');

        return response()->json([
            'success' => true,
            'data' => $contentPage,
            'message' => 'Content page details'
        ]);
    }

    /**
     * Get specific content page types (about, contact, services) - convenience methods.
     */
    public function about(): JsonResponse
    {
        $contentPage = ContentPage::active()
            ->byType('about')
            ->first();

        if (!$contentPage) {
            return response()->json([
                'success' => false,
                'message' => 'About page not found'
            ], 404);
        }

        // Increment view count
        $contentPage->increment('view_count');

        return response()->json([
            'success' => true,
            'data' => $contentPage,
            'message' => 'About page content'
        ]);
    }

    public function contact(): JsonResponse
    {
        $contentPage = ContentPage::active()
            ->byType('contact')
            ->first();

        if (!$contentPage) {
            return response()->json([
                'success' => false,
                'message' => 'Contact page not found'
            ], 404);
        }

        // Increment view count
        $contentPage->increment('view_count');

        return response()->json([
            'success' => true,
            'data' => $contentPage,
            'message' => 'Contact page content'
        ]);
    }

    public function services(): JsonResponse
    {
        $contentPage = ContentPage::active()
            ->byType('service')
            ->first();

        if (!$contentPage) {
            return response()->json([
                'success' => false,
                'message' => 'Services page not found'
            ], 404);
        }

        // Increment view count
        $contentPage->increment('view_count');

        return response()->json([
            'success' => true,
            'data' => $contentPage,
            'message' => 'Services page content'
        ]);
    }

    public function privacy(): JsonResponse
    {
        $contentPage = ContentPage::active()
            ->byType('privacy')
            ->first();

        if (!$contentPage) {
            return response()->json([
                'success' => false,
                'message' => 'Privacy policy page not found'
            ], 404);
        }

        // Increment view count
        $contentPage->increment('view_count');

        return response()->json([
            'success' => true,
            'data' => $contentPage,
            'message' => 'Privacy policy page content'
        ]);
    }

    public function terms(): JsonResponse
    {
        $contentPage = ContentPage::active()
            ->byType('terms')
            ->first();

        if (!$contentPage) {
            return response()->json([
                'success' => false,
                'message' => 'Terms of service page not found'
            ], 404);
        }

        // Increment view count
        $contentPage->increment('view_count');

        return response()->json([
            'success' => true,
            'data' => $contentPage,
            'message' => 'Terms of service page content'
        ]);
    }
}
