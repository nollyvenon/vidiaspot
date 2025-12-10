<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AutomatedAlertReport extends Model
{
    protected $fillable = [
        'alert_type',
        'severity',
        'description',
        'data',
        'is_resolved',
        'triggered_at',
        'resolved_at',
        'metadata'
    ];

    protected $casts = [
        'data' => 'array',
        'metadata' => 'array',
        'is_resolved' => 'boolean',
        'triggered_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];
}