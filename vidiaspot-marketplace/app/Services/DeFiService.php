<?php

namespace App\Services;

use App\Models\StakingPool;
use App\Models\UserStake;
use App\Models\YieldFarmingPool;
use App\Models\UserFarmingPosition;
use App\Models\LiquidityPool;
use App\Models\UserLiquidityPosition;
use Illuminate\Support\Facades\DB;

class DeFiService
{
    /**
     * Create a staking pool
     *
     * @param array $data
     * @return StakingPool
     */
    public function createStakingPool(array $data): StakingPool
    {
        return StakingPool::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'token_address' => $data['token_address'],
            'token_symbol' => $data['token_symbol'],
            'token_name' => $data['token_name'],
            'type' => $data['type'] ?? 'single_asset',
            'apy' => $data['apy'] ?? 0,
            'apr' => $data['apr'] ?? 0,
            'total_staked' => $data['total_staked'] ?? 0,
            'min_stake_amount' => $data['min_stake_amount'] ?? 0,
            'max_stake_amount' => $data['max_stake_amount'] ?? null,
            'lockup_period_days' => $data['lockup_period_days'] ?? 0,
            'reward_period_days' => $data['reward_period_days'] ?? 1,
            'auto_compound' => $data['auto_compound'] ?? false,
            'auto_compound_fee_percent' => $data['auto_compound_fee_percent'] ?? 0,
            'starts_at' => $data['starts_at'] ?? null,
            'ends_at' => $data['ends_at'] ?? null,
            'is_active' => $data['is_active'] ?? true,
            'requires_kyc' => $data['requires_kyc'] ?? false,
            'contract_address' => $data['contract_address'],
            'rewards_token_address' => $data['rewards_token_address'],
            'rewards_token_symbol' => $data['rewards_token_symbol'],
            'terms_and_conditions' => $data['terms_and_conditions'] ?? null,
            'risks' => $data['risks'] ?? null,
        ]);
    }

    /**
     * Create a user stake in a staking pool
     *
     * @param array $data
     * @param int $userId
     * @return UserStake
     */
    public function createUserStake(array $data, int $userId): UserStake
    {
        $pool = StakingPool::find($data['staking_pool_id']);

        if ($data['amount'] < $pool->min_stake_amount) {
            throw new \Exception("Stake amount is below minimum required amount");
        }

        if ($pool->max_stake_amount && $data['amount'] > $pool->max_stake_amount) {
            throw new \Exception("Stake amount exceeds maximum allowed amount");
        }

        $stake = UserStake::create([
            'user_id' => $userId,
            'staking_pool_id' => $data['staking_pool_id'],
            'staked_amount' => $data['amount'],
            'is_compounding' => $data['auto_compound'] ?? $pool->auto_compound,
            'auto_claim_rewards' => $data['auto_claim_rewards'] ?? false,
        ]);

        // Update the staking pool's total staked amount
        $pool->increment('total_staked', $data['amount']);

        return $stake;
    }

    /**
     * Create a yield farming pool
     *
     * @param array $data
     * @return YieldFarmingPool
     */
    public function createYieldFarmingPool(array $data): YieldFarmingPool
    {
        return YieldFarmingPool::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'token_pairs' => $data['token_pairs'] ?? [],
            'pool_type' => $data['pool_type'] ?? 'volatile',
            'apy' => $data['apy'] ?? 0,
            'tvl' => $data['tvl'] ?? 0,
            'volume_24h' => $data['volume_24h'] ?? 0,
            'rewards_tokens' => $data['rewards_tokens'] ?? [],
            'rewards_apy' => $data['rewards_apy'] ?? [],
            'is_active' => $data['is_active'] ?? true,
            'is_liquid_staking' => $data['is_liquid_staking'] ?? false,
            'min_deposit_duration_hours' => $data['min_deposit_duration_hours'] ?? 0,
            'entry_fee_percent' => $data['entry_fee_percent'] ?? 0,
            'exit_fee_percent' => $data['exit_fee_percent'] ?? 0,
            'contract_address' => $data['contract_address'],
            'strategy_description' => $data['strategy_description'] ?? null,
            'risks' => $data['risks'] ?? null,
        ]);
    }

    /**
     * Create a user farming position
     *
     * @param array $data
     * @param int $userId
     * @return UserFarmingPosition
     */
    public function createUserFarmingPosition(array $data, int $userId): UserFarmingPosition
    {
        $pool = YieldFarmingPool::find($data['yield_farming_pool_id']);

        $position = UserFarmingPosition::create([
            'user_id' => $userId,
            'yield_farming_pool_id' => $data['yield_farming_pool_id'],
            'liquidity_amount' => $data['liquidity_amount'],
            'token_amounts' => $data['token_amounts'] ?? [],
            'position_value' => $data['position_value'] ?? $data['liquidity_amount'],
            'auto_compound' => $data['auto_compound'] ?? false,
            'auto_claim_rewards' => $data['auto_claim_rewards'] ?? false,
        ]);

        // Update the farming pool's TVL
        $pool->increment('tvl', $data['liquidity_amount']);

        return $position;
    }

    /**
     * Create a liquidity pool
     *
     * @param array $data
     * @return LiquidityPool
     */
    public function createLiquidityPool(array $data): LiquidityPool
    {
        return LiquidityPool::create([
            'name' => $data['name'],
            'token_symbols' => $data['token_symbols'] ?? [],
            'token_addresses' => $data['token_addresses'] ?? [],
            'tvl' => $data['tvl'] ?? 0,
            'volume_24h' => $data['volume_24h'] ?? 0,
            'volume_7d' => $data['volume_7d'] ?? 0,
            'fees_24h' => $data['fees_24h'] ?? 0,
            'fees_7d' => $data['fees_7d'] ?? 0,
            'apr' => $data['apr'] ?? 0,
            'swap_fee_percent' => $data['swap_fee_percent'] ?? 0.3,
            'is_active' => $data['is_active'] ?? true,
            'is_stable_pool' => $data['is_stable_pool'] ?? false,
            'is_authorized' => $data['is_authorized'] ?? false,
            'pool_address' => $data['pool_address'],
            'lp_token_address' => $data['lp_token_address'],
            'token_weights' => $data['token_weights'] ?? [],
            'token_balances' => $data['token_balances'] ?? [],
            'description' => $data['description'] ?? null,
        ]);
    }

    /**
     * Create a user liquidity position
     *
     * @param array $data
     * @param int $userId
     * @return UserLiquidityPosition
     */
    public function createUserLiquidityPosition(array $data, int $userId): UserLiquidityPosition
    {
        $pool = LiquidityPool::find($data['liquidity_pool_id']);

        $position = UserLiquidityPosition::create([
            'user_id' => $userId,
            'liquidity_pool_id' => $data['liquidity_pool_id'],
            'lp_tokens_owned' => $data['lp_tokens_owned'],
            'token_amounts' => $data['token_amounts'] ?? [],
            'position_value' => $data['position_value'] ?? 0,
            'fees_earned' => $data['fees_earned'] ?? 0,
        ]);

        // Update the liquidity pool's TVL
        $pool->increment('tvl', $data['position_value']);

        return $position;
    }

    /**
     * Calculate staking rewards for a user stake
     *
     * @param UserStake $stake
     * @return float
     */
    public function calculateStakingRewards(UserStake $stake): float
    {
        $pool = $stake->stakingPool;
        
        // Calculate rewards based on APY, time, and amount staked
        $now = now();
        $stakedAt = $stake->staked_at ? \Carbon\Carbon::parse($stake->staked_at) : $now;
        $daysStaked = $stakedAt->diffInDays($now);
        
        // Simple calculation: (staked_amount * apy * days_staked) / 365
        $rewards = ($stake->staked_amount * $pool->apy / 100 * $daysStaked) / 365;
        
        return round($rewards, 8);
    }

    /**
     * Calculate farming rewards for a user position
     *
     * @param UserFarmingPosition $position
     * @return float
     */
    public function calculateFarmingRewards(UserFarmingPosition $position): float
    {
        $pool = $position->yieldFarmingPool;
        
        // Calculate rewards based on pool APY and time invested
        $now = now();
        $startedAt = $position->started_at ? \Carbon\Carbon::parse($position->started_at) : $now;
        $daysInvested = $startedAt->diffInDays($now);
        
        // Simple calculation: (position_value * apy * days_invested) / 365
        $rewards = ($position->position_value * $pool->apy / 100 * $daysInvested) / 365;
        
        return round($rewards, 8);
    }

    /**
     * Calculate liquidity provision rewards for a user
     *
     * @param UserLiquidityPosition $position
     * @return float
     */
    public function calculateLiquidityRewards(UserLiquidityPosition $position): float
    {
        $pool = $position->liquidityPool;
        
        // Calculate rewards based on pool fees and user's share of TVL
        $userShare = $position->lp_tokens_owned / $pool->total_liquidity_tokens; // This would need to be calculated properly
        
        // For now, return an estimate based on pool fees and user's share
        // In a real implementation, this would use actual swap fees
        return 0; // Placeholder - actual calculation depends on implementation details
    }

    /**
     * Get user's total DeFi value
     *
     * @param int $userId
     * @return array
     */
    public function getUserDefiValue(int $userId): array
    {
        $totalStaking = UserStake::where('user_id', $userId)
            ->where('is_active', true)
            ->sum('staked_amount');
            
        $totalFarming = UserFarmingPosition::where('user_id', $userId)
            ->where('is_active', true)
            ->sum('position_value');
            
        $totalLiquidity = UserLiquidityPosition::where('user_id', $userId)
            ->where('is_active', true)
            ->sum('position_value');

        return [
            'total_staking_value' => $totalStaking,
            'total_farming_value' => $totalFarming,
            'total_liquidity_value' => $totalLiquidity,
            'total_defi_value' => $totalStaking + $totalFarming + $totalLiquidity,
        ];
    }

    /**
     * Unstake from a staking pool
     *
     * @param int $stakeId
     * @param int $userId
     * @return bool
     */
    public function unstake(int $stakeId, int $userId): bool
    {
        $stake = UserStake::where('id', $stakeId)
            ->where('user_id', $userId)
            ->first();

        if (!$stake) {
            throw new \Exception("Stake not found or doesn't belong to user");
        }

        if (!$stake->is_active) {
            throw new \Exception("Stake is already inactive");
        }

        $stake->update([
            'is_active' => false,
            'unstaked_at' => now(),
        ]);

        // Update the staking pool's total staked amount
        $stake->stakingPool->decrement('total_staked', $stake->staked_amount);

        return true;
    }

    /**
     * Withdraw from a farming position
     *
     * @param int $positionId
     * @param int $userId
     * @return bool
     */
    public function withdrawFarmingPosition(int $positionId, int $userId): bool
    {
        $position = UserFarmingPosition::where('id', $positionId)
            ->where('user_id', $userId)
            ->first();

        if (!$position) {
            throw new \Exception("Farming position not found or doesn't belong to user");
        }

        if (!$position->is_active) {
            throw new \Exception("Position is already inactive");
        }

        $position->update([
            'is_active' => false,
            'ended_at' => now(),
        ]);

        // Update the farming pool's TVL
        $position->yieldFarmingPool->decrement('tvl', $position->position_value);

        return true;
    }

    /**
     * Remove liquidity from a liquidity pool
     *
     * @param int $positionId
     * @param int $userId
     * @return bool
     */
    public function removeLiquidity(int $positionId, int $userId): bool
    {
        $position = UserLiquidityPosition::where('id', $positionId)
            ->where('user_id', $userId)
            ->first();

        if (!$position) {
            throw new \Exception("Liquidity position not found or doesn't belong to user");
        }

        if (!$position->is_active) {
            throw new \Exception("Position is already inactive");
        }

        $position->update([
            'is_active' => false,
            'removed_at' => now(),
        ]);

        // Update the liquidity pool's TVL
        $position->liquidityPool->decrement('tvl', $position->position_value);

        return true;
    }
}