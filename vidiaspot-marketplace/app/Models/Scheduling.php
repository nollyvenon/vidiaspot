<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Scheduling extends Model
{
    protected $fillable = [
        'ad_id',
        'initiator_user_id',
        'recipient_user_id',
        'title',
        'description',
        'scheduled_datetime',
        'location',
        'status',
        'type',
        'participants',
        'preferences',
        'confirmed_at',
        'completed_at',
        'notes',
    ];

    protected $casts = [
        'ad_id' => 'integer',
        'initiator_user_id' => 'integer',
        'recipient_user_id' => 'integer',
        'title' => 'string',
        'description' => 'string',
        'scheduled_datetime' => 'datetime',
        'location' => 'string',
        'status' => 'string',
        'type' => 'string',
        'participants' => 'array',
        'preferences' => 'array',
        'confirmed_at' => 'datetime',
        'completed_at' => 'datetime',
        'notes' => 'string',
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
