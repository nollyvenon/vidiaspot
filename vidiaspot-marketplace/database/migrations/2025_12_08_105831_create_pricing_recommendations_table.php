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
        Schema::create('pricing_recommendations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ad_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->foreignId('location_id')->nullable()->constrained('cities')->onDelete('set null');
            $table->decimal('current_price', 10, 2);
            $table->decimal('recommended_price', 10, 2);
            $table->decimal('market_average', 10, 2)->nullable();
            $table->decimal('min_price', 10, 2)->nullable();
            $table->decimal('max_price', 10, 2)->nullable();
            $table->decimal('confidence_level', 5, 2); // Confidence level of the recommendation
            $table->string('pricing_strategy'); // competitive, premium, discount, etc.
            $table->json('analysis_data'); // Factors that influenced the recommendation
            $table->json('market_trends'); // Current market trends affecting price
            $table->text('reasoning')->nullable(); // Explanation for the recommendation
            $table->boolean('is_optimal')->default(false); // Indicates if this is the optimal price
            $table->timestamp('generated_at')->useCurrent();
            $table->timestamp('expires_at')->nullable(); // When this recommendation expires
            $table->timestamps();

            // Indexes for performance
            $table->index(['category_id', 'location_id']);
            $table->index('generated_at');
            $table->index('expires_at');
            $table->index(['ad_id', 'generated_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pricing_recommendations');
    }
};
