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
        Schema::create('trading_signals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // The signal provider
            $table->unsignedBigInteger('trading_pair_id'); // The trading pair the signal applies to
            $table->string('signal_type'); // buy, sell, strong_buy, strong_sell, hold
            $table->string('strategy_name'); // Name of the strategy that generated the signal
            $table->string('timeframe'); // 1m, 5m, 15m, 1h, 4h, 1d, etc.
            $table->decimal('entry_price', 16, 8)->nullable(); // Suggested entry price
            $table->decimal('take_profit_price', 16, 8)->nullable(); // Suggested take profit price
            $table->decimal('stop_loss_price', 16, 8)->nullable(); // Suggested stop loss price
            $table->decimal('confidence_level', 5, 2)->default(50.00); // Confidence level of the signal (0-100)
            $table->decimal('risk_level', 5, 2)->default(50.00); // Risk level of the signal (0-100)
            $table->string('status')->default('active'); // active, executed, expired, cancelled
            $table->string('source'); // technical_analysis, fundamental_analysis, news_event, etc.
            $table->text('analysis_notes')->nullable(); // Detailed analysis notes
            $table->json('additional_params')->nullable(); // Additional parameters specific to signal
            $table->timestamp('valid_until')->nullable(); // When the signal expires
            $table->timestamp('executed_at')->nullable(); // When the signal was executed
            $table->decimal('actual_return', 8, 4)->nullable(); // Actual return after execution
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['trading_pair_id', 'signal_type']);
            $table->index(['signal_type', 'confidence_level']);
            $table->index('created_at');
            $table->index('valid_until');
            $table->index('executed_at');
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('trading_pair_id')->references('id')->on('p2p_crypto_trading_pairs')->onDelete('cascade');
        });
        
        Schema::create('signal_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('subscriber_id'); // The user subscribing to signals
            $table->unsignedBigInteger('provider_id'); // The signal provider
            $table->string('subscription_type')->default('basic'); // basic, premium, elite
            $table->decimal('subscription_fee', 10, 2); // Subscription fee
            $table->string('currency')->default('USD');
            $table->timestamp('subscribed_at');
            $table->timestamp('expires_at')->nullable(); // When the subscription expires
            $table->string('status')->default('active'); // active, expired, cancelled
            $table->json('subscription_settings')->nullable(); // Settings for the subscription
            $table->timestamps();

            $table->index(['subscriber_id', 'status']);
            $table->index(['provider_id', 'status']);
            $table->index(['subscribed_at', 'expires_at']);
            $table->index('status');
            
            $table->foreign('subscriber_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('provider_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('signal_subscriptions');
        Schema::dropIfExists('trading_signals');
    }
};