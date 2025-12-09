<?php

namespace App\Services;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

class GreenDeliveryOptionsService
{
    /**
     * Green delivery methods and their environmental impact
     */
    private array $deliveryMethods = [
        'bike_delivery' => [
            'name' => 'Bicycle Delivery',
            'description' => 'Zero-emission delivery using bicycle couriers',
            'carbon_footprint_per_km' => 0.0, // Zero emissions
            'speed' => 'fast', // Usually within city centers
            'range' => 'short', // Up to 15 km
            'cost_multiplier' => 1.2, // Might be slightly more expensive
            'availability' => ['urban', 'dense_suburban'],
            'vehicle_type' => 'bicycle',
            'suitable_items' => ['small_packages', 'documents', 'food'],
        ],
        'electric_vehicle' => [
            'name' => 'Electric Vehicle Delivery',
            'description' => 'Low-emission delivery using electric vehicles',
            'carbon_footprint_per_km' => 0.05, // Very low emissions (0.05 kg CO2/km)
            'speed' => 'medium', // Depending on traffic
            'range' => 'medium', // Up to 150 km
            'cost_multiplier' => 1.1, // Slightly more expensive
            'availability' => ['urban', 'suburban'],
            'vehicle_type' => 'electric_van',
            'suitable_items' => ['small_medium_packages', 'groceries'],
        ],
        'hybrid_vehicle' => [
            'name' => 'Hybrid Vehicle Delivery',
            'description' => 'Lower-emission delivery using hybrid vehicles',
            'carbon_footprint_per_km' => 0.12, // Lower than conventional (0.12 kg CO2/km)
            'speed' => 'medium', // Similar to conventional
            'range' => 'medium', // Up to 200 km
            'cost_multiplier' => 1.0, // Similar to conventional
            'availability' => ['urban', 'suburban', 'rural'],
            'vehicle_type' => 'hybrid_van',
            'suitable_items' => ['medium_large_packages'],
        ],
        'public_transport_integration' => [
            'name' => 'Public Transport Integration',
            'description' => 'Using public transportation networks for delivery',
            'carbon_footprint_per_km' => 0.08, // Lower than private vehicle
            'speed' => 'slow', // Depends on public transport schedules
            'range' => 'variable', // Depends on route availability
            'cost_multiplier' => 0.9, // Often cheaper
            'availability' => ['urban'],
            'vehicle_type' => 'mixed_public_private',
            'suitable_items' => ['documents', 'small_packages'],
        ],
        'drone_delivery' => [
            'name' => 'Drone Delivery',
            'description' => 'Ultra-fast delivery using electric drones',
            'carbon_footprint_per_km' => 0.02, // Very low, all electric
            'speed' => 'very_fast', // Direct point-to-point
            'range' => 'short', // Up to 25 km depending on model
            'cost_multiplier' => 2.0, // Currently more expensive
            'availability' => ['urban_peripheral', 'rural'],
            'vehicle_type' => 'drone',
            'suitable_items' => ['small_packages_under_5kg'],
        ],
        'carbon_offset_delivery' => [
            'name' => 'Carbon Offset Delivery',
            'description' => 'Conventional delivery with carbon offset investment',
            'carbon_footprint_per_km' => 0.2, // Same as conventional, but offset
            'speed' => 'medium', // Same as conventional
            'range' => 'long', // All ranges possible
            'cost_multiplier' => 1.3, // Extra cost for offset
            'availability' => ['all'],
            'vehicle_type' => 'conventional',
            'suitable_items' => ['all'],
            'offset_program' => 'verified_carbon_standard',
        ],
        'crowdsourced_green' => [
            'name' => 'Crowdsourced Green Delivery',
            'description' => 'Delivery by individuals using walk/bike/public transport',
            'carbon_footprint_per_km' => 0.05, // Low, uses existing trips
            'speed' => 'variable', // Depends on volunteer availability
            'range' => 'medium', // Up to 100 km
            'cost_multiplier' => 0.8, // Often cheaper
            'availability' => ['urban', 'suburban'],
            'vehicle_type' => 'crowdsourced',
            'suitable_items' => ['small_packages', 'documents'],
        ],
        'consolidated_delivery' => [
            'name' => 'Consolidated Delivery',
            'description' => 'Multiple orders delivered together to reduce trips',
            'carbon_footprint_per_item' => 0.1, // Reduced per item due to consolidation
            'speed' => 'medium', // May have scheduled windows
            'range' => 'long', // All ranges possible
            'cost_multiplier' => 0.95, // Slightly cheaper due to efficiency
            'availability' => ['all'],
            'vehicle_type' => 'efficiency_optimized',
            'suitable_items' => ['all', 'requires_scheduling'],
        ],
    ];

