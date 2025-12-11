<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Role;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'admin',
                'display_name' => 'Administrator',
                'description' => 'Full system access with all privileges',
                'is_active' => true,
            ],
            [
                'name' => 'seller',
                'display_name' => 'General Seller',
                'description' => 'Business user with ability to create ads and manage business profile',
                'is_active' => true,
            ],
            [
                'name' => 'food_seller',
                'display_name' => 'Food Seller',
                'description' => 'Restaurant/food service provider with ability to create menu items',
                'is_active' => true,
            ],
            [
                'name' => 'farm_seller',
                'display_name' => 'Farm Seller',
                'description' => 'Farm product seller with ability to list farm products with special attributes',
                'is_active' => true,
            ],
            [
                'name' => 'user',
                'display_name' => 'Normal User',
                'description' => 'Standard user with basic marketplace access (browse, contact, buy)',
                'is_active' => true,
            ],
            [
                'name' => 'moderator',
                'display_name' => 'Moderator',
                'description' => 'Content moderation and user management',
                'is_active' => true,
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrInsert(
                ['name' => $role['name']],
                $role
            );
        }
    }
}
