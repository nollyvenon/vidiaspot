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
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Link to user
            $table->string('business_name');
            $table->string('business_email')->nullable();
            $table->string('business_phone')->nullable();
            $table->text('business_description')->nullable();
            $table->string('business_type')->nullable(); // retailer, wholesaler, service provider
            $table->string('business_registration_number')->nullable();
            $table->string('logo_url')->nullable(); // URL to business logo
            $table->string('banner_url')->nullable(); // URL to business banner
            $table->foreignId('country_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('state_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('city_id')->nullable()->constrained()->onDelete('set null');
            $table->string('address')->nullable();
            $table->string('status')->default('pending'); // pending, approved, suspended, rejected
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->json('documents')->nullable(); // Business registration documents
            $table->boolean('is_verified')->default(false);
            $table->decimal('rating', 3, 2)->default(0.00); // Average rating
            $table->integer('total_sales')->default(0); // Number of sales made
            $table->boolean('is_featured')->default(false); // Show as featured vendor
            $table->timestamps();

            // Indexes for performance
            $table->index(['status', 'is_verified']);
            $table->index('user_id');
            $table->index('business_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendors');
    }
};
