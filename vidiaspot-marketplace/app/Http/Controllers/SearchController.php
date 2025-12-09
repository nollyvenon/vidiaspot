<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TrendingSearchService;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    protected $trendingSearchService;

    public function __construct(TrendingSearchService $trendingSearchService)
    {
        $this->trendingSearchService = $trendingSearchService;
    }

    public function search(Request $request)
    {
        $query = $request->get('q');
        $location = $request->get('location');
        $categoryId = $request->get('category');
        $minPrice = $request->get('min_price');
        $maxPrice = $request->get('max_price');

        // Log the search
        if ($query) {
            $userId = auth()->check() ? auth()->id() : null;
            $this->trendingSearchService->addSearchLog($query, $location, $userId);
        }

        // Perform the search
        $adsQuery = DB::table('ads')
            ->select('ads.*', 'users.name as user_name', 'categories.name as category_name')
            ->leftJoin('users', 'ads.user_id', '=', 'users.id')
            ->leftJoin('categories', 'ads.category_id', '=', 'categories.id')
            ->where('ads.status', 'active');

        if ($query) {
            $adsQuery->where(function($q) use ($query) {
                $q->where('ads.title', 'LIKE', "%{$query}%")
                  ->orWhere('ads.description', 'LIKE', "%{$query}%")
                  ->orWhere('categories.name', 'LIKE', "%{$query}%");
            });
        }

        if ($location) {
            $adsQuery->where('ads.location', 'LIKE', "%{$location}%");
        }

        if ($categoryId) {
            $adsQuery->where('ads.category_id', $categoryId);
        }

        if ($minPrice) {
            $adsQuery->where('ads.price', '>=', $minPrice);
        }

        if ($maxPrice) {
            $adsQuery->where('ads.price', '<=', $maxPrice);
        }

        $ads = $adsQuery->paginate(20);

        return view('search-results', compact('ads', 'query', 'location'));
    }
}