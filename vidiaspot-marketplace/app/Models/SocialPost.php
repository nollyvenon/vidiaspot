<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SocialPost extends Model
{
    protected $fillable = [
        'user_id',
        'content',
        'post_type', // 'text', 'image', 'video', 'product_review', 'live_shopping', 'event'
        'media_url',
        'attached_product_id', // for product reviews/shopping
        'attached_product_type', // 'ad', 'vendor_store', 'insurance_policy', 'food_item'
        'attached_vendor_store_id', // for vendor store posts
        'attached_food_vendor_id', // for food vendor posts
        'attached_insurance_provider_id', // for insurance provider posts
        'location',
        'latitude',
        'longitude',
        'is_live', // for live shopping events
        'live_end_time',
        'is_promoted', // for influencer posts
        'influencer_status', // 'regular', 'verified', 'partner'
        'engagement_score', // for reputation system
        'reputation_points',
        'is_approved',
        'post_settings',
    ];

    protected $casts = [
        'location' => 'array',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'is_live' => 'boolean',
        'live_end_time' => 'datetime',
        'is_promoted' => 'boolean',
        'is_approved' => 'boolean',
        'post_settings' => 'array',
        'engagement_score' => 'integer',
        'reputation_points' => 'integer',
    ];

    /**
     * Get the user who created the post
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the comments on this post
     */
    public function comments(): HasMany
    {
        return $this->hasMany(SocialComment::class);
    }

    /**
     * Get the likes on this post
     */
    public function likes(): HasMany
    {
        return $this->hasMany(SocialLike::class);
    }

    /**
     * Get the shares of this post
     */
    public function shares(): HasMany
    {
        return $this->hasMany(SocialShare::class);
    }

    /**
     * Check if user has liked this post
     */
    public function isLikedByUser($userId)
    {
        return $this->likes()->where('user_id', $userId)->exists();
    }

    /**
     * Scope to get only approved posts
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    /**
     * Scope to get posts by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('post_type', $type);
    }

    /**
     * Scope to get live shopping events
     */
    public function scopeLiveShopping($query)
    {
        return $query->where('post_type', 'live_shopping')
                    ->where('is_live', true)
                    ->where('live_end_time', '>', now());
    }
}
