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
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->unique(); // Unique ticket identifier
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // User who raised the ticket
            $table->string('subject');
            $table->text('description');
            $table->enum('category', [
                'technical_support',
                'billing_inquiry',
                'account_issue',
                'feature_request',
                'security_concern',
                'payment_issue',
                'ad_placement',
                'verification',
                'trading_issue',
                'crypto_support',
                'other'
            ])->default('other');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->enum('status', ['open', 'in_progress', 'resolved', 'closed', 'escalated'])->default('open');
            $table->enum('type', ['issue', 'request', 'complaint', 'feedback'])->default('issue');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null'); // Support agent assigned
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->text('resolution_notes')->nullable();
            $table->integer('estimated_resolution_hours')->nullable(); // For SLA tracking
            $table->foreignId('escalated_to')->nullable()->constrained('users')->onDelete('set null'); // Higher level support
            $table->timestamp('sla_deadline')->nullable(); // Service level agreement deadline
            $table->integer('response_count')->default(0); // Number of responses
            $table->boolean('is_urgent')->default(false);
            $table->boolean('requires_manager_attention')->default(false);
            $table->json('attachments')->nullable(); // File attachments
            $table->json('tags')->nullable(); // Ticket tags for categorization
            $table->timestamps();

            // Indexes for performance
            $table->index(['status', 'priority']);
            $table->index(['user_id', 'created_at']);
            $table->index(['assigned_to', 'status']);
            $table->index('ticket_number');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_tickets');
    }
};
