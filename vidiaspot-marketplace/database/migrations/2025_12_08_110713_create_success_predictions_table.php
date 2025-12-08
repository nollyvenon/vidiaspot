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
        Schema::create('success_predictions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ad_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->foreignId('location_id')->nullable()->constrained('cities')->onDelete('set null');
            $table->string('ad_type'); // product, service, rental, etc.
            $table->decimal('success_probability', 5, 2); // 0-100 percentage chance of success
            $table->text('success_factors'); // Factors that increase success probability
            $table->text('improvement_suggestions'); // Suggestions to improve success chances
            $table->json('predicted_metrics'); // Predicted metrics like views, responses, etc.
            $table->json('risk_factors'); // Risk factors that may decrease success
            $table->string('confidence_level'); // high, medium, low
            $table->integer('predicted_duration'); // Predicted number of days to achieve goal
            $table->decimal('engagement_score', 5, 2); // Predicted engagement score
            $table->decimal('conversion_probability', 5, 2); // Probability of converting interest to sale
            $table->text('optimization_tips')->nullable(); // Tips to optimize the listing
            $table->json('comparative_analysis'); // Comparison with similar successful listings
            $table->timestamp('prediction_generated_at');
            $table->timestamp('expires_at')->nullable(); // When this prediction expires
            $table->boolean('is_actual_performance_recorded')->default(false); // Whether actual performance has been recorded
            $table->integer('actual_views')->nullable(); // Actual views after posting
            $table->integer('actual_responses')->nullable(); // Actual responses received
            $table->integer('actual_duration_to_sale')->nullable(); // Days taken to achieve goal
            $table->boolean('prediction_was_accurate')->nullable(); // Whether prediction matched actual performance
            $table->timestamps();

            // Indexes for performance
            $table->index(['user_id', 'ad_id']);
            $table->index(['category_id', 'location_id']);
            $table->index('success_probability');
            $table->index('prediction_generated_at');
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('success_predictions');
    }
};
