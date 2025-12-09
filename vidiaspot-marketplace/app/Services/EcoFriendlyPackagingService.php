<?php

namespace App\Services;

use Illuminate\Support\Str;

class EcoFriendlyPackagingService
{
    /**
     * Available eco-friendly packaging options
     */
    private array $packagingOptions = [
        'recycled_cardboard' => [
            'name' => 'Recycled Cardboard',
            'description' => '100% recycled cardboard boxes',
            'material_composition' => 'Recycled paper fibers',
            'recyclable' => true,
            'compostable' => false,
            'biodegradable' => true,
            'carbon_footprint_factor' => 0.7, // 30% less than standard
            'cost_multiplier' => 1.1, // 10% more expensive
            'protection_level' => 'high',
        ],
        'biodegradable_plastic' => [
            'name' => 'Biodegradable Plastic',
            'description' => 'PLA-based bioplastic bags that decompose in industrial composting',
            'material_composition' => 'PLA (Polylactic Acid) from corn starch',
            'recyclable' => false,
            'compostable' => true,
            'biodegradable' => true,
            'carbon_footprint_factor' => 0.6, // 40% less than standard plastic
            'cost_multiplier' => 1.3, // 30% more expensive
            'protection_level' => 'medium',
        ],
        'kraft_paper' => [
            'name' => 'Kraft Paper Tape & Labels',
            'description' => 'Recyclable kraft paper tape and labels',
            'material_composition' => 'Unbleached kraft paper',
            'recyclable' => true,
            'compostable' => true,
            'biodegradable' => true,
            'carbon_footprint_factor' => 0.5, // 50% less than plastic tape
            'cost_multiplier' => 0.9, // 10% less expensive
            'protection_level' => 'low',
        ],
        'mushroom_packaging' => [
            'name' => 'Mushroom-based Packaging',
            'description' => 'Mycelium-based packaging foam made from agricultural waste',
            'material_composition' => 'Mycelium and agricultural waste',
            'recyclable' => false,
            'compostable' => true,
            'biodegradable' => true,
            'carbon_footprint_factor' => 0.4, // 60% less than styrofoam
            'cost_multiplier' => 1.8, // 80% more expensive
            'protection_level' => 'high',
        ],
        'seaweed_packaging' => [
            'name' => 'Seaweed-based Packaging',
            'description' => 'Edible and dissolvable seaweed-based packaging',
            'material_composition' => 'Seaweed extract and plant-based polymers',
            'recyclable' => false,
            'compostable' => true,
            'biodegradable' => true,
            'carbon_footprint_factor' => 0.3, // 70% less than plastic
            'cost_multiplier' => 2.0, // 100% more expensive
            'protection_level' => 'low',
        ],
        'reusable_boxes' => [
            'name' => 'Reusable Boxes',
            'description' => 'Durable boxes designed for multiple uses with return program',
            'material_composition' => 'Recycled cardboard with reinforced corners',
            'recyclable' => true,
            'compostable' => false,
            'biodegradable' => false,
            'carbon_footprint_factor' => 0.3, // 70% less over 5 uses
            'cost_multiplier' => 2.5, // More expensive initially
            'protection_level' => 'high',
        ],
    ];

    /**
     * Available packaging sizes
     */
    private array $packagingSizes = [
        'small' => [
            'name' => 'Small',
            'dimensions' => '20 x 15 x 10 cm',
            'max_weight' => 1.0, // kg
            'max_items' => 5,
        ],
        'medium' => [
            'name' => 'Medium',
            'dimensions' => '30 x 20 x 15 cm',
            'max_weight' => 3.0, // kg
            'max_items' => 15,
        ],
        'large' => [
            'name' => 'Large',
            'dimensions' => '40 x 30 x 20 cm',
            'max_weight' => 7.0, // kg
            'max_items' => 30,
        ],
        'extra_large' => [
            'name' => 'Extra Large',
            'dimensions' => '50 x 40 x 30 cm',
            'max_weight' => 15.0, // kg
            'max_items' => 50,
        ],
    ];

    /**
     * Get all available eco-friendly packaging options
     */
    public function getPackagingOptions(): array
    {
        return $this->packagingOptions;
    }

    /**
     * Get packaging sizes
     */
    public function getPackagingSizes(): array
    {
        return $this->packagingSizes;
    }

    /**
     * Get a specific packaging option
     */
    public function getPackagingOption(string $type): ?array
    {
        return $this->packagingOptions[$type] ?? null;
    }