    /**
     * Eco-friendly packaging options
     */
    private array $ecoPackaging = [
        'recycled' => [
            'name' => 'Recycled Materials',
            'description' => 'Boxes and packing materials made from recycled content',
            'carbon_footprint_reduction' => 0.3, // 30% less than virgin materials
            'cost_multiplier' => 0.9, // Usually cheaper
            'recyclability' => 'high',
        ],
        'biodegradable' => [
            'name' => 'Biodegradable Materials',
            'description' => 'Packing materials that decompose naturally',
            'carbon_footprint_reduction' => 0.1, // Small reduction in production
            'cost_multiplier' => 1.5, // More expensive
            'recyclability' => 'compostable',
        ],
        'minimal' => [
            'name' => 'Minimal Packaging',
            'description' => 'Right-sized packaging with minimal materials',
            'carbon_footprint_reduction' => 0.4, // 40% reduction by reducing materials
            'cost_multiplier' => 0.7, // Much cheaper
            'recyclability' => 'medium',
        ],
        'reusable' => [
            'name' => 'Reusable Packaging',
            'description' => 'Packaging designed for multiple uses with return program',
            'carbon_footprint_reduction' => 0.6, // 60% reduction over multiple uses
            'cost_multiplier' => 1.8, // Higher initial cost
            'recyclability' => 'reusable',
        ],
    ];

    /**
     * Delivery zones with environmental considerations
     */
    private array $deliveryZones = [
        'urban_density' => [
            'high' => [
                'name' => 'High-Density Urban',
                'best_options' => ['bike_delivery', 'public_transport_integration', 'crowdsourced_green'],
                'restrictions' => ['drone_delivery'], // May have airspace restrictions
                'incentives' => ['density_bonus'],
            ],
            'medium' => [
                'name' => 'Medium-Density Urban',
                'best_options' => ['electric_vehicle', 'hybrid_vehicle'],
                'incentives' => ['electrification_bonus'],
            ],
            'low' => [
                'name' => 'Low-Density Urban',
                'best_options' => ['electric_vehicle', 'consolidated_delivery'],
                'incentives' => ['route_optimization_bonus'],
            ],
        ],
        'distance' => [
            'short' => ['bike_delivery', 'electric_vehicle', 'drone_delivery'],
            'medium' => ['electric_vehicle', 'hybrid_vehicle', 'public_transport_integration'],
            'long' => ['hybrid_vehicle', 'consolidated_delivery', 'carbon_offset_delivery'],
        ],
    ];

    /**
     * Get all green delivery methods
     */
    public function getDeliveryMethods(): array
    {
        return $this->deliveryMethods;
    }

    /**
     * Get eco-friendly packaging options
     */
    public function getEcoPackagingOptions(): array
    {
        return $this->ecoPackaging;
    }

