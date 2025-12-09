<?php

namespace App\Services;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

class EnvironmentalImpactRatingsService
{
    /**
     * Environmental impact criteria and their weights
     */
    private array $impactCriteria = [
        'carbon_footprint' => [
            'name' => 'Carbon Footprint',
            'description' => 'CO2 emissions during production and shipping',
            'weight' => 0.3, // 30% weight in overall score
            'max_points' => 25,
            'calculation_method' => 'per_kg_co2_emissions',
        ],
        'resource_efficiency' => [
            'name' => 'Resource Efficiency',
            'description' => 'Water, energy, and raw material usage',
            'weight' => 0.2, // 20% weight in overall score
            'max_points' => 20,
            'calculation_method' => 'resource_consumption_index',
        ],
        'waste_generation' => [
            'name' => 'Waste Generation',
            'description' => 'Amount of waste produced during manufacturing',
            'weight' => 0.15, // 15% weight in overall score
            'max_points' => 15,
            'calculation_method' => 'waste_per_unit_produced',
        ],
        'packaging_sustainability' => [
            'name' => 'Packaging Sustainability',
            'description' => 'Eco-friendliness of packaging materials',
            'weight' => 0.15, // 15% weight in overall score
            'max_points' => 15,
            'calculation_method' => 'packaging_environmental_score',
        ],
        'end_of_life' => [
            'name' => 'End of Life',
            'description' => 'Recyclability and biodegradability',
            'weight' => 0.1, // 10% weight in overall score
            'max_points' => 10,
            'calculation_method' => 'disposability_score',
        ],
        'social_impact' => [
            'name' => 'Social Impact',
            'description' => 'Fair labor practices and community impact',
            'weight' => 0.1, // 10% weight in overall score
            'max_points' => 15, // Note: This gets 15 points but only 10% weight
            'calculation_method' => 'fair_trade_compliance',
        ],
    ];

    /**
     * Rating scale
     */
    private array $ratingScale = [
        ['min' => 90, 'max' => 100, 'label' => 'Excellent', 'color' => 'green', 'icon' => '🏆'],
        ['min' => 75, 'max' => 89, 'label' => 'Very Good', 'color' => 'lightgreen', 'icon' => '🌱'],
        ['min' => 60, 'max' => 74, 'label' => 'Good', 'color' => 'yellowgreen', 'icon' => '🌿'],
        ['min' => 45, 'max' => 59, 'label' => 'Average', 'color' => 'yellow', 'icon' => '🍃'],
        ['min' => 30, 'max' => 44, 'label' => 'Poor', 'color' => 'orange', 'icon' => '⚠️'],
        ['min' => 0, 'max' => 29, 'label' => 'Very Poor', 'color' => 'red', 'icon' => '❌'],
    ];

    /**
     * Industry-specific impact factors
     */
    private array $industryFactors = [
        'fashion' => [
            'water_usage_multiplier' => 1.5, // Fashion uses more water
            'chemical_usage_multiplier' => 1.2,
            'waste_multiplier' => 1.3,
        ],
        'electronics' => [
            'rare_earth_multiplier' => 1.8, // More rare earth materials
            'energy_usage_multiplier' => 1.4,
            'toxic_waste_multiplier' => 1.6,
        ],
        'food' => [
            'transportation_multiplier' => 1.2, // Perishables need more transport
            'packaging_multiplier' => 1.1,
        ],
        'furniture' => [
            'material_usage_multiplier' => 1.6, // Uses more raw materials
            'durability_divider' => 2.0, // Longer lasting products get better scores
        ],
    ];

    /**
     * Get impact criteria
     */
    public function getImpactCriteria(): array
    {
        return $this->impactCriteria;
    }

