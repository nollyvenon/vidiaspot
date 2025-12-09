<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CourierPartnersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Add major Indian courier partners
        \App\Models\CourierPartner::create([
            'name' => 'Delhivery',
            'description' => 'Leading technology-enabled supply chain solutions provider with end-to-end domestic express parcel distribution service.',
            'logo_url' => 'https://example.com/logos/delhivery.png',
            'website' => 'https://www.delhivery.com',
            'contact_phone' => '+91-120-403-4030',
            'contact_email' => 'support@delhivery.com',
            'address' => 'Building No. 9, Sector 15, Part II, Mahipalpur, New Delhi - 110037',
            'coverage_areas' => ['India'],
            'service_types' => ['express', 'standard', 'eco', 'cold_chain'],
            'delivery_timeframes' => [
                'express' => '24h',
                'standard' => '48h',
                'economy' => '72h'
            ],
            'is_active' => true,
            'is_verified' => true,
            'rating' => 4.3,
            'on_time_delivery_rate' => 92.75,
            'success_rate' => 96.25,
            'insurance_coverage_available' => true,
            'insurance_max_value' => 5000000,
            'insurance_premium_rate' => 2.00, // 2% of package value
            'cold_chain_capabilities' => true,
            'fragile_handling' => true,
            'specialized_vehicle_fleet' => ['refrigerated_trucks', 'vans', 'bikes'],
            'real_time_tracking' => true,
            'customer_support_available' => true,
            'returns_management' => true,
            'pickup_services' => true,
            'same_day_delivery_available' => true,
            'next_day_delivery_available' => true,
            'international_shipping' => false,
            'warehousing_services' => true,
            'commission_rate' => 8.00, // 8% platform commission
            'preferred_partnership' => true,
            'driver_verification_required' => true,
            'driver_background_check' => true,
            'vehicle_insurance_required' => true,
            'driver_training_certified' => true,
            'carbon_neutral_shipping' => true,
            'green_fleet_percentage' => 15.00, // 15% of fleet is electric/eco-friendly
            'sustainability_certifications' => ['ISO_14001', 'Green_Business_Certified'],
            'special_handling_certifications' => ['Fragile_Goods', 'Cold_Chain', 'Oversized_Items']
        ]);

        \App\Models\CourierPartner::create([
            'name' => 'Blue Dart',
            'description' => 'Leading express air and integrated transportation and distribution company.',
            'logo_url' => 'https://example.com/logos/bluedart.png',
            'website' => 'https://www.bluedart.com',
            'contact_phone' => '+91-22-4099-4099',
            'contact_email' => 'customersupport@bluedart.com',
            'address' => 'Corporate Office, Blue Dart Express Ltd, Plot No. 7-A, TTC Industrial Area, MIDC, Turbhe, Navi Mumbai - 400705',
            'coverage_areas' => ['India'],
            'service_types' => ['express', 'standard'],
            'delivery_timeframes' => [
                'express' => '24h',
                'standard' => '48h',
                'economy' => '72h'
            ],
            'is_active' => true,
            'is_verified' => true,
            'rating' => 4.5,
            'on_time_delivery_rate' => 94.50,
            'success_rate' => 97.00,
            'insurance_coverage_available' => true,
            'insurance_max_value' => 10000000,
            'insurance_premium_rate' => 1.50, // 1.5% of package value
            'cold_chain_capabilities' => false,
            'fragile_handling' => true,
            'specialized_vehicle_fleet' => ['vans', 'trucks', 'air_service'],
            'real_time_tracking' => true,
            'customer_support_available' => true,
            'returns_management' => true,
            'pickup_services' => true,
            'same_day_delivery_available' => false,
            'next_day_delivery_available' => true,
            'international_shipping' => true,
            'warehousing_services' => false,
            'commission_rate' => 10.00, // 10% platform commission
            'preferred_partnership' => true,
            'driver_verification_required' => true,
            'driver_background_check' => true,
            'vehicle_insurance_required' => true,
            'driver_training_certified' => true,
            'carbon_neutral_shipping' => false,
            'green_fleet_percentage' => 5.00, // 5% of fleet is eco-friendly
            'sustainability_certifications' => ['ISO_14001'],
            'special_handling_certifications' => ['Fragile_Goods', 'Premium_Services']
        ]);

        \App\Models\CourierPartner::create([
            'name' => 'FedEx Express India',
            'description' => 'Global express transportation company offering fast and reliable delivery services.',
            'logo_url' => 'https://example.com/logos/fedex.png',
            'website' => 'https://www.fedex.com',
            'contact_phone' => '+91-120-435-7000',
            'contact_email' => 'customerservice@fedex.com',
            'address' => 'Plot No. 8, Sector 126, Noida - 201304',
            'coverage_areas' => ['India', 'International'],
            'service_types' => ['express', 'international', 'premium'],
            'delivery_timeframes' => [
                'express' => '24h',
                'international' => '3-5d',
                'premium' => '12h'
            ],
            'is_active' => true,
            'is_verified' => true,
            'rating' => 4.4,
            'on_time_delivery_rate' => 96.75,
            'success_rate' => 98.50,
            'insurance_coverage_available' => true,
            'insurance_max_value' => 50000000,
            'insurance_premium_rate' => 3.00, // 3% of package value
            'cold_chain_capabilities' => true,
            'fragile_handling' => true,
            'specialized_vehicle_fleet' => ['refrigerated_trucks', 'vans', 'air_service', 'international_air'],
            'real_time_tracking' => true,
            'customer_support_available' => true,
            'returns_management' => true,
            'pickup_services' => true,
            'same_day_delivery_available' => true,
            'next_day_delivery_available' => true,
            'international_shipping' => true,
            'warehousing_services' => true,
            'commission_rate' => 12.00, // 12% platform commission (premium partner)
            'preferred_partnership' => false,
            'driver_verification_required' => true,
            'driver_background_check' => true,
            'vehicle_insurance_required' => true,
            'driver_training_certified' => true,
            'carbon_neutral_shipping' => true,
            'green_fleet_percentage' => 20.00, // 20% of fleet is eco-friendly
            'sustainability_certifications' => ['CarbonNeutral', 'ISO_14001'],
            'special_handling_certifications' => ['Fragile_Goods', 'Cold_Chain', 'International', 'Premium_Services']
        ]);

        \App\Models\CourierPartner::create([
            'name' => 'Amazon Logistics',
            'description' => 'Amazon\'s delivery arm providing fast and reliable last-mile delivery services.',
            'logo_url' => 'https://example.com/logos/amazon-delivery.png',
            'website' => 'https://www.amazon.in',
            'contact_phone' => '+91-120-306-6666',
            'contact_email' => 'help@amazon.in',
            'address' => 'Amazon Development Center India Pvt Ltd, Prestige Meridian, Tower-B, 12th Floor, Race Course Road, Bangalore - 560073',
            'coverage_areas' => ['India'],
            'service_types' => ['express', 'standard', 'same_day'],
            'delivery_timeframes' => [
                'express' => '24h',
                'standard' => '48-72h',
                'same_day' => 'same_day'
            ],
            'is_active' => true,
            'is_verified' => true,
            'rating' => 4.6,
            'on_time_delivery_rate' => 97.25,
            'success_rate' => 98.75,
            'insurance_coverage_available' => true,
            'insurance_max_value' => 25000000,
            'insurance_premium_rate' => 1.00, // 1% - lower due to integration with Amazon
            'cold_chain_capabilities' => true,
            'fragile_handling' => true,
            'specialized_vehicle_fleet' => ['refrigerated_vans', 'bikes', 'vans', 'electric_vehicles'],
            'real_time_tracking' => true,
            'customer_support_available' => true,
            'returns_management' => true,
            'pickup_services' => true,
            'same_day_delivery_available' => true,
            'next_day_delivery_available' => true,
            'international_shipping' => false,
            'warehousing_services' => true,
            'commission_rate' => 5.00, // 5% - as a preferred partner due to volume
            'preferred_partnership' => true,
            'driver_verification_required' => true,
            'driver_background_check' => true,
            'vehicle_insurance_required' => true,
            'driver_training_certified' => true,
            'carbon_neutral_shipping' => true,
            'green_fleet_percentage' => 30.00, // 30% of fleet is electric/eco-friendly
            'sustainability_certifications' => ['Shipment_Zero', 'Climate_Pledge', 'ISO_14001'],
            'special_handling_certifications' => ['Fragile_Goods', 'Cold_Chain', 'Prime_Delivery', 'Same_Day']
        ]);

        \App\Models\CourierPartner::create([
            'name' => 'Shadowfax',
            'description' => 'Hyperlocal express logistics company providing same-day delivery services.',
            'logo_url' => 'https://example.com/logos/shadowfax.png',
            'website' => 'https://www.shadowfax.in',
            'contact_phone' => '+91-80-3309-3309',
            'contact_email' => 'hello@shadowfax.in',
            'address' => 'No. 222, 1st Floor, 16th Main, 2nd Stage, BTM Layout, Bangalore - 560068',
            'coverage_areas' => ['India'],
            'service_types' => ['same_day', 'express', 'hyperlocal'],
            'delivery_timeframes' => [
                'same_day' => 'same_day',
                'express' => '24h',
                'hyperlocal' => '2-4h'
            ],
            'is_active' => true,
            'is_verified' => true,
            'rating' => 4.2,
            'on_time_delivery_rate' => 90.50,
            'success_rate' => 94.00,
            'insurance_coverage_available' => true,
            'insurance_max_value' => 2000000,
            'insurance_premium_rate' => 2.50, // 2.5% of package value
            'cold_chain_capabilities' => true,
            'fragile_handling' => true,
            'specialized_vehicle_fleet' => ['bikes', 'tuktuks', 'vans', 'refrigerated_bikes'],
            'real_time_tracking' => true,
            'customer_support_available' => true,
            'returns_management' => false,
            'pickup_services' => true,
            'same_day_delivery_available' => true,
            'next_day_delivery_available' => false,
            'international_shipping' => false,
            'warehousing_services' => false,
            'commission_rate' => 7.00, // 7% platform commission
            'preferred_partnership' => false,
            'driver_verification_required' => true,
            'driver_background_check' => false,
            'vehicle_insurance_required' => true,
            'driver_training_certified' => false,
            'carbon_neutral_shipping' => false,
            'green_fleet_percentage' => 2.00, // 2% of fleet is eco-friendly
            'sustainability_certifications' => [],
            'special_handling_certifications' => ['Fragile_Goods', 'Same_Day', 'Hyperlocal', 'Food_Delivery']
        ]);
    }
}
