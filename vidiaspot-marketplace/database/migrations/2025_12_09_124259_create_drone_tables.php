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
        Schema::create('drones', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('serial_number')->unique();
            $table->string('model');
            $table->string('manufacturer');
            $table->decimal('current_location_lat', 10, 8)->nullable();
            $table->decimal('current_location_lng', 11, 8)->nullable();
            $table->decimal('battery_level', 5, 2)->default(100.00);
            $table->string('status')->default('active'); // active, maintenance, retired
            $table->boolean('is_available')->default(true);
            $table->decimal('max_payload', 8, 2); // in kg
            $table->decimal('max_flight_time', 6, 2); // in minutes
            $table->decimal('max_altitude', 8, 2); // in meters
            $table->decimal('max_speed', 8, 2); // in km/h
            $table->date('last_maintenance_date')->nullable();
            $table->date('next_maintenance_date')->nullable();
            $table->decimal('flight_hours', 8, 2)->default(0);
            $table->decimal('maintenance_hours', 8, 2)->default(0);
            $table->json('specs')->nullable();
            $table->foreignId('assigned_courier_partner_id')->nullable()->constrained('courier_partners')->onDelete('set null');
            $table->foreignId('current_mission_id')->nullable()->constrained('drone_missions')->onDelete('set null');
            $table->timestamps();

            $table->index(['is_available', 'status']);
            $table->index('current_mission_id');
            $table->index('assigned_courier_partner_id');
            $table->index('serial_number');
        });

        Schema::create('drone_missions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('drone_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('delivery_address_id')->nullable()->constrained()->onDelete('set null'); // Assuming Address model exists
            $table->string('mission_type')->default('delivery');
            $table->string('status')->default('pending'); // pending, in_progress, completed, cancelled
            $table->timestamp('estimated_departure_time')->nullable();
            $table->timestamp('estimated_arrival_time')->nullable();
            $table->timestamp('actual_departure_time')->nullable();
            $table->timestamp('actual_arrival_time')->nullable();
            $table->decimal('origin_lat', 10, 8);
            $table->decimal('origin_lng', 11, 8);
            $table->decimal('destination_lat', 10, 8);
            $table->decimal('destination_lng', 11, 8);
            $table->decimal('distance', 8, 2); // in km
            $table->decimal('estimated_duration', 6, 2); // in minutes
            $table->decimal('actual_duration', 6, 2)->nullable(); // in minutes
            $table->json('weather_conditions')->nullable();
            $table->decimal('battery_at_departure', 5, 2);
            $table->decimal('battery_at_arrival', 5, 2)->nullable();
            $table->decimal('payload_weight', 8, 2); // in kg
            $table->json('waypoints')->nullable(); // Array of coordinates for the route
            $table->json('tracking_data')->nullable(); // Real-time tracking information
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['drone_id', 'status']);
            $table->index(['order_id', 'status']);
            $table->index('status');
            $table->index('estimated_departure_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drone_missions');
        Schema::dropIfExists('drones');
    }
};
