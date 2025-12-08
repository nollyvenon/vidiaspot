<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DemandForecast extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'location_id',
        'date_range',
        'forecast_date',
        'predicted_demand',
        'actual_demand',
        'confidence_level',
        'forecast_data',
        'factors',
    ];

    protected $casts = [
        'predicted_demand' => 'integer',
        'actual_demand' => 'integer',
        'confidence_level' => 'decimal:2',
        'forecast_data' => 'array',
        'factors' => 'array',
        'forecast_date' => 'date',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(City::class, 'location_id');
    }
}
