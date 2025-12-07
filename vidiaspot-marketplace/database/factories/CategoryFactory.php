<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Category;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->word,
            'slug' => fake()->slug,
            'description' => fake()->sentence,
            'parent_id' => null,
            'status' => 'active',
            'order' => fake()->numberBetween(0, 100),
            'icon' => null,
            'image_url' => null,
            'meta_title' => null,
            'meta_description' => null,
            'is_featured' => fake()->boolean,
        ];
    }
}