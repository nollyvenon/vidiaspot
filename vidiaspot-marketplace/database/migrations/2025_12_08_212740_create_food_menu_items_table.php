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
        Schema::create('food_menu_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('food_vendor_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('category'); // appetizers, main_course, desserts, drinks
            $table->decimal('price', 10, 2);
            $table->decimal('original_price', 10, 2)->nullable(); // for discounts
            $table->string('image_url')->nullable();
            $table->json('ingredients')->nullable(); // list of ingredients
            $table->json('allergens')->nullable(); // contains nuts, dairy, gluten, etc.
            $table->json('dietary_options')->nullable(); // vegetarian, vegan, gluten_free
            $table->string('spice_level')->default('medium'); // mild, medium, hot
            $table->boolean('is_available')->default(true);
            $table->boolean('is_popular')->default(false);
            $table->boolean('is_new')->default(false);
            $table->integer('max_quantity_per_order')->default(10);
            $table->integer('preparation_time')->default(15); // in minutes
            $table->json('customization_options')->nullable(); // {option_name: [choices], ...}
            $table->string('serving_size')->nullable(); // small, medium, large, 1 piece, 2 pieces, etc.
            $table->integer('calories')->nullable(); // calorie count
            $table->json('item_settings')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('food_vendor_id');
            $table->index('category');
            $table->index('is_available');
            $table->index('is_popular');
            $table->index('price');
            $table->index(['food_vendor_id', 'category']);
            $table->index(['food_vendor_id', 'is_available']);

            // Foreign key constraints
            $table->foreign('food_vendor_id')->references('id')->on('food_vendors')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('food_menu_items');
    }
};
