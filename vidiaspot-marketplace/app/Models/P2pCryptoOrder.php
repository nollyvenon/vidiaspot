<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class P2pCryptoOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_id',
        'buyer_id',
        'crypto_currency_id',
        'order_type',
        'amount',
        'price_per_unit',
        'total_amount',
        'payment_method',
        'status',
        'matched_at',
        'completed_at',
        'cancelled_at',
        'terms_and_conditions',
        'additional_notes',
        'crypto_transaction_id',
        'payment_transaction_id',
    ];

    protected $casts = [
        'amount' => 'decimal:8',
        'price_per_unit' => 'decimal:8',
        'total_amount' => 'decimal:8',
        'matched_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    // Relations
    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function cryptoCurrency()
    {
        return $this->belongsTo(CryptoCurrency::class);
    }

    public function cryptoTransaction()
    {
        return $this->belongsTo(CryptoTransaction::class);
    }

    public function paymentTransaction()
    {
        return $this->belongsTo(PaymentTransaction::class);
    }

    public function tradeDisputes()
    {
        return $this->hasMany(P2pCryptoTradeDispute::class);
    }

    public function escrow()
    {
        return $this->hasOne(P2pCryptoEscrow::class);
    }
}