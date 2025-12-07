<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Models\User;
use App\Models\Ad;
use App\Models\Category;

class RecommendationService
{
    protected $cacheTtl = 3600; // 1 hour
    protected $redisService;

    public function __construct(RedisService $redisService)
    {
        $this->redisService = $redisService;
    }

    /**
     * Get personalized product recommendations for a user
     *
     * @param int $userId
     * @param int $limit
     * @return array
     */
    public function getPersonalizedRecommendations(int $userId, int $limit = 10): array
    {
        $cacheKey = "recommendations:user:{$userId}:limit:{$limit}";

        // Try to get from cache first
        if ($cached = $this->redisService->get($cacheKey)) {
            return $cached;
        }

        // Get user's preferences and activity
        $userPreferences = $this->getUserPreferences($userId);
        $recommendedAds = [];

        // 1. Based on user's browsing history
        $historyBased = $this->getRecommendationsFromHistory($userId, $limit / 3);
        $recommendedAds = array_merge($recommendedAds, $historyBased);

        // 2. Based on user's category preferences
        $categoryBased = $this->getRecommendationsByCategory($userId, $limit / 3);
        $recommendedAds = array_merge($recommendedAds, $categoryBased);

        // 3. Popular items in user's location
        $locationBased = $this->getRecommendationsByLocation($userId, $limit / 3);
        $recommendedAds = array_merge($recommendedAds, $locationBased);

        // 4. Items similar to user's previous purchases
        $purchaseBased = $this->getRecommendationsFromPurchases($userId, $limit / 4);
        $recommendedAds = array_merge($recommendedAds, $purchaseBased);

        // Remove duplicates and limit results
        $uniqueAds = [];
        $seenIds = [];

        foreach ($recommendedAds as $ad) {
            if (!isset($seenIds[$ad['id']])) {
                $uniqueAds[] = $ad;
                $seenIds[$ad['id']] = true;

                if (count($uniqueAds) >= $limit) {
                    break;
                }
            }
        }

        // Cache the results
        $this->redisService->put($cacheKey, $uniqueAds, $this->cacheTtl);

        return $uniqueAds;
    }

    /**
     * Get recommendations based on user's browsing history
     */
    protected function getRecommendationsFromHistory(int $userId, int $limit): array
    {
        $history = $this->redisService->getUserHistory($userId);

        if (empty($history)) {
            return $this->getPopularAds($limit);
        }

        // Get ads similar to items in history
        $similarAds = [];
        $historyAds = Ad::whereIn('id', $history)->get();

        foreach ($historyAds as $ad) {
            $similarQuery = Ad::where('category_id', $ad->category_id)
                            ->where('id', '!=', $ad->id)
                            ->where('status', 'active')
                            ->limit(ceil($limit / count($historyAds)));

            $similarAds = array_merge($similarAds, $similarQuery->get()->toArray());
        }

        return $similarAds;
    }

    /**
     * Get recommendations based on user's category preferences
     */
    protected function getRecommendationsByCategory(int $userId, int $limit): array
    {
        $preferences = $this->getUserCategoryPreferences($userId);

        if (empty($preferences)) {
            return [];
        }

        $ads = [];
        foreach ($preferences as $categoryPreference) {
            $query = Ad::where('category_id', $categoryPreference['category_id'])
                      ->where('status', 'active')
                      ->limit(ceil($limit / count($preferences)))
                      ->orderByRaw('RAND()');

            $ads = array_merge($ads, $query->get()->toArray());
        }

        return $ads;
    }

    /**
     * Get recommendations based on user's location
     */
    protected function getRecommendationsByLocation(int $userId, int $limit): array
    {
        $user = User::find($userId);
        if (!$user) {
            return $this->getPopularAds($limit);
        }

        return Ad::where('location', 'LIKE', "%{$user->city}%")
                 ->orWhere('location', 'LIKE', "%{$user->state}%")
                 ->where('status', 'active')
                 ->limit($limit)
                 ->orderBy('created_at', 'desc')
                 ->get()
                 ->toArray();
    }

