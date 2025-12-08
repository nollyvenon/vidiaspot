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
        Schema::create('social_follows', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('follower_id'); // who follows
            $table->unsignedBigInteger('followed_id'); // who is being followed
            $table->string('follow_type')->default('user'); // user, vendor_store, insurance_provider
            $table->integer('reputation_points')->default(0);
            $table->boolean('is_approved')->default(true); // for follow requests if needed
            $table->timestamps();

            // Indexes
            $table->index('follower_id');
            $table->index('followed_id');
            $table->index('follow_type');
            $table->index(['follower_id', 'follow_type']); // User follows specific type
            $table->index(['followed_id', 'follow_type']); // Count for different types
            $table->index(['follower_id', 'followed_id']); // For checking if already following

            // Foreign key constraints
            $table->foreign('follower_id')->references('id')->on('users')->onDelete('cascade');
            // Note: We can't add foreign key for followed_id since it can reference different tables based on follow_type

            // Unique constraint to prevent duplicate follows
            $table->unique(['follower_id', 'followed_id', 'follow_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_follows');
    }
};
