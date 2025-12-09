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
        Schema::create('escrows', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('transaction_id'); // Related transaction
            $table->unsignedBigInteger('ad_id'); // Related ad
            $table->unsignedBigInteger('buyer_user_id'); // Buyer
            $table->unsignedBigInteger('seller_user_id'); // Seller
            $table->decimal('amount', 10, 2); // Amount held in escrow
            $table->string('currency')->default('NGN'); // Currency
            $table->string('status')->default('pending'); // 'pending', 'released', 'refunded', 'disputed', 'completed'
            $table->string('dispute_status')->nullable(); // 'none', 'buyer_disputed', 'seller_disputed', 'under_review', 'resolved'
            $table->timestamp('release_date')->nullable(); // When funds should be released
            $table->timestamp('dispute_resolved_at')->nullable(); // When dispute was resolved
            $table->json('dispute_details')->nullable(); // Details about the dispute
            $table->json('release_conditions')->nullable(); // Conditions for releasing funds
            $table->text('notes')->nullable(); // Additional notes
            $table->timestamps();

            $table->index(['transaction_id', 'status']);
            $table->index(['buyer_user_id', 'status']);
            $table->index(['seller_user_id', 'status']);
            $table->index(['ad_id', 'status']);
            $table->index('status');
            $table->index('dispute_status');

            $table->foreign('ad_id')->references('id')->on('ads')->onDelete('cascade');
            $table->foreign('buyer_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('seller_user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('escrows');
    }
};
