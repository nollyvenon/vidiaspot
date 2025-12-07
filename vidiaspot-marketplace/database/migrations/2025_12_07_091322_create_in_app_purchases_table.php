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
        Schema::create('in_app_purchases', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_id')->unique(); // Unique transaction ID from payment gateway
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // User who made the purchase
            $table->foreignId('ad_id')->nullable()->constrained()->onDelete('set null'); // For ad promotion purchases
            $table->foreignId('vendor_id')->nullable()->constrained()->onDelete('set null'); // For vendor subscription purchases
            $table->string('purchase_type', 50)->default('promotion'); // promotion, subscription, package, etc.
            $table->string('item_type', 100); // featured_ad, premium_sub, ad_promotion, subscription_plan
            $table->string('item_id'); // ID of the specific item purchased
            $table->string('package_type')->nullable(); // basic, premium, enterprise (for packages)
            $table->decimal('amount', 10, 2); // Amount of the purchase
            $table->string('currency_code', 3)->default('NGN'); // Currency of the purchase
            $table->json('purchase_details')->nullable(); // Additional details about the purchase
            $table->string('payment_gateway'); // paystack, flutterwave, stripe, paypal, etc.
            $table->string('status')->default('pending'); // pending, completed, failed, refunded
            $table->timestamp('completed_at')->nullable(); // When the purchase was completed
            $table->timestamp('expires_at')->nullable(); // When the purchase benefits expire
            $table->json('metadata')->nullable(); // Additional metadata from payment gateway
            $table->timestamps();

            // Indexes for performance
            $table->index(['purchase_type', 'status']);
            $table->index('user_id');
            $table->index('transaction_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('in_app_purchases');
    }
};
