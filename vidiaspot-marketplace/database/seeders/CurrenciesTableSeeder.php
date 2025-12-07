<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CurrenciesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currencies = [
            [
                'code' => 'NGN',
                'name' => 'Nigerian Naira',
                'symbol' => '₦',
                'country' => 'Nigeria',
                'precision' => 2,
                'format' => 'naira',
                'is_active' => true,
                'is_default' => true,
                'minor_unit' => 100,
            ],
            [
                'code' => 'USD',
                'name' => 'US Dollar',
                'symbol' => '$',
                'country' => 'United States',
                'precision' => 2,
                'format' => 'dollar',
                'is_active' => true,
                'is_default' => false,
                'minor_unit' => 100,
            ],
            [
                'code' => 'EUR',
                'name' => 'Euro',
                'symbol' => '€',
                'country' => 'European Union',
                'precision' => 2,
                'format' => 'euro',
                'is_active' => true,
                'is_default' => false,
                'minor_unit' => 100,
            ],
            [
                'code' => 'GBP',
                'name' => 'British Pound',
                'symbol' => '£',
                'country' => 'United Kingdom',
                'precision' => 2,
                'format' => 'pound',
                'is_active' => true,
                'is_default' => false,
                'minor_unit' => 100,
            ],
            [
                'code' => 'ZAR',
                'name' => 'South African Rand',
                'symbol' => 'R',
                'country' => 'South Africa',
                'precision' => 2,
                'format' => 'rand',
                'is_active' => true,
                'is_default' => false,
                'minor_unit' => 100,
            ],
            [
                'code' => 'GHS',
                'name' => 'Ghanaian Cedi',
                'symbol' => 'GH₵',
                'country' => 'Ghana',
                'precision' => 2,
                'format' => 'cedi',
                'is_active' => true,
                'is_default' => false,
                'minor_unit' => 100,
            ],
            [
                'code' => 'KES',
                'name' => 'Kenyan Shilling',
                'symbol' => 'KSh',
                'country' => 'Kenya',
                'precision' => 2,
                'format' => 'shilling',
                'is_active' => true,
                'is_default' => false,
                'minor_unit' => 100,
            ],
            [
                'code' => 'XOF',
                'name' => 'West African CFA Franc',
                'symbol' => 'CFA',
                'country' => 'West African States',
                'precision' => 0,
                'format' => 'cfa',
                'is_active' => true,
                'is_default' => false,
                'minor_unit' => 100,
            ],
        ];

        foreach ($currencies as $currency) {
            DB::table('currencies')->updateOrInsert(
                ['code' => $currency['code']],
                $currency
            );
        }
    }
}
