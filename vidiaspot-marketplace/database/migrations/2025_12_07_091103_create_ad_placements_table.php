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
        Schema::create('ad_placements', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Name of the placement (Top Banner, Side Ad, etc.)
            $table->string('slug')->unique(); // URL-friendly identifier
            $table->string('location')->default('top'); // top, side, bottom, between, header, footer
            $table->string('type')->default('banner'); // banner, text, image, video, native
            $table->string('size')->nullable(); // ad size (300x250, 728x90, etc.)
            $table->integer('priority')->default(0); // Display priority/order
            $table->boolean('is_active')->default(false); // Whether placement is active
            $table->json('settings')->nullable(); // Additional settings for this placement
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // User who created it
            $table->json('content')->nullable(); // The actual ad content (HTML, image URL, etc.)
            $table->timestamp('starts_at')->nullable(); // When to start showing
            $table->timestamp('expires_at')->nullable(); // When to stop showing
            $table->string('target_pages')->nullable(); // Which pages to show on (homepage, category, ad-detail)
            $table->json('targeting_rules')->nullable(); // Rules for when to display (user type, location, etc.)
            $table->integer('view_count')->default(0); // How many times viewed
            $table->integer('click_count')->default(0); // How many times clicked
            $table->timestamps();

            // Indexes for performance
            $table->index(['is_active', 'location']);
            $table->index('starts_at');
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ad_placements');
    }
};
