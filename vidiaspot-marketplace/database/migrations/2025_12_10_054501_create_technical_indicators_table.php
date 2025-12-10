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
        Schema::create('technical_indicators', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('trading_pair_id');
            $table->string('indicator_type'); // rsi, macd, bollinger_bands, moving_average, etc.
            $table->string('interval'); // 1m, 5m, 15m, 30m, 1h, 4h, 1d
            $table->json('indicator_values'); // Store indicator values as JSON (e.g., {rsi: 65.2, signal: 1.2, histogram: 0.5})
            $table->timestamp('calculated_at');
            $table->decimal('value', 16, 8)->nullable(); // Primary indicator value
            $table->string('signal')->nullable(); // buy, sell, hold
            $table->json('additional_params')->nullable(); // Additional parameters specific to indicator type
            $table->timestamps();

            $table->index(['trading_pair_id', 'indicator_type']);
            $table->index(['trading_pair_id', 'interval']);
            $table->index('indicator_type');
            $table->index('calculated_at');
            $table->index('value');
            
            $table->foreign('trading_pair_id')->references('id')->on('p2p_crypto_trading_pairs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('technical_indicators');
    }
};