    /**
     * Calculate green delivery options for an order
     */
    public function calculateGreenOptions(array $orderData): array
    {
        $required = ['weight_kg', 'volume_m3', 'origin', 'destination', 'delivery_window'];
        
        foreach ($required as $field) {
            if (!isset($orderData[$field])) {
                throw new \InvalidArgumentException("Missing required field: {$field}");
            }
        }

        $weight = $orderData['weight_kg'];
        $volume = $orderData['volume_m3'];
        $origin = $orderData['origin'];
        $destination = $orderData['destination'];
        
        // Calculate distance between origin and destination
        $distance = $this->calculateDistance($origin, $destination);
        
        // Determine suitable options based on order characteristics
        $suitableOptions = $this->findSuitableDeliveryOptions($weight, $volume, $distance);
        
        // Calculate environmental impact for each option
        $optionsWithImpact = [];
        $carbonFootprint = 0;
        
        foreach ($suitableOptions as $optionId) {
            if (isset($this->deliveryMethods[$optionId])) {
                $method = $this->deliveryMethods[$optionId];
                
                // Calculate carbon footprint for this option
                $footprint = $this->calculateCarbonFootprint($method, $distance, $weight);
                
                // Calculate cost for this option
                $cost = $this->calculateDeliveryCost($method, $distance, $weight);
                
                $optionData = [
                    'id' => $optionId,
                    'name' => $method['name'],
                    'description' => $method['description'],
                    'carbon_footprint_kg' => round($footprint, 3),
                    'estimated_cost' => round($cost, 2),
                    'estimated_time' => $this->estimateDeliveryTime($method, $distance),
                    'suitability_score' => $this->calculateSuitabilityScore($method, $weight, $volume),
                    'eco_friendly' => $footprint <= 0.1, // Consider <0.1 kg CO2 as eco-friendly
                    'availability' => $method['availability'],
                    'vehicle_type' => $method['vehicle_type'],
                ];
                
                $optionsWithImpact[] = $optionData;
                $carbonFootprint += $footprint; // Add to total
            }
        }
        
        // Sort by environmental impact (lowest first)
        usort($optionsWithImpact, function ($a, $b) {
            return $a['carbon_footprint_kg'] <=> $b['carbon_footprint_kg'];
        });
        
        return [
            'order_id' => $orderData['order_id'] ?? 'unknown',
            'total_weight_kg' => $weight,
            'total_volume_m3' => $volume,
            'distance_km' => round($distance, 2),
            'origin' => $origin,
            'destination' => $destination,
            'green_options' => $optionsWithImpact,
            'total_carbon_footprint_kg' => round($carbonFootprint, 3),
            'equivalents' => $this->calculateEquivalents($carbonFootprint),
            'recommendations' => $this->generateRecommendations($optionsWithImpact),
            'calculated_at' => now()->toISOString(),
        ];
    }

    /**
     * Calculate distance between two points (simplified)
     */
    private function calculateDistance(array $origin, array $destination): float
    {
        // This is a simplified distance calculation
        // In a real implementation, you would use a routing API
        // For this implementation, return a sample distance
        
        if (isset($origin['lat'], $origin['lng'], $destination['lat'], $destination['lng'])) {
            return $this->haversineDistance(
                $origin['lat'], $origin['lng'],
                $destination['lat'], $destination['lng']
            );
        }
        
        // For this implementation, return a default distance
        return 15.0; // 15km default
    }

    /**
     * Calculate haversine distance
     */
    private function haversineDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371; // Earth's radius in kilometers

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Find suitable delivery options based on characteristics
     */
    private function findSuitableDeliveryOptions(float $weight, float $volume, float $distance): array
    {
        $suitable = [];
        
        foreach ($this->deliveryMethods as $id => $method) {
            $isSuitable = true;
            
            // Check weight/volume constraints
            if ($weight > 5 && $id === 'drone_delivery') {
                $isSuitable = false; // Drones can't carry heavy packages
            }
            
            if ($weight > 50 && in_array($id, ['bike_delivery', 'drone_delivery'])) {
                $isSuitable = false; // Heavy packages need larger vehicles
            }
            
            // Check distance constraints
            if ($distance > 25 && $id === 'drone_delivery') {
                $isSuitable = false; // Drone range limitation
            }
            
            if ($distance > 15 && $id === 'bike_delivery') {
                $isSuitable = false; // Bike delivery range limitation
            }
            
            // Check volume constraints
            if ($volume > 0.5 && $id === 'drone_delivery') {
                $isSuitable = false; // Drones have size limitations
            }
            
            if ($isSuitable) {
                $suitable[] = $id;
            }
        }
        
        return $suitable;
    }

    /**
     * Calculate carbon footprint for a delivery method
     */
    private function calculateCarbonFootprint(array $method, float $distance, float $weight): float
    {
        $baseFootprint = $distance * $method['carbon_footprint_per_km'];
        
        // Account for weight effect (heavier items require more energy)
        $weightMultiplier = 1.0 + ($weight * 0.01); // 1% increase per kg
        
        $totalFootprint = $baseFootprint * $weightMultiplier;
        
        return $totalFootprint;
    }

