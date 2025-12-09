<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class CarbonFootprintCalculatorService
{
    /**
     * CO2 emissions factors (kg CO2 per kg per km) by shipping method
     */
    private array $emissionFactors = [
        'ground' => 0.008,      // Standard truck delivery
        'express_ground' => 0.012, // Express truck delivery
        'air' => 0.5,           // Air freight
        'overnight' => 0.3,     // Overnight express
        'freight' => 0.004,     // Less than truckload freight
        'bicycle' => 0.0005,    // Bicycle delivery
        'electric_vehicle' => 0.001, // Electric vehicle
    ];

    /**
     * Package size multipliers
     */
    private array $sizeMultipliers = [
        'small' => 1.0,
        'medium' => 1.5,
        'large' => 2.0,
        'extra_large' => 3.0,
    ];

    /**
     * Calculate carbon footprint for shipping
     */
    public function calculateShippingFootprint(array $shippingData): array
    {
        // Validate required data
        $required = ['weight', 'distance', 'method', 'origin', 'destination'];
        foreach ($required as $field) {
            if (!isset($shippingData[$field])) {
                throw new \InvalidArgumentException("Missing required field: {$field}");
            }
        }

        $weight = floatval($shippingData['weight']); // in kg
        $distance = floatval($shippingData['distance']); // in km
        $method = $shippingData['method'];
        $packageSize = $shippingData['package_size'] ?? 'medium';
        $items = $shippingData['items'] ?? 1;
        $temperatureControlled = $shippingData['temperature_controlled'] ?? false;

        // Validate method and package size
        if (!isset($this->emissionFactors[$method])) {
            throw new \InvalidArgumentException("Invalid shipping method: {$method}");
        }
        
        if (!isset($this->sizeMultipliers[$packageSize])) {
            throw new \InvalidArgumentException("Invalid package size: {$packageSize}");
        }

        // Base calculation: weight * distance * emission factor
        $baseFootprint = $weight * $distance * $this->emissionFactors[$method];

        // Apply package size multiplier
        $sizeAdjustedFootprint = $baseFootprint * $this->sizeMultipliers[$packageSize];

        // Apply additional factors
        $finalFootprint = $sizeAdjustedFootprint;

        // Increase for temperature controlled shipping
        if ($temperatureControlled) {
            $finalFootprint *= 1.3; // 30% more emissions for temperature control
        }

        // Increase for smaller shipments (less efficient)
        if ($items === 1) {
            $finalFootprint *= 1.2; // Single item is less efficient than bulk
        }

        // Calculate environmental impact in various units
        $results = [
            'carbon_footprint_kg' => round($finalFootprint, 4),
            'carbon_footprint_g' => round($finalFootprint * 1000, 2),
            'carbon_footprint_lb' => round($finalFootprint * 2.205, 4),
            'distance_km' => $distance,
            'distance_mi' => round($distance * 0.621371, 2),
            'weight_kg' => $weight,
            'shipping_method' => $method,
            'package_size' => $packageSize,
            'items_count' => $items,
            'temperature_controlled' => $temperatureControlled,
            'origin' => $shippingData['origin'],
            'destination' => $shippingData['destination'],
            'breakdown' => [
                'base_calculation' => [
                    'weight' => $weight,
                    'distance' => $distance,
                    'emission_factor' => $this->emissionFactors[$method],
                    'base_footprint' => round($baseFootprint, 4),
                ],
                'adjustments' => [
                    'size_multiplier' => $this->sizeMultipliers[$packageSize],
                    'size_adjusted' => round($sizeAdjustedFootprint, 4),
                    'temperature_control' => $temperatureControlled ? 1.3 : 1.0,
                    'single_item' => $items === 1 ? 1.2 : 1.0,
                ],
            ],
            'equivalents' => $this->calculateEquivalents($finalFootprint),
            'calculated_at' => now()->toISOString(),
        ];

        return $results;
    }

    /**
     * Calculate environmental equivalents
     */
    private function calculateEquivalents(float $co2Kg): array
    {
        // Common environmental equivalents
        return [
            'tree_absorption_days' => round($co2Kg / 0.022, 1), // Average tree absorbs 22kg CO2/year
            'car_driven_km' => round($co2Kg / 0.12, 1), // Average car emits 120g CO2/km
            'car_driven_mi' => round($co2Kg / 0.193, 1), // Average car emits ~193g CO2/mile
            'gasoline_gallons' => round($co2Kg / 8.89, 3), // 1 gallon of gas produces 8.89kg CO2
            'electricity_kwh' => round($co2Kg / 0.475, 1), // Average grid electricity (varies by region)
            'led_light_bulbs_24h' => round($co2Kg / 0.000432, 0), // LED bulb for 24h
        ];
    }

    /**
     * Calculate distance between two locations (simplified)
     * In a real implementation, this would use a geocoding service
     */
    public function calculateDistance(string $origin, string $destination): float
    {
        // This is a simplified distance calculation
        // In a real implementation, you would use a geocoding API
        // For this implementation, we'll return a default value or calculate based on provided coordinates
        
        if (isset($origin['lat'], $origin['lng'], $destination['lat'], $destination['lng'])) {
            return $this->haversineDistance(
                $origin['lat'], $origin['lng'],
                $destination['lat'], $destination['lng']
            );
        }
        
        // For this implementation, return a default distance since we can't access real geocoding
        return 500.0; // 500km default
    }

    /**
     * Calculate Haversine distance between two points
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
     * Get available shipping methods and their emission factors
     */
    public function getShippingMethods(): array
    {
        $methods = [];
        foreach ($this->emissionFactors as $method => $factor) {
            $methods[$method] = [
                'name' => $this->formatMethodName($method),
                'emission_factor' => $factor,
                'description' => $this->getMethodDescription($method),
                'eco_friendly' => $this->isEcoFriendlyMethod($method),
            ];
        }

        return $methods;
    }

    /**
     * Format method name for display
     */
    private function formatMethodName(string $method): string
    {
        return ucfirst(str_replace('_', ' ', $method));
    }

    /**
     * Get description for a shipping method
     */
    private function getMethodDescription(string $method): string
    {
        $descriptions = [
            'ground' => 'Standard ground shipping via truck',
            'express_ground' => 'Express ground shipping with faster delivery',
            'air' => 'Air freight shipping',
            'overnight' => 'Overnight express delivery',
            'freight' => 'Less than truckload freight shipping',
            'bicycle' => 'Bicycle courier delivery',
            'electric_vehicle' => 'Electric vehicle delivery',
        ];

        return $descriptions[$method] ?? 'Shipping method';
    }

    /**
     * Determine if method is eco-friendly
     */
    private function isEcoFriendlyMethod(string $method): bool
    {
        return in_array($method, ['bicycle', 'electric_vehicle']);
    }

    /**
     * Get package size options
     */
    public function getPackageSizes(): array
    {
        return [
            'small' => ['name' => 'Small (Under 500g)', 'multiplier' => $this->sizeMultipliers['small']],
            'medium' => ['name' => 'Medium (500g - 2kg)', 'multiplier' => $this->sizeMultipliers['medium']],
            'large' => ['name' => 'Large (2kg - 5kg)', 'multiplier' => $this->sizeMultipliers['large']],
            'extra_large' => ['name' => 'Extra Large (Over 5kg)', 'multiplier' => $this->sizeMultipliers['extra_large']],
        ];
    }

    /**
     * Suggest more eco-friendly shipping options
     */
    public function suggestEcoOptions(array $shippingData, int $maxOptions = 3): array
    {
        $currentFootprint = $this->calculateShippingFootprint($shippingData)['carbon_footprint_kg'];
        $suggestions = [];

        // Compare with all other shipping methods
        foreach ($this->emissionFactors as $method => $factor) {
            if ($method !== $shippingData['method']) {
                $testData = $shippingData;
                $testData['method'] = $method;
                $newFootprint = $this->calculateShippingFootprint($testData)['carbon_footprint_kg'];

                if ($newFootprint < $currentFootprint) {
                    $suggestions[] = [
                        'method' => $method,
                        'name' => $this->formatMethodName($method),
                        'new_footprint' => round($newFootprint, 4),
                        'current_footprint' => $currentFootprint,
                        'savings' => round($currentFootprint - $newFootprint, 4),
                        'savings_percentage' => round(($currentFootprint - $newFootprint) / $currentFootprint * 100, 2),
                        'description' => $this->getMethodDescription($method),
                        'eco_friendly' => $this->isEcoFriendlyMethod($method),
                    ];
                }
            }
        }

        // Sort by savings percentage
        usort($suggestions, function ($a, $b) {
            return $b['savings_percentage'] <=> $a['savings_percentage'];
        });

        return array_slice($suggestions, 0, $maxOptions);
    }

    /**
     * Calculate total footprint for multiple shipments
     */
    public function calculateMultipleShipments(array $shipments): array
    {
        $totalFootprint = 0;
        $breakdown = [];
        $itemsCount = 0;

        foreach ($shipments as $shipment) {
            $result = $this->calculateShippingFootprint($shipment);
            $totalFootprint += $result['carbon_footprint_kg'];
            $breakdown[] = $result;
            $itemsCount += $result['items_count'];
        }

        return [
            'total_carbon_footprint_kg' => round($totalFootprint, 4),
            'total_carbon_footprint_lb' => round($totalFootprint * 2.205, 4),
            'shipments_count' => count($shipments),
            'items_count' => $itemsCount,
            'breakdown' => $breakdown,
            'equivalents' => $this->calculateEquivalents($totalFootprint),
            'calculated_at' => now()->toISOString(),
        ];
    }
}