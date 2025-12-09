<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CryptoTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'crypto_currency_id',
        'transaction_type',
        'amount',
        'rate',
        'total_value',
        'status',
        'related_transaction_id',
        'executed_at',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:8',
        'rate' => 'decimal:8',
        'total_value' => 'decimal:8',
        'executed_at' => 'datetime',
    ];

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cryptoCurrency()
    {
        return $this->belongsTo(CryptoCurrency::class);
    }

    public function relatedTransaction()
    {
        return $this->belongsTo(CryptoTransaction::class, 'related_transaction_id');
    }

    public function p2pCryptoEscrows()
    {
        return $this->hasMany(P2pCryptoEscrow::class);
    }
}