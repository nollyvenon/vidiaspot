<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InsuranceProvidersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Add major Indian insurance providers
        \App\Models\InsuranceProvider::create([
            'name' => 'Life Insurance Corporation of India (LIC)',
            'description' => 'India\'s largest life insurance company with a market share of 65.15% as of March 2023.',
            'logo_url' => 'https://example.com/logos/lic.png',
            'website' => 'https://www.licindia.in',
            'phone' => '+91-1234567890',
            'email' => 'info@licindia.in',
            'address' => 'LIC Building, Barakhamba Road, New Delhi - 110001',
            'license_number' => 'LIC/2023/12345',
            'registration_number' => 'IRDAI/123/2023',
            'claim_settlement_ratio' => 98.32,
            'rating' => 4.8,
            'is_active' => true,
            'categories' => ['life', 'health', 'motor', 'travel', 'home'],
            'features' => ['network_hospitals', '24/7_support', 'cashless', 'online_policies'],
            'coverage_areas' => ['India'],
            'min_premium' => 1000,
            'max_premium' => 1000000,
            'min_coverage' => 100000,
            'max_coverage' => 10000000,
            'specializations' => ['term_insurance', 'endowment', 'pension'],
            'network_partners' => []
        ]);

        \App\Models\InsuranceProvider::create([
            'name' => 'ICICI Prudential Life Insurance',
            'description' => 'One of India\'s leading private life insurance companies offering a range of individual and group insurance products.',
            'logo_url' => 'https://example.com/logos/icici-prudential.png',
            'website' => 'https://www.iciciprulife.com',
            'phone' => '+91-1234567891',
            'email' => 'info@iciciprulife.com',
            'address' => 'ICICI Centre, Bandra Kurla Complex, Mumbai - 400051',
            'license_number' => 'ICICI/2023/12346',
            'registration_number' => 'IRDAI/124/2023',
            'claim_settlement_ratio' => 97.21,
            'rating' => 4.5,
            'is_active' => true,
            'categories' => ['life', 'health'],
            'features' => ['online_policies', 'quick_claims', '24/7_support'],
            'coverage_areas' => ['India'],
            'min_premium' => 2000,
            'max_premium' => 2000000,
            'min_coverage' => 200000,
            'max_coverage' => 20000000,
            'specializations' => ['term_insurance', 'unit_linked', 'child_plans'],
            'network_partners' => []
        ]);

        \App\Models\InsuranceProvider::create([
            'name' => 'HDFC ERGO General Insurance',
            'description' => 'A leading private sector general insurance company offering motor, health, travel, and other general insurance products.',
            'logo_url' => 'https://example.com/logos/hdfc-ergo.png',
            'website' => 'https://www.hdfcergo.com',
            'phone' => '+91-1234567892',
            'email' => 'info@hdfcergo.com',
            'address' => 'A-16, MIDC Industrial Area, Andheri East, Mumbai - 400093',
            'license_number' => 'HDFC/2023/12347',
            'registration_number' => 'IRDAI/125/2023',
            'claim_settlement_ratio' => 95.15,
            'rating' => 4.3,
            'is_active' => true,
            'categories' => ['health', 'motor', 'travel', 'home'],
            'features' => ['cashless', '24/7_support', 'network_hospitals'],
            'coverage_areas' => ['India'],
            'min_premium' => 1500,
            'max_premium' => 1500000,
            'min_coverage' => 100000,
            'max_coverage' => 15000000,
            'specializations' => ['health_insurance', 'car_insurance', 'two_wheeler'],
            'network_partners' => ['Apollo Hospitals', 'Fortis', 'Max Healthcare']
        ]);

        \App\Models\InsuranceProvider::create([
            'name' => 'Max Bupa Health Insurance',
            'description' => 'A dedicated health insurance company offering comprehensive health insurance solutions.',
            'logo_url' => 'https://example.com/logos/max-bupa.png',
            'website' => 'https://www.maxbupa.com',
            'phone' => '+91-1234567893',
            'email' => 'info@maxbupa.com',
            'address' => 'Max House, Corporate Office, Mahindra World City, Chennai - 603002',
            'license_number' => 'MAX/2023/12348',
            'registration_number' => 'IRDAI/126/2023',
            'claim_settlement_ratio' => 94.87,
            'rating' => 4.2,
            'is_active' => true,
            'categories' => ['health'],
            'features' => ['cashless', 'network_hospitals', 'wellness_programs'],
            'coverage_areas' => ['India'],
            'min_premium' => 2000,
            'max_premium' => 1000000,
            'min_coverage' => 100000,
            'max_coverage' => 10000000,
            'specializations' => ['family_health', 'individual_health', 'critical_illness'],
            'network_partners' => ['Max Healthcare', 'Fortis', 'Apollo Hospitals']
        ]);

        \App\Models\InsuranceProvider::create([
            'name' => 'Bajaj Allianz General Insurance',
            'description' => 'One of the leading private general insurance companies in India, offering a wide range of insurance products.',
            'logo_url' => 'https://example.com/logos/bajaj-allianz.png',
            'website' => 'https://www.bajajallianz.com',
            'phone' => '+91-1234567894',
            'email' => 'info@bajajallianz.com',
            'address' => 'Bajaj Allianz House, Pune - 411028',
            'license_number' => 'BAJAJ/2023/12349',
            'registration_number' => 'IRDAI/127/2023',
            'claim_settlement_ratio' => 93.45,
            'rating' => 4.1,
            'is_active' => true,
            'categories' => ['health', 'motor', 'travel', 'home'],
            'features' => ['cashless', '24/7_roadside_assistance', 'quick_claims'],
            'coverage_areas' => ['India'],
            'min_premium' => 1800,
            'max_premium' => 1200000,
            'min_coverage' => 50000,
            'max_coverage' => 12000000,
            'specializations' => ['car_insurance', 'bike_insurance', 'travel_insurance'],
            'network_partners' => ['Apollo Hospitals', 'Fortis', 'Medanta']
        ]);
    }
}
