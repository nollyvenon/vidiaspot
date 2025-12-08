<?php

namespace App\Services;

use App\Models\User;
use App\Models\Ad;
use App\Models\UserBehavior;
use App\Models\UserPreference;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class RecommendationService
{
    /**
     * Get personalized recommendations for a user based on their behavior
     */
    public function getPersonalizedRecommendations(User $user, $limit = 10)
    {
        $cacheKey = "user_recommendations_{$user->id}";
        
        // Try to get from cache first (cache for 30 minutes)
        $recommendations = Cache::get($cacheKey);
        
        if ($recommendations === null) {
            $recommendations = $this->calculateRecommendations($user, $limit);
            Cache::put($cacheKey, $recommendations, now()->addMinutes(30));
        }
        
        return $recommendations;
    }
    
    /**
     * Calculate recommendations based on user behavior and preferences
     */
    private function calculateRecommendations(User $user, $limit)
    {
        // Get user's behavior patterns
        $recentBehaviors = UserBehavior::where('user_id', $user->id)
            ->where('occurred_at', '>=', now()->subDays(30))
            ->orderBy('occurred_at', 'desc')
            ->get();
            
        // Get user preferences
        $preferredCategories = UserPreference::getPreference($user->id, 'preferred_categories', []);
        $preferredLocations = UserPreference::getPreference($user->id, 'preferred_locations', []);
        $priceRange = UserPreference::getPreference($user->id, 'price_range', []);
        
        // Calculate weights for different factors
        $behaviorScore = $this->calculateBehaviorScore($recentBehaviors);
        $categoryScore = $this->calculateCategoryScore($recentBehaviors, $preferredCategories);
        $locationScore = $this->calculateLocationScore($preferredLocations);
        $priceScore = $this->calculatePriceScore($priceRange);
        
        // Get candidate ads based on user preferences and behavior
        $query = Ad::where('status', 'active')
            ->where('user_id', '!=', $user->id); // Don't recommend user's own ads
            
        if (!empty($preferredCategories)) {
            $query->whereIn('category_id', $preferredCategories);
        }
        
        if (!empty($preferredLocations)) {
            $query->whereIn('location', $preferredLocations);
        }
        
        if (!empty($priceRange)) {
            if (isset($priceRange['min'])) {
                $query->where('price', '>=', $priceRange['min']);
            }
            if (isset($priceRange['max'])) {
                $query->where('price', '<=', $priceRange['max']);
            }
        }
        
        // Get potential recommendations
        $ads = $query->with(['user', 'category', 'images'])
            ->limit($limit * 2) // Get more than needed for better selection
            ->get();
            
        // Score each ad based on user behavior
        $scoredAds = $ads->map(function ($ad) use ($user, $behaviorScore, $categoryScore, $locationScore, $priceScore) {
            $score = 0;
            
            // Category relevance score
            $score += $categoryScore[$ad->category_id] ?? 0;
            
            // Location relevance score
            $score += $locationScore[$ad->location] ?? 0;
            
            // Price relevance score
            $score += $this->calculatePriceRelevanceScore($ad->price, $priceScore);
            
            // View count boost for popular items
            $score += min($ad->view_count / 10, 5); // Boost based on popularity
            
            // Recency boost for new ads
            $daysSinceCreated = $ad->created_at->diffInDays(now());
            if ($daysSinceCreated <= 3) {
                $score += 2; // Newer ads get a boost
            }
            
            return [
                'ad' => $ad,
                'score' => $score
            ];
        });
        
        // Sort by score and return top recommendations
        $sortedAds = $scoredAds->sortByDesc('score')->values();
        
        return $sortedAds->take($limit)->pluck('ad');
    }
    
    /**
     * Calculate behavior-based scores
     */
    private function calculateBehaviorScore($behaviors)
    {
        $scores = [];
        
        foreach ($behaviors as $behavior) {
            $multiplier = 1;
            
            switch ($behavior->behavior_type) {
                case 'view':
                    $multiplier = 1;
                    break;
                case 'click':
                    $multiplier = 2;
                    break;
                case 'like':
                    $multiplier = 3;
                    break;
                case 'purchase':
                    $multiplier = 5;
                    break;
                case 'search':
                    $multiplier = 1.5;
                    break;
            }
            
            if ($behavior->target_type === 'ad' && $behavior->target_id) {
                $ad = Ad::find($behavior->target_id);
                if ($ad) {
                    $categoryId = $ad->category_id;
                    $scores[$categoryId] = ($scores[$categoryId] ?? 0) + $multiplier;
                }
            }
        }
        
        return $scores;
    }
    
    /**
     * Calculate category scores based on user behavior
     */
    private function calculateCategoryScore($behaviors, $preferredCategories)
    {
        $scores = $this->calculateBehaviorScore($behaviors);
        
        // Boost preferred categories
        foreach ($preferredCategories as $categoryId) {
            $scores[$categoryId] = ($scores[$categoryId] ?? 0) + 10;
        }
        
        return $scores;
    }
    
    /**
     * Calculate location scores
     */
    private function calculateLocationScore($preferredLocations)
    {
        $scores = [];
        foreach ($preferredLocations as $location) {
            $scores[$location] = 5; // Strong preference for preferred locations
        }
        return $scores;
    }
    
    /**
     * Calculate price relevance score
     */
    private function calculatePriceScore($priceRange)
    {
        return $priceRange;
    }
    
    /**
     * Calculate price relevance for an individual ad
     */
    private function calculatePriceRelevanceScore($adPrice, $priceScore)
    {
        if (empty($priceScore)) {
            return 0;
        }
        
        $score = 0;
        
        if (isset($priceScore['min']) && $adPrice >= $priceScore['min']) {
            $score += 2;
        }
        
        if (isset($priceScore['max']) && $adPrice <= $priceScore['max']) {
            $score += 2;
        }
        
        return $score;
    }
    
    /**
     * Track user behavior
     */
    public function trackBehavior($userId, $behaviorType, $targetType, $targetId, $metadata = [])
    {
        return UserBehavior::create([
            'user_id' => $userId,
            'behavior_type' => $behaviorType,
            'target_type' => $targetType,
            'target_id' => $targetId,
            'metadata' => $metadata,
        ]);
    }
    
    /**
     * Get mood-based recommendations
     */
    public function getMoodBasedRecommendations(User $user, $mood = null, $limit = 10)
    {
        if (!$mood) {
            // If no mood is specified, use user's preferred categories
            $mood = UserPreference::getPreference($user->id, 'mood_state', 'normal');
        }
        
        $moodCategories = $this->getMoodToCategoryMapping($mood);
        
        if (empty($moodCategories)) {
            return $this->getPersonalizedRecommendations($user, $limit);
        }
        
        $ads = Ad::where('status', 'active')
            ->whereIn('category_id', $moodCategories)
            ->where('user_id', '!=', $user->id)
            ->with(['user', 'category', 'images'])
            ->limit($limit)
            ->get();
            
        return $ads;
    }
    
    /**
     * Mood to category mapping
     */
    private function getMoodToCategoryMapping($mood)
    {
        $moodCategoryMap = [
            'excited' => [1, 2, 3], // Electronics, Vehicles, Fashion
            'home' => [4, 5, 6],    // Furniture, Home, Appliances  
            'luxury' => [7, 8],     // Cars, Luxury items
            'practical' => [9, 10], // Tools, Books, Services
            'normal' => [],         // Default - use behavior instead
        ];
        
        return $moodCategoryMap[$mood] ?? [];
    }
}