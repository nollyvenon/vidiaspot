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
        Schema::create('content_pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique(); // about-us, contact-us, terms-of-service, privacy-policy, services
            $table->string('title');
            $table->text('content'); // Page content
            $table->string('meta_title')->nullable(); // For SEO
            $table->text('meta_description')->nullable(); // For SEO
            $table->json('meta_keywords')->nullable(); // For SEO
            $table->string('page_type')->default('static'); // static, legal, service, etc.
            $table->boolean('is_active')->default(true); // Whether the page is visible
            $table->boolean('is_featured')->default(false); // Whether to highlight the page
            $table->integer('view_count')->default(0); // Track page views
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null'); // User who last updated
            $table->timestamp('published_at')->nullable(); // When the page was published
            $table->timestamps();

            // Indexes for performance
            $table->index(['is_active', 'slug']);
            $table->index('page_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_pages');
    }
};
