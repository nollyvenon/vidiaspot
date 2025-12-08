<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ad;
use App\Models\Category;
use App\Services\TrendingSearchService;
use App\Services\RecommendationService;
use App\Services\NotificationPreferenceService;
use Illuminate\Support\Facades\Auth;

class LandingController extends Controller
{
    protected $trendingSearchService;
    protected $recommendationService;
    protected $notificationService;

    public function __construct(
        TrendingSearchService $trendingSearchService,
        RecommendationService $recommendationService,
        NotificationPreferenceService $notificationService
    ) {
        $this->trendingSearchService = $trendingSearchService;
        $this->recommendationService = $recommendationService;
        $this->notificationService = $notificationService;
    }

    public function index()
    {
        $user = Auth::user();
        $moodState = null;

        // Get personalized content for logged-in users
        if ($user) {
            $moodState = $this->notificationService->getMoodState($user);

            // Get personalized recommendations if user is logged in
            $personalizedAds = $this->recommendationService->getMoodBasedRecommendations($user, $moodState, 8);
        } else {
            $personalizedAds = collect();
        }

        // Get featured ads (limited for performance)
        $featuredAds = Ad::where('is_featured', true)
            ->where('status', 'active')
            ->with(['user', 'category', 'images'])
            ->limit(4)
            ->get();

        // Get latest ads
        $latestAds = Ad::where('status', 'active')
            ->with(['user', 'category', 'images'])
            ->orderBy('created_at', 'desc')
            ->limit(12)
            ->get();

        // Get popular categories
        $popularCategories = Category::where('status', 'active')
            ->inRandomOrder()
            ->limit(6)
            ->get();

        // Get trending searches from database
        $trendingSearches = $this->trendingSearchService->getTrendingSearches(12, 7);

        // Group trending searches by categories for display
        $trendingByCategory = [
            'mobile_phones' => collect($trendingSearches)->filter(function($search) {
                return stripos($search->query, 'iphone') !== false ||
                       stripos($search->query, 'samsung') !== false ||
                       stripos($search->query, 'android') !== false ||
                       stripos($search->query, 'phone') !== false ||
                       stripos($search->query, 'mobile') !== false;
            })->take(3),
            'laptops' => collect($trendingSearches)->filter(function($search) {
                return stripos($search->query, 'laptop') !== false ||
                       stripos($search->query, 'macbook') !== false ||
                       stripos($search->query, 'computer') !== false ||
                       stripos($search->query, 'desktop') !== false;
            })->take(3),
            'vehicles' => collect($trendingSearches)->filter(function($search) {
                return stripos($search->query, 'toyota') !== false ||
                       stripos($search->query, 'honda') !== false ||
                       stripos($search->query, 'car') !== false ||
                       stripos($search->query, 'vehicle') !== false ||
                       stripos($search->query, 'nissan') !== false ||
                       stripos($search->query, 'hundai') !== false;
            })->take(3),
        ];

        // Add fallback trending searches if no data from database
        if ($trendingByCategory['mobile_phones']->isEmpty()) {
            $trendingByCategory['mobile_phones'] = collect([
                (object)['query' => 'iPhone', 'count' => 10],
                (object)['query' => 'Samsung', 'count' => 8],
                (object)['query' => 'Android', 'count' => 6],
            ]);
        }

        if ($trendingByCategory['laptops']->isEmpty()) {
            $trendingByCategory['laptops'] = collect([
                (object)['query' => 'Laptop', 'count' => 12],
                (object)['query' => 'Desktop PC', 'count' => 7],
                (object)['query' => 'MacBook', 'count' => 5],
            ]);
        }

        if ($trendingByCategory['vehicles']->isEmpty()) {
            $trendingByCategory['vehicles'] = collect([
                (object)['query' => 'Toyota', 'count' => 15],
                (object)['query' => 'Honda', 'count' => 9],
                (object)['query' => 'Car', 'count' => 8],
            ]);
        }

        // Get how it works steps for display
        $howItWorksSteps = \App\Models\HowItWorksStep::active()->ordered()->get();

        return view('landing.index', compact(
            'featuredAds',
            'latestAds',
            'popularCategories',
            'trendingByCategory',
            'howItWorksSteps',
            'personalizedAds',
            'moodState'
        ));
    }
}