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
        // Create courses table
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->text('summary')->nullable(); // Short summary for previews
            $table->text('objectives')->nullable(); // Learning objectives
            $table->enum('level', ['beginner', 'intermediate', 'advanced', 'expert'])->default('beginner');
            $table->enum('category', [
                'trading_basics',
                'advanced_trading',
                'crypto_fundamentals',
                'technical_analysis',
                'fundamental_analysis',
                'risk_management',
                'blockchain_basics',
                'defi_introduction',
                'nft_education',
                'web3_fundamentals',
                'trading_psychology',
                'portfolio_management',
                'tax_considerations',
                'regulatory_compliance'
            ])->default('trading_basics');
            $table->enum('type', ['course', 'tutorial', 'webinar', 'workshop', 'certificate'])->default('course');
            $table->json('tags')->nullable(); // Course tags
            $table->foreignId('instructor_id')->nullable()->constrained('users')->onDelete('set null');
            $table->integer('duration_minutes')->nullable(); // Estimated duration in minutes
            $table->integer('total_lessons')->default(0);
            $table->integer('total_students')->default(0);
            $table->decimal('rating', 3, 2)->default(0.00); // Average rating
            $table->integer('rating_count')->default(0); // Number of ratings
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_free')->default(false);
            $table->decimal('price', 10, 2)->default(0.00);
            $table->decimal('discount_price', 10, 2)->nullable(); // Discounted price
            $table->timestamp('discount_ends_at')->nullable(); // When discount ends
            $table->text('prerequisites')->nullable(); // Prerequisites for the course
            $table->text('what_you_will_learn')->nullable(); // What students will learn
            $table->string('thumbnail_url')->nullable(); // Course thumbnail
            $table->string('preview_video_url')->nullable(); // Preview video
            $table->json('files')->nullable(); // Additional course files/resources
            $table->json('resources')->nullable(); // Additional learning resources
            $table->boolean('requires_certificate')->default(false); // Whether certificate is issued
            $table->integer('certificate_hours')->nullable(); // Hours for certificate
            $table->json('learning_paths')->nullable(); // Related learning paths
            $table->boolean('has_certificate')->default(false); // Whether course has certificate
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            // Indexes for performance
            $table->index(['is_active', 'is_free']);
            $table->index(['category', 'level']);
            $table->index('price');
            $table->index('rating');
            $table->index('created_at');
            $table->index('total_students');
            $table->index('slug');
        });

        // Create course modules table
        Schema::create('course_modules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('module_order')->default(0); // Order of the module in the course
            $table->integer('lesson_count')->default(0); // Number of lessons in this module
            $table->integer('duration_minutes')->nullable(); // Duration of this module
            $table->boolean('is_locked')->default(false); // Whether module is locked for students
            $table->timestamps();

            // Indexes for performance
            $table->index(['course_id', 'module_order']);
        });

        // Create course lessons table
        Schema::create('course_lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->constrained('course_modules')->onDelete('cascade');
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade'); // Added for easier queries
            $table->string('title');
            $table->text('content')->nullable(); // Lesson content (HTML/Markdown)
            $table->text('summary')->nullable(); // Short summary of the lesson
            $table->enum('type', [
                'video',
                'article',
                'quiz',
                'assignment',
                'download',
                'interactive',
                'webinar',
                'podcast'
            ])->default('article');
            $table->string('video_url')->nullable(); // For video lessons
            $table->string('download_url')->nullable(); // For downloadable content
            $table->integer('duration_minutes')->nullable(); // Duration of lesson
            $table->integer('view_count')->default(0); // How many times lesson viewed
            $table->boolean('is_preview')->default(false); // Whether lesson is a preview
            $table->boolean('is_free')->default(false); // Whether lesson is free
            $table->boolean('requires_completion')->default(true); // Whether lesson completion is required
            $table->json('resources')->nullable(); // Additional resources for the lesson
            $table->json('attachments')->nullable(); // Lesson attachments
            $table->integer('lesson_order')->default(0); // Order of lesson in module
            $table->integer('quiz_questions_count')->default(0); // Number of questions if this is a quiz
            $table->timestamps();

            // Indexes for performance
            $table->index(['module_id', 'lesson_order']);
            $table->index(['course_id', 'lesson_order']);
            $table->index('type');
            $table->index('is_preview');
            $table->index('view_count');
        });

        // Create student enrollments table
        Schema::create('course_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['enrolled', 'completed', 'in_progress', 'dropped', 'certified'])->default('enrolled');
            $table->timestamp('enrolled_at')->useCurrent();
            $table->timestamp('completed_at')->nullable();
            $table->integer('progress_percentage')->default(0); // Overall course progress
            $table->integer('completed_lessons')->default(0); // Number of completed lessons
            $table->timestamp('last_accessed_at')->nullable(); // When the user last accessed the course
            $table->integer('total_time_spent')->default(0); // Total time spent in seconds
            $table->decimal('final_score', 5, 2)->nullable(); // Final score if applicable
            $table->boolean('has_certificate')->default(false); // Whether user earned certificate
            $table->foreignId('certificate_id')->nullable()->constrained('certificates')->onDelete('set null');
            $table->json('notes')->nullable(); // Student notes
            $table->timestamps();

            $table->unique(['user_id', 'course_id']); // Each user can only be enrolled once per course

            // Indexes for performance
            $table->index(['user_id', 'status']);
            $table->index(['course_id', 'status']);
            $table->index(['user_id', 'course_id']);
            $table->index('status');
            $table->index('progress_percentage');
        });

        // Create lesson completions table
        Schema::create('lesson_completions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('lesson_id')->constrained('course_lessons')->onDelete('cascade');
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->timestamp('completed_at')->nullable();
            $table->integer('time_spent_seconds')->default(0); // Time spent on this lesson
            $table->integer('score')->nullable(); // Score if it's a quiz
            $table->text('notes')->nullable(); // User notes on the lesson
            $table->boolean('is_passed')->default(false); // Whether quiz was passed
            $table->timestamps();

            $table->unique(['user_id', 'lesson_id']); // Each user can only complete a lesson once

            // Indexes for performance
            $table->index(['user_id', 'lesson_id']);
            $table->index(['lesson_id', 'completed_at']);
            $table->index(['user_id', 'course_id']);
        });

        // Create certificates table
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->string('certificate_number')->unique(); // Unique certificate number
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->string('title'); // Certificate title
            $table->text('description')->nullable();
            $table->timestamp('issued_at')->useCurrent();
            $table->timestamp('expires_at')->nullable(); // When certificate expires
            $table->string('pdf_path')->nullable(); // Path to PDF certificate
            $table->string('verification_url')->nullable(); // URL to verify certificate authenticity
            $table->boolean('is_active')->default(true);
            $table->json('metadata')->nullable(); // Additional metadata about the certificate
            $table->timestamps();

            // Indexes for performance
            $table->index(['user_id', 'course_id']);
            $table->index('certificate_number');
            $table->index('issued_at');
        });

        // Create course categories table
        Schema::create('course_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('icon')->nullable(); // Category icon
            $table->string('color_hex')->nullable(); // Category color
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->foreignId('parent_id')->nullable()->constrained('course_categories')->onDelete('cascade');
            $table->timestamps();

            // Indexes for performance
            $table->index(['parent_id', 'sort_order']);
            $table->index('is_active');
        });

        // Create course reviews table
        Schema::create('course_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->tinyInteger('rating')->unsigned(); // Rating from 1-5
            $table->text('review')->nullable();
            $table->boolean('is_verified_purchase')->default(false); // Whether user actually took the course
            $table->boolean('is_approved')->default(true); // For moderation
            $table->timestamps();

            $table->unique(['user_id', 'course_id']); // Each user can review a course only once

            // Indexes for performance
            $table->index(['course_id', 'rating']);
            $table->index(['user_id', 'created_at']);
            $table->index('rating');
        });

        // Create learning paths table (collections of courses)
        Schema::create('learning_paths', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->enum('level', ['beginner', 'intermediate', 'advanced'])->default('beginner');
            $table->integer('estimated_duration_days')->nullable(); // Estimated time to complete path
            $table->json('course_ids'); // Array of course IDs in this path
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->timestamps();

            // Indexes for performance
            $table->index('is_active');
            $table->index('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('learning_paths');
        Schema::dropIfExists('course_reviews');
        Schema::dropIfExists('course_categories');
        Schema::dropIfExists('certificates');
        Schema::dropIfExists('lesson_completions');
        Schema::dropIfExists('course_enrollments');
        Schema::dropIfExists('course_lessons');
        Schema::dropIfExists('course_modules');
        Schema::dropIfExists('courses');
    }
};