    /**
     * Calculate the best packaging option for an item based on size, weight, and fragility
     */
    public function calculateBestPackaging(array $itemData): array
    {
        $weight = $itemData['weight'] ?? 0.5; // kg
        $fragility = $itemData['fragility'] ?? 'medium'; // low, medium, high
        $preferredEcoLevel = $itemData['preferred_eco_level'] ?? 'high'; // low, medium, high
        $budgetConstraint = $itemData['budget_constraint'] ?? null; // max cost allowed

        // Determine size based on weight and fragility
        $recommendedSize = $this->determinePackageSize($weight, $fragility);

        // Determine eco-friendly options based on preferences and constraints
        $options = $this->filterPackagingOptions($fragility, $preferredEcoLevel, $budgetConstraint);

        // Calculate environmental impact for each option
        $evaluatedOptions = [];
        foreach ($options as $type => $option) {
            $cost = $this->calculatePackagingCost($type, $recommendedSize);
            
            $evaluatedOptions[$type] = [
                'type' => $type,
                'name' => $option['name'],
                'size' => $recommendedSize,
                'size_details' => $this->packagingSizes[$recommendedSize],
                'description' => $option['description'],
                'environmental_score' => $this->calculateEnvironmentalScore($option),
                'cost' => $cost,
                'protection_level' => $option['protection_level'],
                'eco_features' => [
                    'recyclable' => $option['recyclable'],
                    'compostable' => $option['compostable'],
                    'biodegradable' => $option['biodegradable'],
                ],
                'carbon_footprint_reduction' => (1 - $option['carbon_footprint_factor']) * 100 . '%',
            ];
        }

        // Sort by environmental score and cost
        uasort($evaluatedOptions, function ($a, $b) {
            // Sort by environmental score first (higher is better)
            $envDiff = $b['environmental_score'] <=> $a['environmental_score'];
            if ($envDiff !== 0) {
                return $envDiff;
            }
            // Then by cost (lower is better)
            return $a['cost'] <=> $b['cost'];
        });

        return [
            'recommended_option' => reset($evaluatedOptions),
            'available_options' => array_values($evaluatedOptions),
            'recommended_size' => $recommendedSize,
            'item_data' => $itemData,
            'calculated_at' => now()->toISOString(),
        ];
    }

    /**
     * Determine the right package size based on item characteristics
     */
    private function determinePackageSize(float $weight, string $fragility): string
    {
        // Basic logic: higher weight/fragility requires larger package
        if ($weight > 10) {
            return 'extra_large';
        } elseif ($weight > 5) {
            return 'large';
        } elseif ($weight > 2 || $fragility === 'high') {
            return 'medium';
        } else {
            return 'small';
        }
    }

    /**
     * Filter packaging options based on criteria
     */
    private function filterPackagingOptions(string $fragility, string $preferredEcoLevel, ?float $budgetConstraint): array
    {
        $filtered = [];

        foreach ($this->packagingOptions as $type => $option) {
            // Check protection level against fragility
            $protectionOk = true;
            if ($fragility === 'high' && $option['protection_level'] !== 'high') {
                $protectionOk = false;
            }

            // Check budget constraint
            $withinBudget = true;
            if ($budgetConstraint) {
                $estimatedCost = $this->calculatePackagingCost($type, 'medium'); // Use medium as reference
                if ($estimatedCost > $budgetConstraint) {
                    $withinBudget = false;
                }
            }

            if ($protectionOk && $withinBudget) {
                $filtered[$type] = $option;
            }
        }

        return $filtered;
    }

    /**
     * Calculate environmental score for a packaging option
     */
    private function calculateEnvironmentalScore(array $option): float
    {
        $score = 0;

        // Recyclable: +30 points
        if ($option['recyclable']) {
            $score += 30;
        }

        // Compostable: +25 points
        if ($option['compostable']) {
            $score += 25;
        }

        // Biodegradable: +20 points
        if ($option['biodegradable']) {
            $score += 20;
        }

        // Low carbon footprint: +25 points
        if ($option['carbon_footprint_factor'] < 0.6) {
            $score += 25;
        } elseif ($option['carbon_footprint_factor'] < 0.8) {
            $score += 15;
        }

        return min(100, $score); // Cap at 100
    }

    /**
     * Calculate packaging cost
     */
    private function calculatePackagingCost(string $type, string $size): float
    {
        $baseCosts = [
            'small' => 1.0,
            'medium' => 1.5,
            'large' => 2.5,
            'extra_large' => 4.0,
        ];

        $multiplier = $this->packagingOptions[$type]['cost_multiplier'] ?? 1.0;
        $baseCost = $baseCosts[$size] ?? 1.5;

        return round($baseCost * $multiplier, 2);
    }

