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
        Schema::create('duplicate_detections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('primary_ad_id')->constrained('ads')->onDelete('cascade');
            $table->foreignId('duplicate_ad_id')->constrained('ads')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->decimal('similarity_score', 5, 2); // 0-100 similarity percentage
            $table->json('matching_attributes'); // Which attributes matched (title, description, images, etc.)
            $table->json('image_similarity_data'); // Image similarity analysis
            $table->json('text_similarity_data'); // Text similarity analysis (title, description)
            $table->string('detection_method'); // image_matching, text_analysis, combined, etc.
            $table->text('reasoning'); // Why it was flagged as duplicate
            $table->string('status')->default('flagged'); // flagged, confirmed, rejected, appealed
            $table->timestamp('detected_at');
            $table->timestamp('resolved_at')->nullable();
            $table->foreignId('resolved_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('resolution_notes')->nullable(); // Notes about the resolution
            $table->boolean('is_confirmed_duplicate')->default(false);
            $table->boolean('is_false_positive')->default(false);
            $table->json('confidence_factors'); // Factors that influenced the confidence score
            $table->json('recommended_action'); // Recommended action (remove, merge, keep)
            $table->boolean('action_taken')->default(false); // Whether the recommended action was performed
            $table->timestamp('action_taken_at')->nullable();
            $table->timestamps();

            // Indexes for performance
            $table->index(['primary_ad_id', 'duplicate_ad_id']);
            $table->index(['user_id', 'status']);
            $table->index(['category_id', 'detected_at']);
            $table->index('similarity_score');
            $table->index('detected_at');
            $table->unique(['primary_ad_id', 'duplicate_ad_id'], 'unique_duplicate_pair');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('duplicate_detections');
    }
};
