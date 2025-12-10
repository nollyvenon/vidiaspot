<?php

namespace App\Models;

class DeliveryReport extends BaseModel
{
    protected $fillable = [
        'user_id',
        'report_date',
        'report_data',
        'status',
        'on_time_deliveries',
        'late_deliveries',
        'delivery_success_rate',
        'average_delivery_time_hours',
        'total_deliveries',
        'total_distance_km',
        'fuel_cost',
        'currency'
    ];

    protected $casts = [
        'report_date' => 'date',
        'report_data' => 'array',
        'on_time_deliveries' => 'integer',
        'late_deliveries' => 'integer',
        'delivery_success_rate' => 'float',
        'average_delivery_time_hours' => 'float',
        'total_deliveries' => 'integer',
        'total_distance_km' => 'float',
        'fuel_cost' => 'decimal:2'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}