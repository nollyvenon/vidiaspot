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
        Schema::create('social_comments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('social_post_id');
            $table->unsignedBigInteger('parent_comment_id')->nullable(); // for comment threads
            $table->text('content');
            $table->boolean('is_reply')->default(false);
            $table->unsignedBigInteger('reply_to_user_id')->nullable(); // when replying to specific user
            $table->integer('reputation_points')->default(0);
            $table->timestamps();

            // Indexes
            $table->index('user_id');
            $table->index('social_post_id');
            $table->index('parent_comment_id');
            $table->index('reply_to_user_id');
            $table->index(['social_post_id', 'created_at']);
            $table->index(['user_id', 'created_at']);

            // Foreign key constraints
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('social_post_id')->references('id')->on('social_posts')->onDelete('cascade');
            $table->foreign('parent_comment_id')->references('id')->on('social_comments')->onDelete('set null');
            $table->foreign('reply_to_user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_comments');
    }
};