    /**
     * Get recommendations from user's previous purchases
     */
    protected function getRecommendationsFromPurchases(int $userId, int $limit): array
    {
        // This would be based on actual purchase data
        // For now, let's assume related items to previously purchased categories
        $recentPurchases = DB::table('payments')
                           ->join('subscriptions', 'payments.subscription_id', '=', 'subscriptions.id')
                           ->where('payments.user_id', $userId)
                           ->select('subscriptions.id', 'subscriptions.name', 'subscriptions.description')
                           ->limit(5)
                           ->get();

        $ads = [];
        foreach ($recentPurchases as $purchase) {
            // Find ads in similar categories
            $query = Ad::where('title', 'LIKE', "%{$purchase->name}%")
                      ->orWhere('description', 'LIKE', "%{$purchase->description}%")
                      ->where('status', 'active')
                      ->limit(ceil($limit / count($recentPurchases)));

            $ads = array_merge($ads, $query->get()->toArray());
        }

        return $ads;
    }

    /**
     * Get popular ads across the platform
     */
    protected function getPopularAds(int $limit): array
    {
        $cacheKey = "popular_ads:limit:{$limit}";

        if ($cached = $this->redisService->get($cacheKey)) {
            return $cached;
        }

        $popularAds = Ad::where('status', 'active')
                       ->where('created_at', '>', now()->subWeek())
                       ->orderBy('view_count', 'desc')
                       ->limit($limit)
                       ->get()
                       ->toArray();

        $this->redisService->put($cacheKey, $popularAds, $this->cacheTtl);

        return $popularAds;
    }

    /**
     * Get collaborative filtering recommendations
     */
    public function getCollaborativeRecommendations(int $userId, int $limit = 10): array
    {
        $cacheKey = "collab_rec:user:{$userId}:limit:{$limit}";

        if ($cached = $this->redisService->get($cacheKey)) {
            return $cached;
        }

        // Find users with similar preferences
        $similarUsers = $this->findSimilarUsers($userId);

        if (empty($similarUsers)) {
            return $this->getPopularAds($limit);
        }

        // Get ads liked by similar users that the current user hasn't seen
        $ads = Ad::whereIn('user_id', $similarUsers)
                  ->where('status', 'active')
                  ->whereNotIn('id', $this->getUserHistory($userId))
                  ->limit($limit)
                  ->orderBy('created_at', 'desc')
                  ->get()
                  ->toArray();

        $this->redisService->put($cacheKey, $ads, $this->cacheTtl);

        return $ads;
    }

    /**
     * Get category-based recommendations
     */
    public function getCategoryRecommendations(string $categorySlug, int $limit = 10): array
    {
        $cacheKey = "category_rec:{$categorySlug}:limit:{$limit}";

        if ($cached = $this->redisService->get($cacheKey)) {
            return $cached;
        }

        $category = Category::where('slug', $categorySlug)->first();
        if (!$category) {
            return [];
        }

        $ads = Ad::where('category_id', $category->id)
                 ->where('status', 'active')
                 ->limit($limit)
                 ->inRandomOrder()
                 ->get()
                 ->toArray();

        $this->redisService->put($cacheKey, $ads, $this->cacheTtl);

        return $ads;
    }

    /**
     * Get trending items
     */
    public function getTrendingItems(int $limit = 10, string $timeFrame = 'week'): array
    {
        $cacheKey = "trending:{$timeFrame}:limit:{$limit}";

        if ($cached = $this->redisService->get($cacheKey)) {
            return $cached;
        }

        $period = match($timeFrame) {
            'day' => now()->subDay(),
            'week' => now()->subWeek(),
            'month' => now()->subMonth(),
            default => now()->subWeek(),
        };

        $trendingAds = Ad::where('status', 'active')
                        ->where('created_at', '>', $period)
                        ->orderBy('view_count', 'desc')
                        ->orderBy('created_at', 'desc')
                        ->limit($limit)
                        ->get()
                        ->toArray();

        $this->redisService->put($cacheKey, $trendingAds, $this->cacheTtl);

        return $trendingAds;
    }

