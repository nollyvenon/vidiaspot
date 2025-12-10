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
        Schema::create('p2p_crypto_trade_executions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('trading_order_id');
            $table->unsignedBigInteger('maker_order_id')->nullable(); // order that was already in the book
            $table->unsignedBigInteger('taker_order_id')->nullable(); // order that matched with the book order
            $table->unsignedBigInteger('trading_pair_id');
            $table->string('side'); // buy, sell
            $table->decimal('quantity', 15, 8);
            $table->decimal('price', 15, 8);
            $table->decimal('fee', 15, 8)->default(0);
            $table->string('fee_currency')->default('USDT');
            $table->string('fee_payer'); // maker, taker
            $table->timestamp('executed_at');
            $table->timestamps();
            
            $table->foreign('trading_order_id')->references('id')->on('p2p_crypto_trading_orders')->onDelete('cascade');
            $table->foreign('maker_order_id')->references('id')->on('p2p_crypto_trading_orders')->onDelete('set null');
            $table->foreign('taker_order_id')->references('id')->on('p2p_crypto_trading_orders')->onDelete('set null');
            $table->foreign('trading_pair_id')->references('id')->on('p2p_crypto_trading_pairs')->onDelete('cascade');
            
            $table->index(['trading_order_id', 'executed_at']);
            $table->index(['trading_pair_id', 'executed_at']);
            $table->index(['executed_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('p2p_crypto_trade_executions');
    }
};