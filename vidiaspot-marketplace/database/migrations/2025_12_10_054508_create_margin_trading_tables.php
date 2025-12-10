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
        Schema::create('margin_accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('account_type')->default('isolated'); // isolated, cross
            $table->decimal('total_balance', 15, 2); // Total balance in the account
            $table->decimal('used_balance', 15, 2)->default(0.00); // Balance currently used in positions
            $table->decimal('available_balance', 15, 2); // Available balance for new positions
            $table->decimal('borrowed_amount', 15, 2)->default(0.00); // Total amount borrowed
            $table->decimal('unrealized_pnl', 15, 2)->default(0.00); // Unrealized profit/loss
            $table->decimal('maint_margin_ratio', 8, 4)->default(0.0100); // Maintenance margin ratio (1% default)
            $table->decimal('liquidation_price', 16, 8)->nullable(); // Price at which account will be liquidated
            $table->decimal('margin_ratio', 8, 4)->nullable(); // Current margin ratio
            $table->decimal('leverage', 5, 2)->default(1.00); // Current leverage being used
            $table->string('currency')->default('USD'); // Account currency
            $table->string('status')->default('active'); // active, liquidated, suspended
            $table->timestamp('last_updated')->nullable(); // When account was last updated
            $table->json('account_settings')->nullable(); // Account-specific settings like max_leverage
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('total_balance');
            $table->index('margin_ratio');
            $table->index('liquidation_price');
            $table->index('leverage');
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
        
        Schema::create('margin_positions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('account_id');
            $table->unsignedBigInteger('trading_pair_id');
            $table->string('position_type'); // long, short
            $table->decimal('entry_price', 16, 8);
            $table->decimal('current_price', 16, 8);
            $table->decimal('quantity', 16, 8);
            $table->decimal('base_quantity', 16, 8); // Quantity in base currency
            $table->decimal('quote_quantity', 16, 8); // Quantity in quote currency
            $table->decimal('value', 15, 2); // Current value of the position
            $table->decimal('notional_value', 15, 2); // Notional value of the position
            $table->decimal('borrowed_amount', 15, 2); // Amount borrowed for this position
            $table->decimal('interest_rate', 8, 4)->default(0.0000); // Interest rate for borrowed amount
            $table->decimal('accrued_interest', 10, 4)->default(0.0000); // Accrued interest
            $table->decimal('pnl', 15, 2)->default(0.00); // Profit/loss on this position
            $table->decimal('pnl_percentage', 8, 4)->default(0.0000); // Profit/loss percentage
            $table->decimal('leverage', 5, 2); // Leverage used for this position
            $table->decimal('margin_used', 15, 2); // Margin used for this position
            $table->decimal('liquidation_price', 16, 8)->nullable(); // Liquidation price for this position
            $table->decimal('liquidation_fee', 10, 4)->default(0.0000); // Fee charged upon liquidation
            $table->timestamp('opened_at');
            $table->timestamp('closed_at')->nullable();
            $table->string('status')->default('open'); // open, closed, liquidated
            $table->decimal('stop_loss_price', 16, 8)->nullable();
            $table->decimal('take_profit_price', 16, 8)->nullable();
            $table->json('metadata')->nullable(); // Additional position metadata
            $table->timestamps();

            $table->index(['account_id', 'status']);
            $table->index(['trading_pair_id', 'status']);
            $table->index('pnl');
            $table->index('leverage');
            $table->index('liquidation_price');
            $table->index('opened_at');
            $table->index('closed_at');
            
            $table->foreign('account_id')->references('id')->on('margin_accounts')->onDelete('cascade');
            $table->foreign('trading_pair_id')->references('id')->on('p2p_crypto_trading_pairs')->onDelete('cascade');
        });
        
        Schema::create('margin_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('account_id');
            $table->unsignedBigInteger('trading_pair_id');
            $table->string('order_type'); // market, limit, stop, stop_limit, trailing_stop
            $table->string('side'); // buy, sell
            $table->string('margin_type'); // isolated, cross
            $table->decimal('quantity', 16, 8);
            $table->decimal('price', 16, 8)->nullable(); // Limit price
            $table->decimal('stop_price', 16, 8)->nullable(); // Stop price for stop orders
            $table->decimal('leverage', 5, 2)->default(1.00); // Leverage to use for the order
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
            $table->index('side');
            $table->index('leverage');
            $table->index('created_at');
            
            $table->foreign('account_id')->references('id')->on('margin_accounts')->onDelete('cascade');
            $table->foreign('trading_pair_id')->references('id')->on('p2p_crypto_trading_pairs')->onDelete('cascade');
        });
        
        Schema::create('margin_borrowings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('account_id');
            $table->unsignedBigInteger('trading_pair_id');
            $table->string('asset'); // The borrowed asset
            $table->decimal('amount', 16, 8); // Amount borrowed
            $table->decimal('interest_rate', 8, 4); // Interest rate for borrowing
            $table->decimal('accrued_interest', 10, 4)->default(0.0000); // Accrued interest
            $table->timestamp('borrowed_at');
            $table->timestamp('repaid_at')->nullable();
            $table->string('status')->default('active'); // active, repaid, overdue
            $table->json('repayment_history')->nullable(); // History of repayments
            $table->timestamps();

            $table->index(['account_id', 'status']);
            $table->index(['trading_pair_id', 'status']);
            $table->index(['asset', 'status']);
            $table->index('borrowed_at');
            $table->index('repaid_at');
            
            $table->foreign('account_id')->references('id')->on('margin_accounts')->onDelete('cascade');
            $table->foreign('trading_pair_id')->references('id')->on('p2p_crypto_trading_pairs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('margin_borrowings');
        Schema::dropIfExists('margin_orders');
        Schema::dropIfExists('margin_positions');
        Schema::dropIfExists('margin_accounts');
    }
};