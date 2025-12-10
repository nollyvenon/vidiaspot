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
        Schema::create('market_data', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('trading_pair_id');
            $table->decimal('open_price', 16, 8);
            $table->decimal('high_price', 16, 8);
            $table->decimal('low_price', 16, 8);
            $table->decimal('close_price', 16, 8);
            $table->decimal('volume', 20, 8);
            $table->decimal('quote_volume', 20, 8);
            $table->unsignedBigInteger('trade_count');
            $table->decimal('prev_close_price', 16, 8)->nullable();
            $table->decimal('change', 16, 8)->nullable();
            $table->decimal('change_percent', 8, 4)->nullable();
            $table->timestamp('timestamp');
            $table->string('interval'); // 1m, 5m, 15m, 30m, 1h, 4h, 1d, 1w, 1M
            $table->timestamps();

            $table->index(['trading_pair_id', 'timestamp']);
            $table->index(['trading_pair_id', 'interval']);
            $table->index('timestamp');
            $table->index('interval');
            
            $table->foreign('trading_pair_id')->references('id')->on('p2p_crypto_trading_pairs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('market_data');
    }
};