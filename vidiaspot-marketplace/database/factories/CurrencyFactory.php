<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Currency;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Currency>
 */
class CurrencyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => fake()->lexify('???'),
            'name' => fake()->word,
            'symbol' => fake()->lexify('?'),
            'format' => fake()->lexify('?%s'),
            'thousand_separator' => ',',
            'decimal_places' => 2,
            'decimal_separator' => '.',
            'status' => 'active',
            'is_default' => fake()->boolean,
            'exchange_rate' => fake()->randomFloat(4, 0.1, 5),
            'position' => 'before',
            'active' => true,
        ];
    }
}