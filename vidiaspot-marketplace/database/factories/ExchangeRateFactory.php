<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\ExchangeRate;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ExchangeRate>
 */
class ExchangeRateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'from_currency_code' => fake()->lexify('???'),
            'to_currency_code' => fake()->lexify('???'),
            'rate' => fake()->randomFloat(8, 0.1, 10),
            'last_updated' => now(),
            'provider' => 'test',
        ];
    }
}