    /**
     * Calculate delivery cost
     */
    private function calculateDeliveryCost(array $method, float $distance, float $weight): float
    {
        // Base cost: $0.50 per km + $0.10 per kg
        $baseCost = ($distance * 0.5) + ($weight * 0.1);
        
        // Apply method-specific multiplier
        $adjustedCost = $baseCost * $method['cost_multiplier'];
        
        return $adjustedCost;
    }

    /**
     * Estimate delivery time
     */
    private function estimateDeliveryTime(array $method, float $distance): string
    {
        // Rough estimates based on method type
        switch ($method['speed']) {
            case 'very_fast':
                return $distance < 10 ? 'Under 30 mins' : 'Under 1 hour';
            case 'fast':
                return $distance < 15 ? '30 mins - 1 hour' : '1-2 hours';
            case 'medium':
                return $distance < 20 ? '1-2 hours' : 'Same day';
            case 'slow':
                return 'Next day';
            default:
                return '2-3 days';
        }
    }

    /**
     * Calculate suitability score
     */
    private function calculateSuitabilityScore(array $method, float $weight, float $volume): float
    {
        $score = 100; // Start with perfect score
        
        // Adjust based on weight
        if ($weight > 20) {
            if (in_array($method['id'] ?? '', ['bike_delivery', 'drone_delivery'])) {
                $score -= 40; // Significant penalty for heavy items with unsuitable methods
            }
        } elseif ($weight < 2) {
            if (in_array($method['id'] ?? '', ['hybrid_vehicle', 'electric_vehicle'])) {
                $score -= 10; // Light items don't need large vehicles
            }
        }
        
        // Adjust based on volume
        if ($volume > 0.3) {
            $score -= 30; // Large items need appropriate vehicles
        }
        
        return max(0, $score);
    }

    /**
     * Calculate environmental equivalents
     */
    private function calculateEquivalents(float $co2Kg): array
    {
        return [
            'kilometers_driven' => round($co2Kg / 0.2, 2), // Average car emits ~0.2kg/km
            'miles_driven' => round($co2Kg / 0.32, 2), // Average car emits ~0.32kg/mile
            'gasoline_liters' => round($co2Kg / 2.3, 2), // Burning 1 liter of gasoline creates ~2.3kg CO2
            'trees_needed' => round($co2Kg / 22, 2), // Average tree absorbs ~22kg CO2 per year
            'electricity_kwh' => round($co2Kg / 0.5, 2), // Average grid electricity ~0.5kg CO2/kWh (varies by region)
        ];
    }

    /**
     * Generate recommendations
     */
    private function generateRecommendations(array $options): array
    {
        if (empty($options)) {
            return ['No suitable green delivery options available for this order'];
        }

        $recommendations = [];
        $topOption = $options[0]; // Already sorted by environmental impact
        
        $recommendations[] = "Recommended: {$topOption['name']} ({$topOption['carbon_footprint_kg']}kg CO2)";
        
        if ($topOption['carbon_footprint_kg'] == 0) {
            $recommendations[] = "Zero-emission delivery option selected!";
        } else {
            $percentBetter = round((0.2 - $topOption['carbon_footprint_kg']) / 0.2 * 100, 1);
            $recommendations[] = "This option is {$percentBetter}% more environmentally friendly than standard delivery";
        }
        
        // Additional recommendations based on characteristics
        if ($topOption['id'] === 'consolidated_delivery') {
            $recommendations[] = "Consider consolidating future orders to maximize environmental benefits";
        }
        
        if ($topOption['id'] === 'bike_delivery') {
            $recommendations[] = "Supports local employment and urban air quality";
        }
        
        if ($topOption['id'] === 'electric_vehicle') {
            $recommendations[] = "Chosen option supports transition to clean transportation";
        }
        
        return $recommendations;
    }

    /**
     * Get delivery method by ID
     */
    public function getDeliveryMethod(string $methodId): ?array
    {
        return $this->deliveryMethods[$methodId] ?? null;
    }

