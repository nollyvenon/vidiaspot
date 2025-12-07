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
        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Name of the person giving the testimonial
            $table->string('position')->nullable(); // Their position/title
            $table->string('company')->nullable(); // Their company
            $table->text('testimonial'); // The actual testimonial content
            $table->string('avatar_url')->nullable(); // URL to their avatar/photo
            $table->string('source')->nullable(); // Where the testimonial came from
            $table->json('rating'); // Rating (1-5 stars)
            $table->boolean('is_featured')->default(false); // Show as featured on homepage
            $table->boolean('is_active')->default(true); // Whether to show it
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // User who added it
            $table->timestamp('published_at')->nullable(); // When to publish
            $table->timestamps();

            // Indexes for performance
            $table->index(['is_active', 'is_featured']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('testimonials');
    }
};
