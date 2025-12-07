<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Chat extends Model
{
    protected $fillable = [
        'sender_id',
        'receiver_id',
        'message',
        'is_read',
        'is_archived',
        'messageable_type',
        'messageable_id',
        'metadata',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'is_archived' => 'boolean',
        'metadata' => 'array',
    ];

    /**
     * Get the sender of the message.
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Get the receiver of the message.
     */
    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    /**
     * Get the model that the chat message belongs to (polymorphic relationship).
     */
    public function messageable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope to get messages between two users.
     */
    public function scopeBetweenUsers($query, $userId1, $userId2)
    {
        return $query->where(function($q) use ($userId1, $userId2) {
            $q->where('sender_id', $userId1)->where('receiver_id', $userId2);
        })->orWhere(function($q) use ($userId1, $userId2) {
            $q->where('sender_id', $userId2)->where('receiver_id', $userId1);
        })->orderBy('created_at', 'asc');
    }

    /**
     * Scope to get messages by user.
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where(function($q) use ($userId) {
            $q->where('sender_id', $userId)->orWhere('receiver_id', $userId);
        })->orderBy('created_at', 'desc');
    }

    /**
     * Scope to get unread messages for a user.
     */
    public function scopeUnreadByUser($query, $userId)
    {
        return $query->where('receiver_id', $userId)->where('is_read', false);
    }

    /**
     * Mark messages as read for a user.
     */
    public static function markAsRead($userId, $senderId = null)
    {
        $query = self::where('receiver_id', $userId)->where('is_read', false);

        if ($senderId) {
            $query = $query->where('sender_id', $senderId);
        }

        return $query->update(['is_read' => true]);
    }
}
