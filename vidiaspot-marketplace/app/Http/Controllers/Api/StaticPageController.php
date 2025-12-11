<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StaticPage;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class StaticPageController extends Controller
{
    /**
     * Display a listing of static pages
     */
    public function index(Request $request): JsonResponse
    {
        $query = StaticPage::query();

        // Filter by status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Filter by locale
        if ($request->locale) {
            $query->where('locale', $request->locale);
        }

        // Filter by page key
        if ($request->page_key) {
            $query->where('page_key', $request->page_key);
        }

        // Search in title or content
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('content', 'like', '%' . $request->search . '%');
            });
        }

        $pages = $query->orderBy('order', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate($request->per_page ?? 15);

        return response()->json([
            'success' => true,
            'data' => $pages->items(),
            'pagination' => [
                'current_page' => $pages->currentPage(),
                'last_page' => $pages->lastPage(),
                'per_page' => $pages->perPage(),
                'total' => $pages->total(),
                'from' => $pages->firstItem(),
                'to' => $pages->lastItem(),
            ]
        ]);
    }

    /**
     * Store a newly created static page
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'page_key' => 'required|string|unique:static_pages,page_key',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'locale' => 'sometimes|string|size:2|default:en',
            'status' => 'sometimes|in:active,draft,archived|default:active',
            'order' => 'sometimes|integer|min:0|default:0',
            'author_id' => 'nullable|string|max:255',
            'published_at' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $page = StaticPage::create($validator->validated());

        return response()->json([
            'success' => true,
            'data' => $page
        ], 201);
    }

    /**
     * Display the specified static page
     */
    public function show(string $id): JsonResponse
    {
        $page = StaticPage::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $page
        ]);
    }

    /**
     * Update the specified static page
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $page = StaticPage::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'page_key' => 'sometimes|string|unique:static_pages,page_key,' . $id,
            'title' => 'sometimes|string|max:255',
            'content' => 'sometimes|string',
            'locale' => 'sometimes|string|size:2',
            'status' => 'sometimes|in:active,draft,archived',
            'order' => 'sometimes|integer|min:0',
            'author_id' => 'nullable|string|max:255',
            'published_at' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $page->update($validator->validated());

        return response()->json([
            'success' => true,
            'data' => $page
        ]);
    }

    /**
     * Remove the specified static page
     */
    public function destroy(string $id): JsonResponse
    {
        $page = StaticPage::findOrFail($id);
        $page->delete();

        return response()->json([
            'success' => true,
            'message' => 'Page deleted successfully'
        ]);
    }

    /**
     * Get page content by key (public endpoint)
     */
    public function getPageContent(Request $request, string $pageKey): JsonResponse
    {
        $locale = $request->locale ?? 'en';

        $page = StaticPage::where('page_key', $pageKey)
            ->where('locale', $locale)
            ->where('status', 'active')
            ->first();

        if (!$page) {
            return response()->json([
                'success' => false,
                'message' => 'Page not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'title' => $page->title,
                'content' => $page->content,
                'updated_at' => $page->updated_at,
            ]
        ]);
    }

    /**
     * Get predefined static pages by key (for public facing pages)
     */
    public function getStaticPage(string $pageKey): JsonResponse
    {
        $validKeys = ['contact-us', 'safety-tips', 'privacy-policy', 'terms-conditions', 'faq', 'about'];
        if (!in_array(str_replace('_', '-', $pageKey), $validKeys)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid page key'
            ], 400);
        }

        $page = StaticPage::where('page_key', $pageKey)
            ->where('locale', 'en')
            ->where('status', 'active')
            ->first();

        if (!$page) {
            return response()->json([
                'success' => false,
                'message' => 'Page not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'title' => $page->title,
            'content' => $page->content,
            'updated_at' => $page->updated_at,
        ]);
    }
}
