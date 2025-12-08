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
        Schema::create('food_order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('food_order_id');
            $table->unsignedBigInteger('food_menu_item_id');
            $table->string('name');
            $table->decimal('price', 10, 2);
            $table->integer('quantity')->default(1);
            $table->decimal('total_price', 10, 2);
            $table->text('special_instructions')->nullable();
            $table->json('customization_options')->nullable();
            $table->json('item_addons')->nullable(); // additional items ordered with this menu item
            $table->timestamps();

            // Indexes
            $table->index('food_order_id');
            $table->index('food_menu_item_id');
            $table->index(['food_order_id', 'food_menu_item_id']);

            // Foreign key constraints
            $table->foreign('food_order_id')->references('id')->on('food_orders')->onDelete('cascade');
            $table->foreign('food_menu_item_id')->references('id')->on('food_menu_items')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('food_order_items');
    }
};
