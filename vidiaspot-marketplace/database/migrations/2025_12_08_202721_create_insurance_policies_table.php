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
        Schema::create('insurance_policies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable(); // Who purchased the policy
            $table->unsignedBigInteger('ad_id')->nullable(); // Associated ad if applicable
            $table->string('policy_number')->unique();
            $table->string('provider'); // Insurance provider name
            $table->string('coverage_type'); // 'device_protection', 'product_insurance', 'delivery_insurance', 'accident', etc.
            $table->string('policy_title'); // Title shown to users
            $table->text('description'); // Policy details
            $table->decimal('premium_amount', 10, 2); // Cost of the policy
            $table->decimal('coverage_amount', 10, 2); // Maximum payout
            $table->string('status')->default('active'); // 'active', 'claimed', 'expired', 'cancelled'
            $table->string('risk_level')->default('medium'); // 'low', 'medium', 'high'
            $table->date('effective_from');
            $table->date('effective_until');
            $table->string('billing_cycle'); // 'one_time', 'monthly', 'yearly'
            $table->json('coverage_details'); // Detailed coverage information
            $table->json('exclusions'); // What's not covered
            $table->json('claim_requirements'); // Requirements to make a claim
            $table->json('beneficiaries')->nullable(); // Policy beneficiaries
            $table->json('documents')->nullable(); // Policy documents
            $table->text('terms_and_conditions');
            $table->json('custom_fields')->nullable(); // Any custom fields for specific policies
            $table->timestamp('claimed_at')->nullable(); // When claim was filed
            $table->timestamp('expires_at')->nullable(); // When policy expires
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('ad_id');
            $table->index('policy_number');
            $table->index(['coverage_type', 'risk_level']);
            $table->index(['effective_from', 'effective_until']);

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('ad_id')->references('id')->on('ads')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('insurance_policies');
    }
};
