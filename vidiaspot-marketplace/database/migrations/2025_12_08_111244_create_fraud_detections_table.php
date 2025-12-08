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
        Schema::create('fraud_detections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('ad_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('payment_transaction_id')->nullable()->constrained('payment_transactions')->onDelete('set null');
            $table->string('type'); // listing_fraud, payment_fraud, account_fraud, etc.
            $table->string('severity'); // low, medium, high, critical
            $table->decimal('risk_score', 5, 2); // 0-100 risk score
            $table->json('indicators'); // Indicators that triggered fraud detection
            $table->json('behavioral_patterns'); // Unusual behavioral patterns detected
            $table->json('suspicious_activities'); // Specific suspicious activities detected
            $table->text('analysis_details'); // Detailed analysis of why it was flagged
            $table->string('status')->default('pending_review'); // pending_review, investigated, confirmed_fraud, false_positive
            $table->text('investigation_notes')->nullable(); // Notes from investigation
            $table->foreignId('investigated_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('investigated_at')->nullable();
            $table->string('resolution_action')->nullable(); // Action taken (suspend_account, remove_listing, warn_user, etc.)
            $table->text('resolution_details')->nullable(); // Details about the resolution
            $table->boolean('is_confirmed_fraud')->default(false);
            $table->boolean('is_false_positive')->default(false);
            $table->json('confidence_factors'); // Factors contributing to fraud confidence
            $table->json('recommended_actions'); // Recommended actions to address fraud
            $table->json('affected_resources'); // Resources affected by the fraudulent activity
            $table->timestamp('detected_at');
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            // Indexes for performance
            $table->index(['user_id', 'type']);
            $table->index(['ad_id', 'status']);
            $table->index('risk_score');
            $table->index('detected_at');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fraud_detections');
    }
};
