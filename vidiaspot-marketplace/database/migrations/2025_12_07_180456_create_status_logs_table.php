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
        Schema::create('status_logs', function (Blueprint $table) {
            $table->id();
            $table->morphs('statusable'); // Polymorphic relationship to any model
            $table->string('status'); // New status value
            $table->string('previous_status')->nullable(); // Previous status value
            $table->text('reason')->nullable(); // Reason for status change
            $table->foreignId('changed_by')->nullable()->constrained('users')->onDelete('set null'); // User who changed status
            $table->json('metadata')->nullable(); // Additional information about the change
            $table->timestamps();

            // Indexes for performance
            $table->index(['statusable_type', 'statusable_id']);
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('status_logs');
    }
};
