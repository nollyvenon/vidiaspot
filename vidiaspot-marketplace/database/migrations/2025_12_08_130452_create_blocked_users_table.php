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
        Schema::create('blocked_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // The user who blocked someone
            $table->foreignId('blocked_user_id')->constrained('users')->onDelete('cascade'); // The user being blocked
            $table->string('reason')->nullable(); // Reason for blocking
            $table->text('notes')->nullable(); // Additional notes about the block
            $table->boolean('is_active')->default(true); // Whether the block is active
            $table->timestamp('expires_at')->nullable(); // When the block expires (null for indefinite)
            $table->timestamps();

            // Ensure a user can't block the same person multiple times
            $table->unique(['user_id', 'blocked_user_id'], 'unique_user_blocked_user');

            // Indexes for performance
            $table->index(['user_id', 'is_active']);
            $table->index(['blocked_user_id', 'is_active']);
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blocked_users');
    }
};
