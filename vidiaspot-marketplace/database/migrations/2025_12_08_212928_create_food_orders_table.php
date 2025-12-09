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
        Schema::create('food_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('food_vendor_id');
            $table->string('order_number')->unique();
            $table->string('status')->default('pending'); // pending, confirmed, preparing, ready, out_for_delivery, delivered, cancelled, refunded
            $table->decimal('total_amount', 15, 2);
            $table->string('currency')->default('NGN');
            $table->integer('quantity')->default(1);
            $table->string('payment_method')->nullable();
            $table->string('payment_status')->default('pending'); // pending, paid, failed, refunded
            $table->json('delivery_address'); // {street, city, state, country, postal_code}
            $table->string('customer_email');
            $table->string('customer_phone')->nullable();
            $table->string('customer_name');
            $table->text('delivery_instructions')->nullable();
            $table->decimal('delivery_fee', 8, 2)->default(0.00);
            $table->decimal('tax_amount', 10, 2)->default(0.00);
            $table->decimal('discount_amount', 10, 2)->default(0.00);
            $table->decimal('tip_amount', 8, 2)->default(0.00);
            $table->text('notes')->nullable();
            $table->json('order_items'); // {menu_item_id, name, price, quantity, total, special_instructions, customization}
            $table->unsignedBigInteger('driver_id')->nullable();
            $table->string('driver_name')->nullable();
            $table->string('driver_phone')->nullable();
            $table->timestamp('estimated_delivery_time')->nullable();
            $table->timestamp('actual_delivery_time')->nullable();
            $table->string('delivery_status')->default('pending'); // pending, assigned, picked_up, on_the_way, delivered
            $table->string('order_type')->default('delivery'); // delivery, pickup
            $table->timestamp('scheduled_time')->nullable(); // for scheduled orders
            $table->text('special_instructions')->nullable();
            $table->decimal('packaging_fee', 8, 2)->default(0.00);
            $table->decimal('service_fee', 8, 2)->default(0.00);
            $table->integer('order_rating')->nullable(); // 1-5 rating
            $table->text('order_feedback')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->string('cancelled_reason')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->decimal('refunded_amount', 10, 2)->default(0.00);
            $table->timestamps();

            // Indexes
            $table->index('user_id');
            $table->index('food_vendor_id');
            $table->index('order_number');
            $table->index('status');
            $table->index('payment_status');
            $table->index('order_type');
            $table->index(['user_id', 'status']);
            $table->index(['food_vendor_id', 'status']);
            $table->index(['user_id', 'order_type']);
            $table->index('delivery_status');

            // Foreign key constraints
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('food_vendor_id')->references('id')->on('food_vendors')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('food_orders');
    }
};
