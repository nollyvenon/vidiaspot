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
        Schema::create('copy_trading', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('master_trader_id'); // The trader whose trades are being copied
            $table->unsignedBigInteger('copy_trader_id'); // The trader copying the trades
            $table->unsignedBigInteger('original_order_id')->nullable(); // Original order being copied
            $table->unsignedBigInteger('copied_order_id')->nullable(); // The copy of the order
            $table->string('status')->default('active'); // active, closed, cancelled
            $table->string('type'); // copy, mirror, follow
            $table->decimal('allocation_percentage', 5, 2)->default(100.00); // Percentage of funds to allocate
            $table->decimal('allocation_amount', 15, 2); // Actual amount being allocated
            $table->string('allocation_currency');
            $table->decimal('leverage_multiplier', 5, 2)->default(1.00); // Leverage multiplier for copied trades
            $table->json('copy_settings'); // Settings like max_loss_percent, stop_loss_adjustment, etc.
            $table->decimal('entry_price', 16, 8)->nullable(); // Entry price for the copied trade
            $table->decimal('exit_price', 16, 8)->nullable(); // Exit price for the copied trade
            $table->decimal('profit_loss', 15, 2)->default(0.00); // Profit/loss on the copied trade
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();

            $table->index(['master_trader_id', 'status']);
            $table->index(['copy_trader_id', 'status']);
            $table->index(['original_order_id', 'copied_order_id']);
            $table->index('opened_at');
            $table->index('closed_at');
            $table->index('profit_loss');
            
            $table->foreign('master_trader_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('copy_trader_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('original_order_id')->references('id')->on('p2p_crypto_trading_orders')->onDelete('set null');
            $table->foreign('copied_order_id')->references('id')->on('p2p_crypto_trading_orders')->onDelete('set null');
        });
        
        Schema::create('trader_performance', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('period'); // daily, weekly, monthly, quarterly, yearly
            $table->date('period_date'); // Date that represents the period
            $table->decimal('return_percentage', 8, 4)->default(0.0000); // Return percentage
            $table->decimal('return_amount', 15, 2)->default(0.00); // Return amount in currency
            $table->string('currency')->default('USD');
            $table->decimal('total_trades', 10, 0)->default(0); // Total number of trades
            $table->decimal('win_rate', 5, 2)->default(0.00); // Win rate percentage
            $table->decimal('profit_factor', 8, 4)->default(0.0000); // Profit factor
            $table->decimal('max_drawdown', 8, 4)->default(0.0000); // Maximum drawdown
            $table->decimal('sharpe_ratio', 8, 4)->default(0.0000); // Sharpe ratio
            $table->json('trade_details')->nullable(); // Detailed trade statistics
            $table->timestamps();

            $table->index(['user_id', 'period_date']);
            $table->index(['user_id', 'period']);
            $table->index('return_percentage');
            $table->index('period_date');
            $table->index('win_rate');
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trader_performance');
        Schema::dropIfExists('copy_trading');
    }
};