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
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Name of the promotion package (e.g. "Featured Ad", "Premium Placement", "Boost")
            $table->string('slug')->unique(); // URL-friendly slug
            $table->text('description')->nullable(); // Description of what the promotion includes
            $table->decimal('price_per_day', 10, 2); // Price per day for the promotion
            $table->integer('duration_days'); // Duration in days for the promotion
            $table->json('features'); // Features included in the promotion (e.g. "top_placement", "highlighted", "badge")
            $table->boolean('is_active')->default(true); // Whether this promotion is available
            $table->boolean('is_featured')->default(false); // Whether this is featured/prominent
            $table->integer('display_order')->default(0); // Order to display in UI
            $table->string('promotion_type')->default('boost'); // Type: boost, featured, highlighted, priority
            $table->string('target_audience')->default('all'); // Who can use this: all, verified, premium_users
            $table->json('placement_positions'); // Where the promoted ad appears (top, side, featured_section, etc.)
            $table->integer('max_allowed_per_ad')->default(1); // Max times an ad can use this promotion
            $table->json('requirements'); // Requirements to use this promotion (e.g. verified_account, premium_subscription)
            $table->timestamps();

            // Indexes for performance
            $table->index('is_active');
            $table->index('is_featured');
            $table->index('promotion_type');
            $table->index('display_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promotions');
    }
};
