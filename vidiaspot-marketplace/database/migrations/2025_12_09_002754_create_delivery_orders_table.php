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
        Schema::create('delivery_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // User requesting the delivery
            $table->unsignedBigInteger('order_id'); // The order this delivery is for
            $table->string('order_number'); // Order reference number
            $table->unsignedBigInteger('pickup_address_id'); // Address to pick up from
            $table->unsignedBigInteger('delivery_address_id'); // Address to deliver to
            $table->unsignedBigInteger('courier_partner_id')->nullable(); // Courier partner handling delivery
            $table->string('delivery_status')->default('pending'); // pending, assigned, picked_up, in_transit, out_for_delivery, delivered, failed, returned
            $table->string('delivery_type')->default('standard'); // same_day, next_day, standard, express, scheduled
            $table->string('delivery_service_type')->default('standard'); // standard, express, premium, eco-friendly, cold_chain
            $table->decimal('package_weight_kg', 8, 2)->default(0.00);
            $table->json('package_dimensions')->default(json_encode([
                'length_cm' => 0,
                'width_cm' => 0,
                'height_cm' => 0
            ])); // Package dimensions {length, width, height} in cm
            $table->decimal('package_value', 15, 2)->default(0.00); // Package value for insurance
            $table->timestamp('estimated_delivery_time')->nullable();
            $table->timestamp('actual_delivery_time')->nullable();
            $table->decimal('delivery_distance_km', 8, 2)->default(0.00);
            $table->json('delivery_route_data')->nullable(); // Route information for optimization
            $table->decimal('delivery_cost', 10, 2)->default(0.00);
            $table->decimal('carbon_emissions', 8, 3)->default(0.000); // Calculated carbon emissions
            $table->boolean('is_cash_on_delivery')->default(false);
            $table->decimal('cod_amount', 10, 2)->default(0.00);
            $table->boolean('recipient_signature_required')->default(false);
            $table->text('delivery_instructions')->nullable();
            $table->json('special_handling_notes')->nullable(); // Fragile, refrigerated, etc.
            $table->string('tracking_number')->unique()->nullable(); // Unique tracking number
            $table->decimal('current_location_latitude', 10, 8)->nullable();
            $table->decimal('current_location_longitude', 11, 8)->nullable();
            $table->text('last_known_location')->nullable();
            $table->timestamp('eta_timestamp')->nullable();
            $table->timestamp('delivery_window_start')->nullable();
            $table->timestamp('delivery_window_end')->nullable();
            $table->string('delivery_confirmation_token')->nullable(); // Token for delivery confirmation
            $table->string('signature_image_path')->nullable(); // Path to recipient signature
            $table->string('photo_on_delivery_path')->nullable(); // Path to delivery photo
            $table->integer('delivery_attempt_count')->default(0);
            $table->json('delivery_attempts_log')->nullable(); // {attempt_no: {date, result, notes}}
            $table->decimal('delivery_partner_rating', 3, 2)->nullable(); // Rating for the delivery partner
            $table->text('delivery_notes')->nullable();
            $table->boolean('insurance_covered')->default(false);
            $table->boolean('insurance_claim_initiated')->default(false);
            $table->decimal('insurance_claim_amount', 10, 2)->nullable();
            $table->string('insurance_claim_status')->default('pending'); // 'pending', 'approved', 'rejected'
            $table->string('delivery_zone')->nullable();
            $table->unsignedBigInteger('pickup_point_id')->nullable(); // If pickup point used
            $table->unsignedBigInteger('delivery_hub_id')->nullable(); // Which delivery hub handles this
            $table->decimal('delivery_partner_commission', 8, 2)->default(0.00); // Commission for delivery partner
            $table->timestamp('pickup_time')->nullable();
            $table->timestamp('delivery_deadline')->nullable();
            $table->boolean('temperature_control_required')->default(false); // For cold chain deliveries
            $table->json('temperature_log')->nullable(); // Temperature logs for cold chain
            $table->boolean('package_inspection_required')->default(false);
            $table->json('package_inspection_result')->nullable();
            $table->string('payment_status')->default('pending'); // 'pending', 'completed', 'failed'
            $table->boolean('return_requested')->default(false);
            $table->text('return_reason')->nullable();
            $table->timestamp('return_initiated_at')->nullable();
            $table->timestamp('return_shipped_at')->nullable();
            $table->timestamp('return_delivered_at')->nullable();
            $table->string('return_tracking_number')->nullable();
            $table->unsignedBigInteger('warehouse_id')->nullable(); // For items from specific warehouses
            $table->unsignedBigInteger('assigned_driver_id')->nullable(); // ID of the driver assigned
            $table->string('driver_name')->nullable();
            $table->string('driver_phone')->nullable();
            $table->decimal('driver_rating', 3, 2)->default(0.00);
            $table->json('driver_vehicle_info')->nullable(); // Vehicle info {type, plate_number, etc.}
            $table->unsignedBigInteger('delivery_completed_by')->nullable(); // User who marked as delivered
            $table->timestamp('delivered_at')->nullable();
            $table->json('custom_fields')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('user_id');
            $table->index('order_id');
            $table->index('order_number');
            $table->index('pickup_address_id');
            $table->index('delivery_address_id');
            $table->index('courier_partner_id');
            $table->index('delivery_status');
            $table->index('delivery_type');
            $table->index('tracking_number');
            $table->index(['user_id', 'delivery_status']); // For user's delivery status
            $table->index(['courier_partner_id', 'delivery_status']); // For courier partner's deliveries
            $table->index(['delivery_zone', 'delivery_status']); // For zone-based delivery management
            $table->index('created_at');
            $table->index('estimated_delivery_time');
            $table->index(['delivery_type', 'delivery_status']); // For delivery type filtering
            $table->index('temperature_control_required'); // For cold chain deliveries
            $table->index('return_requested'); // For return processing
            $table->index('delivered_at');

            // Foreign key constraints
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('pickup_address_id')->references('id')->on('locations')->onDelete('restrict');
            $table->foreign('delivery_address_id')->references('id')->on('locations')->onDelete('restrict');
            $table->foreign('courier_partner_id')->references('id')->on('courier_partners')->onDelete('set null');
            $table->foreign('pickup_point_id')->references('id')->on('locations')->onDelete('set null');
            $table->foreign('delivery_hub_id')->references('id')->on('locations')->onDelete('set null');
            $table->foreign('warehouse_id')->references('id')->on('locations')->onDelete('set null');
            $table->foreign('assigned_driver_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('delivery_completed_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_orders');
    }
};
