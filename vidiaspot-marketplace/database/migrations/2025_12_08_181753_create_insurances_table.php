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
        Schema::create('insurances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // Who purchased the insurance
            $table->unsignedBigInteger('ad_id'); // Which ad this insurance is for
            $table->unsignedBigInteger('payment_transaction_id'); // Reference to the payment
            $table->string('insurance_type'); // 'device_protection', 'product_insurance', 'delivery_insurance', 'high_value_item'
            $table->string('provider'); // Insurance provider name
            $table->string('policy_number'); // Policy number assigned by insurer
            $table->decimal('premium_amount', 10, 2); // Amount paid for insurance
            $table->decimal('coverage_amount', 10, 2); // Maximum coverage amount
            $table->string('status'); // 'active', 'claimed', 'expired', 'cancelled'
            $table->string('risk_level'); // 'low', 'medium', 'high'
            $table->date('effective_from'); // When coverage starts
            $table->date('effective_until'); // When coverage ends
            $table->text('exclusions')->nullable(); // Terms not covered
            $table->json('beneficiaries')->nullable(); // Who is covered
            $table->json('claim_process')->nullable(); // Steps for claiming
            $table->timestamp('last_claim_date')->nullable(); // When last claim was made
            $table->integer('total_claims')->default(0); // Number of claims made
            $table->json('documents')->nullable(); // Insurance documents
            $table->text('terms_and_conditions')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('ad_id');
            $table->index('policy_number');
            $table->index(['insurance_type', 'risk_level']);
            $table->index(['effective_from', 'effective_until']);

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('ad_id')->references('id')->on('ads')->onDelete('cascade');
            $table->foreign('payment_transaction_id')->references('id')->on('payment_transactions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('insurances');
    }
};
