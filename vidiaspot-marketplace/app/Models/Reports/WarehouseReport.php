<?php

namespace App\Models;

class WarehouseReport extends BaseModel
{
    protected $fillable = [
        'user_id',
        'report_date',
        'report_data',
        'status',
        'total_inventory_items',
        'inventory_turnover_rate',
        'space_utilization_percent',
        'storage_cost',
        'orders_processed',
        'fulfillment_accuracy_rate',
        'average_processing_time',
        'currency'
    ];

    protected $casts = [
        'report_date' => 'date',
        'report_data' => 'array',
        'total_inventory_items' => 'integer',
        'inventory_turnover_rate' => 'float',
        'space_utilization_percent' => 'float',
        'storage_cost' => 'decimal:2',
        'orders_processed' => 'integer',
        'fulfillment_accuracy_rate' => 'float',
        'average_processing_time' => 'float'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}