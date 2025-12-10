<?php

namespace App\Services;

use App\Models\TradingPortfolio;
use App\Models\PortfolioAllocation;
use App\Models\TradingTransaction;
use App\Models\PortfolioRebalancingHistory;
use Illuminate\Support\Facades\DB;

class TradingPortfolioService
{
    /**
     * Create a new trading portfolio
     *
     * @param array $data
     * @param int $userId
     * @return TradingPortfolio
     */
    public function createPortfolio(array $data, int $userId): TradingPortfolio
    {
        $portfolio = TradingPortfolio::create([
            'user_id' => $userId,
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'type' => $data['type'] ?? 'crypto',
            'strategy' => $data['strategy'] ?? 'long_term',
            'initial_capital' => $data['initial_capital'] ?? 0,
            'current_value' => $data['initial_capital'] ?? 0,
            'is_active' => true,
            'auto_rebalance' => $data['auto_rebalance'] ?? false,
            'rebalance_threshold' => $data['rebalance_threshold'] ?? 5.00,
        ]);

        // Generate slug based on name
        $portfolio->update([
            'slug' => $this->generateUniqueSlug($portfolio->name, $portfolio->id)
        ]);

        return $portfolio->fresh();
    }

    /**
     * Add a transaction to a portfolio
     *
     * @param array $data
     * @param TradingPortfolio $portfolio
     * @return TradingTransaction
     */
    public function addTransaction(array $data, TradingPortfolio $portfolio): TradingTransaction
    {
        $transaction = TradingTransaction::create([
            'portfolio_id' => $portfolio->id,
            'user_id' => $portfolio->user_id,
            'asset_symbol' => $data['asset_symbol'],
            'asset_name' => $data['asset_name'],
            'transaction_type' => $data['transaction_type'],
            'order_type' => $data['order_type'] ?? 'market',
            'quantity' => $data['quantity'],
            'price' => $data['price'],
            'total_amount' => $data['quantity'] * $data['price'],
            'fee' => $data['fee'] ?? 0,
            'status' => 'completed',
            'executed_at' => $data['executed_at'] ?? now(),
            'notes' => $data['notes'] ?? null,
        ]);

        // Update portfolio allocation after transaction
        $this->updatePortfolioAllocation($transaction);

        // Update portfolio value and metrics
        $this->updatePortfolioMetrics($portfolio);

        // Check if auto-rebalance is needed
        if ($portfolio->auto_rebalance) {
            $this->checkForRebalancing($portfolio);
        }

        return $transaction;
    }

    /**
     * Update portfolio allocation based on a transaction
     *
     * @param TradingTransaction $transaction
     * @return void
     */
    private function updatePortfolioAllocation(TradingTransaction $transaction): void
    {
        $portfolio = $transaction->portfolio;

        if ($transaction->transaction_type === 'buy') {
            // Find or create allocation for this asset
            $allocation = $portfolio->allocations()->firstOrNew([
                'asset_symbol' => $transaction->asset_symbol,
                'asset_type' => 'crypto', // Default, in real implementation this would come from data
            ]);

            if ($allocation->exists) {
                // Update existing allocation
                $totalQuantity = $allocation->quantity + $transaction->quantity;
                $totalCost = ($allocation->quantity * $allocation->average_buy_price) + 
                            ($transaction->quantity * $transaction->price);
                
                $allocation->update([
                    'quantity' => $totalQuantity,
                    'average_buy_price' => $totalCost / $totalQuantity,
                    'current_price' => $transaction->price, // Update with latest price
                    'current_value' => $totalQuantity * $transaction->price,
                    'cost_basis' => $totalCost,
                    'last_updated_at' => now(),
                ]);
            } else {
                // Create new allocation
                $allocation->fill([
                    'asset_name' => $transaction->asset_name,
                    'asset_type' => 'crypto', // Default
                    'quantity' => $transaction->quantity,
                    'average_buy_price' => $transaction->price,
                    'current_price' => $transaction->price,
                    'current_value' => $transaction->quantity * $transaction->price,
                    'cost_basis' => $transaction->quantity * $transaction->price,
                    'allocation_percentage' => 0, // Will be calculated after
                    'first_bought_at' => now(),
                    'last_updated_at' => now(),
                    'is_active' => true,
                ])->save();
            }
        } elseif ($transaction->transaction_type === 'sell') {
            // Find the allocation for this asset
            $allocation = $portfolio->allocations()
                ->where('asset_symbol', $transaction->asset_symbol)
                ->first();

            if ($allocation) {
                // Update allocation
                $newQuantity = $allocation->quantity - $transaction->quantity;
                
                // If quantity becomes 0 or negative, remove the allocation
                if ($newQuantity <= 0) {
                    $allocation->delete();
                } else {
                    $allocation->update([
                        'quantity' => $newQuantity,
                        'current_price' => $transaction->price,
                        'current_value' => $newQuantity * $transaction->price,
                        'last_updated_at' => now(),
                    ]);
                }
            }
        }

        // Recalculate allocation percentages after updating allocations
        $this->recalculateAllocationPercentages($portfolio);
    }

