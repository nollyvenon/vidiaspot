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
        Schema::create('p2p_crypto_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('seller_id');
            $table->unsignedBigInteger('buyer_id')->nullable();
            $table->unsignedBigInteger('crypto_currency_id');
            $table->string('order_type'); // buy, sell
            $table->decimal('amount', 15, 8);
            $table->decimal('price_per_unit', 15, 8);
            $table->decimal('total_amount', 15, 8); // amount * price_per_unit
            $table->string('payment_method');
            $table->string('status'); // active, matched, in_progress, completed, cancelled, failed
            $table->timestamp('matched_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('terms_and_conditions')->nullable();
            $table->text('additional_notes')->nullable();
            $table->unsignedBigInteger('crypto_transaction_id')->nullable();
            $table->unsignedBigInteger('payment_transaction_id')->nullable();
            $table->timestamps();
            
            $table->foreign('seller_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('buyer_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('crypto_currency_id')->references('id')->on('crypto_currencies')->onDelete('cascade');
            $table->foreign('crypto_transaction_id')->references('id')->on('crypto_transactions')->onDelete('set null');
            $table->foreign('payment_transaction_id')->references('id')->on('payment_transactions')->onDelete('set null');
            
            $table->index(['seller_id', 'status']);
            $table->index(['buyer_id', 'status']);
            $table->index(['crypto_currency_id', 'order_type', 'status']);
            $table->index(['status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('p2p_crypto_orders');
    }
};