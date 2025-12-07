<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use App\Models\AdImage;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AdController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Ad::with(['user', 'category', 'images'])
            ->where('status', 'active');

        // Filter by category
        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by location
        if ($request->location) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        // Filter by price range
        if ($request->min_price) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->max_price) {
            $query->where('price', '<=', $request->max_price);
        }

        // Filter by condition
        if ($request->condition) {
            $query->where('condition', $request->condition);
        }

        // Search
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        // Order by
        $orderBy = $request->order_by ?? 'created_at';
        $orderDirection = $request->order_direction ?? 'desc';

        $query->orderBy($orderBy, $orderDirection);

        $ads = $query->paginate($request->per_page ?? 15);

        return response()->json([
            'success' => true,
            'data' => \App\Http\Resources\AdResource::collection($ads)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'currency_code' => 'sometimes|string|size:3|exists:currencies,code',
            'category_id' => 'required|exists:categories,id',
            'condition' => 'required|in:new,like_new,good,fair,poor',
            'location' => 'required|string|max:255',
            'negotiable' => 'boolean',
            'contact_phone' => 'nullable|string|max:20',
            'images' => 'array|max:10', // Up to 10 images
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:10240', // Max 10MB per image
        ]);

        $ad = Ad::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'description' => $request->description,
            'price' => $request->price,
            'currency_code' => $request->currency_code ?? 'NGN', // Default to NGN if not provided
            'category_id' => $request->category_id,
            'condition' => $request->condition,
            'location' => $request->location,
            'negotiable' => $request->boolean('negotiable', false),
            'contact_phone' => $request->contact_phone,
            'status' => 'active',
        ]);

        // Handle image uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('ads/' . $ad->id, 'public');

                AdImage::create([
                    'ad_id' => $ad->id,
                    'image_path' => $path,
                    'image_url' => Storage::url($path),
                    'is_primary' => $index === 0, // First image is primary
                    'order' => $index,
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'data' => new \App\Http\Resources\AdResource($ad->load(['user', 'category', 'images']))
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $ad = Ad::with(['user', 'category', 'images'])->findOrFail($id);

        // Increment view count
        $ad->increment('view_count');

        return response()->json([
            'success' => true,
            'data' => new \App\Http\Resources\AdResource($ad)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $ad = Ad::findOrFail($id);

        // Check if user owns the ad
        if ($ad->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'price' => 'sometimes|numeric|min:0',
            'currency_code' => 'sometimes|string|size:3|exists:currencies,code',
            'category_id' => 'sometimes|exists:categories,id',
            'condition' => 'sometimes|in:new,like_new,good,fair,poor',
            'location' => 'sometimes|string|max:255',
            'negotiable' => 'boolean',
            'contact_phone' => 'nullable|string|max:20',
            'status' => 'sometimes|in:active,inactive,sold,pending',
            'images' => 'array|max:10',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:10240',
        ]);

        $ad->update($request->only([
            'title', 'description', 'price', 'currency_code', 'category_id',
            'condition', 'location', 'negotiable', 'contact_phone', 'status'
        ]));

        // Handle image uploads if provided
        if ($request->hasFile('images')) {
            // Delete existing images
            $ad->images()->delete();

            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('ads/' . $ad->id, 'public');

                AdImage::create([
                    'ad_id' => $ad->id,
                    'image_path' => $path,
                    'image_url' => Storage::url($path),
                    'is_primary' => $index === 0, // First image is primary
                    'order' => $index,
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'data' => new \App\Http\Resources\AdResource($ad->load(['user', 'category', 'images']))
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $ad = Ad::findOrFail($id);

        // Check if user owns the ad
        if ($ad->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        // Delete associated images and their files
        foreach ($ad->images as $image) {
            if (Storage::exists($image->image_path)) {
                Storage::delete($image->image_path);
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
     * Get ads by authenticated user
     */
    public function myAds(): JsonResponse
    {
        $ads = Ad::with(['category', 'images'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => \App\Http\Resources\AdResource::collection($ads)
        ]);
    }

    /**
     * Add images to an existing ad
     */
    public function addImages(Request $request, string $id): JsonResponse
    {
        $ad = Ad::findOrFail($id);

        // Check if user owns the ad
        if ($ad->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $request->validate([
            'images' => 'required|array|min:1|max:10',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:10240',
        ]);

        $images = [];
        foreach ($request->file('images') as $index => $image) {
            $path = $image->store('ads/' . $ad->id, 'public');

            $imageModel = AdImage::create([
                'ad_id' => $ad->id,
                'image_path' => $path,
                'image_url' => Storage::url($path),
                'is_primary' => $ad->images->count() === 0 && $index === 0, // Make first image primary if no images exist
                'order' => $ad->images->max('order') + $index + 1,
            ]);

            $images[] = $imageModel;
        }

        return response()->json([
            'success' => true,
            'data' => new \App\Http\Resources\AdResource($ad->load(['user', 'category', 'images']))
        ], 200);
    }
}
