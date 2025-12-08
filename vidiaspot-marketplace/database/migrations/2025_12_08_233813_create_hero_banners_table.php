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
        Schema::create('hero_banners', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('subtitle')->nullable();
            $table->text('description')->nullable();
            $table->string('button_text')->default('Learn More');
            $table->string('button_url')->nullable();
            $table->enum('media_type', ['image', 'video', 'video_embed', 'carousel', 'mixed'])->default('image');
            $table->string('media_url')->nullable();
            $table->string('thumbnail_url')->nullable();
            $table->text('embed_code')->nullable(); // For embedded videos like YouTube/Vimeo
            $table->integer('position')->default(0); // Position in carousel
            $table->boolean('is_active')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->boolean('show_timer')->default(false); // For countdown timer
            $table->timestamp('timer_target_date')->nullable(); // Target date for countdown
            $table->string('call_to_action')->nullable();
            $table->json('target_audience')->nullable(); // For specific audience targeting ['buyers', 'sellers', 'all']
            $table->timestamp('start_date')->nullable(); // When banner becomes active
            $table->timestamp('end_date')->nullable(); // When banner expires
            $table->json('display_conditions')->nullable(); // Conditions for displaying the banner
            $table->string('utm_source')->nullable(); // For tracking
            $table->string('utm_medium')->nullable();
            $table->string('utm_campaign')->nullable();
            $table->json('custom_css_classes')->nullable(); // Custom CSS classes for styling
            $table->string('transition_effect')->default('fade'); // fade, slide, zoom, etc.
            $table->integer('animation_duration')->default(500); // in milliseconds
            $table->boolean('auto_advance')->default(true); // For carousels
            $table->integer('advance_interval')->default(5); // Time between slides in seconds
            $table->boolean('show_navigation')->default(true); // Show navigation arrows
            $table->boolean('show_indicators')->default(true); // Show indicator dots
            $table->string('link_target')->default('_self'); // _self, _blank
            $table->string('alt_text')->nullable(); // Accessibility alt text
            $table->json('seo_keywords')->nullable(); // SEO keywords for the banner
            $table->integer('view_count')->default(0);
            $table->integer('click_count')->default(0);
            $table->integer('conversion_count')->default(0);
            $table->unsignedBigInteger('created_by')->nullable(); // User who created this banner
            $table->unsignedBigInteger('updated_by')->nullable(); // User who last updated
            $table->json('custom_fields')->nullable(); // For extendability
            $table->json('metadata')->nullable(); // Additional metadata
            $table->softDeletes(); // Soft deletes capability
            $table->timestamps();

            // Indexes
            $table->index('is_active');
            $table->index('is_featured');
            $table->index('position');
            $table->index('media_type');
            $table->index(['is_active', 'position']); // For getting active banners in order
            $table->index(['start_date', 'end_date']); // For date-based filtering
            $table->index('created_by');
            $table->index('updated_by');
            $table->index('created_at');
            $table->index('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hero_banners');
    }
};
