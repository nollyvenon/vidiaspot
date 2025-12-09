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
        Schema::create('vendor_stores', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('store_name');
            $table->string('store_slug')->unique(); // URL-friendly slug
            $table->text('description')->nullable();
            $table->string('theme')->default('default'); // Selected theme
            $table->json('theme_config')->nullable(); // Theme-specific configuration
            $table->string('logo_url')->nullable();
            $table->string('banner_url')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->json('business_hours')->nullable(); // {monday: {open, close}, etc.}
            $table->json('social_links')->nullable(); // {facebook, twitter, instagram, etc.}
            $table->boolean('is_active')->default(true);
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->json('settings')->nullable(); // Store-specific settings
            $table->timestamps();

            $table->index('user_id');
            $table->index('store_slug');
            $table->index('theme');
            $table->index('is_active');
            $table->index('is_verified');

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_stores');
    }
};