    /**
     * Calculate environmental impact rating for a product
     */
    public function calculateProductRating(array $productData): array
    {
        $requiredFields = [
            'category', 'weight_kg', 'production_location', 'shipping_distance_km',
            'materials', 'packaging_type', 'lifespan_years'
        ];

        foreach ($requiredFields as $field) {
            if (!isset($productData[$field])) {
                throw new \InvalidArgumentException("Missing required field: {$field}");
            }
        }

        $categoryId = $productData['category'];
        $industryFactor = $this->industryFactors[$categoryId] ?? [
            'water_usage_multiplier' => 1.0,
            'chemical_usage_multiplier' => 1.0,
            'waste_multiplier' => 1.0,
        ];

        // Calculate individual impact scores
        $scores = [
            'carbon_footprint' => $this->calculateCarbonFootprintScore($productData, $industryFactor),
            'resource_efficiency' => $this->calculateResourceEfficiencyScore($productData, $industryFactor),
            'waste_generation' => $this->calculateWasteGenerationScore($productData, $industryFactor),
            'packaging_sustainability' => $this->calculatePackagingScore($productData),
            'end_of_life' => $this->calculateEndOfLifeScore($productData),
            'social_impact' => $this->calculateSocialImpactScore($productData),
        ];

        // Calculate weighted total score
        $totalScore = 0;
        foreach ($this->impactCriteria as $criteriaId => $criteria) {
            $scoreValue = $scores[$criteriaId];
            $weightedScore = $scoreValue * $criteria['weight'];
            $totalScore += $weightedScore;
        }

        // Convert to 100-point scale
        $totalScore = min(100, max(0, $totalScore));
        $rating = $this->getRatingByScore($totalScore);

        return [
            'product_id' => $productData['id'] ?? 'unknown',
            'overall_score' => round($totalScore, 2),
            'overall_rating' => $rating,
            'individual_scores' => $scores,
            'breakdown' => [
                'criteria_weights' => array_map(function($c) {
                    return $c['weight'];
                }, $this->impactCriteria),
                'industry_factor_applied' => $industryFactor,
            ],
            'category' => $categoryId,
            'calculated_at' => now()->toISOString(),
            'recommendations' => $this->generateRecommendations($scores, $productData),
        ];
    }

    /**
     * Calculate carbon footprint score
     */
    private function calculateCarbonFootprintScore(array $productData, array $industryFactor): float
    {
        // Calculate based on weight, shipping distance, production location, and materials
        $weight = $productData['weight_kg'];
        $distance = $productData['shipping_distance_km'];
        
        // Base CO2 emissions: 0.1 kg CO2 per kg per km for transport
        $transportEmissions = $weight * $distance * 0.1;
        
        // Add production emissions based on materials
        $materialEmissions = 0;
        if (isset($productData['materials'])) {
            foreach ($productData['materials'] as $material => $percentage) {
                switch ($material) {
                    case 'plastic':
                        $materialEmissions += $weight * $percentage * 2.0; // 2 kg CO2 per kg plastic
                        break;
                    case 'aluminum':
                        $materialEmissions += $weight * $percentage * 12.0; // 12 kg CO2 per kg aluminum
                        break;
                    case 'steel':
                        $materialEmissions += $weight * $percentage * 1.5; // 1.5 kg CO2 per kg steel
                        break;
                    case 'cotton':
                        $materialEmissions += $weight * $percentage * 4.0; // 4 kg CO2 per kg cotton
                        break;
                    case 'recycled':
                        $materialEmissions += $weight * $percentage * 0.5; // 0.5 kg CO2 per kg recycled material
                        break;
                }
            }
        }

        // Apply industry factor
        $totalEmissions = ($transportEmissions + $materialEmissions) * ($industryFactor['carbon_multiplier'] ?? 1.0);

        // Calculate score (higher emissions = lower score)
        $maxEmissions = 50.0; // Maximum possible emissions for normalization
        $score = max(0, 25 - ($totalEmissions / $maxEmissions) * 25);

        return $score;
    }