    /**
     * Get seasonal recommendations
     */
    public function getSeasonalRecommendations(int $limit = 10): array
    {
        $cacheKey = "seasonal_rec:limit:{$limit}";

        if ($cached = $this->redisService->get($cacheKey)) {
            return $cached;
        }

        // Different recommendations based on season/month
        $month = now()->month;

        // Define seasonal categories
        $seasonalCategories = match($month) {
            12, 1, 2 => ['clothing', 'winter'], // Winter
            3, 4, 5 => ['furniture', 'garden'], // Spring
            6, 7, 8 => ['electronics', 'vacation'], // Summer
            9, 10, 11 => ['books', 'education'], // Fall
            default => ['general'],
        };

        $ads = [];
        foreach ($seasonalCategories as $category) {
            $cat = Category::where('slug', 'LIKE', "%{$category}%")->first();
            if ($cat) {
                $query = Ad::where('category_id', $cat->id)
                          ->where('status', 'active')
                          ->limit(ceil($limit / count($seasonalCategories)))
                          ->get();

                $ads = array_merge($ads, $query->toArray());
            }
        }

        $this->redisService->put($cacheKey, $ads, $this->cacheTtl);

        return $ads;
    }

    /**
     * Get user preferences based on activity
     */
    protected function getUserPreferences(int $userId): array
    {
        // This would typically involve analyzing user behavior
        // For now, returning some default preferences based on user's activity
        return [
            'categories' => $this->getUserCategoryPreferences($userId),
            'price_range' => $this->getUserPricePreferences($userId),
            'location_pref' => $this->getUserLocationPreferences($userId),
        ];
    }

    /**
     * Get user's category preferences
     */
    protected function getUserCategoryPreferences(int $userId): array
    {
        $browsingHistory = $this->redisService->getUserHistory($userId);

        if (empty($browsingHistory)) {
            return [];
        }

        return DB::table('ads')
                ->whereIn('id', $browsingHistory)
                ->select('category_id', DB::raw('COUNT(*) as count'))
                ->groupBy('category_id')
                ->orderByDesc('count')
                ->get()
                ->toArray();
    }

    /**
     * Get user's price preferences
     */
    protected function getUserPricePreferences(int $userId): array
    {
        $browsingHistory = $this->redisService->getUserHistory($userId);

        if (empty($browsingHistory)) {
            return ['min' => 0, 'max' => 100000];
        }

        $priceRange = DB::table('ads')
                       ->whereIn('id', $browsingHistory)
                       ->select(DB::raw('AVG(price) as avg_price, MIN(price) as min_price, MAX(price) as max_price'))
                       ->first();

        return [
            'min' => $priceRange->min_price ?? 0,
            'max' => $priceRange->max_price ?? 100000,
        ];
    }

    /**
     * Get user's location preferences
     */
    protected function getUserLocationPreferences(int $userId): string
    {
        $user = User::find($userId);
        return $user ? $user->city . ', ' . $user->state : '';
    }

    /**
     * Find users with similar preferences
     */
    protected function findSimilarUsers(int $userId): array
    {
        // This is a simplified version - in practice, you'd use ML algorithms
        // For now, we'll find users with similar location and category preferences
        $user = User::find($userId);
        if (!$user) {
            return [];
        }

        // Find users in same location
        $similarUsers = User::where(function($query) use ($user) {
            $query->where('city', $user->city)
                  ->orWhere('state', $user->state);
        })
        ->where('id', '!=', $userId)
        ->limit(20)
        ->pluck('id')
        ->toArray();

        return $similarUsers;
    }

    /**
     * Refresh recommendations for a user
     */
    public function refreshRecommendations(int $userId): bool
    {
        $cacheKeys = [
            "recommendations:user:{$userId}:limit:*",
            "collab_rec:user:{$userId}:limit:*",
        ];

        // In Redis, we would do pattern deletion
        // For now, we'll just return success
        return true;
    }

    /**
     * Track user interaction with recommendation
     */
    public function trackRecommendationInteraction(int $userId, int $adId, string $interactionType): bool
    {
        // Log the interaction for analysis
        $logData = [
            'user_id' => $userId,
            'ad_id' => $adId,
            'interaction_type' => $interactionType,
            'timestamp' => now(),
        ];

        // Store in Redis for quick access
        $key = "rec_interaction:user:{$userId}:ad:{$adId}";
        $this->redisService->put($key, $logData, 86400 * 30); // Keep for 30 days

        // Update user's history
        $this->redisService->addToRecentlyViewed($userId, $adId);

        return true;
    }

    /**
     * Get recommendations for cold start problem (new user)
     */
    public function getColdStartRecommendations(int $limit = 10): array
    {
        // For new users, return popular or trending items
        return array_merge(
            $this->getPopularAds(ceil($limit / 2)),
            $this->getTrendingItems(ceil($limit / 2))
        );
    }
}