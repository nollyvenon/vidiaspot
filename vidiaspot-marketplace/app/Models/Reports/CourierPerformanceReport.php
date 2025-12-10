<?php

namespace App\Models;

class CourierPerformanceReport extends BaseModel
{
    protected $fillable = [
        'user_id',
        'report_date',
        'report_data',
        'status',
        'total_shipments',
        'on_time_deliveries',
        'successful_deliveries',
        'delivery_success_rate',
        'average_delivery_time_hours',
        'total_revenue',
        'revenue_per_shipment',
        'cost_per_shipment',
        'profit_margin',
        'courier_partner_id',
        'currency'
    ];

    protected $casts = [
        'report_date' => 'date',
        'report_data' => 'array',
        'total_shipments' => 'integer',
        'on_time_deliveries' => 'integer',
        'successful_deliveries' => 'integer',
        'delivery_success_rate' => 'float',
        'average_delivery_time_hours' => 'float',
        'total_revenue' => 'decimal:2',
        'revenue_per_shipment' => 'decimal:2',
        'cost_per_shipment' => 'decimal:2',
        'profit_margin' => 'float',
        'courier_partner_id' => 'integer'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function courierPartner()
    {
        return $this->belongsTo(CourierPartner::class);
    }
}