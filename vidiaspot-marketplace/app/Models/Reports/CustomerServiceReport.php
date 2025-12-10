<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerServiceReport extends Model
{
    protected $fillable = [
        'support_tickets',
        'user_complaints',
        'feature_requests',
        'churn_analysis',
        'support_agent_performance',
        'period_start',
        'period_end',
        'metadata'
    ];

    protected $casts = [
        'support_tickets' => 'array',
        'user_complaints' => 'array',
        'feature_requests' => 'array',
        'churn_analysis' => 'array',
        'support_agent_performance' => 'array',
        'metadata' => 'array',
        'period_start' => 'datetime',
        'period_end' => 'datetime',
    ];
}