<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $fillable = [
        'ad_id',
        'user1_id',
        'user2_id',
        'title',
        'description',
        'is_active',
        'participants_info',
        'last_message_at',
    ];

    protected $casts = [
        'ad_id' => 'integer',
        'user1_id' => 'integer',
        'user2_id' => 'integer',
        'title' => 'string',
        'description' => 'string',
        'is_active' => 'boolean',
        'participants_info' => 'array',
        'last_message_at' => 'datetime',
    ];

    // Relationships
    public function ad()
    {
        return $this->belongsTo(Ad::class);
    }

    public function user1()
    {
        return $this->belongsTo(User::class, 'user1_id');
    }

    public function user2()
    {
        return $this->belongsTo(User::class, 'user2_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'conversation_id');
    }
}
