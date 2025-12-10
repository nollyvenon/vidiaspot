<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MobileDevice extends Model
{
    protected $fillable = [
        'user_id',
        'device_id',
        'device_token',
        'platform',
        'os_version',
        'app_version',
        'manufacturer',
        'model',
        'last_active_at',
        'push_enabled',
        'push_topics'
    ];

    protected $casts = [
        'last_active_at' => 'datetime',
        'push_enabled' => 'boolean',
        'push_topics' => 'array',
        'user_id' => 'integer',
        'device_id' => 'string',
        'device_token' => 'string',
        'platform' => 'string',
        'os_version' => 'string',
        'app_version' => 'string',
        'manufacturer' => 'string',
        'model' => 'string',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}