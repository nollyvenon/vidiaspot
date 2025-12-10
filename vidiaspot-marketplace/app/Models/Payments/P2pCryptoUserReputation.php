<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class P2pCryptoUserReputation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'counterparty_id',
        'order_id',
        'rating',
        'review',
        'review_tags',
        'is_trusted',
        'trade_count',
        'completion_rate',
    ];

    protected $casts = [
        'rating' => 'decimal:2',
        'completion_rate' => 'decimal:2',
        'review_tags' => 'array',
        'is_trusted' => 'boolean',
        'trade_count' => 'integer',
    ];

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function counterparty()
    {
        return $this->belongsTo(User::class, 'counterparty_id');
    }

    public function order()
    {
        return $this->belongsTo(P2pCryptoOrder::class, 'order_id');
    }
}