<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NegotiationAssistant extends Model
{
    use HasFactory;

    protected $fillable = [
        'ad_id',
        'buyer_id',
        'seller_id',
        'message_thread_id',
        'initial_offer',
        'counter_offer',
        'accepted_price',
        'recommended_price',
        'status',
        'negotiation_stage',
        'negotiation_history',
        'recommendations',
        'factors_considered',
        'sentiment_analysis',
        'offer_count',
        'acceptance_probability',
        'ai_suggestions',
        'negotiation_strategy',
        'expires_at',
    ];

    protected $casts = [
        'initial_offer' => 'decimal:2',
        'counter_offer' => 'decimal:2',
        'accepted_price' => 'decimal:2',
        'recommended_price' => 'decimal:2',
        'negotiation_history' => 'array',
        'recommendations' => 'array',
        'factors_considered' => 'array',
        'sentiment_analysis' => 'array',
        'offer_count' => 'integer',
        'acceptance_probability' => 'decimal:2',
        'expires_at' => 'datetime',
    ];

    public function ad(): BelongsTo
    {
        return $this->belongsTo(Ad::class);
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function messageThread(): BelongsTo
    {
        return $this->belongsTo(Message::class, 'message_thread_id');
    }
}
