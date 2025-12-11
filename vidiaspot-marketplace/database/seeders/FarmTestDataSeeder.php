<?php

namespace Database\Seeders;

use App\Models\Ad;
use App\Models\ECommerce\Category;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FarmTestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create a default user for testing
        $user = User::firstOrCreate([
            'email' => 'farmer@test.com',
        ], [
            'name' => 'Farm Test User',
            'email' => 'farmer@test.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);

        // Get farm-related categories
        $farmCategory = Category::where('name', 'Farm Products')->first();
        $veggiesCategory = Category::where('name', 'Fresh Vegetables')->first();
        $fruitsCategory = Category::where('name', 'Fresh Fruits')->first();
        $organicCategory = Category::where('name', 'Organic Products')->first();

        // Create sample farm products
        $farmProducts = [
            [
                'user_id' => $user->id,
                'category_id' => $veggiesCategory->id ?? $farmCategory->id,
                'title' => 'Fresh Organic Tomatoes',
                'description' => 'Juicy, vine-ripened organic tomatoes from our sustainable farm. Harvested today for maximum freshness.',
                'price' => 1200,
                'currency_code' => 'NGN',
                'condition' => 'new',
                'status' => 'active',
                'location' => 'Surulere, Lagos',
                'latitude' => 6.4527,
                'longitude' => 3.3927,
                'contact_phone' => '+2348012345678',
                'negotiable' => false,
                'view_count' => 0,
                'expires_at' => now()->addDays(30),
                'direct_from_farm' => true,
                'farm_name' => 'Green Valley Organic Farm',
                'is_organic' => true,
                'harvest_date' => now()->subDays(1),
                'farm_location' => 'Ibeju-Lekki, Lagos',
                'farm_latitude' => 6.4167,
                'farm_longitude' => 2.9833,
                'certification' => 'NGOCA Certified Organic',
                'harvest_season' => 'all',
                'farm_size' => 5.0,
                'freshness_days' => 1,
                'quality_rating' => 4.8,
                'seasonal_availability' => ['spring', 'summer', 'fall'],
                'certification_type' => 'organic',
                'certification_body' => 'NGOCA',
                'farm_practices' => ['crop_rotation', 'natural_pest_control'],
                'delivery_options' => ['local_delivery', 'pickup'],
                'minimum_order' => 500,
                'packaging_type' => 'biodegradable',
                'shelf_life' => 7,
                'storage_instructions' => 'Store in cool, dry place',
                'farm_certifications' => ['organic'],
                'pesticide_use' => false,
                'irrigation_method' => 'drip',
                'soil_type' => 'loamy',
                'sustainability_score' => 9.2,
                'carbon_footprint' => 0.15,
                'farm_tour_available' => true,
                'farm_story' => 'Family-owned organic farm practicing sustainable agriculture for over 10 years.',
                'farmer_name' => 'John Doe',
                'farmer_image' => 'https://example.com/farmer1.jpg',
                'farmer_bio' => 'Third-generation farmer specializing in organic vegetables',
                'harvest_method' => 'hand_picked',
                'post_harvest_handling' => 'Immediately cooled and sorted',
                'supply_capacity' => 200,
                'shipping_availability' => 50.0,
                'local_delivery_radius' => 25.0,
            ],
            [
                'user_id' => $user->id,
                'category_id' => $fruitsCategory->id ?? $farmCategory->id,
                'title' => 'Fresh Avocados',
                'description' => 'Creamy Hass avocados from our estate farm. Perfectly ripe and ready to eat.',
                'price' => 2000,
                'currency_code' => 'NGN',
                'condition' => 'new',
                'status' => 'active',
                'location' => 'Ikoyi, Lagos',
                'latitude' => 6.4325,
                'longitude' => 3.4075,
                'contact_phone' => '+2348012345679',
                'negotiable' => true,
                'view_count' => 0,
                'expires_at' => now()->addDays(30),
                'direct_from_farm' => true,
                'farm_name' => 'Sunshine Fruit Farm',
                'is_organic' => false,
                'harvest_date' => now()->subDays(2),
                'farm_location' => 'Ogun State',
                'farm_latitude' => 6.7500,
                'farm_longitude' => 3.5000,
                'certification' => null,
                'harvest_season' => 'all',
                'farm_size' => 10.0,
                'freshness_days' => 2,
                'quality_rating' => 4.7,
                'seasonal_availability' => ['spring', 'summer'],
                'certification_type' => null,
                'certification_body' => null,
                'farm_practices' => ['integrated_pest_management'],
                'delivery_options' => ['shipping', 'pickup'],
                'minimum_order' => 1000,
                'packaging_type' => 'recyclable',
                'shelf_life' => 5,
                'storage_instructions' => 'Keep refrigerated until ready to eat',
                'farm_certifications' => [],
                'pesticide_use' => true,
                'irrigation_method' => 'sprinkler',
                'soil_type' => 'sandy',
                'sustainability_score' => 6.8,
                'carbon_footprint' => 0.25,
                'farm_tour_available' => false,
                'farm_story' => 'Specialty fruit farm known for premium quality avocados.',
                'farmer_name' => 'Jane Smith',
                'farmer_image' => 'https://example.com/farmer2.jpg',
                'farmer_bio' => 'Expert in tropical fruit cultivation',
                'harvest_method' => 'hand_picked',
                'post_harvest_handling' => 'Carefully handled to prevent bruising',
                'supply_capacity' => 500,
                'shipping_availability' => 100.0,
                'local_delivery_radius' => 30.0,
            ],
            [
                'user_id' => $user->id,
                'category_id' => $organicCategory->id ?? $farmCategory->id,
                'title' => 'Organic Spinach',
                'description' => 'Nutrient-rich organic spinach grown without pesticides. Packed with vitamins and minerals.',
                'price' => 800,
                'currency_code' => 'NGN',
                'condition' => 'new',
                'status' => 'active',
                'location' => 'VI, Lagos',
                'latitude' => 6.4167,
                'longitude' => 3.4500,
                'contact_phone' => '+2348012345680',
                'negotiable' => false,
                'view_count' => 0,
                'expires_at' => now()->addDays(30),
                'direct_from_farm' => true,
                'farm_name' => 'Healthy Leaf Organic Farm',
                'is_organic' => true,
                'harvest_date' => now()->subDays(0),
                'farm_location' => 'Ikorodu, Lagos',
                'farm_latitude' => 6.5920,
                'farm_longitude' => 3.5948,
                'certification' => 'Certified Organic by NOP',
                'harvest_season' => 'all',
                'farm_size' => 3.0,
                'freshness_days' => 0,
                'quality_rating' => 4.9,
                'seasonal_availability' => ['spring', 'winter'],
                'certification_type' => 'organic',
                'certification_body' => 'NOP',
                'farm_practices' => ['companion_planting', 'composting'],
                'delivery_options' => ['local_delivery', 'pickup'],
                'minimum_order' => 300,
                'packaging_type' => 'biodegradable',
                'shelf_life' => 5,
                'storage_instructions' => 'Store in refrigerator crisper drawer',
                'farm_certifications' => ['organic', 'nop'],
                'pesticide_use' => false,
                'irrigation_method' => 'drip',
                'soil_type' => 'clay_loam',
                'sustainability_score' => 9.8,
                'carbon_footprint' => 0.08,
                'farm_tour_available' => true,
                'farm_story' => 'Small family farm focused on healthy leafy greens using sustainable methods.',
                'farmer_name' => 'Michael Johnson',
                'farmer_image' => 'https://example.com/farmer3.jpg',
                'farmer_bio' => 'Passionate about providing healthy organic produce',
                'harvest_method' => 'hand_cut',
                'post_harvest_handling' => 'Washed with clean water and dried',
                'supply_capacity' => 100,
                'shipping_availability' => 20.0,
                'local_delivery_radius' => 15.0,
            ],
        ];

        foreach ($farmProducts as $product) {
            Ad::create($product);
        }
    }
}