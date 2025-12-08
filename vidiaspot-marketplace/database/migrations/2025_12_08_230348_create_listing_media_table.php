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
        Schema::create('listing_media', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ad_id');
            $table->unsignedBigInteger('user_id');
            $table->string('media_type'); // 'image', 'video', '360_image', 'vr_tour', 'interactive_demo', 'documentation'
            $table->string('file_path')->nullable(); // Path to the actual file
            $table->string('file_url')->nullable(); // URL to access the media
            $table->string('thumbnail_url')->nullable(); // Thumbnail for videos/images
            $table->string('original_filename')->nullable(); // Original filename
            $table->string('media_caption')->nullable(); // Caption for the media
            $table->string('media_alt_text')->nullable(); // Alt text for accessibility
            $table->boolean('is_primary')->default(false); // Is this the primary media for the listing
            $table->integer('display_order')->default(0); // Order to display this media
            $table->boolean('is_active')->default(true); // Is this media currently active
            $table->integer('view_count')->default(0); // How many times this media was viewed
            $table->integer('interaction_count')->default(0); // Interactions for interactive media
            $table->integer('duration_seconds')->nullable(); // Duration for videos
            $table->integer('width_pixels')->nullable(); // Width in pixels
            $table->integer('height_pixels')->nullable(); // Height in pixels
            $table->integer('file_size_bytes')->nullable(); // File size in bytes
            $table->json('media_metadata')->nullable(); // Additional metadata specific to media type
            $table->string('upload_ip_address', 45)->nullable(); // IP address of uploader
            $table->string('upload_user_agent')->nullable(); // Browser info of uploader
            $table->boolean('uploaded_by_admin')->default(false); // If uploaded by admin on behalf of user
            $table->boolean('is_approved')->default(true); // For moderation
            $table->unsignedBigInteger('approved_by')->nullable(); // Admin who approved
            $table->timestamp('approved_at')->nullable(); // When approved
            $table->text('notes')->nullable(); // Admin notes
            $table->json('custom_fields')->nullable(); // For extended functionality
            $table->timestamps();

            // Indexes
            $table->index('ad_id');
            $table->index('user_id');
            $table->index('media_type');
            $table->index('is_primary');
            $table->index('is_active');
            $table->index('display_order');
            $table->index(['ad_id', 'media_type']); // For getting specific media types for an ad
            $table->index(['user_id', 'is_active']); // For user's active media
            $table->index(['is_primary', 'ad_id']); // For getting primary media
            $table->index('approved_by');
            $table->index('approved_at');

            // Foreign key constraints
            $table->foreign('ad_id')->references('id')->on('ads')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('listing_media');
    }
};
