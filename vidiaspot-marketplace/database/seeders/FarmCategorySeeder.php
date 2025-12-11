<?php

namespace Database\Seeders;

use App\Models\ECommerce\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FarmCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create main farm products category
        $farmCategory = Category::create([
            'name' => 'Farm Products',
            'slug' => 'farm-products',
            'description' => 'Fresh products directly from local farms',
            'icon' => 'local_florist',
            'order' => 10,
            'is_active' => true,
        ]);

        // Create subcategories for farm products
        $subcategories = [
            [
                'name' => 'Fresh Vegetables',
                'slug' => 'fresh-vegetables',
                'description' => 'Fresh vegetables directly from the farm',
                'icon' => 'eco',
                'order' => 1,
                'parent_id' => $farmCategory->id,
                'is_active' => true,
            ],
            [
                'name' => 'Fresh Fruits',
                'slug' => 'fresh-fruits',
                'description' => 'Fresh fruits directly from the farm',
                'icon' => 'eco',
                'order' => 2,
                'parent_id' => $farmCategory->id,
                'is_active' => true,
            ],
            [
                'name' => 'Organic Products',
                'slug' => 'organic-products',
                'description' => 'Certified organic products from certified farms',
                'icon' => 'eco',
                'order' => 3,
                'parent_id' => $farmCategory->id,
                'is_active' => true,
            ],
            [
                'name' => 'Dairy Products',
                'slug' => 'dairy-products',
                'description' => 'Fresh dairy products from local farms',
                'icon' => 'local_drink',
                'order' => 4,
                'parent_id' => $farmCategory->id,
                'is_active' => true,
            ],
            [
                'name' => 'Poultry & Eggs',
                'slug' => 'poultry-eggs',
                'description' => 'Fresh poultry and eggs from local farms',
                'icon' => 'eco',
                'order' => 5,
                'parent_id' => $farmCategory->id,
                'is_active' => true,
            ],
            [
                'name' => 'Fresh Herbs',
                'slug' => 'fresh-herbs',
                'description' => 'Fresh herbs directly from the farm',
                'icon' => 'local_florist',
                'order' => 6,
                'parent_id' => $farmCategory->id,
                'is_active' => true,
            ],
            [
                'name' => 'Grains & Cereals',
                'slug' => 'grains-cereals',
                'description' => 'Rice, maize, wheat and other grains',
                'icon' => 'storefront',
                'order' => 7,
                'parent_id' => $farmCategory->id,
                'is_active' => true,
            ],
            [
                'name' => 'Tubers & Roots',
                'slug' => 'tubers-roots',
                'description' => 'Yams, cassava, potatoes, sweet potatoes',
                'icon' => 'nature',
                'order' => 8,
                'parent_id' => $farmCategory->id,
                'is_active' => true,
            ],
            [
                'name' => 'Livestock',
                'slug' => 'livestock',
                'description' => 'Cattle, goats, sheep and other livestock',
                'icon' => 'pets',
                'order' => 9,
                'parent_id' => $farmCategory->id,
                'is_active' => true,
            ],
            [
                'name' => 'Fresh Fish & Seafood',
                'slug' => 'fresh-fish',
                'description' => 'Fresh fish and seafood from local sources',
                'icon' => 'waves',
                'order' => 10,
                'parent_id' => $farmCategory->id,
                'is_active' => true,
            ],
            [
                'name' => 'Nuts & Seeds',
                'slug' => 'nuts-seeds',
                'description' => 'Groundnuts, cashews, melon seeds, etc.',
                'icon' => 'forest',
                'order' => 11,
                'parent_id' => $farmCategory->id,
                'is_active' => true,
            ],
            [
                'name' => 'Spices & Seasonings',
                'slug' => 'spices-seasonings',
                'description' => 'Pepper, ginger, garlic, onions and other spices',
                'icon' => 'restaurant',
                'order' => 12,
                'parent_id' => $farmCategory->id,
                'is_active' => true,
            ],
            [
                'name' => 'Farm Equipment',
                'slug' => 'farm-equipment',
                'description' => 'Tractors, plows, hoes and farming tools',
                'icon' => 'construction',
                'order' => 13,
                'parent_id' => $farmCategory->id,
                'is_active' => true,
            ],
            [
                'name' => 'Fertilizers & Chemicals',
                'slug' => 'fertilizers-chemicals',
                'description' => 'Fertilizers, pesticides and agricultural chemicals',
                'icon' => 'science',
                'order' => 14,
                'parent_id' => $farmCategory->id,
                'is_active' => true,
            ],
            [
                'name' => 'Seedlings & Plants',
                'slug' => 'seedlings-plants',
                'description' => 'Young plants and seedlings for farming',
                'icon' => 'spa',
                'order' => 15,
                'parent_id' => $farmCategory->id,
                'is_active' => true,
            ],
        ];

        foreach ($subcategories as $subcategory) {
            Category::create($subcategory);
        }

        // Also create additional categories similar to Jiji.ng structure
        $agricultureCategory = Category::create([
            'name' => 'Agriculture',
            'slug' => 'agriculture',
            'description' => 'Agricultural products and services',
            'icon' => 'nature_people',
            'order' => 11,
            'parent_id' => null,
            'is_active' => true,
        ]);

        // Create subcategories under Agriculture
        Category::create([
            'name' => 'Commercial Farming',
            'slug' => 'commercial-farming',
            'description' => 'Large-scale commercial farm products',
            'icon' => 'business',
            'order' => 1,
            'parent_id' => $agricultureCategory->id,
            'is_active' => true,
        ]);

        Category::create([
            'name' => 'Subsistence Farming',
            'slug' => 'subsistence-farming',
            'description' => 'Small-scale subsistence farm products',
            'icon' => 'family_restroom',
            'order' => 2,
            'parent_id' => $agricultureCategory->id,
            'is_active' => true,
        ]);
    }
}
