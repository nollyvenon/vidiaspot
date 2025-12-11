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
        Schema::table('ads', function (Blueprint $table) {
            $table->integer('freshness_days')->nullable()->after('farm_size');
            $table->decimal('quality_rating', 3, 2)->nullable()->after('freshness_days'); // 0-5 rating
            $table->json('seasonal_availability')->nullable()->after('quality_rating'); // ['spring', 'summer', 'fall', 'winter']
            $table->string('certification_type')->nullable()->after('seasonal_availability'); // 'organic', 'fair_trade', 'non_gmo', etc.
            $table->string('certification_body')->nullable()->after('certification_type'); // 'USDA', 'EU Organic', etc.
            $table->json('farm_practices')->nullable()->after('certification_body'); // ['crop_rotation', 'companion_planting', 'natural_pest_control']
            $table->json('delivery_options')->nullable()->after('farm_practices'); // ['local_delivery', 'pickup', 'shipping']
            $table->decimal('minimum_order', 10, 2)->nullable()->after('delivery_options');
            $table->string('packaging_type')->nullable()->after('minimum_order'); // 'biodegradable', 'recyclable', 'none'
            $table->integer('shelf_life')->nullable()->after('packaging_type'); // shelf life in days
            $table->text('storage_instructions')->nullable()->after('shelf_life');
            $table->json('farm_certifications')->nullable()->after('storage_instructions');
            $table->boolean('pesticide_use')->default(false)->after('farm_certifications');
            $table->string('irrigation_method')->nullable()->after('pesticide_use'); // 'drip', 'sprinkler', 'flood', 'rainfed'
            $table->string('soil_type')->nullable()->after('irrigation_method'); // 'loamy', 'sandy', 'clay', etc.
            $table->decimal('sustainability_score', 3, 2)->nullable()->after('soil_type'); // 0-10 sustainability rating
            $table->decimal('carbon_footprint', 8, 2)->nullable()->after('sustainability_score'); // kg CO2 equivalent
            $table->boolean('farm_tour_available')->default(false)->after('carbon_footprint');
            $table->text('farm_story')->nullable()->after('farm_tour_available');
            $table->string('farmer_name')->nullable()->after('farm_story');
            $table->string('farmer_image')->nullable()->after('farmer_name');
            $table->text('farmer_bio')->nullable()->after('farmer_image');
            $table->string('harvest_method')->nullable()->after('farmer_bio'); // 'hand_picked', 'machine_harvested', etc.
            $table->text('post_harvest_handling')->nullable()->after('harvest_method');
            $table->integer('supply_capacity')->nullable()->after('post_harvest_handling'); // units per day/week
            $table->decimal('shipping_availability', 5, 2)->nullable()->after('supply_capacity'); // radius in km
            $table->decimal('local_delivery_radius', 5, 2)->nullable()->after('shipping_availability'); // radius in km
            $table->integer('like_count')->default(0)->after('view_count'); // Number of likes for the product
            $table->integer('comment_count')->default(0)->after('like_count'); // Number of comments
            $table->integer('share_count')->default(0)->after('comment_count'); // Number of shares
            $table->json('nutrition_facts')->nullable()->after('harvest_method'); // Nutrition facts for farm products [Calories, Protein, etc.]
            $table->json('allergens')->nullable()->after('nutrition_facts'); // Potential allergens in farm products
            $table->boolean('is_locally_grown')->default(false)->after('pesticide_use'); // Is locally grown within Nigeria?
            $table->string('variety')->nullable()->after('harvest_method'); // Product variety (e.g., 'Roma tomatoes', 'Granny Smith apples')
            $table->string('grade')->nullable()->after('variety'); // Product grade (e.g., 'Grade A', 'Premium', 'Standard')
            $table->string('unit_size')->nullable()->after('packaging_type'); // Unit size (e.g., '1kg', '12 pieces', '1 bunch')
            $table->string('size_dimension')->nullable()->after('unit_size'); // Physical dimensions (e.g., 'Large', 'Medium', 'Small')
            $table->json('quality_attributes')->nullable()->after('quality_rating'); // Additional quality attributes [texture, taste, appearance, etc.]
            $table->boolean('is_seasonal')->default(false)->after('seasonal_availability'); // Is this product seasonal?
            $table->json('seasonal_months')->nullable()->after('is_seasonal'); // Months when product is available ['jan', 'feb', 'mar', ...]
            $table->decimal('yield_per_unit', 8, 2)->nullable()->after('supply_capacity'); // Yield per farming unit (e.g., kg per acre)
            $table->json('available_deliveries')->nullable()->after('delivery_options'); // Specific delivery methods available
            $table->text('handling_instructions')->nullable()->after('post_harvest_handling'); // Special handling instructions
            $table->string('certification_document')->nullable()->after('certification'); // Path to certification document
            $table->string('farm_verification_status')->default('pending')->after('farm_tour_available'); // Verification status of the farm
            $table->json('farm_gallery')->nullable()->after('farmer_image'); // Multiple farm images
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ads', function (Blueprint $table) {
            $table->dropColumn([
                'freshness_days',
                'quality_rating',
                'seasonal_availability',
                'certification_type',
                'certification_body',
                'farm_practices',
                'delivery_options',
                'minimum_order',
                'packaging_type',
                'shelf_life',
                'storage_instructions',
                'farm_certifications',
                'pesticide_use',
                'irrigation_method',
                'soil_type',
                'sustainability_score',
                'carbon_footprint',
                'farm_tour_available',
                'farm_story',
                'farmer_name',
                'farmer_image',
                'farmer_bio',
                'harvest_method',
                'post_harvest_handling',
                'supply_capacity',
                'shipping_availability',
                'local_delivery_radius',
                // Additional columns added later
                'like_count',
                'comment_count',
                'share_count',
                'nutrition_facts',
                'allergens',
                'is_locally_grown',
                'variety',
                'grade',
                'unit_size',
                'size_dimension',
                'quality_attributes',
                'is_seasonal',
                'seasonal_months',
                'yield_per_unit',
                'available_deliveries',
                'handling_instructions',
                'certification_document',
                'farm_verification_status',
                'farm_gallery',
            ]);
        });
    }
};
