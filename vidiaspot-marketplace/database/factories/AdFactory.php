<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Ad;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ad>
 */
class AdFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'category_id' => \App\Models\Category::factory(),
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph,
            'price' => fake()->randomFloat(2, 10, 1000),
            'currency_code' => 'NGN',
            'condition' => fake()->randomElement(['new', 'like_new', 'good', 'fair', 'poor']),
            'status' => 'active',
            'location' => fake()->city . ', ' . fake()->state,
            'latitude' => fake()->latitude,
            'longitude' => fake()->longitude,
            'contact_phone' => fake()->phoneNumber,
            'negotiable' => fake()->boolean,
            'view_count' => fake()->numberBetween(0, 1000),
            'expires_at' => now()->addDays(fake()->numberBetween(7, 30)),
        ];
    }
}