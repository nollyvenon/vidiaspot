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
        Schema::create('buyer_protections', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // Who bought the protection
            $table->unsignedBigInteger('transaction_id'); // The transaction this protects
            $table->string('transaction_type'); // ad_purchase, food_order, insurance_purchase, service_booking
            $table->string('transaction_reference'); // Reference number of the original transaction
            $table->unsignedBigInteger('provider_id')->nullable(); // ID of the insurance/provider company
            $table->string('policy_number')->nullable();
            $table->decimal('coverage_amount', 15, 2)->default(0.00);
            $table->decimal('premium_amount', 10, 2)->default(0.00);
            $table->string('status')->default('pending'); // active, pending, expired, claimed, cancelled
            $table->string('protection_type')->nullable(); // full_refund, partial_refund, replacement, repair
            $table->json('coverage_terms')->nullable(); // Terms and conditions
            $table->json('exclusions')->nullable(); // What's not covered
            $table->string('claim_status')->default('no_claim'); // no_claim, pending, approved, rejected
            $table->decimal('claim_amount', 15, 2)->default(0.00); // Amount claimed
            $table->decimal('claim_resolved_amount', 15, 2)->default(0.00); // Amount actually paid
            $table->timestamp('claim_date')->nullable();
            $table->timestamp('claim_resolution_date')->nullable();
            $table->json('claim_details')->nullable();
            $table->string('support_ticket_id')->nullable(); // Link to support ticket for the claim
            $table->timestamp('renewal_date')->nullable();
            $table->timestamp('purchase_date')->nullable();
            $table->timestamp('expiry_date')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('user_id');
            $table->index('transaction_id');
            $table->index('transaction_type');
            $table->index('policy_number');
            $table->index('status');
            $table->index('claim_status');
            $table->index(['user_id', 'status']); // For user's protection history
            $table->index(['transaction_id', 'transaction_type']); // For transaction lookup
            $table->index(['status', 'expiry_date']); // For expired protection cleanup
            $table->index('purchase_date');
            $table->index('expiry_date');
            $table->index('claim_date');
            $table->index('provider_id');

            // Foreign key constraints
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('provider_id')->references('id')->on('insurance_providers')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buyer_protections');
    }
};
