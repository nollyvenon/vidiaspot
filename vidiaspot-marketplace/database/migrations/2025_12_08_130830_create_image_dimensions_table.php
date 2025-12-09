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
        Schema::create('image_dimensions', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Name of the dimension set (e.g. 'ad_thumbnail', 'ad_large', 'user_avatar', 'blog_cover')
            $table->string('purpose'); // Purpose (e.g., 'thumbnail', 'listing', 'featured', 'profile')
            $table->integer('width'); // Width in pixels
            $table->integer('height'); // Height in pixels
            $table->boolean('maintain_aspect_ratio')->default(true); // Whether to maintain aspect ratio
            $table->string('quality_setting')->default('medium'); // low, medium, high, original
            $table->string('format_preference')->default('webp'); // jpeg, png, webp, auto
            $table->text('description')->nullable(); // Description of this dimension set
            $table->boolean('is_active')->default(true); // Whether this dimension set is active
            $table->integer('sort_order')->default(0); // Order to apply dimensions (priority)
            $table->json('allowed_extensions'); // Allowed file extensions for this dimension (will be set in model)
            $table->integer('max_file_size_kb')->default(5000); // Maximum allowed file size in KB
            $table->boolean('enable_cropping')->default(false); // Whether to enable cropping for this dimension
            $table->boolean('enable_upscaling')->default(false); // Whether to allow upscaling smaller images
            $table->json('crop_positions')->nullable(); // Crop positions if cropping is enabled (center, top, bottom, left, right, custom)
            $table->timestamps();

            // Indexes for performance
            $table->index('name');
            $table->index('purpose');
            $table->index('is_active');
            $table->index('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('image_dimensions');
    }
};
