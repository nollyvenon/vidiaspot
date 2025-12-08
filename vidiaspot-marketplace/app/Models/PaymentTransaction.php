<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaymentTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'payment_gateway',
        'transaction_reference',
        'user_id',
        'ad_id',
        'subscription_id',
        'type',
        'amount',
        'currency',
        'status',
        'gateway_response',
        'metadata',
        'paid_at'
    ];

    protected $casts = [
        'gateway_response' => 'array',
        'metadata' => 'array',
        'paid_at' => 'datetime',
        'amount' => 'decimal:2'
    ];

    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    // Relationship to User
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Relationship to Ad
    public function ad(): BelongsTo
    {
        return $this->belongsTo(Ad::class);
    }

    // Relationship to Subscription
    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }
}
