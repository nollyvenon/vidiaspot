<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LiveDashboardReport extends Model
{
    protected $fillable = [
        'market_data',
        'active_sessions',
        'pending_transactions',
        'system_health',
        'alerts',
        'last_updated',
        'metadata'
    ];

    protected $casts = [
        'market_data' => 'array',
        'active_sessions' => 'array',
        'pending_transactions' => 'array',
        'system_health' => 'array',
        'alerts' => 'array',
        'metadata' => 'array',
        'last_updated' => 'datetime',
    ];
}