<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Location;

class DefaultLocationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Add default delivery hubs and pickup points for major cities
        Location::create([
            'location_name' => 'Lagos Central Delivery Hub',
            'address_line1' => '123 Victoria Island Road',
            'address_line2' => 'Victoria Island',
            'city' => 'Lagos',
            'state' => 'Lagos',
            'country' => 'Nigeria',
            'postal_code' => '101233',
            'latitude' => 6.4474,
            'longitude' => 3.4059,
            'location_type' => 'delivery_hub',
            'is_active' => true,
            'is_primary' => false,
            'geofence_radius' => 500, // 500m radius around hub
            'delivery_zone' => 'lagos_central',
            'contact_person' => 'Hub Manager',
            'contact_phone' => '+234-8012345678',
            'location_metadata' => [
                'description' => 'Central delivery hub for Lagos metropolitan area',
                'operating_hours' => [
                    'monday' => ['open' => '07:00', 'close' => '22:00'],
                    'tuesday' => ['open' => '07:00', 'close' => '22:00'],
                    'wednesday' => ['open' => '07:00', 'close' => '22:00'],
                    'thursday' => ['open' => '07:00', 'close' => '22:00'],
                    'friday' => ['open' => '07:00', 'close' => '23:00'],
                    'saturday' => ['open' => '08:00', 'close' => '20:00'],
                    'sunday' => ['open' => '10:00', 'close' => '18:00']
                ],
                'delivery_availability' => [
                    'same_day' => true,
                    'next_day' => true,
                    'time_slots' => [
                        'morning' => ['start' => '08:00', 'end' => '12:00'],
                        'afternoon' => ['start' => '12:00', 'end' => '17:00'],
                        'evening' => ['start' => '17:00', 'end' => '21:00']
                    ]
                ],
            ],
            'cold_chain_supported' => true,
            'max_package_size' => ['length_cm' => 100, 'width_cm' => 100, 'height_cm' => 100],
            'max_package_weight' => 30.00,
            'special_handling_available' => ['fragile', 'temperature_controlled', 'oversized'],
            'warehouse_capacity' => ['capacity_sqm' => 5000, 'max_packages' => 2000],
        ]);

        Location::create([
            'location_name' => 'Abuja Pickup Point',
            'address_line1' => '45 Shehu Shagari Way',
            'address_line2' => 'Central Business District',
            'city' => 'Abuja',
            'state' => 'Federal Capital Territory',
            'country' => 'Nigeria',
            'postal_code' => '900211',
            'latitude' => 9.0765,
            'longitude' => 7.3986,
            'location_type' => 'pickup_point',
            'is_active' => true,
            'is_primary' => false,
            'geofence_radius' => 1000, // 1km radius around pickup point
            'delivery_zone' => 'abuja_cbd',
            'contact_person' => 'Pickup Coordinator',
            'contact_phone' => '+234-8012345679',
            'location_metadata' => [
                'description' => 'Main pickup point in Abuja CBD for customer collections',
                'operating_hours' => [
                    'monday' => ['open' => '08:00', 'close' => '20:00'],
                    'tuesday' => ['open' => '08:00', 'close' => '20:00'],
                    'wednesday' => ['open' => '08:00', 'close' => '20:00'],
                    'thursday' => ['open' => '08:00', 'close' => '20:00'],
                    'friday' => ['open' => '08:00', 'close' => '21:00'],
                    'saturday' => ['open' => '09:00', 'close' => '18:00'],
                    'sunday' => ['open' => '10:00', 'close' => '16:00']
                ],
            ],
            'cold_chain_supported' => false,
            'max_package_size' => ['length_cm' => 50, 'width_cm' => 50, 'height_cm' => 50],
            'max_package_weight' => 10.00,
            'special_handling_available' => ['fragile', 'document'],
        ]);

        Location::create([
            'location_name' => 'Port Harcourt Delivery Hub',
            'address_line1' => '78 Trans Amadi Industrial Layout',
            'address_line2' => 'Rivers State',
            'city' => 'Port Harcourt',
            'state' => 'Rivers',
            'country' => 'Nigeria',
            'postal_code' => '500211',
            'latitude' => 4.8000,
            'longitude' => 7.0000,
            'location_type' => 'delivery_hub',
            'is_active' => true,
            'is_primary' => false,
            'geofence_radius' => 300, // 300m radius around hub
            'delivery_zone' => 'port_harcourt',
            'contact_person' => 'Hub Supervisor',
            'contact_phone' => '+234-8012345680',
            'location_metadata' => [
                'description' => 'Delivery hub for Port Harcourt and surrounding areas',
                'operating_hours' => [
                    'monday' => ['open' => '07:00', 'close' => '21:00'],
                    'tuesday' => ['open' => '07:00', 'close' => '21:00'],
                    'wednesday' => ['open' => '07:00', 'close' => '21:00'],
                    'thursday' => ['open' => '07:00', 'close' => '21:00'],
                    'friday' => ['open' => '07:00', 'close' => '22:00'],
                    'saturday' => ['open' => '08:00', 'close' => '18:00'],
                    'sunday' => ['open' => '10:00', 'close' => '16:00']
                ],
                'delivery_availability' => [
                    'same_day' => false,
                    'next_day' => true,
                    'time_slots' => [
                        'morning' => ['start' => '09:00', 'end' => '13:00'],
                        'afternoon' => ['start' => '13:00', 'end' => '18:00']
                    ]
                ],
            ],
            'cold_chain_supported' => true,
            'max_package_size' => ['length_cm' => 80, 'width_cm' => 80, 'height_cm' => 80],
            'max_package_weight' => 25.00,
            'special_handling_available' => ['fragile', 'oversized'],
            'warehouse_capacity' => ['capacity_sqm' => 2500, 'max_packages' => 1500],
        ]);

        Location::create([
            'location_name' => 'Kano Distribution Center',
            'address_line1' => '23 Murtala Mohammed way',
            'address_line2' => 'Dawakin Tofa',
            'city' => 'Kano',
            'state' => 'Kano',
            'country' => 'Nigeria',
            'postal_code' => '700212',
            'latitude' => 12.0000,
            'longitude' => 7.7300,
            'location_type' => 'delivery_hub',
            'is_active' => true,
            'is_primary' => false,
            'geofence_radius' => 400, // 400m radius around hub
            'delivery_zone' => 'kano_north',
            'contact_person' => 'Distribution Manager',
            'contact_phone' => '+234-8012345681',
            'location_metadata' => [
                'description' => 'Northern region distribution center for Kano and surrounding states',
                'operating_hours' => [
                    'monday' => ['open' => '06:00', 'close' => '20:00'],
                    'tuesday' => ['open' => '06:00', 'close' => '20:00'],
                    'wednesday' => ['open' => '06:00', 'close' => '20:00'],
                    'thursday' => ['open' => '06:00', 'close' => '20:00'],
                    'friday' => ['open' => '06:00', 'close' => '18:00'],
                    'saturday' => ['open' => '08:00', 'close' => '16:00'],
                    'sunday' => ['open' => '10:00', 'close' => '14:00']
                ],
                'delivery_availability' => [
                    'same_day' => false,
                    'next_day' => true,
                    'time_slots' => [
                        'morning' => ['start' => '08:00', 'end' => '12:00'],
                        'afternoon' => ['start' => '12:00', 'end' => '17:00']
                    ]
                ],
            ],
            'cold_chain_supported' => true,
            'max_package_size' => ['length_cm' => 75, 'width_cm' => 75, 'height_cm' => 75],
            'max_package_weight' => 20.00,
            'special_handling_available' => ['fragile', 'oversized', 'temperature_controlled'],
            'warehouse_capacity' => ['capacity_sqm' => 3500, 'max_packages' => 1800],
        ]);

        Location::create([
            'location_name' => 'Ibadan Service Center',
            'address_line1' => 'University of Ibadan Campus',
            'address_line2' => 'Oyo State',
            'city' => 'Ibadan',
            'state' => 'Oyo',
            'country' => 'Nigeria',
            'postal_code' => '200282',
            'latitude' => 7.3775,
            'longitude' => 3.9470,
            'location_type' => 'service_center',
            'is_active' => true,
            'is_primary' => false,
            'geofence_radius' => 200,
            'delivery_zone' => 'ibadan_south',
            'contact_person' => 'Service Manager',
            'contact_phone' => '+234-8012345682',
            'location_metadata' => [
                'description' => 'Service center for Ibadan metropolitan and surrounding areas',
                'operating_hours' => [
                    'monday' => ['open' => '08:00', 'close' => '19:00'],
                    'tuesday' => ['open' => '08:00', 'close' => '19:00'],
                    'wednesday' => ['open' => '08:00', 'close' => '19:00'],
                    'thursday' => ['open' => '08:00', 'close' => '19:00'],
                    'friday' => ['open' => '08:00', 'close' => '20:00'],
                    'saturday' => ['open' => '09:00', 'close' => '15:00'],
                    'sunday' => ['open' => '10:00', 'close' => '14:00']
                ],
                'delivery_availability' => [
                    'same_day' => false,
                    'next_day' => true,
                    'time_slots' => [
                        'morning' => ['start' => '09:00', 'end' => '14:00'],
                        'afternoon' => ['start' => '14:00', 'end' => '18:00']
                    ]
                ],
            ],
            'cold_chain_supported' => false,
            'max_package_size' => ['length_cm' => 60, 'width_cm' => 60, 'height_cm' => 60],
            'max_package_weight' => 15.00,
            'special_handling_available' => ['document', 'small_packages'],
        ]);
    }
}