<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class P2pCryptoEscrow extends Model
{
    use HasFactory;

    protected $fillable = [
        'p2p_order_id',
        'crypto_transaction_id',
        'amount',
        'status',
        'released_at',
        'refunded_at',
        'release_notes',
        'refund_notes',
    ];

    protected $casts = [
        'amount' => 'decimal:8',
        'released_at' => 'datetime',
        'refunded_at' => 'datetime',
    ];

    // Relations
    public function p2pOrder()
    {
        return $this->belongsTo(P2pCryptoOrder::class, 'p2p_order_id');
    }

    public function cryptoTransaction()
    {
        return $this->belongsTo(CryptoTransaction::class);
    }
}