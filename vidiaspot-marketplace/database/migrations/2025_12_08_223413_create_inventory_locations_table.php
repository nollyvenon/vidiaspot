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
        Schema::create('inventory_locations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('vendor_store_id')->nullable();
            $table->string('name');
            $table->json('address')->nullable(); // {street, city, state, country, postal_code}
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_primary')->default(false);
            $table->string('contact_person')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('contact_email')->nullable();
            $table->json('operating_hours')->nullable(); // {monday: {open, close}, tuesday: {open, close}, etc.}
            $table->integer('capacity')->nullable(); // Total capacity of the location
            $table->integer('max_storage_units')->nullable(); // Max storage units allowed
            $table->integer('current_usage')->default(0); // Current storage units used
            $table->unsignedBigInteger('manager_id')->nullable(); // ID of manager for this location
            $table->string('timezone')->default('UTC');
            $table->json('settings')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('user_id');
            $table->index('vendor_store_id');
            $table->index('is_active');
            $table->index('is_primary');
            $table->index(['user_id', 'is_active']);
            $table->index(['vendor_store_id', 'is_active']);
            $table->index(['latitude', 'longitude']); // For location-based queries
            $table->index('manager_id');

            // Foreign key constraints
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('vendor_store_id')->references('id')->on('vendor_stores')->onDelete('set null');
            $table->foreign('manager_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_locations');
    }
};
