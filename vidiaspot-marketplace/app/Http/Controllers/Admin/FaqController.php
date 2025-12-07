<?php

namespace App\Http\Controllers\Admin;

use App\Models\Faq;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class FaqController extends Controller
{
    /**
     * Display FAQs management page.
     */
    public function index(Request $request): View
    {
        $this->checkAdminAccess();

        $query = Faq::with(['category', 'parent']);

        if ($request->filled('search')) {
            $query->where('question', 'LIKE', '%' . $request->search . '%')
                  ->orWhere('answer', 'LIKE', '%' . $request->search . '%');
        }

        if ($request->filled('active')) {
            $isActive = $request->active === 'yes';
            $query->where('is_active', $isActive);
        }

        if ($request->filled('featured')) {
            $isFeatured = $request->featured === 'yes';
            $query->where('is_featured', $isFeatured);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $faqs = $query->orderBy('category_id')->orderBy('order')->orderBy('question')->paginate(25);

        $categories = Category::all();

        return $this->adminView('admin.faqs.index', [
            'faqs' => $faqs,
            'categories' => $categories,
        ]);
    }

    /**
     * Store a new FAQ.
     */
    public function store(Request $request): JsonResponse
    {
        $this->checkAdminAccess();

        $request->validate([
            'question' => 'required|string|max:500',
            'answer' => 'required|string',
            'category_id' => 'nullable|exists:categories,id',
            'parent_id' => 'nullable|exists:faqs,id',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ]);

        $faq = Faq::create([
            'question' => $request->question,
            'answer' => $request->answer,
            'category_id' => $request->category_id,
            'parent_id' => $request->parent_id,
            'order' => $request->order ?? 0,
            'is_active' => $request->is_active ?? true,
            'is_featured' => $request->is_featured ?? false,
        ]);

        return response()->json([
            'message' => 'FAQ created successfully',
            'faq' => $faq,
        ], 201);
    }

    /**
     * Display the specified FAQ.
     */
    public function show(Faq $faq): View
    {
        $this->checkAdminAccess();

        return $this->adminView('admin.faqs.show', [
            'faq' => $faq,
        ]);
    }

    /**
     * Update the specified FAQ.
     */
    public function update(Request $request, Faq $faq): JsonResponse
    {
        $this->checkAdminAccess();

        $request->validate([
            'question' => 'required|string|max:500',
            'answer' => 'required|string',
            'category_id' => 'nullable|exists:categories,id',
            'parent_id' => 'nullable|exists:faqs,id',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ]);

        $faq->update([
            'question' => $request->question,
            'answer' => $request->answer,
            'category_id' => $request->category_id,
            'parent_id' => $request->parent_id,
            'order' => $request->order ?? 0,
            'is_active' => $request->is_active ?? true,
            'is_featured' => $request->is_featured ?? false,
        ]);

        return response()->json([
            'message' => 'FAQ updated successfully',
            'faq' => $faq->refresh(),
        ]);
    }

    /**
     * Remove the specified FAQ.
     */
    public function destroy(Faq $faq): JsonResponse
    {
        $this->checkAdminAccess();

        $faq->delete();

        return response()->json([
            'message' => 'FAQ deleted successfully',
        ]);
    }

    /**
     * Get FAQs by category for front-end display.
     */
    public function getFaqsWithCategory(Request $request): JsonResponse
    {
        $query = Faq::with(['category', 'children'])
            ->where('is_active', true)
            ->orderBy('category_id')
            ->orderBy('order')
            ->orderBy('question');

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $faqs = $query->get();

        // Group FAQs by category
        $groupedFaqs = $faqs->groupBy('category_id')->map(function($categoryFaqs) {
            return [
                'category' => $categoryFaqs->first()->category,
                'faqs' => $categoryFaqs->filter(function($faq) {
                    return is_null($faq->parent_id); // Only top-level FAQs
                })->values(),
            ];
        })->values();

        return response()->json([
            'faqs' => $groupedFaqs,
        ]);
    }

    /**
     * Get all FAQ categories.
     */
    public function getFaqCategories(): JsonResponse
    {
        $categories = Category::whereHas('faqs', function($query) {
            $query->where('is_active', true);
        })->get();

        return response()->json([
            'categories' => $categories,
        ]);
    }
}