<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VideoCall extends Model
{
    protected $fillable = [
        'ad_id',
        'initiator_user_id',
        'recipient_user_id',
        'room_id',
        'call_status',
        'call_type',
        'scheduled_at',
        'started_at',
        'ended_at',
        'duration',
        'participants',
        'settings',
    ];

    protected $casts = [
        'ad_id' => 'integer',
        'initiator_user_id' => 'integer',
        'recipient_user_id' => 'integer',
        'room_id' => 'string',
        'call_status' => 'string',
        'call_type' => 'string',
        'scheduled_at' => 'datetime',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'duration' => 'integer',
        'participants' => 'array',
        'settings' => 'array',
    ];

    // Relationships
    public function ad()
    {
        return $this->belongsTo(Ad::class);
    }

    public function initiator()
    {
        return $this->belongsTo(User::class, 'initiator_user_id');
    }

    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_user_id');
    }
}
