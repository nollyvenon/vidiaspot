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
        // Create forums table
        Schema::create('forums', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->text('rules')->nullable(); // Forum rules
            $table->boolean('is_active')->default(true);
            $table->boolean('is_public')->default(true);
            $table->boolean('requires_moderation')->default(false);
            $table->integer('sort_order')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null'); // Forum moderator
            $table->foreignId('parent_id')->nullable()->constrained('forums')->onDelete('cascade'); // For nested forums
            $table->json('moderators')->nullable(); // List of user IDs who moderate this forum
            $table->integer('thread_count')->default(0);
            $table->integer('post_count')->default(0);
            $table->timestamp('last_post_at')->nullable();
            $table->foreignId('last_post_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('last_post_thread_title')->nullable();
            $table->string('icon')->nullable(); // Forum icon
            $table->string('color_hex')->nullable(); // Forum color
            $table->json('permissions')->nullable(); // Custom permissions per forum
            $table->timestamps();

            // Indexes for performance
            $table->index(['is_active', 'is_public']);
            $table->index('sort_order');
            $table->index('created_at');
            $table->index(['parent_id', 'is_active']);
        });

        // Create forum threads table
        Schema::create('forum_threads', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content'); // Main thread content
            $table->foreignId('forum_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Thread starter
            $table->foreignId('last_reply_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('last_reply_at')->nullable();
            $table->integer('reply_count')->default(0);
            $table->integer('view_count')->default(0);
            $table->boolean('is_pinned')->default(false); // Pinned threads stay at top
            $table->boolean('is_locked')->default(false); // Locked threads can't be replied to
            $table->boolean('is_sticky')->default(false); // Sticky threads stay at top of forum
            $table->boolean('is_approved')->default(true); // For moderation
            $table->boolean('is_reported')->default(false); // If thread has been reported
            $table->json('tags')->nullable(); // Thread tags
            $table->json('attachments')->nullable(); // File attachments
            $table->string('slug')->unique(); // SEO-friendly URL
            $table->timestamp('pinned_until')->nullable(); // When pin expires
            $table->enum('type', ['discussion', 'question', 'announcement', 'poll'])->default('discussion');
            $table->text('summary')->nullable(); // Thread summary for preview
            $table->timestamps();

            // Indexes for performance
            $table->index(['forum_id', 'is_approved', 'is_locked']);
            $table->index(['user_id', 'created_at']);
            $table->index(['is_pinned', 'pinned_until', 'created_at']);
            $table->index('last_reply_at');
            $table->index('view_count');
            $table->index(['forum_id', 'last_reply_at']);
        });

        // Create forum posts table (replies to threads)
        Schema::create('forum_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('thread_id')->constrained('forum_threads')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('content');
            $table->boolean('is_approved')->default(true);
            $table->boolean('is_reported')->default(false);
            $table->boolean('is_deleted')->default(false); // Soft delete
            $table->foreignId('edited_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('edited_at')->nullable();
            $table->json('attachments')->nullable();
            $table->text('reason_for_edit')->nullable();
            $table->timestamps();

            // Indexes for performance
            $table->index(['thread_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index(['is_approved', 'is_deleted']);
        });

        // Create forum categories table
        Schema::create('forum_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->foreignId('parent_id')->nullable()->constrained('forum_categories')->onDelete('cascade');
            $table->timestamps();

            // Indexes for performance
            $table->index(['parent_id', 'sort_order']);
            $table->index('is_active');
        });

        // Create forum subscriptions table (for notifications)
        Schema::create('forum_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('forum_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('thread_id')->nullable()->constrained('forum_threads')->onDelete('cascade');
            $table->enum('subscription_type', ['forum', 'thread'])->default('thread');
            $table->boolean('email_notifications')->default(true);
            $table->boolean('push_notifications')->default(true);
            $table->timestamp('subscribed_at')->useCurrent();
            $table->timestamps();

            $table->unique(['user_id', 'forum_id', 'thread_id']); // Only one subscription per combination

            // Indexes for performance
            $table->index(['user_id', 'subscription_type']);
            $table->index(['forum_id', 'subscription_type']);
            $table->index(['thread_id', 'subscription_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forum_subscriptions');
        Schema::dropIfExists('forum_posts');
        Schema::dropIfExists('forum_threads');
        Schema::dropIfExists('forum_categories');
        Schema::dropIfExists('forums');
    }
};
