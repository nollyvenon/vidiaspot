<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CryptoCurrency extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'symbol',
        'slug',
        'description',
        'price',
        'market_cap',
        'logo_url',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:8',
        'market_cap' => 'decimal:8',
        'is_active' => 'boolean',
    ];

    // Relations
    public function cryptoTransactions()
    {
        return $this->hasMany(CryptoTransaction::class);
    }

    public function p2pCryptoOrders()
    {
        return $this->hasMany(P2pCryptoOrder::class);
    }
}