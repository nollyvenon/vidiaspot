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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_id')->unique(); // Payment gateway transaction ID
            $table->string('payment_gateway', 50); // paystack, flutterwave, stripe, paypal
            $table->string('payment_method', 50)->nullable(); // credit_card, bank_transfer, etc.
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('ad_id')->nullable()->constrained()->onDelete('set null'); // For ad-related payments
            $table->foreignId('subscription_id')->nullable()->constrained()->onDelete('set null'); // For subscription payments
            $table->string('currency_code', 3)->default('NGN');
            $table->decimal('amount', 10, 2);
            $table->decimal('fees', 10, 2)->default(0);
            $table->string('status', 20)->default('pending'); // pending, completed, failed, refunded
            $table->json('metadata')->nullable(); // Payment gateway response data
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            // Indexes for performance
            $table->index(['payment_gateway', 'status']); // Fixed: these are short enough
            $table->index('user_id');
            $table->index('transaction_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
