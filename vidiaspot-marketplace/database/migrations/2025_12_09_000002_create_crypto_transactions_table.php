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
        Schema::create('crypto_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('crypto_currency_id');
            $table->string('transaction_type'); // buy, sell, transfer
            $table->decimal('amount', 15, 8);
            $table->decimal('rate', 15, 8); // exchange rate at transaction time
            $table->decimal('total_value', 15, 8); // amount * rate
            $table->string('status'); // pending, completed, failed, cancelled
            $table->unsignedBigInteger('related_transaction_id')->nullable(); // for matching trades
            $table->timestamp('executed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('crypto_currency_id')->references('id')->on('crypto_currencies')->onDelete('cascade');
            $table->foreign('related_transaction_id')->references('id')->on('crypto_transactions')->onDelete('set null');
            
            $table->index(['user_id', 'transaction_type', 'status']);
            $table->index(['crypto_currency_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crypto_transactions');
    }
};