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
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable(); // Who owns this location (null for public locations like pickup points)
            $table->string('location_name')->nullable();
            $table->string('address_line1');
            $table->string('address_line2')->nullable();
            $table->string('city');
            $table->string('state');
            $table->string('country');
            $table->string('postal_code');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->enum('location_type', [
                'residential', 'commercial', 'warehouse', 'pickup_point',
                'delivery_hub', 'marketplace', 'retail_outlet', 'office'
            ])->default('residential');
            $table->boolean('is_primary')->default(false);
            $table->boolean('is_active')->default(true);
            $table->decimal('geofence_radius', 8, 2)->default(100.00); // In meters
            $table->string('delivery_zone')->nullable(); // Delivery zone identifier
            $table->json('operating_hours')->nullable(); // {monday: {open: '09:00', close: '18:00'}, ...}
            $table->string('contact_person')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('contact_email')->nullable();
            $table->json('indoor_map_data')->nullable(); // JSON for indoor mapping (floors, aisles, sections)
            $table->json('floor_plan')->nullable(); // Floor plan information
            $table->json('aisle_positions')->nullable(); // Product aisle positions for indoor mapping
            $table->string('coordinates_precision', 20)->default('high'); // 'low', 'medium', 'high', 'very_high'
            $table->decimal('altitude', 8, 2)->nullable(); // Altitude in meters
            $table->string('timezone')->default('Africa/Lagos'); // Default to Lagos timezone
            $table->json('location_metadata')->nullable(); // Additional location-specific metadata
            $table->json('delivery_availability')->default(json_encode([
                'same_day' => false,
                'next_day' => true,
                'time_slots' => []
            ])); // Delivery availability settings
            $table->boolean('cold_chain_supported')->default(false); // Whether cold chain storage is available
            $table->json('max_package_size')->default(json_encode([
                'length_cm' => 100,
                'width_cm' => 100,
                'height_cm' => 100
            ])); // Maximum package dimensions
            $table->decimal('max_package_weight', 8, 2)->default(30.00); // Maximum package weight in kg
            $table->json('special_handling_available')->default(json_encode([])); // Special handling options ['fragile', 'oversized', 'temperature_controlled']
            $table->json('warehouse_capacity')->nullable(); // For warehouse locations {capacity_sqm: 500, max_packages: 1000}
            $table->json('available_slot_times')->nullable(); // Available time slots for deliveries
            $table->timestamp('last_updated')->nullable();
            $table->json('custom_fields')->nullable();
            $table->json('metadata')->nullable();
            $table->softDeletes();
            $table->timestamps();

            // Indexes
            $table->index('user_id');
            $table->index('location_type');
            $table->index('is_primary');
            $table->index('is_active');
            $table->index('delivery_zone');
            $table->index(['latitude', 'longitude']); // For geolocation queries
            $table->index(['user_id', 'location_type']); // For user's specific location types
            $table->index(['city', 'state']); // For regional filtering
            $table->index('country'); // For country-level filtering
            $table->index('postal_code'); // For postal code filtering
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('last_updated');

            // Foreign key constraints
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
