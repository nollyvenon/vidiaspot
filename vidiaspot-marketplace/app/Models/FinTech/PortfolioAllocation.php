<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PortfolioAllocation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'portfolio_id',
        'asset_symbol',
        'asset_name',
        'asset_type',
        'quantity',
        'average_buy_price',
        'current_price',
        'current_value',
        'cost_basis',
        'unrealized_pnl',
        'unrealized_pnl_percentage',
        'allocation_percentage',
        'target_allocation_percentage',
        'first_bought_at',
        'last_updated_at',
        'is_active',
        'transaction_history',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'quantity' => 'decimal:8',
        'average_buy_price' => 'decimal:8',
        'current_price' => 'decimal:8',
        'current_value' => 'decimal:8',
        'cost_basis' => 'decimal:8',
        'unrealized_pnl' => 'decimal:8',
        'unrealized_pnl_percentage' => 'decimal:4',
        'allocation_percentage' => 'decimal:2',
        'target_allocation_percentage' => 'decimal:2',
        'first_bought_at' => 'datetime',
        'last_updated_at' => 'datetime',
        'is_active' => 'boolean',
        'transaction_history' => 'array',
        'metadata' => 'array',
    ];

    /**
     * Get the portfolio that owns the allocation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function portfolio(): BelongsTo
    {
        return $this->belongsTo(TradingPortfolio::class);
    }
}
