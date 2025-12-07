<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $categories = Category::with('children')
            ->whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('order')
            ->get();

        return response()->json([
            'success' => true,
            'data' => \App\Http\Resources\CategoryResource::collection($categories)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'order' => 'nullable|integer',
        ]);

        $category = Category::create([
            'name' => $request->name,
            'slug' => \Str::slug($request->name),
            'description' => $request->description,
            'parent_id' => $request->parent_id,
            'order' => $request->order ?? 0,
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'data' => new \App\Http\Resources\CategoryResource($category)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $category = Category::with(['children', 'ads'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => new \App\Http\Resources\CategoryResource($category)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $category = Category::findOrFail($id);

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'order' => 'nullable|integer',
        ]);

        $category->update([
            'name' => $request->name ?? $category->name,
            'slug' => $request->name ? \Str::slug($request->name) : $category->slug,
            'description' => $request->description,
            'parent_id' => $request->parent_id,
            'order' => $request->order ?? $category->order,
        ]);

        return response()->json([
            'success' => true,
            'data' => new \App\Http\Resources\CategoryResource($category)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $category = Category::findOrFail($id);

        // Check if category has subcategories or ads
        if ($category->children()->count() > 0 || $category->ads()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete category with subcategories or ads'
            ], 422);
        }

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully'
        ]);
    }

    /**
     * Get all categories as a tree structure
     */
    public function tree(): JsonResponse
    {
        $categories = Category::with('children')->whereNull('parent_id')->get();

        return response()->json([
            'success' => true,
            'data' => $this->buildCategoryTree($categories)
        ]);
    }

    private function buildCategoryTree($categories)
    {
        $result = [];

        foreach ($categories as $category) {
            $item = [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'description' => $category->description,
            ];

            if ($category->children()->count() > 0) {
                $item['children'] = $this->buildCategoryTree($category->children);
            }

            $result[] = $item;
        }

        return $result;
    }
}
