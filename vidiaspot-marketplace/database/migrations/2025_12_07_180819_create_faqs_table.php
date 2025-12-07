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
        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->string('question');
            $table->text('answer');
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
            $table->unsignedBigInteger('parent_id')->nullable(); // For grouped FAQs
            $table->foreign('parent_id')->references('id')->on('faqs')->onDelete('cascade'); // For grouped FAQs
            $table->integer('order')->default(0); // Order within category
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false); // Featured on homepage
            $table->json('metadata')->nullable(); // Additional information
            $table->timestamps();

            // Indexes for performance
            $table->index('category_id');
            $table->index('parent_id');
            $table->index('order');
            $table->index('is_active');
            $table->index('is_featured');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faqs');
    }
};
