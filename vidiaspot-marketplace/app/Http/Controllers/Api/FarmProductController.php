<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use App\Models\ECommerce\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FarmProductController extends Controller
{
    /**
     * Display a listing of farm products.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Ad::with(['user', 'category', 'images'])
            ->where('direct_from_farm', true)  // Only direct farm products
            ->where('status', 'active');

        // Filter by farm location/region
        if ($request->farm_location) {
            $query->where('farm_location', 'like', '%' . $request->farm_location . '%');
        }

        // Filter by organic status
        if ($request->is_organic !== null) {
            $query->where('is_organic', $request->is_organic);
        }

        // Filter by harvest season
        if ($request->harvest_season) {
            $query->where('harvest_season', $request->harvest_season);
        }

        // Filter by farm name
        if ($request->farm_name) {
            $query->where('farm_name', 'like', '%' . $request->farm_name . '%');
        }

        // Proximity search based on user's location
        if ($request->lat && $request->lng) {
            $lat = $request->lat;
            $lng = $request->lng;
            $radius = $request->radius ?? 50; // Default to 50km

            // Calculate distance and filter within radius
            $query->whereRaw("
                (6371 * acos(
                    cos(radians(?)) *
                    cos(radians(farm_latitude)) *
                    cos(radians(farm_longitude) - radians(?)) +
                    sin(radians(?)) *
                    sin(radians(farm_latitude))
                )) <= ?", [$lat, $lng, $lat, $radius]);
        }

        // Search in title and description
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        // Order by
        $orderBy = $request->order_by ?? 'created_at';
        $orderDirection = $request->order_direction ?? 'desc';

        // If proximity search is used, order by distance
        if ($request->lat && $request->lng) {
            $orderBy = $orderBy === 'distance' ? $orderBy : 'distance';
        }

        if ($orderBy === 'distance' && $request->lat && $request->lng) {
            $query->selectRaw("
                *,
                (6371 * acos(
                    cos(radians(?)) *
                    cos(radians(farm_latitude)) *
                    cos(radians(farm_longitude) - radians(?)) +
                    sin(radians(?)) *
                    sin(radians(farm_latitude))
                )) AS distance", [$request->lat, $request->lng, $request->lat])
            ->orderByRaw("
                (6371 * acos(
                    cos(radians(?)) *
                    cos(radians(farm_latitude)) *
                    cos(radians(farm_longitude) - radians(?)) +
                    sin(radians(?)) *
                    sin(radians(farm_latitude))
                )) ASC", [$request->lat, $request->lng, $request->lat]);
        } else {
            $query->orderBy($orderBy, $orderDirection);
        }

        $ads = $query->paginate($request->per_page ?? 15);

        return response()->json([
            'success' => true,
            'data' => \App\Http\Resources\AdResource::collection($ads)
        ]);
    }

    /**
     * Store a newly created farm product in storage.
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
            'location' => 'required|string|max:255', // Customer pickup/delivery location
            'negotiable' => 'boolean',
            'contact_phone' => 'nullable|string|max:20',
            'images' => 'array|max:10',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:10240',
            // Farm-specific fields
            'farm_name' => 'required|string|max:255',
            'is_organic' => 'boolean',
            'harvest_date' => 'date|nullable',
            'farm_location' => 'required|string|max:255',
            'farm_latitude' => 'numeric|between:-90,90|nullable',
            'farm_longitude' => 'numeric|between:-180,180|nullable',
            'certification' => 'string|nullable|max:255',
            'harvest_season' => 'string|nullable|max:50',
            'farm_size' => 'numeric|min:0|nullable',
            // Additional farm-specific fields
            'freshness_days' => 'integer|min:0|nullable',
            'quality_rating' => 'numeric|min:0|max:5|nullable',
            'seasonal_availability' => 'array|nullable',
            'seasonal_availability.*' => 'string|in:spring,summer,fall,winter',
            'certification_type' => 'string|nullable|max:50',
            'certification_body' => 'string|nullable|max:100',
            'farm_practices' => 'array|nullable',
            'farm_practices.*' => 'string|max:50',
            'delivery_options' => 'array|nullable',
            'delivery_options.*' => 'string|in:local_delivery,pickup,shipping',
            'minimum_order' => 'numeric|min:0|nullable',
            'packaging_type' => 'string|nullable|in:biodegradable,recyclable,none',
            'shelf_life' => 'integer|min:0|nullable',
            'storage_instructions' => 'string|nullable',
            'farm_certifications' => 'array|nullable',
            'farm_certifications.*' => 'string|max:100',
            'pesticide_use' => 'boolean|nullable',
            'irrigation_method' => 'string|nullable|in:drip,sprinkler,flood,rainfed',
            'soil_type' => 'string|nullable|max:50',
            'sustainability_score' => 'numeric|min:0|max:10|nullable',
            'carbon_footprint' => 'numeric|min:0|nullable',
            'farm_tour_available' => 'boolean|nullable',
            'farm_story' => 'string|nullable',
            'farmer_name' => 'string|nullable|max:255',
            'farmer_image' => 'string|nullable',
            'farmer_bio' => 'string|nullable',
            'harvest_method' => 'string|nullable|in:hand_picked,machine_harvested',
            'post_harvest_handling' => 'string|nullable',
            'supply_capacity' => 'integer|min:0|nullable',
            'shipping_availability' => 'numeric|min:0|nullable',
            'local_delivery_radius' => 'numeric|min:0|nullable',
        ]);

        $ad = Ad::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'description' => $request->description,
            'price' => $request->price,
            'currency_code' => $request->currency_code ?? 'NGN',
            'category_id' => $request->category_id,
            'condition' => $request->condition,
            'location' => $request->location,
            'negotiable' => $request->boolean('negotiable', false),
            'contact_phone' => $request->contact_phone,
            'status' => 'active',
            // Farm-specific attributes
            'direct_from_farm' => true,
            'farm_name' => $request->farm_name,
            'is_organic' => $request->boolean('is_organic', false),
            'harvest_date' => $request->harvest_date,
            'farm_location' => $request->farm_location,
            'farm_latitude' => $request->farm_latitude,
            'farm_longitude' => $request->farm_longitude,
            'certification' => $request->certification,
            'harvest_season' => $request->harvest_season,
            'farm_size' => $request->farm_size,
            // Additional farm-specific attributes
            'freshness_days' => $request->freshness_days,
            'quality_rating' => $request->quality_rating,
            'seasonal_availability' => $request->seasonal_availability,
            'certification_type' => $request->certification_type,
            'certification_body' => $request->certification_body,
            'farm_practices' => $request->farm_practices,
            'delivery_options' => $request->delivery_options,
            'minimum_order' => $request->minimum_order,
            'packaging_type' => $request->packaging_type,
            'shelf_life' => $request->shelf_life,
            'storage_instructions' => $request->storage_instructions,
            'farm_certifications' => $request->farm_certifications,
            'pesticide_use' => $request->boolean('pesticide_use', false),
            'irrigation_method' => $request->irrigation_method,
            'soil_type' => $request->soil_type,
            'sustainability_score' => $request->sustainability_score,
            'carbon_footprint' => $request->carbon_footprint,
            'farm_tour_available' => $request->boolean('farm_tour_available', false),
            'farm_story' => $request->farm_story,
            'farmer_name' => $request->farmer_name,
            'farmer_image' => $request->farmer_image,
            'farmer_bio' => $request->farmer_bio,
            'harvest_method' => $request->harvest_method,
            'post_harvest_handling' => $request->post_harvest_handling,
            'supply_capacity' => $request->supply_capacity,
            'shipping_availability' => $request->shipping_availability,
            'local_delivery_radius' => $request->local_delivery_radius,
        ]);

        // Handle image uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('ads/' . $ad->id, 'public');

                \App\Models\AdImage::create([
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
     * Display the specified farm product.
     */
    public function show(string $id): JsonResponse
    {
        $ad = Ad::with(['user', 'category', 'images'])->where('id', $id)->where('direct_from_farm', true)->findOrFail($id);

        // Increment view count
        $ad->increment('view_count');

        return response()->json([
            'success' => true,
            'data' => new \App\Http\Resources\AdResource($ad)
        ]);
    }

    /**
     * Update the specified farm product in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $ad = Ad::where('id', $id)->where('direct_from_farm', true)->findOrFail($id);

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
            // Farm-specific fields
            'farm_name' => 'sometimes|string|max:255',
            'is_organic' => 'boolean',
            'harvest_date' => 'date|nullable',
            'farm_location' => 'sometimes|string|max:255',
            'farm_latitude' => 'numeric|between:-90,90|nullable',
            'farm_longitude' => 'numeric|between:-180,180|nullable',
            'certification' => 'string|nullable|max:255',
            'harvest_season' => 'string|nullable|max:50',
            'farm_size' => 'numeric|min:0|nullable',
            // Additional farm-specific fields
            'freshness_days' => 'integer|min:0|nullable',
            'quality_rating' => 'numeric|min:0|max:5|nullable',
            'seasonal_availability' => 'array|nullable',
            'seasonal_availability.*' => 'string|in:spring,summer,fall,winter',
            'certification_type' => 'string|nullable|max:50',
            'certification_body' => 'string|nullable|max:100',
            'farm_practices' => 'array|nullable',
            'farm_practices.*' => 'string|max:50',
            'delivery_options' => 'array|nullable',
            'delivery_options.*' => 'string|in:local_delivery,pickup,shipping',
            'minimum_order' => 'numeric|min:0|nullable',
            'packaging_type' => 'string|nullable|in:biodegradable,recyclable,none',
            'shelf_life' => 'integer|min:0|nullable',
            'storage_instructions' => 'string|nullable',
            'farm_certifications' => 'array|nullable',
            'farm_certifications.*' => 'string|max:100',
            'pesticide_use' => 'boolean|nullable',
            'irrigation_method' => 'string|nullable|in:drip,sprinkler,flood,rainfed',
            'soil_type' => 'string|nullable|max:50',
            'sustainability_score' => 'numeric|min:0|max:10|nullable',
            'carbon_footprint' => 'numeric|min:0|nullable',
            'farm_tour_available' => 'boolean|nullable',
            'farm_story' => 'string|nullable',
            'farmer_name' => 'string|nullable|max:255',
            'farmer_image' => 'string|nullable',
            'farmer_bio' => 'string|nullable',
            'harvest_method' => 'string|nullable|in:hand_picked,machine_harvested',
            'post_harvest_handling' => 'string|nullable',
            'supply_capacity' => 'integer|min:0|nullable',
            'shipping_availability' => 'numeric|min:0|nullable',
            'local_delivery_radius' => 'numeric|min:0|nullable',
        ]);

        $ad->update($request->only([
            'title', 'description', 'price', 'currency_code', 'category_id',
            'condition', 'location', 'negotiable', 'contact_phone', 'status',
            // Farm-specific attributes
            'farm_name', 'is_organic', 'harvest_date', 'farm_location',
            'farm_latitude', 'farm_longitude', 'certification', 'harvest_season', 'farm_size',
            // Additional farm-specific attributes
            'freshness_days', 'quality_rating', 'seasonal_availability', 'certification_type',
            'certification_body', 'farm_practices', 'delivery_options', 'minimum_order',
            'packaging_type', 'shelf_life', 'storage_instructions', 'farm_certifications',
            'pesticide_use', 'irrigation_method', 'soil_type', 'sustainability_score',
            'carbon_footprint', 'farm_tour_available', 'farm_story', 'farmer_name',
            'farmer_image', 'farmer_bio', 'harvest_method', 'post_harvest_handling',
            'supply_capacity', 'shipping_availability', 'local_delivery_radius'
        ]));

        // Handle image uploads if provided
        if ($request->hasFile('images')) {
            // Delete existing images
            $ad->images()->delete();

            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('ads/' . $ad->id, 'public');

                \App\Models\AdImage::create([
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
     * Remove the specified farm product from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $ad = Ad::where('id', $id)->where('direct_from_farm', true)->findOrFail($id);

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
            'message' => 'Farm product deleted successfully'
        ]);
    }

    /**
     * Get farm products by authenticated user
     */
    public function myFarmProducts(): JsonResponse
    {
        $ads = Ad::with(['category', 'images'])
            ->where('user_id', Auth::id())
            ->where('direct_from_farm', true)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => \App\Http\Resources\AdResource::collection($ads)
        ]);
    }
}
