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
        Schema::create('careers', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Job title
            $table->string('slug')->unique(); // URL-friendly slug
            $table->string('department'); // Department (Engineering, Marketing, etc.)
            $table->string('job_type')->default('full_time'); // full_time, part_time, contract, internship
            $table->string('location'); // Job location
            $table->string('salary_range')->nullable(); // Salary range
            $table->text('description'); // Job description
            $table->text('requirements'); // Job requirements
            $table->text('benefits')->nullable(); // Benefits offered
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // User who created it
            $table->string('status')->default('draft'); // draft, published, archived
            $table->boolean('is_active')->default(false); // Whether to show on careers page
            $table->timestamp('published_at')->nullable(); // When to publish
            $table->timestamp('application_deadline')->nullable(); // Application deadline
            $table->json('meta')->nullable(); // Additional metadata for SEO
            $table->timestamps();

            // Indexes for performance
            $table->index(['status', 'is_active']);
            $table->index('department');
            $table->index('location');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('careers');
    }
};