    /**
     * Calculate resource efficiency score
     */
    private function calculateResourceEfficiencyScore(array $productData, array $industryFactor): float
    {
        // Calculate based on water usage and energy consumption during production
        $waterUsage = $productData['water_usage_liters'] ?? 100 * $productData['weight_kg']; // Default estimation
        $energyUsage = $productData['energy_usage_kwh'] ?? 5 * $productData['weight_kg']; // Default estimation

        // Apply industry factors
        $waterUsage *= $industryFactor['water_usage_multiplier'] ?? 1.0;
        $energyUsage *= $industryFactor['energy_usage_multiplier'] ?? 1.0;

        // Calculate score (higher usage = lower score)
        $maxWaterUsage = 1000.0; // Maximum possible water usage for normalization
        $maxEnergyUsage = 100.0; // Maximum possible energy usage for normalization

        $waterScore = max(0, 10 - ($waterUsage / $maxWaterUsage) * 10);
        $energyScore = max(0, 10 - ($energyUsage / $maxEnergyUsage) * 10);

        // Additional points if the product has energy or water saving features
        $bonusScore = 0;
        if ($productData['energy_efficient'] ?? false) {
            $bonusScore += 5;
        }
        if ($productData['water_efficient'] ?? false) {
            $bonusScore += 5;
        }

        return min(20, $waterScore + $energyScore + $bonusScore);
    }

    /**
     * Calculate waste generation score
     */
    private function calculateWasteGenerationScore(array $productData, array $industryFactor): float
    {
        // Calculate based on manufacturing waste and packaging waste
        $materialWaste = $productData['waste_generated_kg'] ?? $productData['weight_kg'] * 0.1; // Estimate 10% waste
        
        // Apply industry factor
        $materialWaste *= $industryFactor['waste_multiplier'] ?? 1.0;

        // Calculate score (higher waste = lower score)
        $maxWaste = 5.0; // Maximum possible waste for normalization
        $score = max(0, 15 - ($materialWaste / $maxWaste) * 15);

        return $score;
    }

    /**
     * Calculate packaging sustainability score
     */
    private function calculatePackagingScore(array $productData): float
    {
        $packagingType = $productData['packaging_type'] ?? 'standard';
        
        // Score based on packaging type
        $packagingScores = [
            'compostable' => 15,
            'recycled' => 12,
            'recyclable' => 10,
            'biodegradable' => 13,
            'minimal' => 11,
            'standard' => 5,
            'excessive' => 2,
        ];

        $score = $packagingScores[$packagingType] ?? 5;

        // Bonus points for reduced packaging
        if ($productData['minimal_packaging'] ?? false) {
            $score += 3;
        }

        return min(15, $score);
    }

    /**
     * Calculate end of life score
     */
    private function calculateEndOfLifeScore(array $productData): float
    {
        // Score based on recyclability and biodegradability
        $recyclable = $productData['recyclable'] ?? false;
        $biodegradable = $productData['biodegradable'] ?? false;
        $take_back_program = $productData['take_back_program'] ?? false;
        $lifespan = $productData['lifespan_years'] ?? 1;

        $score = 0;

        if ($recyclable) {
            $score += 5;
        }
        if ($biodegradable) {
            $score += 4;
        }
        if ($take_back_program) {
            $score += 3;
        }

        // Bonus for longer lifespan (sustainability through durability)
        if ($lifespan > 5) {
            $score += 3;
        } elseif ($lifespan > 2) {
            $score += 2;
        }

        return min(10, $score);
    }

    /**
     * Calculate social impact score
     */
    private function calculateSocialImpactScore(array $productData): float
    {
        $score = 0;

        // Points for various social responsibility measures
        if ($productData['fair_trade_certified'] ?? false) {
            $score += 5;
        }
        if ($productData['local_production'] ?? false) {
            $score += 3;
        }
        if ($productData['living_wage'] ?? false) {
            $score += 4;
        }
        if ($productData['ethical_sourcing'] ?? false) {
            $score += 3;
        }

        return min(15, $score);
    }