    /**
     * Recalculate allocation percentages for all assets in a portfolio
     *
     * @param TradingPortfolio $portfolio
     * @return void
     */
    private function recalculateAllocationPercentages(TradingPortfolio $portfolio): void
    {
        $totalValue = $portfolio->current_value;
        
        if ($totalValue <= 0) {
            return;
        }

        foreach ($portfolio->allocations as $allocation) {
            $percentage = ($allocation->current_value / $totalValue) * 100;
            $allocation->update([
                'allocation_percentage' => round($percentage, 2)
            ]);
        }
    }

    /**
     * Update portfolio metrics (value, P&L, etc.)
     *
     * @param TradingPortfolio $portfolio
     * @return void
     */
    private function updatePortfolioMetrics(TradingPortfolio $portfolio): void
    {
        // Calculate total value from current allocations
        $totalValue = $portfolio->allocations->sum('current_value');
        
        // Update portfolio metrics
        $portfolio->update([
            'current_value' => $totalValue,
            'total_profit_loss' => $totalValue - $portfolio->initial_capital,
        ]);
    }

    /**
     * Check if a portfolio needs rebalancing
     *
     * @param TradingPortfolio $portfolio
     * @return void
     */
    private function checkForRebalancing(TradingPortfolio $portfolio): void
    {
        if (!$portfolio->auto_rebalance) {
            return;
        }

        // Calculate if any allocation is beyond the rebalance threshold
        foreach ($portfolio->allocations as $allocation) {
            $deviation = abs($allocation->allocation_percentage - $allocation->target_allocation_percentage);
            
            if ($deviation > $portfolio->rebalance_threshold) {
                $this->triggerRebalancing($portfolio);
                break;
            }
        }
    }

    /**
     * Trigger portfolio rebalancing
     *
     * @param TradingPortfolio $portfolio
     * @return void
     */
    private function triggerRebalancing(TradingPortfolio $portfolio): void
    {
        // Get current allocation
        $currentAllocation = $portfolio->allocations->map(function ($allocation) {
            return [
                'asset_symbol' => $allocation->asset_symbol,
                'allocation_percentage' => $allocation->allocation_percentage,
                'quantity' => $allocation->quantity,
                'current_price' => $allocation->current_price
            ];
        })->toArray();

        // In a real implementation, this would calculate target allocation based on strategy
        // For now, we'll just record the rebalancing event
        PortfolioRebalancingHistory::create([
            'portfolio_id' => $portfolio->id,
            'user_id' => $portfolio->user_id,
            'before_allocation' => $currentAllocation,
            'after_allocation' => $currentAllocation, // Placeholder - would be calculated
            'rebalance_actions' => [], // Placeholder - would contain actual rebalancing actions
            'total_value' => $portfolio->current_value,
            'rebalance_cost' => 0, // Placeholder - would calculate actual fees
            'reason' => 'Auto-rebalance triggered due to allocation deviation',
            'rebalanced_at' => now(),
        ]);

        // Update last rebalanced time
        $portfolio->update([
            'last_rebalanced_at' => now()
        ]);
    }

    /**
     * Generate a unique slug for the portfolio
     *
     * @param string $name
     * @param int $id
     * @return string
     */
    private function generateUniqueSlug(string $name, int $id): string
    {
        $slug = \Str::slug($name);
        $originalSlug = $slug;

        $count = 1;
        while (TradingPortfolio::where('slug', $slug)->where('id', '!=', $id)->exists()) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        return $slug;
    }

    /**
     * Get portfolio performance metrics
     *
     * @param TradingPortfolio $portfolio
     * @return array
     */
    public function getPerformanceMetrics(TradingPortfolio $portfolio): array
    {
        // Calculate various performance metrics
        $initialCapital = $portfolio->initial_capital;
        $currentValue = $portfolio->current_value;
        
        $totalReturn = $currentValue - $initialCapital;
        $totalReturnPercentage = $initialCapital > 0 ? ($totalReturn / $initialCapital) * 100 : 0;

        return [
            'initial_capital' => $initialCapital,
            'current_value' => $currentValue,
            'total_return' => $totalReturn,
            'total_return_percentage' => round($totalReturnPercentage, 2),
            'total_trades' => $portfolio->transactions()->count(),
            'allocation_count' => $portfolio->allocations()->count(),
            'top_allocation' => $portfolio->allocations()
                ->orderBy('allocation_percentage', 'desc')
                ->first(),
        ];
    }
}