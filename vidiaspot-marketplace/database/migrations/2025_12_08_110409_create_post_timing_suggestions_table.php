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
        Schema::create('post_timing_suggestions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('ad_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->foreignId('location_id')->nullable()->constrained('cities')->onDelete('set null');
            $table->string('best_day_of_week'); // Monday, Tuesday, etc.
            $table->integer('best_hour'); // Hour of day (0-23)
            $table->decimal('optimal_score', 5, 2); // Score representing the quality of this time slot
            $table->json('factors'); // Factors that influenced the timing suggestion
            $table->json('historical_data'); // Historical data that informed the suggestion
            $table->string('seasonal_trend')->nullable(); // Seasonal trend affecting timing
            $table->integer('expected_views'); // Expected number of views if posted at suggested time
            $table->integer('expected_responses'); // Expected number of responses/interest
            $table->integer('competition_level'); // Competition level at suggested time (1-10)
            $table->text('reasoning')->nullable(); // Explanation for the timing suggestion
            $table->json('alternative_times'); // Alternative good times with scores
            $table->timestamp('valid_from');
            $table->timestamp('valid_until');
            $table->boolean('is_used')->default(false); // Whether the suggestion was followed
            $table->timestamp('used_at')->nullable(); // When the suggestion was used
            $table->timestamps();

            // Indexes for performance
            $table->index(['user_id', 'category_id']);
            $table->index(['location_id', 'category_id']);
            $table->index('best_hour');
            $table->index('valid_until');
            $table->index(['user_id', 'is_used']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_timing_suggestions');
    }
};
