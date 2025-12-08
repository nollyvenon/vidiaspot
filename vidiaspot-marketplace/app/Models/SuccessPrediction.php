<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SuccessPrediction extends Model
{
    use HasFactory;

    protected $fillable = [
        'ad_id',
        'user_id',
        'category_id',
        'location_id',
        'ad_type',
        'success_probability',
        'success_factors',
        'improvement_suggestions',
        'predicted_metrics',
        'risk_factors',
        'confidence_level',
        'predicted_duration',
        'engagement_score',
        'conversion_probability',
        'optimization_tips',
        'comparative_analysis',
        'prediction_generated_at',
        'expires_at',
        'is_actual_performance_recorded',
        'actual_views',
        'actual_responses',
        'actual_duration_to_sale',
        'prediction_was_accurate',
    ];

    protected $casts = [
        'success_probability' => 'decimal:2',
        'engagement_score' => 'decimal:2',
        'conversion_probability' => 'decimal:2',
        'predicted_metrics' => 'array',
        'risk_factors' => 'array',
        'comparative_analysis' => 'array',
        'predicted_duration' => 'integer',
        'actual_views' => 'integer',
        'actual_responses' => 'integer',
        'actual_duration_to_sale' => 'integer',
        'is_actual_performance_recorded' => 'boolean',
        'prediction_was_accurate' => 'boolean',
        'prediction_generated_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function ad(): BelongsTo
    {
        return $this->belongsTo(Ad::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(City::class, 'location_id');
    }
}
