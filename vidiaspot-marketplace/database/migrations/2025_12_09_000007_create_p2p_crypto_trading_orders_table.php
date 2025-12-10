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
        Schema::create('p2p_crypto_trading_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('trading_pair_id');
            $table->string('order_type'); // market, limit, stop_loss, trailing_stop, etc.
            $table->string('side'); // buy, sell
            $table->decimal('quantity', 15, 8);
            $table->decimal('executed_quantity', 15, 8)->default(0);
            $table->decimal('price', 15, 8)->nullable(); // for limit orders
            $table->decimal('stop_price', 15, 8)->nullable(); // for stop orders
            $table->decimal('avg_price', 15, 8)->default(0); // average execution price
            $table->string('status'); // pending, partially_filled, filled, cancelled, rejected
            $table->string('time_in_force'); // GTC, IOC, FOK, GTD
            $table->timestamp('good_till_date')->nullable();
            $table->decimal('fee', 15, 8)->default(0);
            $table->string('fee_currency')->default('USDT');
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('trading_pair_id')->references('id')->on('p2p_crypto_trading_pairs')->onDelete('cascade');
            
            $table->index(['user_id', 'status']);
            $table->index(['trading_pair_id', 'status']);
            $table->index(['side', 'status']);
            $table->index(['created_at', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('p2p_crypto_trading_orders');
    }
};