    /**
     * Get rating by score
     */
    private function getRatingByScore(float $score): array
    {
        foreach ($this->ratingScale as $rating) {
            if ($score >= $rating['min'] && $score <= $rating['max']) {
                return $rating;
            }
        }

        // Default to lowest rating if no match found
        return $this->ratingScale[count($this->ratingScale) - 1];
    }

    /**
     * Generate improvement recommendations
     */
    private function generateRecommendations(array $scores, array $productData): array
    {
        $recommendations = [];

        // Carbon footprint recommendations
        if ($scores['carbon_footprint'] < 15) {
            $recommendations[] = [
                'category' => 'carbon_footprint',
                'title' => 'Reduce Carbon Footprint',
                'description' => 'Consider using carbon-neutral shipping options',
                'priority' => 'high'
            ];
        }

        // Resource efficiency recommendations
        if ($scores['resource_efficiency'] < 15) {
            $recommendations[] = [
                'category' => 'resource_efficiency',
                'title' => 'Improve Resource Efficiency',
                'description' => 'Optimize production processes to reduce water and energy consumption',
                'priority' => 'medium'
            ];
        }

        // Packaging recommendations
        if ($scores['packaging_sustainability'] < 10) {
            $recommendations[] = [
                'category' => 'packaging_sustainability',
                'title' => 'Improve Packaging Sustainability',
                'description' => 'Switch to recycled or biodegradable packaging materials',
                'priority' => 'medium'
            ];
        }

        // End of life recommendations
        if ($scores['end_of_life'] < 7) {
            $recommendations[] = [
                'category' => 'end_of_life',
                'title' => 'Improve End-of-Life Options',
                'description' => 'Design product for easier recycling or biodegradation',
                'priority' => 'low'
            ];
        }

        return $recommendations;
    }

    /**
     * Get product comparison data
     */
    public function getComparisonData(array $productIds): array
    {
        $comparison = [
            'products' => [],
            'comparison_metrics' => array_keys($this->impactCriteria),
            'rankings' => [],
        ];

        foreach ($productIds as $productId) {
            // In a real implementation, we'd fetch the product rating from a database
            // For this example, we'll generate sample data
            $comparison['products'][] = [
                'id' => $productId,
                'name' => "Product {$productId}",
                'category' => 'sample',
                'rating' => [
                    'overall_score' => rand(40, 95),
                    'overall_rating' => $this->getRatingByScore(rand(40, 95)),
                ],
            ];
        }

        // Sort by overall score for rankings
        usort($comparison['products'], function($a, $b) {
            return $b['rating']['overall_score'] <=> $a['rating']['overall_score'];
        });

        $rankings = [];
        foreach ($comparison['products'] as $index => $product) {
            $rankings[] = [
                'rank' => $index + 1,
                'product_id' => $product['id'],
                'score' => $product['rating']['overall_score'],
            ];
        }
        $comparison['rankings'] = $rankings;

        return $comparison;
    }

    /**
     * Get industry benchmarks
     */
    public function getIndustryBenchmarks(string $category): array
    {
        $benchmarks = [
            'technology' => [
                'avg_score' => 62,
                'best_practice_score' => 85,
                'min_acceptable_score' => 45,
                'key_factors' => ['energy_efficiency', 'recyclability', 'packaging'],
            ],
            'fashion' => [
                'avg_score' => 48,
                'best_practice_score' => 78,
                'min_acceptable_score' => 35,
                'key_factors' => ['water_usage', 'chemical_impact', 'labor_practices'],
            ],
            'food' => [
                'avg_score' => 68,
                'best_practice_score' => 88,
                'min_acceptable_score' => 50,
                'key_factors' => ['local_sourcing', 'packaging', 'waste_reduction'],
            ],
            'furniture' => [
                'avg_score' => 58,
                'best_practice_score' => 82,
                'min_acceptable_score' => 40,
                'key_factors' => ['sustainable_materials', 'durability', 'end_of_life'],
            ],
            'default' => [
                'avg_score' => 60,
                'best_practice_score' => 80,
                'min_acceptable_score' => 45,
                'key_factors' => ['carbon_footprint', 'packaging', 'resource_efficiency'],
            ],
        ];

        $categoryBenchmarks = $benchmarks[$category] ?? $benchmarks['default'];

        return [
            'category' => $category,
            'benchmarks' => $categoryBenchmarks,
            'rating_scale' => $this->ratingScale,
            'calculated_at' => now()->toISOString(),
        ];
    }

