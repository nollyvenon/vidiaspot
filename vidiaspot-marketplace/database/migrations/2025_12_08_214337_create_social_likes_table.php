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
        Schema::create('social_likes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('social_post_id');
            $table->integer('reputation_points')->default(0);
            $table->timestamps();

            // Indexes
            $table->index('user_id');
            $table->index('social_post_id');
            $table->index(['user_id', 'social_post_id']); // For checking if user liked a specific post
            $table->index(['social_post_id', 'created_at']); // For counting likes per post

            // Foreign key constraints
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('social_post_id')->references('id')->on('social_posts')->onDelete('cascade');

            // Unique constraint to prevent duplicate likes
            $table->unique(['user_id', 'social_post_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_likes');
    }
};
