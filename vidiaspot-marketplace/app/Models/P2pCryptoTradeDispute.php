<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class P2pCryptoTradeDispute extends Model
{
    use HasFactory;

    protected $fillable = [
        'p2p_order_id',
        'initiator_user_id',
        'dispute_type',
        'description',
        'status',
        'resolution',
        'resolution_notes',
        'resolved_by',
        'resolved_at',
        'evidence',
        'resolver_id',
        'refund_amount',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
        'evidence' => 'array',
        'refund_amount' => 'decimal:8',
    ];

    // Relations
    public function p2pOrder()
    {
        return $this->belongsTo(P2pCryptoOrder::class, 'p2p_order_id');
    }

    public function initiator()
    {
        return $this->belongsTo(User::class, 'initiator_user_id');
    }

    public function resolver()
    {
        return $this->belongsTo(User::class, 'resolver_id');
    }
}