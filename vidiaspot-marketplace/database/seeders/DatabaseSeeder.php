<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(LanguagesTableSeeder::class);
        $this->call(CurrenciesTableSeeder::class);
        $this->call(RolesTableSeeder::class);
        $this->call(CryptoCurrenciesSeeder::class);

        // Create an admin user
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@vidiaspot.com',
            'email_verified_at' => now(),
        ]);

        // Assign admin role to the admin user
        $adminRole = \App\Models\Role::where('name', 'admin')->first();
        if ($adminRole) {
            $admin->assignRole('admin');
        }

        // Create a normal user
        $normalUser = User::factory()->create([
            'name' => 'Normal User',
            'email' => 'user@vidiaspot.com',
            'email_verified_at' => now(),
        ]);

        // Assign user role to normal user
        $userRole = \App\Models\Role::where('name', 'user')->first();
        if ($userRole) {
            $normalUser->assignRole('user');
        }

        // Create a seller user
        $seller = User::factory()->create([
            'name' => 'Seller User',
            'email' => 'seller@vidiaspot.com',
            'email_verified_at' => now(),
        ]);

        // Assign seller role to seller user
        $sellerRole = \App\Models\Role::where('name', 'seller')->first();
        if ($sellerRole) {
            $seller->assignRole('seller');
        }
    }
}
