<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Cache;
use App\Services\MySqlToSqliteCacheService;
use App\Models\User;
use App\Models\Ad;
use App\Models\Friendship;

/**
 * Service for social search - finding listings from friends' networks
 */
class SocialSearchService
{
    protected $cacheService;
    
    public function __construct(MySqlToSqliteCacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }
    
    /**
     * Find listings from friends' networks
     */
    public function findListingsFromFriendsNetwork(int $userId, array $filters = []): array
    {
        $filtersHash = md5(serialize($filters));
        $cacheKey = "social_listings_{$userId}_{$filtersHash}";
        
        return $this->cacheService->getFromCacheOrDb(
            $cacheKey,
            function() use ($userId, $filters) {
                return $this->getSocialListings($userId, $filters);
            },
            1800 // 30 minutes
        );
    }
    
    /**
     * Get listings from friends and their networks
     */
    private function getSocialListings(int $userId, array $filters): array
    {
        $user = User::find($userId);
        if (!$user) {
            return [];
        }
        
        // Get friends
        $friends = $this->getFriends($userId);
        
        // Get friend IDs
        $friendIds = $friends->pluck('id')->toArray();
        
        // Get friends of friends (network)
        $networkIds = $this->getNetworkIds($friendIds);
        
        // Combine all IDs
        $allConnectedIds = array_unique(array_merge($friendIds, $networkIds));
        
        // Query ads from connected users
        $query = Ad::with(['user', 'category', 'images'])
            ->whereIn('user_id', $allConnectedIds)
            ->where('status', 'active');
        
        // Apply filters
        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }
        
        if (!empty($filters['max_price'])) {
            $query->where('price', '<=', $filters['max_price']);
        }
        
        if (!empty($filters['min_price'])) {
            $query->where('price', '>=', $filters['min_price']);
        }
        
        if (!empty($filters['location'])) {
            $query->where('location', 'LIKE', '%' . $filters['location'] . '%');
        }
        
        // Order by recency and relevance
        $query->orderBy('created_at', 'desc');
        
        $ads = $query->get();
        
