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
        Schema::create('trust_scores', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique(); // Each user has one trust score
            $table->decimal('trust_score', 5, 2)->default(50.00); // Overall trust score (0-100)
            $table->json('trust_metrics')->nullable(); // Detailed metrics like {transaction_success_rate: 95, dispute_rate: 2, complaint_count: 1}
            $table->string('verification_level')->default('basic'); // basic, verified, trusted, elite
            $table->json('performance_indicators')->nullable(); // {seller_rating: 4.5, buyer_feedback: 4.7, response_time_avg: 2.5}
            $table->json('activity_history')->nullable(); // Recent activity patterns
            $table->integer('suspicious_activity_count')->default(0);
            $table->integer('dispute_count')->default(0);
            $table->integer('complaint_count')->default(0);
            $table->integer('positive_interactions')->default(0);
            $table->integer('negative_interactions')->default(0);
            $table->integer('account_age_months')->default(0);
            $table->integer('total_transactions')->default(0);
            $table->decimal('total_revenue', 15, 2)->default(0.00);
            $table->json('fraud_indicators')->nullable(); // Risk factors detected
            $table->boolean('protection_eligibility')->default(true); // Eligibility for buyer protection
            $table->boolean('insurance_eligibility')->default(true); // Eligibility for insurance
            $table->string('background_check_status')->default('pending'); // pending, verified, flagged
            $table->json('background_check_details')->nullable();
            $table->timestamp('last_updated')->nullable();
            $table->timestamp('next_review_date')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('user_id');
            $table->index('trust_score');
            $table->index('verification_level');
            $table->index('background_check_status');
            $table->index('protection_eligibility');
            $table->index('insurance_eligibility');
            $table->index(['trust_score', 'verification_level']);
            $table->index('last_updated');
            $table->index('next_review_date');

            // Foreign key constraints
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trust_scores');
    }
};
