<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use App\Models\ECommerce\Category;
use App\Http\Resources\FarmProducts\FarmProductResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class FarmProductSpecificController extends Controller
{
    /**
     * Display a listing of farm-specific products
     */
    public function index(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'search' => 'string|nullable',
            'category_id' => 'integer|exists:categories,id|nullable',
            'location' => 'string|nullable',
            'organic' => 'boolean|nullable',
            'season' => 'string|in:spring,summer,fall,winter,all_season|nullable',
            'harvest_date_from' => 'date|nullable',
            'harvest_date_to' => 'date|nullable',
            'max_freshness_days' => 'integer|nullable',
            'min_sustainability_score' => 'numeric|between:0,10|nullable',
            'lat' => 'numeric|between:-90,90|nullable',
            'lng' => 'numeric|between:-180,180|nullable',
            'radius' => 'numeric|min:1|nullable',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $query = Ad::with(['user', 'category', 'images'])
            ->where('direct_from_farm', true)
            ->where('status', 'active');

        // Apply filters
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%')
                  ->orWhere('farm_name', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->location) {
            $query->where('farm_location', 'like', '%' . $request->location . '%');
        }

        if ($request->boolean('organic') !== null) {
            $query->where('is_organic', $request->organic);
        }

        if ($request->harvest_date_from) {
            $query->where('harvest_date', '>=', $request->harvest_date_from);
        }

        if ($request->harvest_date_to) {
            $query->where('harvest_date', '<=', $request->harvest_date_to);
        }

        if ($request->season) {
            $query->where('harvest_season', $request->season);
        }

        if ($request->max_freshness_days) {
            $query->where('freshness_days', '<=', $request->max_freshness_days);
        }

        if ($request->min_sustainability_score) {
            $query->where('sustainability_score', '>=', $request->min_sustainability_score);
        }

        // Proximity search
        if ($request->lat && $request->lng) {
            $lat = $request->lat;
            $lng = $request->lng;
            $radius = $request->radius ?? 50; // Default to 50km

            $query->selectRaw("
                *,
                (6371 * acos(
                    cos(radians(?)) * 
                    cos(radians(farm_latitude)) * 
                    cos(radians(farm_longitude) - radians(?)) + 
                    sin(radians(?)) * 
                    sin(radians(farm_latitude))
                )) AS distance", [$lat, $lng, $lat])
            ->whereNotNull('farm_latitude')
            ->whereNotNull('farm_longitude')
            ->whereRaw("
                (6371 * acos(
                    cos(radians(?)) * 
                    cos(radians(farm_latitude)) * 
                    cos(radians(farm_longitude) - radians(?)) + 
                    sin(radians(?)) * 
                    sin(radians(farm_latitude))
                )) <= ?", [$lat, $lng, $lat, $radius])
            ->orderByRaw("
                (6371 * acos(
                    cos(radians(?)) * 
                    cos(radians(farm_latitude)) * 
                    cos(radians(farm_longitude) - radians(?)) + 
                    sin(radians(?)) * 
                    sin(radians(farm_latitude))
                )) ASC", [$lat, $lng, $lat]);
        } else {
            $orderBy = $request->order_by ?? 'created_at';
            $orderDirection = $request->order_direction ?? 'desc';
            $query->orderBy($orderBy, $orderDirection);
        }

        $farmProducts = $query->paginate($request->per_page ?? 15);

        return response()->json([
            'success' => true,
            'data' => FarmProductResource::collection($farmProducts),
            'pagination' => [
                'current_page' => $farmProducts->currentPage(),
                'last_page' => $farmProducts->lastPage(),
                'per_page' => $farmProducts->perPage(),
                'total' => $farmProducts->total(),
                'from' => $farmProducts->firstItem(),
                'to' => $farmProducts->lastItem(),
            ]
        ]);
    }

    /**
     * Store a newly created farm product
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'currency_code' => 'sometimes|string|size:3|exists:currencies,code',
            'category_id' => 'required|exists:categories,id',
            'condition' => 'required|in:new,like_new,good,fair,poor',
            'location' => 'required|string|max:255', // customer pickup/delivery location
            'negotiable' => 'boolean',
            'contact_phone' => 'nullable|string|max:20',
            'status' => 'in:active,inactive,sold,pending',
            'images' => 'array|max:10',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:10240',
            // Farm-specific validations
            'direct_from_farm' => 'boolean',
            'farm_name' => 'required|string|max:255',
            'is_organic' => 'boolean',
            'harvest_date' => 'date|nullable',
            'farm_location' => 'required|string|max:255',
            'farm_latitude' => 'numeric|between:-90,90|nullable',
            'farm_longitude' => 'numeric|between:-180,180|nullable',
            'certification' => 'string|max:255|nullable',
            'harvest_season' => 'string|max:20|nullable|in:spring,summer,fall,winter,all_season',
            'farm_size' => 'numeric|min:0|nullable',
            'freshness_days' => 'integer|min:0|nullable',
            'quality_rating' => 'numeric|min:0|max:5|nullable',
            'seasonal_availability' => 'array|nullable',
            'seasonal_availability.*' => 'string|in:spring,summer,fall,winter',
            'certification_type' => 'string|max:50|nullable',
            'certification_body' => 'string|max:100|nullable',
            'farm_practices' => 'array|nullable',
            'farm_practices.*' => 'string|max:50',
            'delivery_options' => 'array|nullable',
            'delivery_options.*' => 'string|in:local_delivery,pickup,shipping,express_delivery',
            'minimum_order' => 'numeric|min:0|nullable',
            'packaging_type' => 'string|max:50|nullable|in:biodegradable,recyclable,plastic,none',
            'shelf_life' => 'integer|min:0|nullable',
            'storage_instructions' => 'string|nullable',
            'farm_certifications' => 'array|nullable',
            'farm_certifications.*' => 'string|max:100',
            'pesticide_use' => 'boolean',
            'irrigation_method' => 'string|max:50|nullable|in:drip,sprinkler,flood,rainfed,other',
            'soil_type' => 'string|max:50|nullable|in:loamy,sandy,clay,silty,peaty,chalky',
            'sustainability_score' => 'numeric|min:0|max:10|nullable',
            'carbon_footprint' => 'numeric|min:0|nullable',
            'farm_tour_available' => 'boolean',
            'farm_story' => 'string|nullable',
            'farmer_name' => 'string|max:255|nullable',
            'farmer_image' => 'string|nullable',
            'farmer_bio' => 'string|nullable',
            'harvest_method' => 'string|max:50|nullable|in:hand_picked,machine_harvested,combination',
            'post_harvest_handling' => 'string|nullable',
            'supply_capacity' => 'integer|min:0|nullable',
            'shipping_availability' => 'numeric|min:0|nullable',
            'local_delivery_radius' => 'numeric|min:0|nullable',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $farmProduct = Ad::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'description' => $request->description,
            'price' => $request->price,
            'currency_code' => $request->currency_code ?? 'NGN',
            'category_id' => $request->category_id,
            'condition' => $request->condition,
            'location' => $request->location, // Customer pickup/delivery location
            'negotiable' => $request->boolean('negotiable', false),
            'contact_phone' => $request->contact_phone,
            'status' => $request->status ?? 'active',
            // Farm-specific attributes
            'direct_from_farm' => true, // Explicitly set to true for farm products
            'farm_name' => $request->farm_name,
            'is_organic' => $request->boolean('is_organic', false),
            'harvest_date' => $request->harvest_date,
            'farm_location' => $request->farm_location, // Farm location
            'farm_latitude' => $request->farm_latitude,
            'farm_longitude' => $request->farm_longitude,
            'certification' => $request->certification,
            'harvest_season' => $request->harvest_season,
            'farm_size' => $request->farm_size,
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
                $path = $image->store('ads/' . $farmProduct->id, 'public');

                \App\Models\AdImage::create([
                    'ad_id' => $farmProduct->id,
                    'image_path' => $path,
                    'image_url' => Storage::url($path),
                    'is_primary' => $index === 0, // First image is primary
                    'order' => $index,
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'data' => new FarmProductResource($farmProduct->load(['user', 'category', 'images']))
        ], 201);
    }

    /**
     * Display the specified farm product
     */
    public function show(string $id): JsonResponse
    {
        $farmProduct = Ad::with(['user', 'category', 'images'])->where('direct_from_farm', true)->findOrFail($id);

        // Increment view count
        $farmProduct->increment('view_count');

        return response()->json([
            'success' => true,
            'data' => new FarmProductResource($farmProduct)
        ]);
    }

    /**
     * Update the specified farm product in storage
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $farmProduct = Ad::where('id', $id)->where('direct_from_farm', true)->findOrFail($id);

        // Check if user owns the ad
        if ($farmProduct->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
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
            // Farm-specific validations
            'farm_name' => 'sometimes|string|max:255',
            'is_organic' => 'boolean|nullable',
            'harvest_date' => 'date|nullable',
            'farm_location' => 'sometimes|string|max:255',
            'farm_latitude' => 'numeric|between:-90,90|nullable',
            'farm_longitude' => 'numeric|between:-180,180|nullable',
            'certification' => 'string|max:255|nullable',
            'harvest_season' => 'string|max:20|nullable|in:spring,summer,fall,winter,all_season',
            'farm_size' => 'numeric|min:0|nullable',
            'freshness_days' => 'integer|min:0|nullable',
            'quality_rating' => 'numeric|min:0|max:5|nullable',
            'seasonal_availability' => 'array|nullable',
            'seasonal_availability.*' => 'string|in:spring,summer,fall,winter',
            'certification_type' => 'string|max:50|nullable',
            'certification_body' => 'string|max:100|nullable',
            'farm_practices' => 'array|nullable',
            'farm_practices.*' => 'string|max:50',
            'delivery_options' => 'array|nullable',
            'delivery_options.*' => 'string|in:local_delivery,pickup,shipping,express_delivery',
            'minimum_order' => 'numeric|min:0|nullable',
            'packaging_type' => 'string|max:50|nullable|in:biodegradable,recyclable,plastic,none',
            'shelf_life' => 'integer|min:0|nullable',
            'storage_instructions' => 'string|nullable',
            'farm_certifications' => 'array|nullable',
            'farm_certifications.*' => 'string|max:100',
            'pesticide_use' => 'boolean|nullable',
            'irrigation_method' => 'string|max:50|nullable|in:drip,sprinkler,flood,rainfed,other',
            'soil_type' => 'string|max:50|nullable|in:loamy,sandy,clay,silty,peaty,chalky',
            'sustainability_score' => 'numeric|min:0|max:10|nullable',
            'carbon_footprint' => 'numeric|min:0|nullable',
            'farm_tour_available' => 'boolean|nullable',
            'farm_story' => 'string|nullable',
            'farmer_name' => 'string|max:255|nullable',
            'farmer_image' => 'string|nullable',
            'farmer_bio' => 'string|nullable',
            'harvest_method' => 'string|max:50|nullable|in:hand_picked,machine_harvested,combination',
            'post_harvest_handling' => 'string|nullable',
            'supply_capacity' => 'integer|min:0|nullable',
            'shipping_availability' => 'numeric|min:0|nullable',
            'local_delivery_radius' => 'numeric|min:0|nullable',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $farmProduct->update($request->only([
            'title', 'description', 'price', 'currency_code', 'category_id',
            'condition', 'location', 'negotiable', 'contact_phone', 'status',
            // Farm-specific attributes
            'farm_name', 'is_organic', 'harvest_date', 'farm_location',
            'farm_latitude', 'farm_longitude', 'certification', 'harvest_season',
            'farm_size', 'freshness_days', 'quality_rating', 'seasonal_availability',
            'certification_type', 'certification_body', 'farm_practices', 'delivery_options',
            'minimum_order', 'packaging_type', 'shelf_life', 'storage_instructions',
            'farm_certifications', 'pesticide_use', 'irrigation_method', 'soil_type',
            'sustainability_score', 'carbon_footprint', 'farm_tour_available',
            'farm_story', 'farmer_name', 'farmer_image', 'farmer_bio',
            'harvest_method', 'post_harvest_handling', 'supply_capacity',
            'shipping_availability', 'local_delivery_radius'
        ]));

        // Handle image uploads if provided
        if ($request->hasFile('images')) {
            // Delete existing images
            $farmProduct->images()->delete();

            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('ads/' . $farmProduct->id, 'public');

                \App\Models\AdImage::create([
                    'ad_id' => $farmProduct->id,
                    'image_path' => $path,
                    'image_url' => Storage::url($path),
                    'is_primary' => $index === 0, // First image is primary
                    'order' => $index,
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'data' => new FarmProductResource($farmProduct->load(['user', 'category', 'images']))
        ]);
    }

    /**
     * Remove the specified farm product from storage
     */
    public function destroy(string $id): JsonResponse
    {
        $farmProduct = Ad::where('id', $id)->where('direct_from_farm', true)->findOrFail($id);

        // Check if user owns the ad
        if ($farmProduct->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        // Delete associated images and their files
        foreach ($farmProduct->images as $image) {
            if (Storage::exists($image->image_path)) {
                Storage::delete($image->image_path);
            }
            $image->delete();
        }

        $farmProduct->delete();

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
        $farmProducts = Ad::with(['category', 'images'])
            ->where('user_id', Auth::id())
            ->where('direct_from_farm', true)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => FarmProductResource::collection($farmProducts)
        ]);
    }

    /**
     * Get farm products by location (nearest first)
     */
    public function getByLocation(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
            'radius' => 'numeric|min:1|max:100|nullable',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $lat = $request->lat;
        $lng = $request->lng;
        $radius = $request->radius ?? 50; // Default to 50km

        $farmProducts = Ad::with(['user', 'category', 'images'])
            ->where('direct_from_farm', true)
            ->where('status', 'active')
            ->selectRaw("
                *,
                (6371 * acos(
                    cos(radians(?)) * 
                    cos(radians(farm_latitude)) * 
                    cos(radians(farm_longitude) - radians(?)) + 
                    sin(radians(?)) * 
                    sin(radians(farm_latitude))
                )) AS distance", [$lat, $lng, $lat])
            ->whereNotNull('farm_latitude')
            ->whereNotNull('farm_longitude')
            ->whereRaw("
                (6371 * acos(
                    cos(radians(?)) * 
                    cos(radians(farm_latitude)) * 
                    cos(radians(farm_longitude) - radians(?)) + 
                    sin(radians(?)) * 
                    sin(radians(farm_latitude))
                )) <= ?", [$lat, $lng, $lat, $radius])
            ->orderByRaw("
                (6371 * acos(
                    cos(radians(?)) * 
                    cos(radians(farm_latitude)) * 
                    cos(radians(farm_longitude) - radians(?)) + 
                    sin(radians(?)) * 
                    sin(radians(farm_latitude))
                )) ASC", [$lat, $lng, $lat])
            ->paginate($request->per_page ?? 15);

        return response()->json([
            'success' => true,
            'data' => FarmProductResource::collection($farmProducts),
            'filters' => [
                'lat' => $lat,
                'lng' => $lng,
                'radius' => $radius,
            ]
        ]);
    }
}