        return [
            'direct_friends_listings' => $this->filterByDirectFriends($ads, $friendIds),
            'network_listings' => $this->filterByNetwork($ads, $networkIds),
            'total_results' => count($ads),
            'friends_count' => count($friendIds),
            'network_count' => count($networkIds),
            'filters_applied' => $filters
        ];
    }
    
    /**
     * Get direct friends of a user
     */
    private function getFriends(int $userId)
    {
        // Assuming we have a friendship system
        // In a real implementation, this would query a friendships table
        $friendships = Friendship::where('user_id', $userId)
            ->where('status', 'accepted')
            ->orWhere('friend_id', $userId)
            ->where('status', 'accepted')
            ->get();
        
        $friendIds = [];
        foreach ($friendships as $friendship) {
            $friendIds[] = $friendship->user_id == $userId ? $friendship->friend_id : $friendship->user_id;
        }
        
        return User::whereIn('id', $friendIds)->get();
    }
    
    /**
     * Get network IDs (friends of friends)
     */
    private function getNetworkIds(array $friendIds): array
    {
        $networkIds = [];
        
        foreach ($friendIds as $friendId) {
            $friendFriendships = Friendship::where('user_id', $friendId)
                ->where('status', 'accepted')
                ->orWhere('friend_id', $friendId)
                ->where('status', 'accepted')
                ->get();
            
            foreach ($friendFriendships as $ff) {
                $networkId = $ff->user_id == $friendId ? $ff->friend_id : $ff->user_id;
                if (!in_array($networkId, $friendIds)) { // Exclude direct friends
                    $networkIds[] = $networkId;
                }
            }
        }
        
        return array_unique($networkIds);
    }
    
    /**
     * Filter ads by direct friends only
     */
    private function filterByDirectFriends($ads, array $friendIds): array
    {
        $directFriendAds = [];
        foreach ($ads as $ad) {
            if (in_array($ad->user_id, $friendIds)) {
                $directFriendAds[] = $ad;
            }
        }
        return $directFriendAds;
    }
    
    /**
     * Filter ads by network (friends of friends)
     */
    private function filterByNetwork($ads, array $networkIds): array
    {
        $networkAds = [];
        foreach ($ads as $ad) {
            if (in_array($ad->user_id, $networkIds) && !in_array($ad->user_id, $friendIds)) {
                $networkAds[] = $ad;
            }
        }
        return $networkAds;
    }
    
    /**
     * Get friend recommendations for a user
     */
    public function getFriendRecommendations(int $userId, int $limit = 10): array
    {
        $cacheKey = "friend_recommendations_{$userId}_{$limit}";
        
        return $this->cacheService->getFromCacheOrDb(
            $cacheKey,
            function() use ($userId, $limit) {
                return $this->computeRecommendations($userId, $limit);
            },
            3600
        );
    }
    
    /**
     * Compute friend recommendations using common interests and connections
     */
    private function computeRecommendations(int $userId, int $limit): array
    {
        // Find users who share similar interests as the user
        // This would involve:
        // 1. Looking at categories of items the user lists/purchases
        // 2. Finding users with similar patterns
        // 3. Getting their friends who aren't already friends
        
        $user = User::find($userId);
        if (!$user) {
            return [];
        }
        
        // Get user's ad categories
        $userAdCategories = Ad::where('user_id', $userId)
            ->where('status', 'active')
            ->pluck('category_id')
            ->toArray();
        
        // Find users who list similar categories
        $similarUsers = User::whereHas('ads', function($q) use ($userAdCategories) {
            $q->whereIn('category_id', $userAdCategories)
              ->where('status', 'active');
        })
        ->where('id', '!=', $userId)
        ->limit($limit * 2)
        ->get();
        
        // Get friend IDs to exclude
        $existingFriends = $this->getFriends($userId);
        $existingFriendIds = $existingFriends->pluck('id')->toArray();
        
        // Filter out direct friends and compute similarity score
        $recommendations = [];
        foreach ($similarUsers as $similarUser) {
            if (!in_array($similarUser->id, $existingFriendIds)) {
                $similarityScore = $this->computeSimilarityScore($userId, $similarUser->id);
                $recommendations[] = [
                    'user' => $similarUser,
                    'similarity_score' => $similarityScore,
                    'common_interests' => $this->getCommonInterests($userId, $similarUser->id)
                ];
            }
        }
        
        // Sort by similarity score
        usort($recommendations, function($a, $b) {
            return $b['similarity_score'] <=> $a['similarity_score'];
        });
        
        return array_slice($recommendations, 0, $limit);
    }
    
    /**
     * Compute similarity score between two users based on ad categories
     */
    private function computeSimilarityScore(int $userId1, int $userId2): float
    {
        $user1Categories = Ad::where('user_id', $userId1)->pluck('category_id')->toArray();
        $user2Categories = Ad::where('user_id', $userId2)->pluck('category_id')->toArray();
        
        if (empty($user1Categories) || empty($user2Categories)) {
            return 0;
        }
        
        $commonCategories = array_intersect($user1Categories, $user2Categories);
        $totalCategories = array_unique(array_merge($user1Categories, $user2Categories));
        
        return count($commonCategories) / count($totalCategories);
    }
    
    /**
     * Get common interests between two users
     */
    private function getCommonInterests(int $userId1, int $userId2): array
    {
        $user1Categories = Ad::where('user_id', $userId1)
            ->pluck('category.category.name')
            ->toArray();
        $user2Categories = Ad::where('user_id', $userId2)
            ->pluck('category.category.name')
            ->toArray();
        
        return array_values(array_intersect($user1Categories, $user2Categories));
    }
    
    /**
     * Get social activity feed for a user
     */
    public function getSocialActivityFeed(int $userId, int $limit = 20): array
    {
        $cacheKey = "social_feed_{$userId}_{$limit}";
        
        return $this->cacheService->getFromCacheOrDb(
            $cacheKey,
            function() use ($userId, $limit) {
                return $this->generateActivityFeed($userId, $limit);
            },
            900 // 15 minutes
        );
    }
    
    /**
     * Generate social activity feed
     */
    private function generateActivityFeed(int $userId, int $limit): array
    {
        $friends = $this->getFriends($userId);
        $friendIds = $friends->pluck('id')->toArray();
        
        if (empty($friendIds)) {
            return [];
        }
        
        // Get recent activities from friends
        $activities = Ad::with(['user', 'category'])
            ->whereIn('user_id', $friendIds)
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function($ad) use ($friends) {
                $friend = $friends->firstWhere('id', $ad->user_id);
                return [
                    'type' => 'new_listing',
                    'user_name' => $friend ? $friend->name : 'Unknown Friend',
                    'user_id' => $ad->user_id,
                    'ad' => $ad,
                    'timestamp' => $ad->created_at,
                    'action' => 'just listed',
                    'is_friend' => true
                ];
            })
            ->toArray();
        
        // Get listings from network (friends of friends)
        $networkIds = $this->getNetworkIds($friendIds);
        if (!empty($networkIds)) {
            $networkActivities = Ad::with(['user', 'category'])
                ->whereIn('user_id', $networkIds)
                ->where('status', 'active')
                ->orderBy('created_at', 'desc')
                ->limit($limit / 2)
                ->get()
                ->map(function($ad) {
                    return [
                        'type' => 'network_listing',
                        'user_name' => $ad->user->name,
                        'user_id' => $ad->user_id,
                        'ad' => $ad,
                        'timestamp' => $ad->created_at,
                        'action' => 'in your network listed',
                        'is_friend' => false
                    ];
                })
                ->toArray();
                
            $activities = array_merge($activities, $networkActivities);
        }
        
        // Sort by timestamp
        usort($activities, function($a, $b) {
            return $b['timestamp'] <=> $a['timestamp'];
        });
        
        return array_slice($activities, 0, $limit);
    }
    
    /**
     * Share a listing with friends
     */
    public function shareListingWithFriends(int $userId, int $adId, array $friendIds = []): bool
    {
        // In a real implementation, this would send notifications to friends
        // For now, we'll just validate and return true
        
        $user = User::find($userId);
        $ad = Ad::find($adId);
        
        if (!$user || !$ad) {
            return false;
        }
        
        // If no specific friends, share with all friends
        if (empty($friendIds)) {
            $allFriends = $this->getFriends($userId);
            $friendIds = $allFriends->pluck('id')->toArray();
        }
        
        // In real implementation, would send notifications
        // For this demo, just return success
        return true;
    }
}