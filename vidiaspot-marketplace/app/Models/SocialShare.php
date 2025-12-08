<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SocialShare extends Model
{
    protected $fillable = [
        'user_id',
        'social_post_id',
        'target_user_id', // for sharing with specific friend
        'share_platform', // 'internal', 'facebook', 'twitter', 'whatsapp', etc.
        'reputation_points',
    ];

    /**
     * Get the user who shared the post
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the post that was shared
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(SocialPost::class, 'social_post_id');
    }

    /**
     * Get the target user (if shared with specific person)
     */
    public function targetUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'target_user_id');
    }
}
