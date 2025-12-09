<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'ad_id',
        'conversation_id',
        'content',
        'message_type',
        'language',
        'translated_content',
        'status',
        'is_read',
        'read_at',
        'metadata',
    ];

    protected $casts = [
        'sender_id' => 'integer',
        'receiver_id' => 'integer',
        'ad_id' => 'integer',
        'conversation_id' => 'integer',
        'content' => 'string',
        'message_type' => 'string',
        'language' => 'string',
        'translated_content' => 'string',
        'status' => 'string',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'metadata' => 'array',
    ];

    // Relationships
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function ad()
    {
        return $this->belongsTo(Ad::class);
    }
}
