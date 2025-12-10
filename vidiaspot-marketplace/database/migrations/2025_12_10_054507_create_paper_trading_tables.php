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
        Schema::create('paper_trading_accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('account_name');
            $table->decimal('initial_balance', 15, 2); // Initial virtual balance
            $table->decimal('current_balance', 15, 2); // Current virtual balance
            $table->decimal('total_pnl', 15, 2)->default(0.00); // Total profit/loss
            $table->decimal('total_pnl_percentage', 8, 4)->default(0.0000); // Total P&L percentage
            $table->decimal('unrealized_pnl', 15, 2)->default(0.00); // Unrealized P&L from open positions
            $table->decimal('max_drawdown', 8, 4)->default(0.0000); // Maximum drawdown experienced
            $table->decimal('sharpe_ratio', 8, 4)->default(0.0000); // Sharpe ratio
            $table->integer('total_trades')->default(0); // Total number of trades made
            $table->decimal('win_rate', 5, 2)->default(0.00); // Win rate percentage
            $table->string('currency')->default('USD'); // Account currency
            $table->string('status')->default('active'); // active, suspended, closed
            $table->json('account_settings')->nullable(); // Account-specific settings
            $table->timestamp('last_reset_at')->nullable(); // When the account was last reset
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('current_balance');
            $table->index('total_pnl');
            $table->index('win_rate');
            $table->index('created_at');
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
        
        Schema::create('paper_trading_positions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('account_id');
            $table->unsignedBigInteger('trading_pair_id');
            $table->string('position_type'); // long, short
            $table->decimal('entry_price', 16, 8);
            $table->decimal('current_price', 16, 8);
            $table->decimal('quantity', 16, 8);
            $table->decimal('value', 15, 2); // Current value of the position
            $table->decimal('pnl', 15, 2)->default(0.00); // Profit/loss on this position
            $table->decimal('pnl_percentage', 8, 4)->default(0.0000); // Profit/loss percentage
            $table->decimal('leverage', 5, 2)->default(1.00);
            $table->timestamp('opened_at');
            $table->timestamp('closed_at')->nullable();
            $table->string('status')->default('open'); // open, closed, liquidated
            $table->decimal('stop_loss_price', 16, 8)->nullable();
            $table->decimal('take_profit_price', 16, 8)->nullable();
            $table->decimal('liquidation_price', 16, 8)->nullable(); // Price at which position would be liquidated
            $table->json('metadata')->nullable(); // Additional position metadata
            $table->timestamps();

            $table->index(['account_id', 'status']);
            $table->index(['trading_pair_id', 'status']);
            $table->index('pnl');
            $table->index('opened_at');
            $table->index('closed_at');
            
            $table->foreign('account_id')->references('id')->on('paper_trading_accounts')->onDelete('cascade');
            $table->foreign('trading_pair_id')->references('id')->on('p2p_crypto_trading_pairs')->onDelete('cascade');
        });
        
        Schema::create('paper_trading_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('account_id');
            $table->unsignedBigInteger('trading_pair_id');
            $table->string('order_type'); // market, limit, stop, stop_limit, trailing_stop
            $table->string('side'); // buy, sell
            $table->decimal('quantity', 16, 8);
            $table->decimal('price', 16, 8)->nullable(); // Limit price
            $table->decimal('stop_price', 16, 8)->nullable(); // Stop price for stop orders
            $table->string('time_in_force')->default('GTC'); // Good Till Cancelled, Immediate or Cancel, Fill or Kill
            $table->string('status')->default('pending'); // pending, filled, partially_filled, cancelled, rejected
            $table->decimal('filled_quantity', 16, 8)->default(0.00);
            $table->decimal('average_fill_price', 16, 8)->nullable();
            $table->timestamp('created_at');
            $table->timestamp('filled_at')->nullable();
            $table->json('order_details')->nullable(); // Additional order details
            $table->timestamps();

            $table->index(['account_id', 'status']);
            $table->index(['trading_pair_id', 'status']);
            $table->index('status');
            $table->index('created_at');
            $table->index('side');
            
            $table->foreign('account_id')->references('id')->on('paper_trading_accounts')->onDelete('cascade');
            $table->foreign('trading_pair_id')->references('id')->on('p2p_crypto_trading_pairs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paper_trading_orders');
        Schema::dropIfExists('paper_trading_positions');
        Schema::dropIfExists('paper_trading_accounts');
    }
};