<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DuplicateDetection extends Model
{
    use HasFactory;

    protected $fillable = [
        'primary_ad_id',
        'duplicate_ad_id',
        'user_id',
        'category_id',
        'similarity_score',
        'matching_attributes',
        'image_similarity_data',
        'text_similarity_data',
        'detection_method',
        'reasoning',
        'status',
        'detected_at',
        'resolved_at',
        'resolved_by_user_id',
        'resolution_notes',
        'is_confirmed_duplicate',
        'is_false_positive',
        'confidence_factors',
        'recommended_action',
        'action_taken',
        'action_taken_at',
    ];

    protected $casts = [
        'similarity_score' => 'decimal:2',
        'matching_attributes' => 'array',
        'image_similarity_data' => 'array',
        'text_similarity_data' => 'array',
        'confidence_factors' => 'array',
        'recommended_action' => 'array',
        'is_confirmed_duplicate' => 'boolean',
        'is_false_positive' => 'boolean',
        'action_taken' => 'boolean',
        'detected_at' => 'datetime',
        'resolved_at' => 'datetime',
        'action_taken_at' => 'datetime',
    ];

    public function primaryAd(): BelongsTo
    {
        return $this->belongsTo(Ad::class, 'primary_ad_id');
    }

    public function duplicateAd(): BelongsTo
    {
        return $this->belongsTo(Ad::class, 'duplicate_ad_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function resolvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by_user_id');
    }
}