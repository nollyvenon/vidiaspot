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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('vendor_store_id')->nullable(); // Optional, since ads can be from non-store users too
            $table->string('order_number')->unique(); // e.g., ORD-20231208-001
            $table->string('status')->default('pending'); // pending, confirmed, shipped, delivered, cancelled
            $table->decimal('total_amount', 15, 2);
            $table->string('currency')->default('NGN');
            $table->integer('quantity')->default(1);
            $table->string('payment_method')->nullable();
            $table->string('payment_status')->default('pending'); // pending, paid, failed, refunded
            $table->json('shipping_address')->nullable();
            $table->json('billing_address')->nullable();
            $table->string('customer_email');
            $table->string('customer_phone')->nullable();
            $table->string('customer_name');
            $table->string('shipping_method')->nullable();
            $table->decimal('shipping_cost', 10, 2)->default(0);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->json('order_items'); // Array of items in the order
            $table->string('tracking_number')->nullable();
            $table->timestamp('estimated_delivery')->nullable();
            $table->string('fulfillment_status')->default('pending'); // pending, processing, shipped, delivered
            $table->timestamp('cancelled_at')->nullable();
            $table->string('cancelled_reason')->nullable();
            $table->timestamps();

            // Indexes for better performance
            $table->index('user_id');
            $table->index('vendor_store_id');
            $table->index('order_number');
            $table->index('status');
            $table->index('payment_status');
            $table->index(['user_id', 'status']);
            $table->index(['vendor_store_id', 'status']);

            // Foreign key constraints
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('vendor_store_id')->references('id')->on('vendor_stores')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