    /**
     * Calculate packaging recommendations for multiple items
     */
    public function calculateMultipleItemPackaging(array $items): array
    {
        $recommendations = [];
        $totalCost = 0;
        $totalEnvironmentalScore = 0;

        foreach ($items as $index => $item) {
            $itemRecommendation = $this->calculateBestPackaging($item);
            $recommendations[] = $itemRecommendation;
            $totalCost += $itemRecommendation['recommended_option']['cost'];
            $totalEnvironmentalScore += $itemRecommendation['recommended_option']['environmental_score'];
        }

        return [
            'item_recommendations' => $recommendations,
            'total_cost' => round($totalCost, 2),
            'average_environmental_score' => round($totalEnvironmentalScore / count($items), 2),
            'items_count' => count($items),
            'consolidation_opportunities' => $this->findConsolidationOpportunities($items),
            'calculated_at' => now()->toISOString(),
        ];
    }

    /**
     * Find opportunities to consolidate items to reduce packaging
     */
    private function findConsolidationOpportunities(array $items): array
    {
        // Simple consolidation logic: group items that could fit in the same package
        $consolidation = [
            'potential_savings' => 0,
            'consolidation_opportunities' => [],
        ];

        // For this implementation, we'll just return the possibility
        // A real implementation would group items based on size/weight
        return $consolidation;
    }

    /**
     * Get carbon footprint reduction by using eco packaging
     */
    public function calculateCarbonFootprintReduction(string $packagingType, string $standardType = 'standard_cardboard'): ?array
    {
        if (!isset($this->packagingOptions[$packagingType]) || !isset($this->packagingOptions[$standardType])) {
            return null;
        }

        $ecoFactor = $this->packagingOptions[$packagingType]['carbon_footprint_factor'];
        $standardFactor = $this->packagingOptions[$standardType]['carbon_footprint_factor'];

        $reduction = ($standardFactor - $ecoFactor) / $standardFactor * 100;

        return [
            'packaging_type' => $packagingType,
            'standard_type' => $standardType,
            'eco_factor' => $ecoFactor,
            'standard_factor' => $standardFactor,
            'reduction_percentage' => round($reduction, 2),
            'reduction_description' => $reduction > 0 ? 
                "Using this packaging reduces carbon footprint by " . round($reduction, 2) . "%" :
                "This packaging has a higher carbon footprint than standard",
        ];
    }

    /**
     * Generate packaging sustainability report
     */
    public function generateSustainabilityReport(array $packagingSelections): array
    {
        $totalItems = count($packagingSelections);
        $packagingTypesUsed = [];
        $environmentalScores = [];
        $costs = [];

        foreach ($packagingSelections as $selection) {
            $type = $selection['type'] ?? 'unknown';
            $packagingTypesUsed[$type] = ($packagingTypesUsed[$type] ?? 0) + 1;
            $environmentalScores[] = $selection['environmental_score'] ?? 0;
            $costs[] = $selection['cost'] ?? 0;
        }

        return [
            'summary' => [
                'total_packages' => $totalItems,
                'average_environmental_score' => count($environmentalScores) > 0 ? round(array_sum($environmentalScores) / count($environmentalScores), 2) : 0,
                'total_cost' => round(array_sum($costs), 2),
                'packaging_type_distribution' => $packagingTypesUsed,
            ],
            'environmental_impact' => [
                'eco_friendly_packages' => array_sum(array_filter($packagingTypesUsed, function($type) {
                    return $this->packagingOptions[$type]['compostable'] ?? false;
                }, ARRAY_FILTER_USE_KEY)),
                'recyclable_packages' => array_sum(array_filter($packagingTypesUsed, function($type) {
                    return $this->packagingOptions[$type]['recyclable'] ?? false;
                }, ARRAY_FILTER_USE_KEY)),
            ],
            'recommendations' => $this->generateReportRecommendations($packagingTypesUsed),
            'report_generated_at' => now()->toISOString(),
        ];
    }

    /**
     * Generate recommendations based on usage patterns
     */
    private function generateReportRecommendations(array $packagingTypesUsed): array
    {
        $recommendations = [];

        // Check if standard packaging is overused
        if (isset($packagingTypesUsed['standard_cardboard']) && 
            $packagingTypesUsed['standard_cardboard'] / array_sum($packagingTypesUsed) > 0.5) {
            $recommendations[] = "Consider increasing use of eco-friendly packaging options";
        }

        // Check if high-impact options are underused
        if (!isset($packagingTypesUsed['mushroom_packaging']) && !isset($packagingTypesUsed['seaweed_packaging'])) {
            $recommendations[] = "Explore high-impact eco-friendly options like mushroom or seaweed packaging for fragile items";
        }

        return $recommendations;
    }
}