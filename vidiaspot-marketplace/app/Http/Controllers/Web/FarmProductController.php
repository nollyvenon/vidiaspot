<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use App\Models\ECommerce\Category;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FarmProductController extends Controller
{
    /**
     * Display a listing of farm products
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request): View
    {
        $query = Ad::with(['user', 'category', 'images'])
            ->where('direct_from_farm', true)  // Only direct farm products
            ->where('status', 'active');

        // Get filters from request
        $search = $request->input('search');
        $category = $request->input('category');
        $location = $request->input('location');
        $isOrganic = $request->input('organic');
        $harvestSeason = $request->input('season');

        // Apply filters
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        if ($category) {
            // Handle category slug
            $categoryModel = Category::where('slug', $category)->first();
            if ($categoryModel) {
                $query->where('category_id', $categoryModel->id);
            }
        }

        if ($location) {
            $query->where('location', 'like', '%' . $location . '%');
        }

        if ($isOrganic !== null) {
            $query->where('is_organic', $isOrganic);
        }

        if ($harvestSeason) {
            $query->where('harvest_season', $harvestSeason);
        }

        // Proximity search based on user's location
        $lat = $request->input('lat');
        $lng = $request->input('lng');
        $radius = $request->input('radius', 50); // Default to 50km

        if ($lat && $lng) {
            // Calculate distance and filter within radius
            $query->selectRaw("
                *,
                (6371 * acos(
                    cos(radians(?)) *
                    cos(radians(farm_latitude)) *
                    cos(radians(farm_longitude) - radians(?)) +
                    sin(radians(?)) *
                    sin(radians(farm_latitude))
                )) AS distance", [$lat, $lng, $lat])
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
            // Default ordering
            $query->orderBy('created_at', 'desc');
        }


        $farmProducts = $query->paginate(20);
        $farmProducts->appends($request->query());

        // Get farm categories
        $farmCategories = Category::whereHas('ads', function($query) {
            $query->where('direct_from_farm', true);
        })->get();

        return view('web.farm_products.index', compact('farmProducts', 'farmCategories'));
    }

    public function show($id): View
    {
        $farmProduct = Ad::with(['user', 'category', 'images'])->where('direct_from_farm', true)->findOrFail($id);

        // Increment view count
        $farmProduct->increment('view_count');

        return view('web.pages.farm_product_show', compact('farmProduct'));
    }

    public function sellerProfile($id): View
    {
        // Get user as farm seller
        $farmSeller = \App\Models\User::findOrFail($id);

        // Get their farm products
        $farmProducts = Ad::with(['category', 'images'])
            ->where('user_id', $id)
            ->where('direct_from_farm', true)
            ->where('status', 'active')
            ->get();

        return view('web.farm_products.seller_profile', compact('farmSeller', 'farmProducts'));
    }
}
