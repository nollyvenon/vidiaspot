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
        Schema::create('p2p_crypto_trading_pairs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('base_currency_id'); // e.g., BTC
            $table->unsignedBigInteger('quote_currency_id'); // e.g., USDT
            $table->string('pair_name'); // e.g., BTC/USDT
            $table->string('symbol'); // e.g., BTCUSDT
            $table->decimal('min_price', 15, 8);
            $table->decimal('max_price', 15, 8);
            $table->decimal('min_quantity', 15, 8);
            $table->decimal('max_quantity', 15, 8);
            $table->decimal('price_tick_size', 15, 8); // minimum price increment
            $table->decimal('quantity_step_size', 15, 8); // minimum quantity increment
            $table->string('status')->default('active'); // active, inactive, suspended
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->foreign('base_currency_id')->references('id')->on('crypto_currencies')->onDelete('cascade');
            $table->foreign('quote_currency_id')->references('id')->on('crypto_currencies')->onDelete('cascade');
            
            $table->unique(['base_currency_id', 'quote_currency_id']);
            $table->index(['pair_name', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('p2p_crypto_trading_pairs');
    }
};