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
        Schema::create('promotion_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('ad_id')->constrained()->onDelete('cascade');
            $table->foreignId('promotion_id')->constrained()->onDelete('cascade');
            $table->string('order_reference')->unique(); // Unique reference for the order
            $table->decimal('total_amount', 10, 2); // Total amount paid
            $table->string('currency', 3)->default('NGN'); // Currency code
            $table->string('status')->default('pending'); // pending, paid, active, expired, cancelled
            $table->integer('duration_days'); // How many days the promotion will run
            $table->dateTime('start_date'); // When the promotion starts
            $table->dateTime('end_date'); // When the promotion ends
            $table->json('features_applied'); // Features that were applied to the ad
            $table->json('promotion_settings'); // Specific settings for this promotion instance
            $table->timestamp('activated_at')->nullable(); // When the promotion was activated
            $table->timestamp('deactivated_at')->nullable(); // When the promotion was deactivated
            $table->json('activity_log'); // Log of activities during the promotion
            $table->boolean('auto_renew')->default(false); // Whether to auto renew when expires
            $table->foreignId('payment_transaction_id')->nullable()->constrained('payment_transactions')->onDelete('set null');
            $table->timestamps();

            // Indexes for performance
            $table->index(['user_id', 'status']);
            $table->index(['ad_id', 'status']);
            $table->index(['promotion_id', 'status']);
            $table->index('order_reference');
            $table->index('start_date');
            $table->index('end_date');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promotion_orders');
    }
};
