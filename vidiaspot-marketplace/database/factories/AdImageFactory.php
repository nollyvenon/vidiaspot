<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\AdImage;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AdImage>
 */
class AdImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ad_id' => \App\Models\Ad::factory(),
            'image_path' => 'ads/' . fake()->numberBetween(1, 100) . '/image.jpg',
            'image_url' => fake()->imageUrl,
            'is_primary' => fake()->boolean,
            'order' => fake()->numberBetween(0, 10),
        ];
    }
}