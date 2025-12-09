<?php

namespace App\Services;

use App\Models\Location;
use App\Models\DeliveryOrder;
use App\Models\CourierPartner;
use App\Models\Ad;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class LocationService
{
    /**
     * Calculate optimal delivery route between pickup and delivery points
     */
    public function calculateOptimalRoute($pickupLocation, $deliveryLocation, $additionalStops = [])
    {
        // This would connect to routing services like Google Maps, Mapbox, or OpenStreetMap in production
        // For simulation, we'll calculate approximate distances and suggest optimal route
        
        $distance = $this->calculateDistanceBetweenLocations($pickupLocation, $deliveryLocation);
        
        // If we have additional stops, calculate the best route (using basic approximation)
        $routeStops = [$pickupLocation, $deliveryLocation];
        if (!empty($additionalStops)) {
            $routeStops = array_merge([$pickupLocation], $additionalStops, [$deliveryLocation]);
        }
        
        // Calculate total route metrics
        $totalDistance = $this->calculateTotalRouteDistance($routeStops);
        $estimatedTime = $totalDistance * 2; // 2 minutes per km as approximation
        $fuelEstimate = $totalDistance * 0.1; // 0.1 liters per km as approximation
        
        // For real implementation, this would use a routing algorithm like Dijkstra or A*
        return [
            'route_legs' => $this->generateRouteLegs($routeStops),
            'total_distance_km' => round($totalDistance, 2),
            'estimated_time_minutes' => round($estimatedTime),
            'fuel_consumption_liters' => round($fuelEstimate, 2),
            'emission_estimate_kg' => round($totalDistance * 0.002, 2), // 0.002 kg CO2 per km
            'optimal_sequence' => $this->calculateOptimalSequence($routeStops),
            'routing_algorithm' => 'simulated_optimization', // In production, would be the actual algorithm used
            'traffic_considered' => false, // In production, real traffic data would be considered
            'route_coordinates' => $this->generateRouteCoordinates($routeStops),
        ];
    }

    /**
     * Generate indoor mapping for large marketplaces
     */
    public function generateIndoorMapping($locationId, $floorIndex = 0)
    {
        $location = Location::findOrFail($locationId);
        
        if (!$location->indoor_map_data) {
            // If no indoor map data exists, generate default layout
            return [
                'location_id' => $location->id,
                'location_name' => $location->location_name,
                'has_indoor_mapping' => false,
                'message' => 'No indoor mapping data available for this location',
                'suggestion' => 'Contact administrator to set up indoor mapping'
            ];
        }
        
        $indoorMap = $location->indoor_map_data;
        $floorData = $indoorMap['floors'][$floorIndex] ?? $indoorMap['floors'][0] ?? [];
        
        return [
            'location_id' => $location->id,
            'location_name' => $location->location_name,
            'floor_index' => $floorIndex,
            'floor_name' => $floorData['name'] ?? "Floor {$floorIndex}",
            'map_data' => $floorData['layout'] ?? [],
            'departments' => $floorData['departments'] ?? [],
            'aisles' => $floorData['aisles'] ?? [],
            'special_zones' => $location->special_zones ?? [],
            'coordinates_system' => $indoorMap['coordinates_system'] ?? 'cartesian_2d',
            'scale' => $indoorMap['scale'] ?? 1, // meters per unit
            'entry_points' => $floorData['entry_points'] ?? [],
            'exit_points' => $floorData['exit_points'] ?? [],
            'elevators' => $floorData['elevators'] ?? [],
            'escalators' => $floorData['escalators'] ?? [],
            'stairs' => $floorData['stairs'] ?? [],
            'restrooms' => $floorData['restrooms'] ?? [],
            'information_desks' => $floorData['information_desks'] ?? [],
            'security_desks' => $floorData['security_desks'] ?? [],
        ];
    }

    /**
     * Get real-time delivery tracking information
     */
    public function getRealTimeDeliveryTracking($orderId)
    {
        $order = DeliveryOrder::findOrFail($orderId);
        
        // In a real implementation, this would connect to GPS tracking systems
        // For simulation, we'll generate realistic tracking data
        
        $progress = $this->calculateDeliveryProgress($order);
        
        return [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'status' => $order->delivery_status,
            'progress_percent' => $progress['percentage'],
            'estimated_arrival_time' => $order->eta_timestamp,
            'current_location' => [
                'latitude' => $order->current_location_latitude ?? $order->delivery_address['latitude'] ?? null,
                'longitude' => $order->current_location_longitude ?? $order->delivery_address['longitude'] ?? null,
                'location_name' => $progress['current_stage'],
            ],
            'route_info' => [
                'total_distance' => $order->delivery_distance_km,
                'distance_remaining' => max(0, $order->delivery_distance_km - $progress['distance_travelled']),
                'estimated_time_remaining' => $this->estimateTimeRemaining($order->delivery_distance_km, $progress['percentage']),
            ],
            'courier_info' => [
                'name' => $order->driver_name ?? $order->courierPartner->name ?? 'Unknown',
                'phone' => $order->driver_phone ?? $order->courierPartner->contact_phone ?? 'N/A',
                'rating' => $order->driver_rating ?? $order->courierPartner->rating ?? 0,
                'vehicle_info' => $order->driver_vehicle_info ?? null,
            ],
            'delivery_timeline' => [
                'placed_at' => $order->created_at,
                'accepted_at' => $order->assigned_at ?? null,
                'picked_up_at' => $order->picked_up_at ?? null,
                'out_for_delivery_at' => $order->out_for_delivery_at ?? null,
                'estimated_completion' => $order->eta_timestamp,
            ],
            'tracking_updates' => $this->getRecentTrackingUpdates($order),
        ];
    }

    /**
     * Calculate delivery progress based on time elapsed
     */
    private function calculateDeliveryProgress($order)
    {
        $now = now();
        $placedAt = $order->created_at;
        $totalEstimatedTime = $order->delivery_distance_km * 2; // 2 minutes per km
        
        $timeElapsed = $placedAt->diffInMinutes($now);
        
        // Calculate approximate progress based on time
        $progressPercentage = min(100, ($timeElapsed / $totalEstimatedTime) * 100);
        
        if ($progressPercentage < 10) {
            $currentStage = 'Order placed - Preparing for pickup';
        } elseif ($progressPercentage < 30) {
            $currentStage = 'Courier en route to pickup location';
        } elseif ($progressPercentage < 60) {
            $currentStage = 'Package picked up - In transit';
        } elseif ($progressPercentage < 90) {
            $currentStage = 'Out for delivery';
        } else {
            $currentStage = 'Close to destination - Almost there!';
        }
        
        return [
            'percentage' => round($progressPercentage),
            'current_stage' => $currentStage,
            'distance_travelled' => ($progressPercentage / 100) * $order->delivery_distance_km,
        ];
    }

    /**
     * Estimate time remaining for delivery
     */
    private function estimateTimeRemaining($totalDistance, $progressPercentage)
    {
        $remainingPercent = 100 - $progressPercentage;
        $remainingDistance = ($remainingPercent / 100) * $totalDistance;
        
        // 2 minutes per km remaining
        return round($remainingDistance * 2);
    }

    /**
     * Get recent tracking updates
     */
    private function getRecentTrackingUpdates($order)
    {
        // Simulate tracking updates based on delivery timeline
        $updates = [];
        
        if ($order->created_at) {
            $updates[] = [
                'timestamp' => $order->created_at,
                'status' => 'Order Placed',
                'location' => 'System',
                'description' => 'Order has been placed and is being processed',
            ];
        }
        
        if ($order->assigned_at) {
            $updates[] = [
                'timestamp' => $order->assigned_at,
                'status' => 'Assigned to Courier',
                'location' => 'Dispatch Center',
                'description' => 'Courier has been assigned to your order',
            ];
        }
        
        if ($order->picked_up_at) {
            $updates[] = [
                'timestamp' => $order->picked_up_at,
                'status' => 'Package Picked Up',
                'location' => 'Origin Location',
                'description' => 'Your package has been picked up and is in transit',
            ];
        }
        
        if ($order->out_for_delivery_at) {
            $updates[] = [
                'timestamp' => $order->out_for_delivery_at,
                'status' => 'Out for Delivery',
                'location' => 'Neighborhood',
                'description' => 'Your package is out for delivery',
            ];
        }
        
        if ($order->eta_timestamp) {
            $updates[] = [
                'timestamp' => $order->eta_timestamp,
                'status' => 'Estimated Arrival',
                'location' => 'Destination',
                'description' => 'Estimated arrival time at destination',
            ];
        }
        
        return $updates;
    }

    /**
     * Calculate distance between two locations using Haversine formula
     */
    private function calculateDistanceBetweenLocations($loc1, $loc2)
    {
        $lat1 = $loc1 instanceof Location ? $loc1->latitude : $loc1['latitude'];
        $lon1 = $loc1 instanceof Location ? $loc1->longitude : $loc1['longitude'];
        $lat2 = $loc2 instanceof Location ? $loc2->latitude : $loc2['latitude'];
        $lon2 = $loc2 instanceof Location ? $loc2->longitude : $loc2['longitude'];
        
        $earthRadius = 6371; // Earth radius in kilometers
        
        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lon2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        
        return $angle * $earthRadius;
    }

    /**
     * Calculate total distance for a route with multiple stops
     */
    private function calculateTotalRouteDistance($stops)
    {
        $totalDistance = 0;
        
        for ($i = 0; $i < count($stops) - 1; $i++) {
            $totalDistance += $this->calculateDistanceBetweenLocations($stops[$i], $stops[$i + 1]);
        }
        
        return $totalDistance;
    }

    /**
     * Generate route legs for navigation
     */
    private function generateRouteLegs($stops)
    {
        $legs = [];
        
        for ($i = 0; $i < count($stops) - 1; $i++) {
            $from = $stops[$i];
            $to = $stops[$i + 1];
            
            $distance = $this->calculateDistanceBetweenLocations($from, $to);
            
            $legs[] = [
                'from' => $from instanceof Location ? $from->location_name : ($from['name'] ?? 'Unknown'),
                'to' => $to instanceof Location ? $to->location_name : ($to['name'] ?? 'Unknown'),
                'distance_km' => round($distance, 2),
                'estimated_time_minutes' => round($distance * 2), // Approximation
            ];
        }
        
        return $legs;
    }

    /**
     * Calculate optimal sequence using nearest neighbor heuristic (simplified)
     */
    private function calculateOptimalSequence($stops)
    {
        if (count($stops) <= 2) return $stops;
        
        // Start from first stop (pickup location)
        $sequence = [$stops[0]];
        $remaining = array_slice($stops, 1);
        
        while (!empty($remaining)) {
            $lastStop = end($sequence);
            $closestStop = null;
            $shortestDistance = PHP_FLOAT_MAX;
            
            foreach ($remaining as $stop) {
                $distance = $this->calculateDistanceBetweenLocations($lastStop, $stop);
                if ($distance < $shortestDistance) {
                    $shortestDistance = $distance;
                    $closestStop = $stop;
                }
            }
            
            if ($closestStop) {
                $sequence[] = $closestStop;
                $remaining = array_filter($remaining, function($stop) use ($closestStop) {
                    return !($stop === $closestStop || 
                           ($stop instanceof Location && $closestStop instanceof Location && $stop->id === $closestStop->id) ||
                           (is_array($stop) && is_array($closestStop) && ($stop['id'] ?? null) === ($closestStop['id'] ?? null));
                });
            }
        }
        
        return $sequence;
    }

    /**
     * Generate route coordinates (simulated)
     */
    private function generateRouteCoordinates($stops)
    {
        $coordinates = [];
        
        for ($i = 0; $i < count($stops); $i++) {
            $stop = $stops[$i];
            $coordinates[] = [
                'latitude' => $stop instanceof Location ? $stop->latitude : $stop['latitude'],
                'longitude' => $stop instanceof Location ? $stop->longitude : $stop['longitude'],
                'name' => $stop instanceof Location ? $stop->location_name : ($stop['name'] ?? "Stop {$i}"),
                'stop_number' => $i + 1,
                'is_pickup' => $i === 0,
                'is_dropoff' => $i === count($stops) - 1,
            ];
        }
        
        return $coordinates;
    }

    /**
     * Get nearby pickup points for a location
     */
    public function getNearbyPickupPoints($latitude, $longitude, $radiusKm = 10, $limit = 10)
    {
        $pickupPoints = Location::where('location_type', 'pickup_point')
                               ->where('is_active', true)
                               ->nearby($latitude, $longitude, $radiusKm)
                               ->take($limit)
                               ->get();

        $pointsWithDistance = [];
        foreach ($pickupPoints as $point) {
            $distance = $this->calculateDistanceBetweenLocations(
                ['latitude' => $latitude, 'longitude' => $longitude], 
                $point
            );
            
            $pointsWithDistance[] = [
                'id' => $point->id,
                'name' => $point->location_name,
                'address' => $point->address_line1 . ', ' . $point->city . ', ' . $point->state . ', ' . $point->country,
                'distance_km' => round($distance, 2),
                'coordinates' => [
                    'latitude' => $point->latitude,
                    'longitude' => $point->longitude,
                ],
                'opening_hours' => $point->operating_hours,
                'contact_info' => [
                    'phone' => $point->contact_phone,
                    'email' => $point->contact_email,
                ],
                'features' => [
                    'contactless_pickup' => $point->contactless_pickup_available,
                    'qr_scan_available' => $point->qr_code_enabled,
                    'beacon_enabled' => $point->beacon_enabled,
                    'special_instructions' => $point->delivery_instructions,
                ],
            ];
        }
        
        // Sort by distance
        usort($pointsWithDistance, function($a, $b) {
            return $a['distance_km'] <=> $b['distance_km'];
        });

        return $pointsWithDistance;
    }

    /**
     * Get local courier marketplace for a region
     */
    public function getLocalCourierMarketplace($latitude, $longitude, $radiusKm = 25)
    {
        $couriers = CourierPartner::where('is_active', true)
                                 ->get();

        $eligibleCouriers = [];
        foreach ($couriers as $courier) {
            // Check if courier serves this area based on coverage_areas
            if ($this->courierServesArea($courier, $latitude, $longitude)) {
                $distance = $this->calculateCourierDistance($courier, $latitude, $longitude);
                
                $eligibleCouriers[] = [
                    'id' => $courier->id,
                    'name' => $courier->name,
                    'rating' => $courier->rating,
                    'on_time_delivery_rate' => $courier->on_time_delivery_rate,
                    'success_rate' => $courier->success_rate,
                    'distance_from_request' => round($distance, 2),
                    'coverage_areas' => $courier->coverage_areas,
                    'service_types' => $courier->service_types,
                    'delivery_timeframes' => $courier->delivery_timeframes,
                    'pricing_tiers' => $courier->pricing_tiers,
                    'is_same_day_available' => $courier->same_day_delivery_available,
                    'cold_chain_capable' => $courier->cold_chain_capabilities,
                    'fragile_handling' => $courier->fragile_handling,
                    'specialized_vehicles' => $courier->specialized_vehicle_fleet,
                    'features' => [
                        'real_time_tracking' => $courier->real_time_tracking,
                        'customer_support' => $courier->customer_support_available,
                        'returns_management' => $courier->returns_management,
                        'pickup_services' => $courier->pickup_services,
                    ],
                    'commission_rate' => $courier->commission_rate,
                    'estimated_cost' => $this->estimateCourierCost($courier, $distance),
                ];
            }
        }

        // Sort by rating and then by distance
        usort($eligibleCouriers, function($a, $b) {
            if ($b['rating'] != $a['rating']) {
                return $b['rating'] <=> $a['rating'];
            }
            return $a['distance_from_request'] <=> $b['distance_from_request'];
        });

        return $eligibleCouriers;
    }

    /**
     * Check if courier serves a specific area
     */
    private function courierServesArea($courier, $latitude, $longitude)
    {
        // For now, just check if they have coverage areas or if they're globally available
        if (empty($courier->coverage_areas)) {
            return true; // If no specific coverage is set, assume they serve everywhere
        }
        
        // In a real implementation, this would use geofencing to match areas
        // For simulation, we'll just return true
        return true;
    }

    /**
     * Estimate distance to courier's main hub/depot
     */
    private function calculateCourierDistance($courier, $latitude, $longitude)
    {
        // In a real implementation, this would calculate distance to courier's nearest depot
        // For simulation, return a random distance based on their coverage
        return mt_rand(1, 50); // Random distance between 1-50 km
    }

    /**
     * Estimate courier cost based on distance and other factors
     */
    private function estimateCourierCost($courier, $distance)
    {
        // Base calculation based on distance and pricing tiers
        $baseCost = 100; // Base cost in naira
        $distanceCost = $distance * 10; // 10 naira per km
        
        // Apply pricing from the courier's pricing tiers
        $pricingTiers = $courier->pricing_tiers;
        $weightTiers = $pricingTiers['weight'] ?? [];
        $distanceTiers = $pricingTiers['distance'] ?? [];
        
        $tierCost = 0;
        
        // Find applicable distance tier
        foreach ($distanceTiers as $tier) {
            if (($tier['min'] === 0 || $distance >= $tier['min']) && ($tier['max'] === null || $distance <= $tier['max'])) {
                $tierCost += $tier['rate'];
                break;
            }
        }
        
        return $baseCost + $distanceCost + $tierCost;
    }

    /**
     * Check if same-day delivery is available for a location
     */
    public function isSameDayDeliveryAvailable($latitude, $longitude)
    {
        // Same-day delivery is available in major cities
        // This is a simplified implementation based on Nigerian major cities
        $majorCitiesCoordinates = [
            ['name' => 'Lagos', 'lat' => 6.5244, 'lng' => 3.3792],
            ['name' => 'Abuja', 'lat' => 9.0820, 'lng' => 7.5091],
            ['name' => 'Port Harcourt', 'lat' => 4.8036, 'lng' => 7.0384],
            ['name' => 'Kano', 'lat' => 12.0000, 'lng' => 7.7300],
            ['name' => 'Ibadan', 'lat' => 7.3775, 'lng' => 3.8960],
        ];
        
        foreach ($majorCitiesCoordinates as $city) {
            $distance = $this->calculateDistanceBetweenLocations(
                ['latitude' => $latitude, 'longitude' => $longitude],
                ['latitude' => $city['lat'], 'longitude' => $city['lng']]
            );
            
            if ($distance <= 50) { // Within 50km of major city
                return [
                    'available' => true,
                    'city' => $city['name'],
                    'distance_to_city' => round($distance, 2),
                ];
            }
        }
        
        return [
            'available' => false,
            'message' => 'Same-day delivery available in major cities of Nigeria within 50km radius',
        ];
    }

    /**
     * Generate cold chain delivery configuration
     */
    public function generateColdChainConfig($itemType, $quantity, $deliveryOptions = [])
    {
        // Determine cold chain requirements based on item type
        $isRequired = in_array($itemType, ['food', 'medical', 'pharmaceutical', 'perishable', 'dairy', 'frozen']);
        
        if (!$isRequired) {
            return [
                'cold_chain_required' => false,
                'temperature_range' => null,
                'equipment_required' => null,
                'cost_multiplier' => 1.0,
                'delivery_time_constraint' => null,
            ];
        }
        
        // Define cold chain configuration based on item type
        $config = [
            'cold_chain_required' => true,
            'item_type' => $itemType,
            'quantity' => $quantity,
        ];
        
        switch ($itemType) {
            case 'food':
            case 'perishable':
                $config['temperature_range'] = ['2°C', '8°C'];
                $config['equipment_required'] = ['refrigerated_vehicle', 'thermal_packaging', 'temperature_monitoring'];
                $config['cost_multiplier'] = 1.5; // 50% increase for cold chain
                $config['delivery_time_constraint'] = 'within_2_hours';
                $config['handling_requirements'] = ['keep_cold', 'avoid_temperature_fluctuations', 'handle_with_care'];
                break;
                
            case 'frozen':
                $config['temperature_range'] = ['-18°C', '-20°C'];
                $config['equipment_required'] = ['deep_freeze_vehicles', 'frozen_packaging', 'continuous_monitoring'];
                $config['cost_multiplier'] = 1.8;
                $config['delivery_time_constraint'] = 'within_1_hour';
                $config['handling_requirements'] = ['maintain_freezing_temp', 'no_thawing_periods', 'urgent_handling'];
                break;
                
            case 'medical':
            case 'pharmaceutical':
                $config['temperature_range'] = ['2°C', '8°C'];
                $config['equipment_required'] = ['specialized_medical_transport', 'temperature_logging', 'sealed_containers'];
                $config['cost_multiplier'] = 2.0; // Double for medical items
                $config['delivery_time_constraint'] = 'within_1_hour';
                $config['handling_requirements'] = ['sterile_environment', 'temperature_logging', 'direct_handling_only'];
                break;
                
            case 'dairy':
                $config['temperature_range'] = ['2°C', '4°C'];
                $config['equipment_required'] = ['refrigerated_vehicle', 'dairy_packaging', 'temperature_monitoring'];
                $config['cost_multiplier'] = 1.4;
                $config['delivery_time_constraint'] = 'within_2_hours';
                $config['handling_requirements'] = ['maintain_cold_chain', 'handle_gently', 'inspect_packing'];
                break;
                
            default:
                $config['temperature_range'] = ['2°C', '8°C'];
                $config['equipment_required'] = ['refrigerated_vehicle', 'temperature_monitoring'];
                $config['cost_multiplier'] = 1.5;
                $config['delivery_time_constraint'] = 'within_2_hours';
                $config['handling_requirements'] = ['keep_cold', 'handle_with_care'];
        }
        
        // Adjust for delivery options
        if ($deliveryOptions['express'] ?? false) {
            $config['cost_multiplier'] *= 1.3;
            $config['delivery_time_constraint'] = 'within_1_hour';
        }
        
        if ($deliveryOptions['scheduled'] ?? false) {
            $config['cost_multiplier'] *= 0.9; // Slightly cheaper for scheduled delivery
        }
        
        return $config;
    }

    /**
     * Calculate international shipping cost
     */
    public function calculateInternationalShipping($destinationCountry, $weightKg, $dimensions, $itemValue, $shippingClass = 'standard')
    {
        // This would connect to international shipping providers in production
        // For simulation, we'll use a basic calculation model
        
        $destinationFactors = [
            'United States' => 1.8, // High factor due to distance
            'United Kingdom' => 1.5,
            'Canada' => 1.7,
            'Germany' => 1.4,
            'France' => 1.4,
            'South Africa' => 1.2, // Closest international destination
            'Ghana' => 1.1, // Neighboring West African country
            'Kenya' => 1.0, // African destination
            'India' => 1.3,
            'China' => 1.6,
        ];
        
        $classFactors = [
            'standard' => 1.0,
            'express' => 2.0,
            'economy' => 0.8,
            'priority' => 1.5,
        ];
        
        $distanceFactor = $destinationFactors[$destinationCountry] ?? 1.5;
        $classFactor = $classFactors[$shippingClass] ?? 1.0;
        
        // Base calculation: weight + distance + value + class
        $baseCost = 2000; // Base cost in naira
        $weightCost = $weightKg * 500; // 500 naira per kg
        $sizeMultiplier = (isset($dimensions['volume']) ? $dimensions['volume'] : ($dimensions['length'] * $dimensions['width'] * $dimensions['height'] / 1000000)) * 100; // Volume multiplier
        $valueFactor = max(1, $itemValue * 0.02); // Insurance for valuable items
        
        $totalCost = ($baseCost + $weightCost + ($sizeMultiplier * 100) + $valueFactor) * $distanceFactor * $classFactor;
        
        return [
            'destination_country' => $destinationCountry,
            'weight_kg' => $weightKg,
            'dimensions' => $dimensions,
            'item_value' => $itemValue,
            'shipping_class' => $shippingClass,
            'estimated_cost_ngn' => round($totalCost),
            'estimated_cost_usd' => round($totalCost * 0.0007, 2), // Exchange rate approximation
            'estimated_delivery_time' => $this->getEstDeliveryTime($destinationCountry, $shippingClass),
            'shipping_options' => [
                'tracking_available' => true,
                'insurance_included' => $itemValue > 50000,
                'express_delivery' => $shippingClass === 'express',
                'customs_processing' => true,
            ],
            'customs_info' => [
                'documents_required' => ['invoice', 'packing_list', 'certificate_of_origin'],
                'possible_duties' => $itemValue * 0.15, // 15% average duty
                'clearance_time' => '1-3 days',
            ],
        ];
    }

    /**
     * Get estimated delivery time based on destination and class
     */
    private function getEstDeliveryTime($country, $class)
    {
        $baseDays = [
            'South Africa' => 5,
            'Ghana' => 3,
            'Kenya' => 4,
            'United Kingdom' => 7,
            'United States' => 10,
            'Canada' => 10,
            'Germany' => 7,
            'France' => 7,
            'India' => 8,
            'China' => 12,
        ];
        
        $days = $baseDays[$country] ?? 7; // Default 7 days if country not found
        
        switch ($class) {
            case 'express':
                return $days - 2 . ' days';
            case 'economy':
                return ($days + 2) . ' days';
            case 'priority':
                return ($days - 1) . ' days';
            default:
                return $days . ' days';
        }
    }

    /**
     * Get all available pickup points
     */
    public function getAllPickupPoints($filters = [])
    {
        $query = Location::where('location_type', 'pickup_point')
                        ->where('is_active', true);

        if (isset($filters['city'])) {
            $query = $query->where('city', $filters['city']);
        }

        if (isset($filters['state'])) {
            $query = $query->where('state', $filters['state']);
        }

        if (isset($filters['delivery_zone'])) {
            $query = $query->where('delivery_zone', $filters['delivery_zone']);
        }

        if (isset($filters['features'])) {
            foreach ($filters['features'] as $feature) {
                if ($feature === 'contactless') {
                    $query = $query->where('contactless_pickup_available', true);
                } elseif ($feature === 'qr_scanning') {
                    $query = $query->where('qr_code_enabled', true);
                }
            }
        }

        return $query->orderBy('delivery_zone')
                    ->orderBy('location_name')
                    ->get();
    }

    /**
     * Get delivery availability for a location
     */
    public function getLocationDeliveryAvailability($locationId)
    {
        $location = Location::findOrFail($locationId);

        return [
            'location_id' => $location->id,
            'location_name' => $location->location_name,
            'delivery_availability' => $location->delivery_availability,
            'cold_chain_supported' => $location->cold_chain_supported,
            'same_day_delivery' => $location->delivery_availability['same_day'] ?? false,
            'next_day_delivery' => $location->delivery_availability['next_day'] ?? true,
            'operating_hours' => $location->operating_hours,
            'contact_info' => [
                'phone' => $location->contact_phone,
                'email' => $location->contact_email,
            ],
            'special_features' => [
                'contactless_pickup' => $location->contactless_pickup_available,
                'qr_code_scanning' => $location->qr_code_enabled,
                'appointment_required' => $location->appointment_required,
            ],
        ];
    }
}