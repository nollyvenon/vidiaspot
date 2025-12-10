<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TradingTransaction extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'portfolio_id',
        'user_id',
        'asset_symbol',
        'asset_name',
        'transaction_type',
        'order_type',
        'quantity',
        'price',
        'total_amount',
        'fee',
        'fee_currency',
        'exchange_rate',
        'exchange',
        'transaction_id',
        'blockchain_transaction_hash',
        'status',
        'executed_at',
        'notes',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'quantity' => 'decimal:8',
        'price' => 'decimal:8',
        'total_amount' => 'decimal:8',
        'fee' => 'decimal:8',
        'exchange_rate' => 'decimal:8',
        'executed_at' => 'datetime',
        'metadata' => 'array',
    ];

    /**
     * Get the portfolio that the transaction belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function portfolio(): BelongsTo
    {
        return $this->belongsTo(TradingPortfolio::class);
    }

    /**
     * Get the user that the transaction belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
