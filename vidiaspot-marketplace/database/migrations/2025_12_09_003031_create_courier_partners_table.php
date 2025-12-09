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
        Schema::create('courier_partners', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('logo_url')->nullable();
            $table->string('website')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('contact_email')->nullable();
            $table->text('address')->nullable();
            $table->json('coverage_areas')->default(json_encode(['India'])); // JSON array of coverage areas
            $table->json('service_types')->default(json_encode(['standard'])); // ['express', 'standard', 'eco', 'cold_chain']
            $table->json('delivery_timeframes')->default(json_encode([
                'express' => '24h',
                'standard' => '48h',
                'economy' => '72h'
            ])); // {express: '24h', standard: '48h', economy: '72h'}
            $table->json('pricing_tiers')->default(json_encode([
                'weight' => [
                    ['min' => 0, 'max' => 1, 'rate' => 150],
                    ['min' => 1, 'max' => 5, 'rate' => 250],
                    ['min' => 5, 'max' => 10, 'rate' => 400],
                    ['min' => 10, 'max' => null, 'rate' => 600]
                ],
                'distance' => [
                    ['min' => 0, 'max' => 10, 'rate' => 100],
                    ['min' => 10, 'max' => 50, 'rate' => 200],
                    ['min' => 50, 'max' => null, 'rate' => 400]
                ]
            ])); // {weight_tiers: [...], distance_tiers: [...]}
            $table->boolean('is_active')->default(true);
            $table->boolean('is_verified')->default(false);
            $table->decimal('rating', 3, 2)->default(0.00); // Average rating from users (0-5 scale)
            $table->integer('total_shipments')->default(0);
            $table->decimal('on_time_delivery_rate', 5, 2)->default(0.00); // Percentage of on-time deliveries
            $table->decimal('success_rate', 5, 2)->default(0.00); // Success rate percentage
            $table->boolean('insurance_coverage_available')->default(false); // Availability of insurance
            $table->decimal('insurance_max_value', 12, 2)->default(0.00); // Maximum value covered by insurance
            $table->decimal('insurance_premium_rate', 5, 2)->default(0.00); // Premium rate as percentage of package value
            $table->boolean('cold_chain_capabilities')->default(false); // Ability to handle temperature-sensitive goods
            $table->boolean('fragile_handling')->default(false); // Capability for fragile item handling
            $table->json('specialized_vehicle_fleet')->nullable(); // Types of special vehicles available ['refrigerated_vans', 'bikes', 'cars']
            $table->boolean('real_time_tracking')->default(false); // Whether real-time tracking is available
            $table->boolean('customer_support_available')->default(false); // Availability of customer support
            $table->boolean('returns_management')->default(false); // Ability to manage returns
            $table->boolean('pickup_services')->default(false); // Availability of pickup services
            $table->json('delivery_windows')->default(json_encode([
                'morning' => ['start' => '09:00', 'end' => '12:00'],
                'afternoon' => ['start' => '12:00', 'end' => '17:00'],
                'evening' => ['start' => '17:00', 'end' => '20:00']
            ])); // {morning: {start: '09:00', end: '12:00'}, afternoon: {...}}
            $table->boolean('same_day_delivery_available')->default(false);
            $table->boolean('next_day_delivery_available')->default(true);
            $table->boolean('international_shipping')->default(false);
            $table->boolean('warehousing_services')->default(false);
            $table->json('integration_api_details')->nullable(); // API integration details
            $table->string('api_endpoint')->nullable(); // API endpoint for integration
            $table->string('api_key')->nullable(); // API key for authentication
            $table->string('secret_key')->nullable(); // Secret key for authentication
            $table->string('webhook_url')->nullable(); // Webhook for status updates
            $table->decimal('commission_rate', 5, 2)->default(5.00); // Platform commission percentage
            $table->boolean('preferred_partnership')->default(false); // Preferred partner status
            $table->integer('minimum_contract_period')->default(1); // Minimum contract period in months
            $table->text('contract_terms')->nullable(); // Terms of contract
            $table->json('sla_metrics')->nullable(); // Service level agreement metrics
            $table->decimal('performance_score', 5, 2)->default(50.00); // Performance score (0-100)
            $table->timestamp('last_performance_review')->nullable(); // Last performance review date
            $table->timestamp('next_review_date')->nullable(); // Next review date
            $table->boolean('driver_verification_required')->default(true); // Whether driver verification is required
            $table->boolean('driver_background_check')->default(false); // Whether background checks are required
            $table->boolean('vehicle_insurance_required')->default(true); // Whether vehicle insurance is required
            $table->boolean('driver_training_certified')->default(false); // Whether drivers are certified
            $table->boolean('carbon_neutral_shipping')->default(false); // Whether offers carbon neutral shipping
            $table->decimal('green_fleet_percentage', 5, 2)->default(0.00); // Percentage of green vehicles
            $table->json('sustainability_certifications')->nullable(); // Sustainability certifications
            $table->json('special_handling_certifications')->nullable(); // Certifications for special handling
            $table->json('custom_fields')->nullable(); // For additional custom fields
            $table->json('metadata')->nullable(); // Additional metadata
            $table->timestamps();

            // Indexes
            $table->index('name');
            $table->index('is_active');
            $table->index('is_verified');
            $table->index('rating');
            $table->index('coverage_areas');
            $table->index('service_types');
            $table->index(['is_active', 'rating']); // For filtering active partners by rating
            $table->index(['same_day_delivery_available', 'is_active']); // For same-day delivery partners
            $table->index(['cold_chain_capabilities', 'is_active']); // For cold-chain capable partners
            $table->index(['international_shipping', 'is_active']); // For international shipping partners
            $table->index('created_at');
            $table->index('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courier_partners');
    }
};
