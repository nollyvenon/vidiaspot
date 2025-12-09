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
        Schema::create('shopping_carts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable(); // For logged in users
            $table->string('session_id')->nullable(); // For guest users
            $table->unsignedBigInteger('ad_id'); // The ad/product being added to cart
            $table->integer('quantity')->default(1);
            $table->decimal('price', 10, 2); // Price at time of adding to cart
            $table->decimal('total_price', 10, 2); // Price * Quantity
            $table->json('selected_options')->nullable(); // For product variations (size, color, etc.)
            $table->timestamps();

            $table->index(['user_id', 'ad_id']); // Index for user-specific cart items
            $table->index(['session_id', 'ad_id']); // Index for session-specific cart items
            $table->index('ad_id'); // Index for quick lookups by ad

            // Foreign key constraints
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('ad_id')->references('id')->on('ads')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shopping_carts');
    }
};
