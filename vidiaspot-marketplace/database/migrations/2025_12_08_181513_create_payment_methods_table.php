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
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // Which user owns this payment method
            $table->string('method_type'); // 'credit_card', 'paypal', 'bitcoin', 'ethereum', 'mpesa', 'mobile_money', 'qr_code', 'klarna', 'afterpay'
            $table->string('method_name'); // Name for user reference
            $table->string('provider')->nullable(); // Provider name like 'Visa', 'Bitcoin', 'MPesa'
            $table->string('identifier')->nullable(); // Tokenized/encrypted identifier for payment method
            $table->json('details')->nullable(); // Store provider-specific details
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamp('expires_at')->nullable(); // For methods that expire
            $table->timestamps();

            $table->index(['user_id', 'is_default']);
            $table->index(['method_type', 'is_active']);
            $table->index('provider');

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
