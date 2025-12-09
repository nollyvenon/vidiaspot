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
        Schema::create('p2p_crypto_trade_disputes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('p2p_order_id');
            $table->unsignedBigInteger('initiator_user_id'); // user who initiated the dispute
            $table->string('dispute_type'); // payment_not_received, payment_not_made, other
            $table->text('description');
            $table->string('status'); // open, in_review, resolved, rejected
            $table->text('resolution_notes')->nullable();
            $table->unsignedBigInteger('resolved_by')->nullable(); // admin/moderator who resolved
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
            
            $table->foreign('p2p_order_id')->references('id')->on('p2p_crypto_orders')->onDelete('cascade');
            $table->foreign('initiator_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('resolved_by')->references('id')->on('users')->onDelete('set null');
            
            $table->index(['p2p_order_id', 'status']);
            $table->index(['status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('p2p_crypto_trade_disputes');
    }
};