    /**
     * Get zone-specific recommendations
     */
    public function getZoneRecommendations(array $locationData): array
    {
        $zoneType = $locationData['density'] ?? 'medium';
        $distance = $locationData['distance'] ?? 'medium';
        
        $bestOptions = $this->deliveryZones['urban_density'][$zoneType]['best_options'] ?? [];
        $distanceOptions = $this->deliveryZones['distance'][$distance] ?? [];
        
        // Find intersection of options
        $suitableOptions = array_intersect($bestOptions, $distanceOptions);
        
        $recommendations = [];
        foreach ($suitableOptions as $optionId) {
            if (isset($this->deliveryMethods[$optionId])) {
                $method = $this->deliveryMethods[$optionId];
                $recommendations[] = [
                    'id' => $optionId,
                    'name' => $method['name'],
                    'reason' => $this->getRecommendationReason($optionId, $locationData)
                ];
            }
        }
        
        return [
            'zone_type' => $zoneType,
            'distance_range' => $distance,
            'recommended_options' => $recommendations,
            'location_data' => $locationData,
            'calculated_at' => now()->toISOString(),
        ];
    }

    /**
     * Get reason for recommendation
     */
    private function getRecommendationReason(string $optionId, array $locationData): string
    {
        switch ($optionId) {
            case 'bike_delivery':
                return 'Ideal for dense urban areas with short distances';
            case 'electric_vehicle':
                return 'Perfect for city driving with moderate distances';
            case 'drone_delivery':
                return 'Fastest option for light items in less congested areas';
            case 'consolidated_delivery':
                return 'Efficient for all areas, reduces overall fleet emissions';
            case 'carbon_offset_delivery':
                return 'Good fallback for long distances where other options aren\'t feasible';
            default:
                return 'Environmentally sound delivery option';
        }
    }

    /**
     * Calculate the environmental benefit of choosing green delivery
     */
    public function calculateEnvironmentalBenefit(array $orderData, string $greenMethod, string $standardMethod = 'standard_truck'): array
    {
        // Calculate footprint for green method
        $greenMethodData = $this->deliveryMethods[$greenMethod] ?? null;
        if (!$greenMethodData) {
            throw new \InvalidArgumentException("Invalid green method: {$greenMethod}");
        }
        
        $distance = $this->calculateDistance($orderData['origin'], $orderData['destination']);
        $weight = $orderData['weight_kg'];
        
        $greenFootprint = $this->calculateCarbonFootprint($greenMethodData, $distance, $weight);
        
        // Calculate footprint for standard method
        $standardMethodData = $this->deliveryMethods[$standardMethod] ?? [
            'carbon_footprint_per_km' => 0.2, // Default standard truck emission
            'name' => 'Standard Truck Delivery'
        ];
        
        $standardFootprint = $this->calculateCarbonFootprint($standardMethodData, $distance, $weight);
        
        $benefit = $standardFootprint - $greenFootprint;
        $benefitPercentage = $standardFootprint > 0 ? ($benefit / $standardFootprint) * 100 : 0;
        
        return [
            'order_id' => $orderData['order_id'] ?? 'unknown',
            'distance_km' => round($distance, 2),
            'weight_kg' => $weight,
            'green_method' => $greenMethodData['name'],
            'standard_method' => $standardMethodData['name'],
            'green_footprint_kg' => round($greenFootprint, 3),
            'standard_footprint_kg' => round($standardFootprint, 3),
            'environmental_benefit_kg' => round($benefit, 3),
            'benefit_percentage' => round($benefitPercentage, 2),
            'equivalents' => [
                'gasoline_saved_liters' => round($benefit / 2.3, 2),
                'tree_months_compensation' => round($benefit / 22 * 12, 1),
            ],
            'calculated_at' => now()->toISOString(),
        ];
    }

