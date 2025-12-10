<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PortfolioRebalancingHistory extends Model
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
        'before_allocation',
        'after_allocation',
        'rebalance_actions',
        'total_value',
        'rebalance_cost',
        'reason',
        'rebalanced_at',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'before_allocation' => 'array',
        'after_allocation' => 'array',
        'rebalance_actions' => 'array',
        'total_value' => 'decimal:8',
        'rebalance_cost' => 'decimal:8',
        'rebalanced_at' => 'datetime',
        'metadata' => 'array',
    ];

    /**
     * Get the portfolio that the rebalancing history belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function portfolio(): BelongsTo
    {
        return $this->belongsTo(TradingPortfolio::class);
    }

    /**
     * Get the user that the rebalancing history belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
