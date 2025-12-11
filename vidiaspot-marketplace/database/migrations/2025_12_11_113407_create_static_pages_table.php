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
        Schema::create('static_pages', function (Blueprint $table) {
            $table->id();
            $table->string('page_key')->unique(); // e.g., 'contact_us', 'safety_tips', 'privacy_policy', 'terms_conditions', 'faq'
            $table->string('title')->nullable();
            $table->longText('content');
            $table->string('locale')->default('en'); // For multi-language support
            $table->string('status')->default('active'); // 'active', 'draft', 'archived'
            $table->integer('order')->default(0);
            $table->string('author_id')->nullable(); // In case we track authors
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index(['page_key', 'locale', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('static_pages');
    }
};
