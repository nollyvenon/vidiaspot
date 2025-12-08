<?php

namespace App\Http\Controllers;

use App\Services\SocialCommerceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SocialCommerceController extends Controller
{
    protected $socialCommerceService;

    public function __construct(SocialCommerceService $socialCommerceService)
    {
        $this->socialCommerceService = $socialCommerceService;
    }

    /**
     * Create a social post
     */
    public function createPost(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User must be logged in'
            ], 401);
        }

        $request->validate([
            'content' => 'required|string|max:5000',
            'post_type' => 'in:text,image,video,product_review,live_shopping,event',
            'media_url' => 'nullable|url',
            'attached_product_id' => 'nullable|integer',
            'attached_product_type' => 'nullable|in:ad,vendor_store,insurance_policy,food_item,food_vendor',
            'attached_vendor_store_id' => 'nullable|integer|exists:vendor_stores,id',
            'attached_food_vendor_id' => 'nullable|integer|exists:food_vendors,id',
            'attached_insurance_provider_id' => 'nullable|integer|exists:insurance_providers,id',
            'is_live' => 'boolean',
            'is_promoted' => 'boolean',
            'location' => 'array',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        try {
            $post = $this->socialCommerceService->createPost($user->id, $request->all());

            return response()->json([
                'success' => true,
                'message' => 'Post created successfully',
                'post' => $post
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create post: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get social feed
     */
    public function getFeed(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User must be logged in'
            ], 401);
        }

        $page = $request->get('page', 1);
        $limit = $request->get('limit', 20);

        $feed = $this->socialCommerceService->getSocialFeed($user->id, $page, $limit);

        return response()->json([
            'success' => true,
            'feed' => $feed,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => count($feed)
            ]
        ]);
    }

    /**
     * Get trending posts
     */
    public function getTrending(Request $request)
    {
        $limit = $request->get('limit', 20);

        $trending = $this->socialCommerceService->getTrendingPosts($limit);

        return response()->json([
            'success' => true,
            'trending_posts' => $trending
        ]);
    }

    /**
     * Like a post
     */
    public function likePost($postId)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User must be logged in'
            ], 401);
        }

        try {
            $like = $this->socialCommerceService->likePost($user->id, $postId);

            return response()->json([
                'success' => true,
                'message' => 'Post liked successfully',
                'like' => $like
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to like post: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Unlike a post
     */
    public function unlikePost($postId)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User must be logged in'
            ], 401);
        }

        try {
            $result = $this->socialCommerceService->unlikePost($user->id, $postId);

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Post unliked successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Post was not liked by this user'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to unlike post: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Comment on a post
     */
    public function commentOnPost(Request $request, $postId)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User must be logged in'
            ], 401);
        }

        $request->validate([
            'content' => 'required|string|max:2000',
            'parent_comment_id' => 'nullable|integer',
            'reply_to_user_id' => 'nullable|integer|exists:users,id',
        ]);

        try {
            $comment = $this->socialCommerceService->commentOnPost($user->id, $postId, $request->all());

            return response()->json([
                'success' => true,
                'message' => 'Comment added successfully',
                'comment' => $comment
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add comment: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Share a post
     */
    public function sharePost(Request $request, $postId)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User must be logged in'
            ], 401);
        }

        $request->validate([
            'target_user_id' => 'nullable|integer|exists:users,id',
            'share_platform' => 'in:internal,facebook,twitter,whatsapp,instagram',
        ]);

        try {
            $share = $this->socialCommerceService->sharePost($user->id, $postId, $request->all());

            return response()->json([
                'success' => true,
                'message' => 'Post shared successfully',
                'share' => $share
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to share post: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Follow an entity
     */
    public function followEntity(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User must be logged in'
            ], 401);
        }

        $request->validate([
            'entity_id' => 'required|integer',
            'entity_type' => 'in:user,vendor_store,insurance_provider,food_vendor',
        ]);

        try {
            $follow = $this->socialCommerceService->followEntity($user->id, $request->entity_id, $request->entity_type);

            return response()->json([
                'success' => true,
                'message' => 'Followed successfully',
                'follow' => $follow
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to follow: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Unfollow an entity
     */
    public function unfollowEntity(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User must be logged in'
            ], 401);
        }

        $request->validate([
            'entity_id' => 'required|integer',
            'entity_type' => 'in:user,vendor_store,insurance_provider,food_vendor',
        ]);

        try {
            $result = $this->socialCommerceService->unfollowEntity($user->id, $request->entity_id, $request->entity_type);

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Unfollowed successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Not following this entity'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to unfollow: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get user's followers
     */
    public function getFollowers($userId)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User must be logged in'
            ], 401);
        }

        $followers = $this->socialCommerceService->getUserFollowers($userId);

        return response()->json([
            'success' => true,
            'followers' => $followers,
            'count' => $followers->count()
        ]);
    }

    /**
     * Get user's following
     */
    public function getFollowing($userId)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User must be logged in'
            ], 401);
        }

        $following = $this->socialCommerceService->getUserFollowing($userId);

        return response()->json([
            'success' => true,
            'following' => $following,
            'count' => $following->count()
        ]);
    }

    /**
     * Get social proof for a product
     */
    public function getSocialProof($productId, $productType)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User must be logged in'
            ], 401);
        }

        $proof = $this->socialCommerceService->getSocialProof($user->id, $productId, $productType);

        return response()->json([
            'success' => true,
            'social_proof' => $proof
        ]);
    }

    /**
     * Get group buying opportunities
     */
    public function getGroupBuying($userId = null)
    {
        $user = Auth::user();
        $userId = $user ? $user->id : $userId;

        $opportunities = $this->socialCommerceService->getGroupBuyingOpportunities($userId);

        return response()->json([
            'success' => true,
            'opportunities' => $opportunities,
            'count' => $opportunities->count()
        ]);
    }

    /**
     * Get community posts for a category
     */
    public function getCategoryCommunity(Request $request, $categoryId)
    {
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 20);

        $community = $this->socialCommerceService->getCategoryCommunity($categoryId, $page, $limit);

        return response()->json([
            'success' => true,
            'category' => $categoryId,
            'posts' => $community,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => count($community)
            ]
        ]);
    }

    /**
     * Get user's reputation
     */
    public function getUserReputation($userId = null)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User must be logged in'
            ], 401);
        }

        if (!$userId) {
            $userId = $user->id;
        }

        $reputation = $this->socialCommerceService->getUserReputation($userId);

        return response()->json([
            'success' => true,
            'user_id' => $userId,
            'reputation_score' => $reputation
        ]);
    }

    /**
     * Get influencers
     */
    public function getInfluencers(Request $request)
    {
        $limit = $request->get('limit', 20);

        $influencers = $this->socialCommerceService->getInfluencers($limit);

        return response()->json([
            'success' => true,
            'influencers' => $influencers,
            'count' => $influencers->count()
        ]);
    }

    /**
     * Get live shopping events
     */
    public function getLiveShoppingEvents()
    {
        $events = $this->socialCommerceService->getLiveShoppingEvents();

        return response()->json([
            'success' => true,
            'live_events' => $events,
            'count' => $events->count()
        ]);
    }

    /**
     * Get user-generated content campaigns
     */
    public function getUserGeneratedContentCampaigns()
    {
        $campaigns = $this->socialCommerceService->getUserGeneratedContentCampaigns();

        return response()->json([
            'success' => true,
            'campaigns' => $campaigns,
            'count' => $campaigns->count()
        ]);
    }
}
