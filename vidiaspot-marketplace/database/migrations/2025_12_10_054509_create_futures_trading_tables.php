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
        Schema::create('futures_contracts', function (Blueprint $table) {
            $table->id();
            $table->string('contract_name'); // e.g., BTCUSDT_PERP, ETHUSDT_QUARTER
            $table->string('symbol'); // e.g., BTCUSDT, ETHUSDT
            $table->string('contract_type'); // perpetual, quarterly, bi_quarterly
            $table->unsignedBigInteger('base_asset_id'); // ID of the base asset (e.g., BTC)
            $table->unsignedBigInteger('quote_asset_id'); // ID of the quote asset (e.g., USDT)
            $table->string('settlement_asset'); // Asset used for settlement (e.g., USDT, BUSD)
            $table->string('status')->default('trading'); // pre_trading, trading, post_trading, end_of_day, halt, auction_match, break
            $table->decimal('contract_size', 16, 8); // Size of one contract
            $table->decimal('min_leverage', 5, 2)->default(1.00); // Minimum leverage allowed
            $table->decimal('max_leverage', 5, 2)->default(125.00); // Maximum leverage allowed
            $table->decimal('initial_margin_ratio', 8, 4)->default(0.0100); // Initial margin ratio (1% default)
            $table->decimal('maint_margin_ratio', 8, 4)->default(0.0050); // Maintenance margin ratio (0.5% default)
            $table->decimal('max_quantity', 16, 8); // Maximum quantity allowed per order
            $table->decimal('max_notional', 16, 2); // Maximum notional allowed per order
            $table->decimal('price_tick_size', 16, 8); // Minimum price change
            $table->decimal('quantity_step_size', 16, 8); // Minimum quantity change
            $table->decimal('price_limit', 16, 8)->nullable(); // Price limit for the day
            $table->string('liquidation_fee_rate', 8, 4)->default(0.0010); // Liquidation fee rate (0.1%)
            $table->string('market_fee_rate', 8, 4)->default(0.0004); // Fee rate for market makers
            $table->string('taker_fee_rate', 8, 4)->default(0.0005); // Fee rate for takers
            $table->timestamp('delivery_date')->nullable(); // Date when quarterly contracts are delivered
            $table->timestamp('launch_date'); // Date when contract was launched
            $table->timestamp('last_traded_at')->nullable(); // Last time the contract was traded
            $table->string('quote_asset_unit'); // Unit of quote asset (e.g., USDT)
            $table->json('contract_details')->nullable(); // Additional contract-specific details
            $table->timestamps();

            $table->index(['symbol', 'contract_type']);
            $table->index('contract_type');
            $table->index('status');
            $table->index('max_leverage');
            $table->index('delivery_date');
            
            // Assuming there are asset tables that these foreign keys reference
            // $table->foreign('base_asset_id')->references('id')->on('crypto_currencies');
            // $table->foreign('quote_asset_id')->references('id')->on('crypto_currencies');
        });
        
        Schema::create('futures_accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('account_type')->default('unified'); // unified, isolated
            $table->decimal('total_balance', 15, 2); // Total balance in the account
            $table->decimal('available_balance', 15, 2); // Available balance for trading
            $table->decimal('used_balance', 15, 2)->default(0.00); // Balance used in positions and orders
            $table->decimal('position_margin', 15, 2)->default(0.00); // Margin used for positions
            $table->decimal('order_margin', 15, 2)->default(0.00); // Margin used for orders
            $table->decimal('unrealized_pnl', 15, 2)->default(0.00); // Unrealized profit/loss
            $table->decimal('realized_pnl', 15, 2)->default(0.00); // Realized profit/loss
            $table->decimal('cross_wallet_balance', 15, 2)->default(0.00); // Cross wallet balance for cross margin
            $table->decimal('cross_un_pnl', 15, 2)->default(0.00); // Cross unrealized P&L
            $table->decimal('available_withdrawal_balance', 15, 2); // Available balance for withdrawal
            $table->string('currency')->default('USDT'); // Account currency/asset
            $table->string('status')->default('active'); // active, inactive, liquidated
            $table->json('account_settings')->nullable(); // Account-specific settings
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('total_balance');
            $table->index('available_balance');
            $table->index('unrealized_pnl');
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
        
        Schema::create('futures_positions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('account_id');
            $table->unsignedBigInteger('contract_id');
            $table->string('position_side'); // long, short, both (for hedge mode)
            $table->string('position_status')->default('active'); // active, closed, liquidated, adl
            $table->decimal('entry_price', 16, 8); // Price at which position was opened
            $table->decimal('mark_price', 16, 8); // Mark price for the contract
            $table->decimal('current_price', 16, 8); // Current market price
            $table->decimal('quantity', 16, 8); // Quantity of contracts held
            $table->decimal('position_value', 15, 2); // Value of the position
            $table->decimal('unrealized_pnl', 15, 2); // Unrealized profit/loss
            $table->decimal('unrealized_pnl_pct', 8, 4); // Unrealized P&L percentage
            $table->decimal('leverage', 5, 2)->default(1.00); // Leverage used for position
            $table->decimal('initial_margin', 15, 2); // Initial margin used for position
            $table->decimal('maint_margin', 15, 2); // Maintenance margin required
            $table->decimal('position_margin', 15, 2); // Total position margin
            $table->decimal('liquidation_price', 16, 8)->nullable(); // Liquidation price
            $table->decimal('bankruptcy_price', 16, 8)->nullable(); // Bankruptcy price
            $table->decimal('margin_type', 10, 2)->default(1.00); // Margin type as decimal
            $table->decimal('isolated_margin', 15, 2)->nullable(); // Isolated margin amount
            $table->decimal('auto_add_margin', 5, 2)->default(0.00); // Auto add margin status
            $table->decimal('cum_realized_pnl', 15, 2)->default(0.00); // Cumulative realized P&L
            $table->decimal('position_adl', 5, 2); // Position ADL quantile
            $table->timestamp('updated_at'); // When position was last updated
            $table->timestamp('opened_at');
            $table->timestamp('closed_at')->nullable();
            $table->json('position_settings')->nullable(); // Additional position settings
            $table->timestamps();

            $table->index(['account_id', 'position_status']);
            $table->index(['contract_id', 'position_status']);
            $table->index('position_side');
            $table->index('unrealized_pnl');
            $table->index('liquidation_price');
            $table->index('leverage');
            $table->index('opened_at');
            $table->index('closed_at');
            
            $table->foreign('account_id')->references('id')->on('futures_accounts')->onDelete('cascade');
            $table->foreign('contract_id')->references('id')->on('futures_contracts')->onDelete('cascade');
        });
        
        Schema::create('futures_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('account_id');
            $table->unsignedBigInteger('contract_id');
            $table->string('order_type'); // limit, market, stop, stop_limit, take_profit, take_profit_limit, trailing_stop
            $table->string('side'); // buy, sell
            $table->string('position_side'); // long, short (for hedge mode)
            $table->string('time_in_force'); // GTC, IOC, FOK, GTX (post-only)
            $table->string('working_type'); // mark_price, contract_price
            $table->decimal('quantity', 16, 8); // Quantity of contracts
            $table->decimal('price', 16, 8)->nullable(); // Price for limit orders
            $table->decimal('stop_price', 16, 8)->nullable(); // Stop price for stop orders
            $table->decimal('activation_price', 16, 8)->nullable(); // Activation price for trailing stop orders
            $table->decimal('callback_rate', 5, 2)->nullable(); // Callback rate for trailing stop orders
            $table->decimal('cum_quote', 15, 2)->default(0.00); // Cumulative quote asset amount
            $table->decimal('avg_price', 16, 8)->nullable(); // Average fill price
            $table->string('status')->default('pending'); // pending, partially_filled, filled, canceled, expired
            $table->decimal('orig_qty', 16, 8); // Original quantity
            $table->decimal('executed_qty', 16, 8)->default(0.00); // Executed quantity
            $table->decimal('leverage', 5, 2)->default(1.00); // Leverage to use for the order
            $table->json('order_details')->nullable(); // Additional order details
            $table->timestamp('time'); // Order timestamp
            $table->timestamp('update_time')->nullable(); // Last update time
            $table->timestamp('cancel_time')->nullable(); // When order was canceled
            $table->timestamps();

            $table->index(['account_id', 'status']);
            $table->index(['contract_id', 'status']);
            $table->index('order_type');
            $table->index('side');
            $table->index('status');
            $table->index('time');
            $table->index('leverage');
            
            $table->foreign('account_id')->references('id')->on('futures_accounts')->onDelete('cascade');
            $table->foreign('contract_id')->references('id')->on('futures_contracts')->onDelete('cascade');
        });
        
        Schema::create('futures_trades', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('contract_id');
            $table->unsignedBigInteger('position_id')->nullable(); // Associated position ID
            $table->string('side'); // buy, sell
            $table->string('position_side'); // long, short
            $table->decimal('quantity', 16, 8); // Quantity of contracts traded
            $table->decimal('price', 16, 8); // Trade price
            $table->decimal('realized_pnl', 15, 2)->default(0.00); // Realized profit/loss
            $table->string('margin_asset'); // Asset used as margin
            $table->decimal('base_qty', 16, 8); // Base asset quantity
            $table->string('buyer_id'); // Buyer user ID (string to match exchange format)
            $table->string('seller_id'); // Seller user ID (string to match exchange format)
            $table->boolean('buyer_is_maker')->default(false); // Whether buyer was maker
            $table->string('trade_group_id'); // Group ID to link related trades
            $table->timestamp('trade_time'); // When trade occurred
            $table->json('trade_details')->nullable(); // Additional trade details
            $table->timestamps();

            $table->index(['order_id', 'trade_time']);
            $table->index(['contract_id', 'trade_time']);
            $table->index(['position_id', 'trade_time']);
            $table->index('side');
            $table->index('price');
            $table->index('trade_time');
            $table->index('buyer_id');
            $table->index('seller_id');
            
            $table->foreign('order_id')->references('id')->on('futures_orders')->onDelete('cascade');
            $table->foreign('contract_id')->references('id')->on('futures_contracts')->onDelete('cascade');
            $table->foreign('position_id')->references('id')->on('futures_positions')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('futures_trades');
        Schema::dropIfExists('futures_orders');
        Schema::dropIfExists('futures_positions');
        Schema::dropIfExists('futures_accounts');
        Schema::dropIfExists('futures_contracts');
    }
};