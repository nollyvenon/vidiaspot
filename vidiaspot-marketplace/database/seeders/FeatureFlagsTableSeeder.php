<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FeatureFlag;

class FeatureFlagsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Add feature flags for main features
        FeatureFlag::create([
            'feature_key' => 'insurance_policy',
            'name' => 'Insurance Policy Sales',
            'description' => 'Enable insurance policy sales and comparison features',
            'is_enabled' => true,
            'allowed_countries' => ['India'], // Initially limited to India
            'allowed_states' => null, // All states initially
            'allowed_cities' => null, // All cities initially
            'config' => [
                'providers_count' => 5,
                'categories' => ['life', 'health', 'motor', 'travel', 'home'],
                'max_coverage_limit' => 10000000,
            ],
            'starts_at' => now()->subDay(),
            'expires_at' => null,
        ]);

        FeatureFlag::create([
            'feature_key' => 'online_store',
            'name' => 'Online Store',
            'description' => 'Enable vendor online store creation and management',
            'is_enabled' => true,
            'allowed_countries' => ['India', 'Nigeria', 'Ghana'], // Multi-country support
            'allowed_states' => null,
            'allowed_cities' => null,
            'config' => [
                'max_themes' => 6,
                'max_products_per_store' => 100,
                'custom_domain' => false,
            ],
            'starts_at' => now()->subDay(),
            'expires_at' => null,
        ]);

        FeatureFlag::create([
            'feature_key' => 'pay_later',
            'name' => 'Pay Later (BNPL)',
            'description' => 'Enable buy-now-pay-later functionality',
            'is_enabled' => true,
            'allowed_countries' => ['India'], // Initially India only
            'allowed_states' => ['Maharashtra', 'Karnataka', 'Delhi', 'Tamil Nadu'],
            'allowed_cities' => null,
            'config' => [
                'max_amount' => 500000,
                'tenure_options' => [3, 6, 9, 12],
                'interest_rate' => 12.0,
            ],
            'starts_at' => now()->subDay(),
            'expires_at' => null,
        ]);

        FeatureFlag::create([
            'feature_key' => 'crypto_payments',
            'name' => 'Cryptocurrency Payments',
            'description' => 'Enable cryptocurrency payment options',
            'is_enabled' => false, // Disabled by default
            'allowed_countries' => ['Nigeria', 'Ghana'], // Initially for crypto-friendly markets
            'allowed_states' => null,
            'allowed_cities' => null,
            'config' => [
                'supported_coins' => ['bitcoin', 'ethereum', 'usdc'],
                'max_transaction_amount' => 10000,
            ],
            'starts_at' => null,
            'expires_at' => null,
        ]);

        FeatureFlag::create([
            'feature_key' => 'split_payments',
            'name' => 'Split Payments',
            'description' => 'Enable split payment functionality among multiple users',
            'is_enabled' => true,
            'allowed_countries' => ['India', 'Nigeria'],
            'allowed_states' => null,
            'allowed_cities' => null,
            'config' => [
                'max_participants' => 6,
                'fee_percentage' => 0.5,
            ],
            'starts_at' => now()->subDay(),
            'expires_at' => null,
        ]);

        FeatureFlag::create([
            'feature_key' => 'insurance_aggregator',
            'name' => 'Insurance Aggregator',
            'description' => 'Enable insurance comparison and aggregation features (PolicyBazaar-like)',
            'is_enabled' => true,
            'allowed_countries' => ['India'],
            'allowed_states' => null,
            'allowed_cities' => null,
            'config' => [
                'providers_count' => 10,
                'categories' => ['life', 'health', 'motor', 'travel', 'home', 'term'],
                'comparison_features' => ['premium', 'coverage', 'rating', 'features'],
            ],
            'starts_at' => now()->subDay(),
            'expires_at' => null,
        ]);
    }
}
