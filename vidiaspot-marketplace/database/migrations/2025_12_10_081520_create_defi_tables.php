<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create staking pools table
        Schema::create('staking_pools', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('token_address'); // Smart contract address
            $table->string('token_symbol');
            $table->string('token_name');
            $table->enum('type', ['single_asset', 'lp_token', 'hybrid'])->default('single_asset');
            $table->decimal('apy', 8, 4)->default(0.0000); // Annual percentage yield
            $table->decimal('apr', 8, 4)->default(0.0000); // Annual percentage rate
            $table->decimal('total_staked', 20, 8)->default(0.00000000); // Total tokens staked
            $table->decimal('total_rewards_distributed', 20, 8)->default(0.00000000); // Total rewards distributed
            $table->decimal('min_stake_amount', 20, 8)->default(0.00000000); // Minimum stake amount
            $table->decimal('max_stake_amount', 20, 8)->nullable(); // Maximum stake amount (null for unlimited)
            $table->integer('lockup_period_days')->default(0); // Days tokens are locked for
            $table->integer('reward_period_days')->default(1); // Reward distribution period in days
            $table->boolean('auto_compound')->default(false); // Whether rewards auto compound
            $table->decimal('auto_compound_fee_percent', 5, 2)->default(0.00); // Fee percentage for auto-compounding
            $table->timestamp('starts_at')->nullable(); // When staking starts
            $table->timestamp('ends_at')->nullable(); // When staking ends (null for indefinite)
            $table->timestamp('last_reward_distribution_at')->nullable(); // Last reward distribution
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->boolean('requires_kyc')->default(false); // Whether KYC is required
            $table->json('supported_chains')->nullable(); // Chains where staking is supported
            $table->string('contract_address'); // Staking contract address
            $table->string('rewards_token_address'); // Address of reward tokens
            $table->string('rewards_token_symbol'); // Reward token symbol
            $table->json('risk_metrics')->nullable(); // Risk metrics and scores
            $table->json('performance_history')->nullable(); // Historical performance data
            $table->json('fees')->nullable(); // Different fee structures
            $table->text('terms_and_conditions')->nullable(); // Terms and conditions
            $table->text('risks')->nullable(); // Risk disclosures
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            // Indexes for performance
            $table->index('is_active');
            $table->index('apy');
            $table->index('token_symbol');
            $table->index('total_staked');
            $table->index('starts_at');
            $table->index('ends_at');
            $table->index('slug');
        });

        // Create user stakes table
        Schema::create('user_stakes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('staking_pool_id')->constrained()->onDelete('cascade');
            $table->decimal('staked_amount', 20, 8)->default(0.00000000); // Amount staked
            $table->decimal('rewards_earned', 20, 8)->default(0.00000000); // Rewards earned so far
            $table->decimal('rewards_claimed', 20, 8)->default(0.00000000); // Rewards claimed
            $table->decimal('pending_rewards', 20, 8)->default(0.00000000); // Rewards pending claim
            $table->timestamp('staked_at')->useCurrent();
            $table->timestamp('unstaked_at')->nullable(); // When tokens were unstaked
            $table->timestamp('unstake_requested_at')->nullable(); // When unstake was requested
            $table->timestamp('claimable_at')->nullable(); // When rewards can be claimed
            $table->timestamp('expires_at')->nullable(); // When stake expires (if has lockup)
            $table->boolean('is_active')->default(true);
            $table->boolean('is_compounding')->default(false); // Whether stake is auto-compounding
            $table->boolean('auto_claim_rewards')->default(false); // Whether to auto-claim rewards
            $table->json('transaction_hash')->nullable(); // Blockchain transaction hashes
            $table->json('metadata')->nullable(); // Additional stake metadata
            $table->timestamps();

            // Indexes for performance
            $table->index(['user_id', 'is_active']);
            $table->index(['staking_pool_id', 'is_active']);
            $table->index('staked_at');
            $table->index('expires_at');
            $table->index('pending_rewards');
        });

        // Create yield farming pools table
        Schema::create('yield_farming_pools', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->json('token_pairs'); // Token pairs in the pool (e.g., ETH/USDT)
            $table->enum('pool_type', ['stable', 'volatile', 'hybrid'])->default('volatile');
            $table->decimal('apy', 8, 4)->default(0.0000); // Annual percentage yield
            $table->decimal('tvl', 20, 8)->default(0.00000000); // Total value locked
            $table->decimal('volume_24h', 20, 8)->default(0.00000000); // 24h trading volume
            $table->json('rewards_tokens')->nullable(); // Tokens distributed as rewards
            $table->json('rewards_apy')->nullable(); // APY for each reward token
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_liquid_staking')->default(false); // Whether pool supports liquid staking
            $table->integer('min_deposit_duration_hours')->default(0); // Minimum deposit duration
            $table->decimal('entry_fee_percent', 5, 2)->default(0.00); // Entry fee percentage
            $table->decimal('exit_fee_percent', 5, 2)->default(0.00); // Exit fee percentage
            $table->json('supported_protocols')->nullable(); // Protocols supported
            $table->string('contract_address'); // Farming contract address
            $table->json('reward_schedule')->nullable(); // Reward distribution schedule
            $table->json('risk_indicators')->nullable(); // Risk indicators for the pool
            $table->json('token_addresses'); // Addresses of tokens in the pool
            $table->json('fees_structure')->nullable(); // Detailed fees structure
            $table->text('strategy_description')->nullable(); // Description of farming strategy
            $table->text('risks')->nullable(); // Risk disclosures
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            // Indexes for performance
            $table->index('is_active');
            $table->index('apy');
            $table->index('tvl');
            $table->index('volume_24h');
            $table->index('slug');
        });

        // Create user farming positions table
        Schema::create('user_farming_positions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('yield_farming_pool_id')->constrained()->onDelete('cascade');
            $table->decimal('liquidity_amount', 20, 8)->default(0.00000000); // Amount of liquidity provided
            $table->json('token_amounts'); // Individual token amounts in the position
            $table->decimal('position_value', 20, 8)->default(0.00000000); // Current value of position
            $table->decimal('rewards_earned', 20, 8)->default(0.00000000); // Rewards earned so far
            $table->decimal('rewards_claimed', 20, 8)->default(0.00000000); // Rewards claimed
            $table->timestamp('started_at')->useCurrent();
            $table->timestamp('ended_at')->nullable(); // When position ended (if withdrawn)
            $table->boolean('is_active')->default(true);
            $table->boolean('auto_compound')->default(false);
            $table->boolean('auto_claim_rewards')->default(false);
            $table->json('transaction_hashes')->nullable(); // Blockchain transaction hashes
            $table->json('metadata')->nullable(); // Additional position metadata
            $table->timestamps();

            // Indexes for performance
            $table->index(['user_id', 'is_active']);
            $table->index(['yield_farming_pool_id', 'is_active']);
            $table->index('started_at');
            $table->index('position_value');
        });

        // Create liquidity pools table
        Schema::create('liquidity_pools', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->json('token_symbols'); // Token symbols in the pool (e.g., ['ETH', 'USDT'])
            $table->json('token_addresses'); // Token addresses in the pool
            $table->decimal('tvl', 20, 8)->default(0.00000000); // Total value locked
            $table->decimal('volume_24h', 20, 8)->default(0.00000000); // 24h trading volume
            $table->decimal('volume_7d', 20, 8)->default(0.00000000); // 7d trading volume
            $table->decimal('fees_24h', 20, 8)->default(0.00000000); // 24h fees collected
            $table->decimal('fees_7d', 20, 8)->default(0.00000000); // 7d fees collected
            $table->decimal('apr', 8, 4)->default(0.0000); // Current APR for liquidity providers
            $table->decimal('swap_fee_percent', 5, 4)->default(0.3000); // Swap fee percentage
            $table->boolean('is_active')->default(true);
            $table->boolean('is_stable_pool')->default(false); // Whether it's a stable coin pool
            $table->boolean('is_authorized')->default(false); // Whether pool is authorized
            $table->string('pool_address'); // Pool contract address
            $table->string('lp_token_address'); // Address of LP tokens
            $table->json('token_weights')->nullable(); // Token weights in the pool
            $table->json('token_balances')->nullable(); // Current token balances
            $table->json('lp_token_stats')->nullable(); // LP token statistics
            $table->json('protocol_fees')->nullable(); // Protocol fee structure
            $table->json('metadata')->nullable(); // Additional pool metadata
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            // Indexes for performance
            $table->index('is_active');
            $table->index('tvl');
            $table->index('volume_24h');
            $table->index('apr');
            $table->index('slug');
        });

        // Create user liquidity positions table
        Schema::create('user_liquidity_positions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('liquidity_pool_id')->constrained()->onDelete('cascade');
            $table->decimal('lp_tokens_owned', 20, 8)->default(0.00000000); // LP tokens owned
            $table->json('token_amounts'); // Amounts of each token in the position
            $table->decimal('position_value', 20, 8)->default(0.00000000); // Current value of position
            $table->decimal('fees_earned', 20, 8)->default(0.00000000); // Fees earned so far
            $table->decimal('fees_claimed', 20, 8)->default(0.00000000); // Fees claimed
            $table->timestamp('added_at')->useCurrent();
            $table->timestamp('removed_at')->nullable(); // When position was removed
            $table->boolean('is_active')->default(true);
            $table->json('transaction_hashes')->nullable(); // Blockchain transaction hashes
            $table->json('metadata')->nullable(); // Additional position metadata
            $table->timestamps();

            // Indexes for performance
            $table->index(['user_id', 'is_active']);
            $table->index(['liquidity_pool_id', 'is_active']);
            $table->index('added_at');
            $table->index('position_value');
        });

        // Create DeFi protocols table
        Schema::create('defi_protocols', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('protocol_type'); // e.g., 'amm', 'lending', 'derivatives', 'yield_aggregator'
            $table->text('description')->nullable();
            $table->json('supported_chains'); // Supported blockchain networks
            $table->json('contracts'); // Contract addresses
            $table->decimal('tvl', 20, 8)->default(0.00000000); // Total value locked in protocol
            $table->decimal('volume_24h', 20, 8)->default(0.00000000); // 24h volume
            $table->json('metrics')->nullable(); // Additional protocol metrics
            $table->boolean('is_active')->default(true);
            $table->boolean('is_recommended')->default(false); // Whether protocol is recommended
            $table->json('security_features')->nullable(); // Security features
            $table->json('supported_assets')->nullable(); // Supported assets
            $table->text('risks')->nullable(); // Risk disclosures
            $table->text('audits')->nullable(); // Audit information
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            // Indexes for performance
            $table->index('is_active');
            $table->index('tvl');
            $table->index('protocol_type');
            $table->index('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('defi_protocols');
        Schema::dropIfExists('user_liquidity_positions');
        Schema::dropIfExists('liquidity_pools');
        Schema::dropIfExists('user_farming_positions');
        Schema::dropIfExists('yield_farming_pools');
        Schema::dropIfExists('user_stakes');
        Schema::dropIfExists('staking_pools');
    }
};
