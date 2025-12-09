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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reporter_user_id'); // Who made the report
            $table->string('reported_entity_type'); // user, ad, vendor_store, insurance_provider, order, review, message, post
            $table->unsignedBigInteger('reported_entity_id'); // ID of the reported entity
            $table->string('report_type'); // fraud, inappropriate_content, scam, misleading_info, spam, harassment, other
            $table->string('severity_level')->default('medium'); // low, medium, high, critical
            $table->text('description');
            $table->json('evidence_attachments')->nullable(); // Paths to evidence files
            $table->string('status')->default('pending'); // pending, under_review, resolved, dismissed, escalated
            $table->text('resolution_notes')->nullable();
            $table->unsignedBigInteger('resolved_by_admin_id')->nullable(); // Admin who resolved the report
            $table->timestamp('resolved_at')->nullable();
            $table->json('ai_analysis_results')->nullable(); // Results from AI analysis of the report
            $table->json('automated_response')->nullable(); // Action taken by automated system
            $table->boolean('manual_review_required')->default(false); // Whether manual review is required
            $table->text('escalation_reason')->nullable(); // Reason for escalating to human review
            $table->string('moderation_decision')->nullable(); // dismissed, warning_issued, account_suspended, account_terminated
            $table->text('moderation_notes')->nullable();
            $table->json('trust_score_impact')->nullable(); // Impact on trust scores of involved parties
            $table->integer('reputation_point_change')->default(0); // Change to reputation scores
            $table->timestamps();

            // Indexes
            $table->index('reporter_user_id');
            $table->index('reported_entity_type');
            $table->index('reported_entity_id');
            $table->index(['reported_entity_type', 'reported_entity_id']); // For looking up reports for specific entities
            $table->index('report_type');
            $table->index('severity_level');
            $table->index('status');
            $table->index(['status', 'created_at']); // For admin queue management
            $table->index(['reporter_user_id', 'created_at']); // For user's report history
            $table->index('resolved_by_admin_id');
            $table->index('resolved_at');

            // Foreign key constraints
            $table->foreign('reporter_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('resolved_by_admin_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
