<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SocialComment extends Model
{
    protected $fillable = [
        'user_id',
        'social_post_id',
        'parent_comment_id', // for comment threads
        'content',
        'is_reply',
        'reply_to_user_id', // when replying to specific user
        'reputation_points',
    ];

    /**
     * Get the user who created the comment
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the post this comment belongs to
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(SocialPost::class, 'social_post_id');
    }

    /**
     * Get the parent comment (if this is a reply)
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(SocialComment::class, 'parent_comment_id');
    }

    /**
     * Get replies to this comment
     */
    public function replies()
    {
        return $this->hasMany(SocialComment::class, 'parent_comment_id');
    }

    /**
     * Get the user being replied to
     */
    public function repliedToUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reply_to_user_id');
    }
}
