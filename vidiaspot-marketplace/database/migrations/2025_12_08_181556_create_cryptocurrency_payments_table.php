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
        Schema::create('cryptocurrency_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('payment_transaction_id'); // Reference to payment transactions
            $table->string('crypto_currency'); // 'BTC', 'ETH', 'USDT', etc.
            $table->string('wallet_address'); // Destination wallet address
            $table->decimal('amount_crypto', 16, 8); // Amount in cryptocurrency
            $table->decimal('amount_ngn', 10, 2); // Equivalent amount in naira
            $table->decimal('exchange_rate', 16, 8); // Exchange rate at time of transaction
            $table->string('transaction_hash')->nullable(); // Blockchain transaction hash
            $table->string('status'); // 'pending', 'confirmed', 'failed', 'expired'
            $table->string('network')->nullable(); // 'bitcoin', 'ethereum', etc.
            $table->timestamp('confirmed_at')->nullable(); // When confirmed on blockchain
            $table->timestamp('expires_at')->nullable(); // When the payment expires
            $table->json('raw_data')->nullable(); // Raw blockchain data
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('crypto_currency');
            $table->index('transaction_hash');
            $table->index('confirmed_at');

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('payment_transaction_id')->references('id')->on('payment_transactions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cryptocurrency_payments');
    }
};
