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
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('ad_id');
            $table->string('transaction_id');
            $table->string('payment_method');
            $table->decimal('amount', 10, 2);
            $table->string('currency')->default('NGN');
            $table->string('status')->default('pending');
            $table->string('payment_gateway')->nullable();
            $table->string('provider_reference')->nullable();
            $table->json('payment_details')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->string('callback_url')->nullable();
            $table->json('metadata')->nullable();

            // Tax fields added via the separate migration
            $table->string('tax_region')->nullable();
            $table->decimal('tax_rate', 5, 2)->nullable();
            $table->decimal('tax_amount', 10, 2)->nullable();
            $table->decimal('total_amount_with_tax', 10, 2)->nullable();
            $table->json('tax_breakdown')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['ad_id', 'created_at']);
            $table->index('transaction_id');
            $table->index('payment_gateway');
            $table->index(['created_at', 'status']);

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('ad_id')->references('id')->on('ads')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};
