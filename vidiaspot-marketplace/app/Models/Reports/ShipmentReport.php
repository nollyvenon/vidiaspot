<?php

namespace App\Models\Reports;

use App\Models\BaseModel;

class ShipmentReport extends BaseModel
{
    protected $fillable = [
        'user_id',
        'report_date',
        'report_data',
        'status',
        'total_shipments',
        'successful_deliveries',
        'failed_deliveries',
        'average_delivery_time',
        'total_cost',
        'currency'
    ];

    protected $casts = [
        'report_date' => 'date',
        'report_data' => 'array',
        'total_shipments' => 'integer',
        'successful_deliveries' => 'integer',
        'failed_deliveries' => 'integer',
        'average_delivery_time' => 'float',
        'total_cost' => 'decimal:2'
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}