    /**
     * Calculate vendor sustainability score
     */
    public function calculateVendorSustainabilityScore(array $vendorData): array
    {
        // Calculate a vendor's overall sustainability based on their products
        $totalScore = 0;
        $productCount = 0;
        
        if (isset($vendorData['products'])) {
            foreach ($vendorData['products'] as $product) {
                if (isset($product['environmental_rating'])) {
                    $totalScore += $product['environmental_rating']['overall_score'];
                    $productCount++;
                }
            }
        }

        $averageScore = $productCount > 0 ? $totalScore / $productCount : 0;
        $rating = $this->getRatingByScore($averageScore);

        return [
            'vendor_id' => $vendorData['id'] ?? 'unknown',
            'vendor_name' => $vendorData['name'] ?? 'Unknown Vendor',
            'average_product_score' => round($averageScore, 2),
            'vendor_sustainability_rating' => $rating,
            'products_rated' => $productCount,
            'certifications' => $vendorData['certifications'] ?? [],
            'sustainability_practices' => $vendorData['sustainability_practices'] ?? [],
            'calculated_at' => now()->toISOString(),
        ];
    }

    /**
     * Get rating history for a product
     */
    public function getRatingHistory(string $productId): array
    {
        // In a real implementation, this would fetch from a database
        // For this example, we'll generate sample history

        $history = [];
        $currentScore = rand(50, 90);
        
        for ($i = 30; $i >= 0; $i--) {
            $date = now()->subDays($i)->toISOString();
            // Simulate score changes over time
            $score = max(0, min(100, $currentScore + rand(-5, 5)));
            $history[] = [
                'date' => $date,
                'score' => round($score, 2),
                'rating' => $this->getRatingByScore($score),
            ];
        }

        return [
            'product_id' => $productId,
            'history' => $history,
            'trend' => $this->analyzeTrend($history),
            'calculated_at' => now()->toISOString(),
        ];
    }

    /**
     * Analyze rating trend
     */
    private function analyzeTrend(array $history): string
    {
        if (count($history) < 2) {
            return 'insufficient_data';
        }

        $firstScore = $history[0]['score'];
        $lastScore = end($history)['score'];

        if ($lastScore > $firstScore) {
            return 'improving';
        } elseif ($lastScore < $firstScore) {
            return 'declining';
        } else {
            return 'stable';
        }
    }

    /**
     * Get environmental impact leaderboard
     */
    public function getLeaderboard(string $category = null, int $limit = 10): array
    {
        // In a real implementation, this would query a database
        // For this example, we'll generate sample data

        $leaderboard = [];
        $categories = $category ? [$category] : ['technology', 'fashion', 'food', 'furniture'];

        foreach ($categories as $cat) {
            for ($i = 0; $i < 3; $i++) { // 3 products per category
                $leaderboard[] = [
                    'rank' => count($leaderboard) + 1,
                    'product_id' => 'prod-' . Str::random(8),
                    'product_name' => "Sample {$cat} Product " . ($i + 1),
                    'category' => $cat,
                    'score' => rand(75, 98),
                    'rating' => $this->getRatingByScore(rand(75, 98)),
                    'vendor' => 'Sample Vendor',
                ];
            }
        }

        // Sort by score
        usort($leaderboard, function($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        // Take top 'limit' items
        $leaderboard = array_slice($leaderboard, 0, $limit);

        return [
            'leaderboard' => $leaderboard,
            'category' => $category,
            'limit' => $limit,
            'generated_at' => now()->toISOString(),
        ];
    }
}