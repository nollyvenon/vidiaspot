<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cryptocurrency payments
        \App\Models\PaymentSetting::updateOrCreate([
            'feature_key' => 'cryptocurrency_payments'
        ], [
            'feature_name' => 'Cryptocurrency Payments',
            'feature_type' => 'payment_method',
            'is_enabled' => true,
            'available_countries' => ['NG', 'GH', 'KE', 'ZA'], // Countries where crypto is accepted
            'configuration' => [
                'supported_coins' => ['BTC', 'ETH', 'USDT', 'USDC', 'BNB', 'ADA', 'SOL'],
                'min_amount' => 1000,
                'max_amount' => 10000000,
                'verification_required' => true,
            ],
            'description' => 'Accept cryptocurrency payments using Bitcoin, Ethereum, and other digital currencies',
            'sort_order' => 1,
        ]);

        // Buy Now Pay Later - Klarna
        \App\Models\PaymentSetting::updateOrCreate([
            'feature_key' => 'bnpl_klarna'
        ], [
            'feature_name' => 'Klarna (Buy Now Pay Later)',
            'feature_type' => 'payment_service',
            'is_enabled' => false, // Disabled by default
            'available_countries' => ['NG'], // Available in Nigeria
            'configuration' => [
                'installment_options' => [2, 3, 4, 6],
                'max_purchase_amount' => 500000,
                'apr_rate' => 39.99,
                'credit_check_required' => true,
            ],
            'description' => 'Offer Klarna buy now, pay later options to customers',
            'sort_order' => 2,
        ]);

        // Buy Now Pay Later - Afterpay
        \App\Models\PaymentSetting::updateOrCreate([
            'feature_key' => 'bnpl_afterpay'
        ], [
            'feature_name' => 'Afterpay (Buy Now Pay Later)',
            'feature_type' => 'payment_service',
            'is_enabled' => false, // Disabled by default
            'available_countries' => ['NG'],
            'configuration' => [
                'installment_options' => [4],
                'max_purchase_amount' => 200000,
                'fee_per_installment' => 500,
                'late_fee' => 1000,
            ],
            'description' => 'Offer Afterpay buy now, pay later options to customers',
            'sort_order' => 3,
        ]);

        // Mobile Money - MTN Mobile Money
        \App\Models\PaymentSetting::updateOrCreate([
            'feature_key' => 'mobile_money_mtn'
        ], [
            'feature_name' => 'MTN Mobile Money',
            'feature_type' => 'payment_method',
            'is_enabled' => true,
            'available_countries' => ['NG', 'GH', 'CM', 'UG', 'RW'], // MTN operating countries
            'configuration' => [
                'api_key' => env('MTN_API_KEY', ''),
                'user_id' => env('MTN_USER_ID', ''),
                'callback_url' => env('MTN_CALLBACK_URL', ''),
                'supported_currencies' => ['NGN', 'GHS', 'XAF', 'UGX', 'RWF'],
            ],
            'description' => 'Accept payments via MTN Mobile Money service',
            'sort_order' => 4,
        ]);

        // Mobile Money - M-Pesa
        \App\Models\PaymentSetting::updateOrCreate([
            'feature_key' => 'mobile_money_mpese'
        ], [
            'feature_name' => 'M-Pesa Mobile Money',
            'feature_type' => 'payment_method',
            'is_enabled' => true,
            'available_countries' => ['KE', 'TZ', 'UG', 'GH'], // M-Pesa operating countries
            'configuration' => [
                'business_shortcode' => env('MPESA_BUSINESS_SHORTCODE', ''),
                'consumer_key' => env('MPESA_CONSUMER_KEY', ''),
                'consumer_secret' => env('MPESA_CONSUMER_SECRET', ''),
                'passkey' => env('MPESA_PASSKEY', ''),
                'timeout_url' => env('MPESA_TIMEOUT_URL', ''),
                'result_url' => env('MPESA_RESULT_URL', ''),
            ],
            'description' => 'Accept payments via M-Pesa mobile money service',
            'sort_order' => 5,
        ]);

        // QR Code Payments
        \App\Models\PaymentSetting::updateOrCreate([
            'feature_key' => 'qr_code_payments'
        ], [
            'feature_name' => 'QR Code Payments',
            'feature_type' => 'payment_method',
            'is_enabled' => true,
            'available_countries' => ['NG', 'GH', 'KE', 'ZA', 'UG'],
            'configuration' => [
                'code_lifetime_minutes' => 15,
                'max_amount' => 1000000,
                'supported_currencies' => ['NGN', 'GHS', 'KES', 'ZAR', 'UGX'],
                'show_amount_on_qr' => true,
            ],
            'description' => 'Accept payments via QR code scanning for local transactions',
            'sort_order' => 6,
        ]);

        // Split Payments
        \App\Models\PaymentSetting::updateOrCreate([
            'feature_key' => 'split_payments'
        ], [
            'feature_name' => 'Split Payments',
            'feature_type' => 'payment_service',
            'is_enabled' => true,
            'available_countries' => ['NG', 'GH', 'KE', 'ZA', 'UG', 'RW', 'CM'],
            'configuration' => [
                'max_participants' => 10,
                'max_duration_days' => 365,
                'min_amount_per_person' => 100,
                'allowed_payment_methods' => ['all'],
            ],
            'description' => 'Allow group purchases with split payments among participants',
            'sort_order' => 7,
        ]);

        // Insurance Integration
        \App\Models\PaymentSetting::updateOrCreate([
            'feature_key' => 'insurance_integration'
        ], [
            'feature_name' => 'Insurance Integration',
            'feature_type' => 'service',
            'is_enabled' => true,
            'available_countries' => ['NG', 'GH', 'KE', 'ZA'],
            'configuration' => [
                'providers' => ['allianz', 'african_crest', 'insurance_company_ng'],
                'coverage_types' => ['device_protection', 'product_insurance', 'delivery_insurance', 'high_value_items'],
                'min_coverage_amount' => 5000,
                'max_coverage_amount' => 5000000,
                'premium_percentage' => 2.5,
            ],
            'description' => 'Integrated insurance options for high-value items and protection',
            'sort_order' => 8,
        ]);

        // Tax Calculation
        \App\Models\PaymentSetting::updateOrCreate([
            'feature_key' => 'automatic_tax_calculation'
        ], [
            'feature_name' => 'Automatic Tax Calculation',
            'feature_type' => 'service',
            'is_enabled' => true,
            'available_countries' => ['NG', 'GH', 'KE', 'ZA', 'UG', 'RW', 'CM', 'TZ'],
            'configuration' => [
                'default_tax_rate' => 7.5,
                'tax_regions' => [
                    'NG' => ['federal' => 7.5],
                    'GH' => ['vat' => 12.5],
                    'KE' => ['vat' => 16],
                    'ZA' => ['vat' => 15],
                ],
                'apply_tax_to_shipping' => true,
                'tax_exempt_categories' => ['education', 'health'],
            ],
            'description' => 'Automatically calculate and apply taxes based on location',
            'sort_order' => 9,
        ]);
    }
}
