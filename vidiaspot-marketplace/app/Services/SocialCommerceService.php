<?php

namespace App\Services;

use App\Models\SocialPost;
use App\Models\SocialComment;
use App\Models\SocialLike;
use App\Models\SocialShare;
use App\Models\SocialFollow;
use App\Models\Ad;
use App\Models\VendorStore;
use App\Models\InsurancePolicy;
use App\Models\FoodVendor;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class SocialCommerceService
{
    /**
     * Create a social post
     */
    public function createPost($userId, $postData)
    {
        $post = SocialPost::create([
            'user_id' => $userId,
            'content' => $postData['content'],
            'post_type' => $postData['post_type'] ?? 'text',
            'media_url' => $postData['media_url'] ?? null,
            'attached_product_id' => $postData['attached_product_id'] ?? null,
            'attached_product_type' => $postData['attached_product_type'] ?? null,
            'attached_vendor_store_id' => $postData['attached_vendor_store_id'] ?? null,
            'attached_food_vendor_id' => $postData['attached_food_vendor_id'] ?? null,
            'attached_insurance_provider_id' => $postData['attached_insurance_provider_id'] ?? null,
            'location' => $postData['location'] ?? null,
            'latitude' => $postData['latitude'] ?? null,
            'longitude' => $postData['longitude'] ?? null,
            'is_live' => $postData['is_live'] ?? false,
            'live_end_time' => $postData['live_end_time'] ?? null,
            'is_promoted' => $postData['is_promoted'] ?? false,
            'influencer_status' => $postData['influencer_status'] ?? 'regular',
            'is_approved' => true, // Auto-approve for now
            'post_settings' => $postData['post_settings'] ?? [],
        ]);

        // Update user's reputation score
        $this->updateUserReputation($userId, 10); // 10 points for creating post

        return $post;
    }

    /**
     * Get social feed for user (posts from followed entities)
     */
    public function getSocialFeed($userId, $page = 1, $limit = 20)
    {
        $followingIds = SocialFollow::where('follower_id', $userId)
                                   ->pluck('followed_id')
                                   ->toArray();

        // Get posts from users this user follows
        $postsQuery = SocialPost::where('is_approved', true)
                                ->whereIn('user_id', $followingIds)
                                ->orderBy('created_at', 'desc');

        $posts = $postsQuery->offset(($page - 1) * $limit)
                           ->limit($limit)
                           ->get();

        // Enrich posts with interaction data
        foreach ($posts as $post) {
            $post->likes_count = $post->likes()->count();
            $post->comments_count = $post->comments()->count();
            $post->shares_count = $post->shares()->count();
            $post->is_liked_by_current_user = $post->isLikedByUser($userId);
        }

        return $posts;
    }

    /**
     * Get trending posts
     */
    public function getTrendingPosts($limit = 20)
    {
        return SocialPost::where('is_approved', true)
                         ->where('engagement_score', '>', 50) // Only posts with decent engagement
                         ->orderBy('engagement_score', 'desc')
                         ->limit($limit)
                         ->get();
    }

    /**
     * Like a post
     */
    public function likePost($userId, $postId)
    {
        $post = SocialPost::findOrFail($postId);
        
        $like = SocialLike::firstOrCreate([
            'user_id' => $userId,
            'social_post_id' => $postId,
        ]);

        // Update post engagement score
        $post->increment('engagement_score', 5);
        $post->increment('reputation_points', 1);

        // Update user's reputation
        $this->updateUserReputation($userId, 1); // 1 point for liking

        return $like;
    }

    /**
     * Unlike a post
     */
    public function unlikePost($userId, $postId)
    {
        $post = SocialPost::findOrFail($postId);
        
        $result = SocialLike::where('user_id', $userId)
                           ->where('social_post_id', $postId)
                           ->delete();

        if ($result) {
            // Update post engagement score
            $post->decrement('engagement_score', 5);
            $post->decrement('reputation_points', 1);
        }

        return $result;
    }

    /**
     * Comment on a post
     */
    public function commentOnPost($userId, $postId, $commentData)
    {
        $post = SocialPost::findOrFail($postId);

        $comment = SocialComment::create([
            'user_id' => $userId,
            'social_post_id' => $postId,
            'parent_comment_id' => $commentData['parent_comment_id'] ?? null,
            'content' => $commentData['content'],
            'is_reply' => !empty($commentData['parent_comment_id']),
            'reply_to_user_id' => $commentData['reply_to_user_id'] ?? null,
        ]);

        // Update post engagement score
        $post->increment('engagement_score', 3);
        $post->increment('reputation_points', 1);

        // Update user's reputation
        $this->updateUserReputation($userId, 2); // 2 points for commenting

        return $comment;
    }

    /**
     * Share a post
     */
    public function sharePost($userId, $postId, $shareData = [])
    {
        $post = SocialPost::findOrFail($postId);

        $share = SocialShare::create([
            'user_id' => $userId,
            'social_post_id' => $postId,
            'target_user_id' => $shareData['target_user_id'] ?? null,
            'share_platform' => $shareData['share_platform'] ?? 'internal',
        ]);

        // Update post engagement score
        $post->increment('engagement_score', 10);
        $post->increment('reputation_points', 2);

        // Update user's reputation
        $this->updateUserReputation($userId, 3); // 3 points for sharing

        return $share;
    }

    /**
     * Follow an entity (user, vendor store, etc.)
     */
    public function followEntity($userId, $entityId, $entityType = 'user')
    {
        $follow = SocialFollow::firstOrCreate([
            'follower_id' => $userId,
            'followed_id' => $entityId,
            'follow_type' => $entityType,
        ]);

        if ($follow->wasRecentlyCreated) {
            // Update user's reputation
            $this->updateUserReputation($userId, 5); // 5 points for following
        }

        return $follow;
    }

    /**
     * Unfollow an entity
     */
    public function unfollowEntity($userId, $entityId, $entityType = 'user')
    {
        return SocialFollow::where('follower_id', $userId)
                          ->where('followed_id', $entityId)
                          ->where('follow_type', $entityType)
                          ->delete();
    }

    /**
     * Get user's followers
     */
    public function getUserFollowers($userId)
    {
        return SocialFollow::where('followed_id', $userId)
                          ->where('follow_type', 'user')
                          ->with('follower')
                          ->get();
    }

    /**
     * Get what user is following
     */
    public function getUserFollowing($userId)
    {
        return SocialFollow::where('follower_id', $userId)
                          ->with(['followed'])
                          ->get();
    }

    /**
     * Get social proof (friends who bought/liked)
     */
    public function getSocialProof($userId, $productId, $productType)
    {
        // Get friends of the user
        $followingUserIds = SocialFollow::where('follower_id', $userId)
                                        ->where('follow_type', 'user')
                                        ->pluck('followed_id');

        // Get posts from friends related to this product
        $proofPosts = SocialPost::whereIn('user_id', $followingUserIds)
                               ->where('attached_product_id', $productId)
                               ->where('attached_product_type', $productType)
                               ->where('post_type', '!=', 'event') // Exclude events
                               ->with(['user:id,name,avatar'])
                               ->limit(10)
                               ->get();

        return [
            'count' => $proofPosts->count(),
            'friends' => $proofPosts->map(function($post) {
                return [
                    'user' => $post->user,
                    'action' => $post->post_type,
                    'date' => $post->created_at->diffForHumans()
                ];
            }),
            'message' => $proofPosts->count() > 0 ? 
                        'Friends have engaged with this product recently' : 
                        'Be the first among your friends to engage with this product!'
        ];
    }

    /**
     * Get group buying opportunities
     */
    public function getGroupBuyingOpportunities($userId = null)
    {
        // This would typically look for posts with group buying tags or events
        $query = SocialPost::where('is_approved', true)
                          ->where('post_type', 'event')
                          ->where('attached_product_type', 'group_buy')
                          ->where('is_live', true)
                          ->where('live_end_time', '>=', now());

        if ($userId) {
            $query = $query->where('user_id', '!=', $userId); // Don't show own posts
        }

        return $query->with(['user:id,name,avatar'])->get();
    }

    /**
     * Get community forums/posts for specific categories
     */
    public function getCategoryCommunity($categoryId, $page = 1, $limit = 20)
    {
        $posts = SocialPost::where('is_approved', true)
                          ->where(function($q) use ($categoryId) {
                              $q->where('attached_product_type', 'ad')
                                ->where('attached_product_id', $categoryId);
                          })
                          ->orWhere(function($q) use ($categoryId) {
                              $q->where('content', 'like', '%#' . $categoryId . '%');
                          })
                          ->orderBy('created_at', 'desc')
                          ->offset(($page - 1) * $limit)
                          ->limit($limit)
                          ->with(['user:id,name,avatar', 'likes', 'comments'])
                          ->get();

        return $posts;
    }

    /**
     * Get user's reputation score
     */
    public function getUserReputation($userId)
    {
        $user = \App\Models\User::find($userId);
        return $user->reputation_score ?? 0;
    }

    /**
     * Get influencers in the system
     */
    public function getInfluencers($limit = 20)
    {
        return SocialPost::select('user_id')
                    ->where('is_promoted', true)
                    ->orWhere('influencer_status', '!=', 'regular')
                    ->groupBy('user_id')
                    ->with(['user:id,name,avatar,reputation_score'])
                    ->limit($limit)
                    ->get();
    }

    /**
     * Get live shopping events
     */
    public function getLiveShoppingEvents()
    {
        return SocialPost::where('is_approved', true)
                        ->where('post_type', 'live_shopping')
                        ->where('is_live', true)
                        ->where('live_end_time', '>', now())
                        ->with(['user:id,name,avatar', 'attachedProduct'])
                        ->get();
    }

    /**
     * Get user-generated content campaigns
     */
    public function getUserGeneratedContentCampaigns()
    {
        return SocialPost::where('is_approved', true)
                        ->where('post_type', 'campaign')
                        ->where('attached_product_type', 'ugc')
                        ->orderBy('created_at', 'desc')
                        ->get();
    }

    /**
     * Update user's reputation score
     */
    private function updateUserReputation($userId, $points)
    {
        $user = \App\Models\User::find($userId);
        $currentScore = $user->reputation_score ?? 0;
        $user->update(['reputation_score' => $currentScore + $points]);
    }

    /**
     * Get attached product based on type
     */
    public function getAttachedProduct($productId, $productType)
    {
        switch ($productType) {
            case 'ad':
                return Ad::find($productId);
            case 'vendor_store':
                return VendorStore::find($productId);
            case 'insurance_policy':
                return InsurancePolicy::find($productId);
            case 'food_item':
                // For food items, we might need to fetch from menu items
                return \App\Models\FoodMenuItem::find($productId);
            case 'food_vendor':
                return FoodVendor::find($productId);
            default:
                return null;
        }
    }
}