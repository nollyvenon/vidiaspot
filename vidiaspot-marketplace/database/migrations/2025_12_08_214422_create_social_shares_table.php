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
        Schema::create('social_shares', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('social_post_id');
            $table->unsignedBigInteger('target_user_id')->nullable(); // for sharing with specific friend
            $table->string('share_platform')->default('internal'); // internal, facebook, twitter, whatsapp, etc.
            $table->integer('reputation_points')->default(0);
            $table->timestamps();

            // Indexes
            $table->index('user_id');
            $table->index('social_post_id');
            $table->index('target_user_id');
            $table->index(['user_id', 'social_post_id']); // For checking shares by user
            $table->index(['social_post_id', 'created_at']); // For counting shares per post
            $table->index('share_platform');

            // Foreign key constraints
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('social_post_id')->references('id')->on('social_posts')->onDelete('cascade');
            $table->foreign('target_user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_shares');
    }
};
