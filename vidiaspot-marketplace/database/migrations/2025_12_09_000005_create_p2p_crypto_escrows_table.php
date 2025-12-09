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
        Schema::create('p2p_crypto_escrows', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('p2p_order_id');
            $table->unsignedBigInteger('crypto_transaction_id');
            $table->decimal('amount', 15, 8);
            $table->string('status'); // held, released, refunded
            $table->timestamp('released_at')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->text('release_notes')->nullable();
            $table->text('refund_notes')->nullable();
            $table->timestamps();
            
            $table->foreign('p2p_order_id')->references('id')->on('p2p_crypto_orders')->onDelete('cascade');
            $table->foreign('crypto_transaction_id')->references('id')->on('crypto_transactions')->onDelete('cascade');
            
            $table->unique('p2p_order_id');
            $table->index(['status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('p2p_crypto_escrows');
    }
};