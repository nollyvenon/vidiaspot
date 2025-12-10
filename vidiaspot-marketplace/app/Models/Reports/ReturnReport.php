<?php

namespace App\Models;

class ReturnReport extends BaseModel
{
    protected $fillable = [
        'user_id',
        'report_date',
        'report_data',
        'status',
        'total_returns',
        'processed_returns',
        'pending_returns',
        'return_rate_percent',
        'average_resolution_time_days',
        'refund_amount',
        'exchange_rate_percent',
        'return_reasons',
        'currency'
    ];

    protected $casts = [
        'report_date' => 'date',
        'report_data' => 'array',
        'total_returns' => 'integer',
        'processed_returns' => 'integer',
        'pending_returns' => 'integer',
        'return_rate_percent' => 'float',
        'average_resolution_time_days' => 'float',
        'refund_amount' => 'decimal:2',
        'exchange_rate_percent' => 'float',
        'return_reasons' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}