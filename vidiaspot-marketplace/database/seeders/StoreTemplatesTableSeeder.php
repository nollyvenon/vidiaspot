<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StoreTemplatesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Add the default templates that were previously hardcoded
        \App\Models\StoreTemplate::create([
            'key' => 'default',
            'name' => 'Default Theme',
            'description' => 'Clean and simple theme',
            'features' => ['responsive', 'fast', 'minimal'],
            'config' => [],
            'is_active' => true,
            'sort_order' => 0,
        ]);

        \App\Models\StoreTemplate::create([
            'key' => 'modern',
            'name' => 'Modern Theme',
            'description' => 'Contemporary and sleek design',
            'features' => ['gallery', 'animations', 'modern'],
            'config' => [],
            'is_active' => true,
            'sort_order' => 1,
        ]);

        \App\Models\StoreTemplate::create([
            'key' => 'classic',
            'name' => 'Classic Theme',
            'description' => 'Traditional and reliable look',
            'features' => ['trusted', 'professional', 'classic'],
            'config' => [],
            'is_active' => true,
            'sort_order' => 2,
        ]);

        \App\Models\StoreTemplate::create([
            'key' => 'premium',
            'name' => 'Premium Theme',
            'description' => 'Elegant and feature-rich',
            'features' => ['exclusive', 'enhanced', 'premium'],
            'config' => [],
            'is_active' => true,
            'sort_order' => 3,
        ]);

        \App\Models\StoreTemplate::create([
            'key' => 'storefront',
            'name' => 'Storefront Theme',
            'description' => 'Designed for shopping experience',
            'features' => ['cart', 'checkout', 'shopping'],
            'config' => [],
            'is_active' => true,
            'sort_order' => 4,
        ]);

        \App\Models\StoreTemplate::create([
            'key' => 'portfolio',
            'name' => 'Portfolio Theme',
            'description' => 'Showcase your products beautifully',
            'features' => ['gallery', 'presentation', 'showcase'],
            'config' => [],
            'is_active' => true,
            'sort_order' => 5,
        ]);
    }
}
