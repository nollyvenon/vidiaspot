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
        Schema::create('food_vendors', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('cuisine_type'); // Italian, Chinese, Indian, etc.
            $table->json('address'); // {street, city, state, country, postal_code}
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('contact_email')->nullable();
            $table->time('opening_time')->nullable();
            $table->time('closing_time')->nullable();
            $table->decimal('delivery_radius', 5, 2)->default(5.00); // in km
            $table->decimal('min_order_amount', 10, 2)->default(0.00);
            $table->decimal('delivery_fee', 8, 2)->default(0.00);
            $table->integer('estimated_delivery_time')->default(30); // in minutes
            $table->string('image_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_verified')->default(false);
            $table->decimal('rating', 3, 2)->default(0.00);
            $table->integer('total_reviews')->default(0);
            $table->json('payment_methods')->nullable(); // ['cash', 'card', 'digital_wallet']
            $table->json('dietary_options')->nullable(); // ['vegetarian', 'vegan', 'gluten_free']
            $table->json('special_offers')->nullable();
            $table->json('operating_days')->nullable(); // {monday: {open, close}, tuesday: {open, close}, etc.}
            $table->decimal('tax_percentage', 5, 2)->default(0.00);
            $table->decimal('commission_rate', 5, 2)->default(15.00); // Platform commission
            $table->boolean('accepting_orders')->default(true);
            $table->json('vendor_settings')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('user_id');
            $table->index('cuisine_type');
            $table->index('is_active');
            $table->index('is_verified');
            $table->index('rating');
            $table->index(['latitude', 'longitude']);
            $table->index('accepting_orders');

            // Foreign key constraints
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('food_vendors');
    }
};
