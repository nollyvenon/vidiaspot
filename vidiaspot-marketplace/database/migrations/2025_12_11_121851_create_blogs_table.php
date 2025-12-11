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
        Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->longText('content');
            $table->string('author')->nullable();
            $table->unsignedBigInteger('author_id')->nullable(); // Reference to user if author is registered
            $table->string('category')->default('news'); // 'news', 'tips', 'sustainability', 'farm', 'marketplace'
            $table->string('status')->default('published'); // 'draft', 'published', 'archived'
            $table->boolean('is_featured')->default(false);
            $table->text('tags')->nullable(); // JSON string of tags
            $table->string('featured_image')->nullable();
            $table->unsignedInteger('view_count')->default(0);
            $table->unsignedInteger('like_count')->default(0);
            $table->unsignedInteger('comment_count')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->unsignedBigInteger('featured_video_id')->nullable(); // If linked to a video
            $table->json('seo_meta')->nullable(); // SEO metadata
            $table->json('reading_stats')->nullable(); // Reading time, word count, etc.
            $table->timestamps();

            $table->index(['status', 'category', 'published_at']);
            $table->index(['is_featured', 'published_at']);
            $table->index('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blogs');
    }
};
