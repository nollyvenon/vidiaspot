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
        Schema::create('social_posts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->text('content');
            $table->string('post_type')->default('text'); // text, image, video, product_review, live_shopping, event
            $table->string('media_url')->nullable();
            $table->unsignedBigInteger('attached_product_id')->nullable(); // for product reviews/shopping
            $table->string('attached_product_type')->nullable(); // ad, vendor_store, insurance_policy, food_item
            $table->unsignedBigInteger('attached_vendor_store_id')->nullable();
            $table->unsignedBigInteger('attached_food_vendor_id')->nullable();
            $table->unsignedBigInteger('attached_insurance_provider_id')->nullable();
            $table->json('location')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->boolean('is_live')->default(false); // for live shopping events
            $table->timestamp('live_end_time')->nullable();
            $table->boolean('is_promoted')->default(false); // for influencer posts
            $table->string('influencer_status')->default('regular'); // regular, verified, partner
            $table->integer('engagement_score')->default(0); // for reputation system
            $table->integer('reputation_points')->default(0);
            $table->boolean('is_approved')->default(false); // for moderation
            $table->json('post_settings')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('user_id');
            $table->index('post_type');
            $table->index('is_approved');
            $table->index('is_live');
            $table->index('attached_product_type');
            $table->index(['user_id', 'post_type']);
            $table->index(['latitude', 'longitude']);
            $table->index(['is_live', 'live_end_time']);
            $table->index('engagement_score');
            $table->index('reputation_points');

            // Foreign key constraints
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_posts');
    }
};
