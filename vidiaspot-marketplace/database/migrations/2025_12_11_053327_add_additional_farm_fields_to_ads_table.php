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
            ]);
        });
    }
};
