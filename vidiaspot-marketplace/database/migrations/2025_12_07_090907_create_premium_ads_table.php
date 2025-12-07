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
        Schema::create('premium_ads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ad_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // User who created the premium ad
            $table->foreignId('payment_id')->nullable()->constrained()->onDelete('set null'); // Associated payment
            $table->string('campaign_name'); // Name for the ad campaign
            $table->decimal('budget', 10, 2); // Total campaign budget
            $table->string('currency_code', 3)->default('NGN');
            $table->string('ad_type')->default('featured'); // featured, promoted, top, urgent, etc.
            $table->json('targeting_settings')->nullable(); // Targeting settings (location, category, etc.)
            $table->integer('impressions_goal')->nullable(); // Target number of impressions
            $table->integer('clicks_goal')->nullable(); // Target number of clicks
            $table->timestamp('start_date'); // When the campaign starts
            $table->timestamp('end_date'); // When the campaign ends
            $table->string('status')->default('pending'); // pending, active, completed, paused, cancelled
            $table->decimal('daily_budget', 10, 2)->nullable(); // Daily budget limit
            $table->json('placement_settings')->nullable(); // Placement settings (top, side, etc.)
            $table->integer('impressions_count')->default(0); // Actual impressions
            $table->integer('clicks_count')->default(0); // Actual clicks
            $table->decimal('spent_amount', 10, 2)->default(0); // Amount spent from budget
            $table->timestamps();

            // Indexes for performance
            $table->index(['status', 'end_date']);
            $table->index('user_id');
            $table->index('ad_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('premium_ads');
    }
};
