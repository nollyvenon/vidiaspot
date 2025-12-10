<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TradingPortfolio extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
        'user_id',
        'description',
        'type',
        'strategy',
        'initial_capital',
        'current_value',
        'total_profit_loss',
        'total_profit_loss_percentage',
        'daily_profit_loss',
        'weekly_profit_loss',
        'monthly_profit_loss',
        'yearly_profit_loss',
        'max_drawdown',
        'sharpe_ratio',
        'sortino_ratio',
        'volatility',
        'total_trades',
        'winning_trades',
        'losing_trades',
        'win_rate',
        'avg_win_amount',
        'avg_loss_amount',
        'best_trade',
        'worst_trade',
        'allocation_count',
        'asset_allocation',
        'risk_metrics',
        'performance_history',
        'metadata',
        'tags',
        'is_active',
        'is_public',
        'is_featured',
        'auto_rebalance',
        'rebalance_threshold',
        'last_rebalanced_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'initial_capital' => 'decimal:2',
        'current_value' => 'decimal:2',
        'total_profit_loss' => 'decimal:2',
        'total_profit_loss_percentage' => 'decimal:4',
        'daily_profit_loss' => 'decimal:2',
        'weekly_profit_loss' => 'decimal:2',
        'monthly_profit_loss' => 'decimal:2',
        'yearly_profit_loss' => 'decimal:2',
        'max_drawdown' => 'decimal:4',
        'sharpe_ratio' => 'decimal:4',
        'sortino_ratio' => 'decimal:4',
        'volatility' => 'decimal:4',
        'win_rate' => 'decimal:2',
        'avg_win_amount' => 'decimal:2',
        'avg_loss_amount' => 'decimal:2',
        'best_trade' => 'decimal:2',
        'worst_trade' => 'decimal:2',
        'asset_allocation' => 'array',
        'risk_metrics' => 'array',
        'performance_history' => 'array',
        'metadata' => 'array',
        'tags' => 'array',
        'is_active' => 'boolean',
        'is_public' => 'boolean',
        'is_featured' => 'boolean',
        'auto_rebalance' => 'boolean',
        'rebalance_threshold' => 'decimal:2',
        'last_rebalanced_at' => 'datetime',
    ];

    /**
     * Get the user that owns the portfolio.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the portfolio allocations for the portfolio.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function allocations(): HasMany
    {
        return $this->hasMany(PortfolioAllocation::class);
    }

    /**
     * Get the trading transactions for the portfolio.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(TradingTransaction::class);
    }

    /**
     * Get the rebalancing history for the portfolio.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function rebalancingHistory(): HasMany
    {
        return $this->hasMany(PortfolioRebalancingHistory::class);
    }
}
