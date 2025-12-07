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
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->morphs('commentable'); // Polymorphic relationship to any model
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('content');
            $table->unsignedBigInteger('parent_id')->nullable(); // For replies
            $table->foreign('parent_id')->references('id')->on('comments')->onDelete('cascade'); // For replies
            $table->boolean('is_private')->default(false); // For admin-only comments
            $table->boolean('is_approved')->default(true); // For moderation
            $table->json('metadata')->nullable(); // Additional metadata
            $table->timestamps();

            // Indexes for performance
            $table->index(['commentable_type', 'commentable_id']);
            $table->index('user_id');
            $table->index('parent_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