    /**
     * Get carbon offset options for non-green deliveries
     */
    public function getCarbonOffsetOptions(float $co2Kg): array
    {
        $offsetPrograms = [
            'reforestation' => [
                'name' => 'Reforestation Projects',
                'rate' => 15.00, // $15 per ton of CO2
                'description' => 'Planting trees to absorb CO2 from the atmosphere',
                'verification' => 'Verified by Gold Standard',
                'timeframe' => '10-20 years for full impact',
            ],
            'renewable_energy' => [
                'name' => 'Renewable Energy Investment',
                'rate' => 20.00, // $20 per ton of CO2
                'description' => 'Investing in wind and solar projects to displace fossil fuels',
                'verification' => 'Verified by VCS',
                'timeframe' => 'Immediate impact',
            ],
            'methane_capture' => [
                'name' => 'Methane Capture',
                'rate' => 12.00, // $12 per ton of CO2 equivalent
                'description' => 'Capturing methane from landfills and farms',
                'verification' => 'Verified by Climate Action Reserve',
                'timeframe' => 'Immediate impact',
            ],
        ];

        $offsets = [];
        foreach ($offsetPrograms as $programId => $program) {
            $cost = ($co2Kg / 1000) * $program['rate']; // Convert kg to tons for calculation
            $offsets[] = [
                'id' => $programId,
                'name' => $program['name'],
                'description' => $program['description'],
                'rate_per_ton' => $program['rate'],
                'estimated_cost' => round($cost, 2),
                'co2_compensated_kg' => $co2Kg,
                'verification' => $program['verification'],
                'timeframe' => $program['timeframe'],
            ];
        }

        return [
            'offset_programs' => $offsets,
            'total_co2_to_offset_kg' => round($co2Kg, 3),
            'recommendation' => 'reforestation', // Recommend the most cost-effective
            'calculated_at' => now()->toISOString(),
        ];
    }

    /**
     * Create a green delivery promise for marketing
     */
    public function createDeliveryPromise(array $orderData, string $deliveryMethod): array
    {
        $method = $this->deliveryMethods[$deliveryMethod] ?? null;
        if (!$method) {
            throw new \InvalidArgumentException("Invalid delivery method: {$deliveryMethod}");
        }

        $distance = $this->calculateDistance($orderData['origin'], $orderData['destination']);
        $footprint = $this->calculateCarbonFootprint($method, $distance, $orderData['weight_kg']);
        
        $promise = [
            'method_name' => $method['name'],
            'description' => $method['description'],
            'carbon_footprint_kg' => round($footprint, 3),
            'environmental_impact' => $this->getEnvironmentalImpactStatement($method, $footprint),
            'commitment' => $this->getDeliveryCommitment($method),
            'customer_benefit' => $this->getCustomerBenefit($method),
            'order_id' => $orderData['order_id'] ?? 'unknown',
            'created_at' => now()->toISOString(),
        ];

        return $promise;
    }

    /**
     * Get environmental impact statement
     */
    private function getEnvironmentalImpactStatement(array $method, float $footprint): string
    {
        if ($footprint == 0) {
            return "Zero emissions - completely carbon neutral delivery!";
        } elseif ($footprint < 0.1) {
            return "Ultra-low emissions - less than 100g CO2 for your delivery";
        } elseif ($footprint < 0.5) {
            return "Low emissions - significantly better than standard delivery";
        } else {
            return "This delivery has a measurable environmental impact";
        }
    }

    /**
     * Get delivery commitment
     */
    private function getDeliveryCommitment(array $method): string
    {
        return match($method['id']) {
            'bike_delivery' => 'Supporting clean urban transport and local jobs',
            'electric_vehicle' => 'Using clean energy for transportation',
            'drone_delivery' => 'Efficient point-to-point delivery with minimal footprint',
            'carbon_offset_delivery' => 'Actively compensating for delivery emissions',
            'consolidated_delivery' => 'Reducing total delivery trips to minimize impact',
            'crowdsourced_green' => 'Utilizing existing journeys to reduce new trips',
            default => 'Minimizing environmental impact where possible'
        };
    }

    /**
     * Get customer benefit
     */
    private function getCustomerBenefit(array $method): string
    {
        return match($method['id']) {
            'bike_delivery' => 'Often fastest for urban deliveries',
            'electric_vehicle' => 'Quiet, clean delivery experience',
            'drone_delivery' => 'Ultra-fast delivery to your doorstep',
            'public_transport_integration' => 'Supporting public infrastructure',
            'consolidated_delivery' => 'Potentially lower costs due to efficiency',
            default => 'Environmental impact reduction'
        };
    }
}