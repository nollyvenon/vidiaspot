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
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Author
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable(); // Short description
            $table->longText('content'); // Full blog content
            $table->string('featured_image')->nullable(); // Featured image URL
            $table->string('status')->default('draft'); // draft, published, archived
            $table->boolean('is_featured')->default(false); // Show as featured blog
            $table->boolean('is_published')->default(false); // Published or draft
            $table->timestamp('published_at')->nullable(); // When published
            $table->integer('view_count')->default(0); // Number of views
            $table->json('tags')->nullable(); // Blog tags
            $table->json('meta')->nullable(); // Additional meta data for SEO
            $table->timestamps();

            // Indexes for performance
            $table->index(['status', 'is_published']);
            $table->index('user_id');
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
