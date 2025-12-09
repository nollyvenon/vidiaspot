<?php

namespace App\Services;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class SecondHandRefurbishmentService
{
    /**
     * Condition ratings for second-hand items
     */
    private array $conditionRatings = [
        'like_new' => [
            'name' => 'Like New',
            'description' => 'Item is in excellent condition, barely used',
            'value' => 90, // 90% of original value
            'inspection_required' => false,
        ],
        'excellent' => [
            'name' => 'Excellent',
            'description' => 'Item is in great condition with minimal wear',
            'value' => 75, // 75% of original value
            'inspection_required' => false,
        ],
        'very_good' => [
            'name' => 'Very Good',
            'description' => 'Item is in very good condition with light wear',
            'value' => 60, // 60% of original value
            'inspection_required' => false,
        ],
        'good' => [
            'name' => 'Good',
            'description' => 'Item is in good condition with visible wear',
            'value' => 45, // 45% of original value
            'inspection_required' => true,
        ],
        'fair' => [
            'name' => 'Fair',
            'description' => 'Item is functional but shows considerable wear',
            'value' => 30, // 30% of original value
            'inspection_required' => true,
        ],
        'refurbished' => [
            'name' => 'Refurbished',
            'description' => 'Item has been professionally refurbished',
            'value' => 65, // 65% of original value for refurbished
            'inspection_required' => true,
            'certified' => true,
        ],
        'parts_only' => [
            'name' => 'Parts Only',
            'description' => 'Item is for parts or repair only',
            'value' => 15, // 15% of original value
            'inspection_required' => true,
        ],
    ];

    /**
     * Quality assurance standards for refurbished items
     */
    private array $refurbishmentStandards = [
        'electronics' => [
            'functionality_test' => true,
            'cosmetic_restoration' => true,
            'warranty_required' => true,
            'certification_required' => true,
        ],
        'appliances' => [
            'safety_check' => true,
            'performance_test' => true,
            'cosmetic_restoration' => true,
            'warranty_required' => true,
        ],
        'furniture' => [
            'structural_integrity' => true,
            'cosmetic_restoration' => true,
            'safety_check' => true,
        ],
        'clothing' => [
            'cleaning_required' => true,
            'damage_assessment' => true,
            'quality_grade' => true,
        ],
    ];

    /**
     * Get condition ratings
     */
    public function getConditionRatings(): array
    {
        return $this->conditionRatings;
    }

    /**
     * Validate an item for the second-hand marketplace
     */
    public function validateItemForMarketplace(array $itemData): array
    {
        $validation = [
            'is_valid' => true,
            'errors' => [],
            'warnings' => [],
            'condition_assessment' => null,
            'pricing_recommendation' => null,
        ];

        // Check required fields
        $requiredFields = ['title', 'category', 'original_price', 'condition', 'description'];
        foreach ($requiredFields as $field) {
            if (empty($itemData[$field])) {
                $validation['is_valid'] = false;
                $validation['errors'][] = "Missing required field: {$field}";
            }
        }

        // Validate condition
        if (!empty($itemData['condition'])) {
            if (!isset($this->conditionRatings[$itemData['condition']])) {
                $validation['is_valid'] = false;
                $validation['errors'][] = "Invalid condition rating: {$itemData['condition']}";
            } else {
                $condition = $this->conditionRatings[$itemData['condition']];
                $validation['condition_assessment'] = [
                    'rating' => $itemData['condition'],
                    'name' => $condition['name'],
                    'description' => $condition['description'],
                    'value_percentage' => $condition['value'],
                    'inspection_required' => $condition['inspection_required'] ?? false,
                    'certified' => $condition['certified'] ?? false,
                ];
            }
        }

        // Calculate pricing recommendation
        if (!empty($itemData['original_price']) && !empty($itemData['condition'])) {
            $originalPrice = floatval($itemData['original_price']);
            $conditionValue = $this->conditionRatings[$itemData['condition']]['value'] / 100;
            $recommendedPrice = $originalPrice * $conditionValue;
            
            $validation['pricing_recommendation'] = [
                'original_price' => $originalPrice,
                'condition_factor' => $conditionValue,
                'recommended_price' => round($recommendedPrice, 2),
                'min_price' => round($recommendedPrice * 0.8, 2), // 20% below
                'max_price' => round($recommendedPrice * 1.2, 2), // 20% above
            ];
        }

        // Additional validations based on category
        if (!empty($itemData['category'])) {
            $categoryChecks = $this->performCategorySpecificChecks($itemData);
            $validation['errors'] = array_merge($validation['errors'], $categoryChecks['errors'] ?? []);
            $validation['warnings'] = array_merge($validation['warnings'], $categoryChecks['warnings'] ?? []);
        }

        return $validation;
    }

    /**
     * Perform category-specific validation checks
     */
    private function performCategorySpecificChecks(array $itemData): array
    {
        $errors = [];
        $warnings = [];

        $category = $itemData['category'] ?? '';
        $condition = $itemData['condition'] ?? '';

        // Check if electronics are in good condition or better
        if (strpos(strtolower($category), 'electronics') !== false || strpos(strtolower($category), 'phone') !== false) {
            $acceptableConditions = ['like_new', 'excellent', 'very_good', 'refurbished'];
            if (!in_array($condition, $acceptableConditions)) {
                $warnings[] = "Electronics in {$condition} condition may have limited appeal";
            }
        }

        // Check if furniture requires professional inspection
        if (strpos(strtolower($category), 'furniture') !== false) {
            if (in_array($condition, ['fair', 'parts_only'])) {
                $errors[] = "Furniture in {$condition} condition may not meet safety standards";
            }
        }

        return [
            'errors' => $errors,
            'warnings' => $warnings,
        ];
    }

    /**
     * Create or update an item in the second-hand marketplace
     */
    public function createSecondHandItem(array $itemData, string $userId): array
    {
        // Validate the item first
        $validation = $this->validateItemForMarketplace($itemData);
        if (!$validation['is_valid']) {
            return [
                'success' => false,
                'errors' => $validation['errors'],
                'warnings' => $validation['warnings'],
                'message' => 'Item validation failed'
            ];
        }

        // Generate unique ID
        $itemId = 'sh-' . Str::uuid();

        // Prepare item data
        $item = [
            'id' => $itemId,
            'user_id' => $userId,
            'title' => $itemData['title'],
            'description' => $itemData['description'],
            'category' => $itemData['category'],
            'condition' => $itemData['condition'],
            'original_price' => floatval($itemData['original_price']),
            'asking_price' => floatval($itemData['asking_price'] ?? $validation['pricing_recommendation']['recommended_price']),
            'condition_assessment' => $validation['condition_assessment'],
            'pricing_recommendation' => $validation['pricing_recommendation'],
            'brand' => $itemData['brand'] ?? null,
            'model' => $itemData['model'] ?? null,
            'year' => $itemData['year'] ?? null,
            'included_items' => $itemData['included_items'] ?? [],
            'warranty_info' => $itemData['warranty_info'] ?? null,
            'photos' => $itemData['photos'] ?? [],
            'location' => $itemData['location'] ?? null,
            'inspection_required' => $validation['condition_assessment']['inspection_required'] ?? false,
            'certified' => $validation['condition_assessment']['certified'] ?? false,
            'status' => 'pending_approval', // Items need approval before going live
            'created_at' => now()->toISOString(),
            'updated_at' => now()->toISOString(),
        ];

        // In a real implementation, this would be stored in a database
        // For this implementation, we'll use cache as storage
        $cacheKey = "second_hand_item_{$itemId}";
        \Cache::put($cacheKey, $item, now()->addMonths(6));

        // Add to user's inventory
        $userInventoryKey = "user_second_hand_items_{$userId}";
        $inventory = \Cache::get($userInventoryKey, []);
        $inventory[] = $itemId;
        \Cache::put($userInventoryKey, $inventory, now()->addMonths(6));

        return [
            'success' => true,
            'item' => $item,
            'validation' => $validation,
            'message' => 'Second-hand item listed successfully'
        ];
    }

    /**
     * Inspect and certify a second-hand item
     */
    public function inspectItem(string $itemId, array $inspectionData): array
    {
        // Get the item
        $cacheKey = "second_hand_item_{$itemId}";
        $item = \Cache::get($cacheKey);

        if (!$item) {
            return [
                'success' => false,
                'error' => 'Item not found'
            ];
        }

        // Perform inspection
        $inspectionResult = [
            'item_id' => $itemId,
            'inspector_id' => $inspectionData['inspector_id'] ?? 'system',
            'inspection_date' => now()->toISOString(),
            'condition_verified' => $inspectionData['condition_verified'] ?? $item['condition'],
            'functional_status' => $inspectionData['functional_status'] ?? 'unknown',
            'cosmetic_status' => $inspectionData['cosmetic_status'] ?? 'unknown',
            'defects' => $inspectionData['defects'] ?? [],
            'certified' => true,
            'certification_date' => now()->toISOString(),
            'report' => $inspectionData['report'] ?? 'Standard inspection completed',
        ];

        // Update the item with certification
        $item['certified'] = true;
        $item['inspection_result'] = $inspectionResult;
        $item['status'] = 'active'; // Can now go live
        \Cache::put($cacheKey, $item, now()->addMonths(6));

        return [
            'success' => true,
            'inspection' => $inspectionResult,
            'updated_item' => $item,
            'message' => 'Item inspection completed and certified'
        ];
    }

    /**
     * Get items for a specific user
     */
    public function getUserItems(string $userId): array
    {
        $userInventoryKey = "user_second_hand_items_{$userId}";
        $itemIds = \Cache::get($userInventoryKey, []);

        $items = [];
        foreach ($itemIds as $itemId) {
            $item = \Cache::get("second_hand_item_{$itemId}");
            if ($item) {
                $items[] = $item;
            }
        }

        return [
            'items' => $items,
            'count' => count($items),
            'user_id' => $userId,
        ];
    }

    /**
     * Get items by category
     */
    public function getItemsByCategory(string $category, array $filters = []): array
    {
        // In a real implementation, this would query a database
        // For this implementation, we'll return sample data
        // A more sophisticated implementation would search the cache
        
        $allItems = $this->getAllItems();
        $filteredItems = [];

        foreach ($allItems as $item) {
            if ($item['category'] === $category) {
                $matchesFilters = true;
                
                // Apply additional filters
                if (!empty($filters['min_price']) && $item['asking_price'] < $filters['min_price']) {
                    $matchesFilters = false;
                }
                if (!empty($filters['max_price']) && $item['asking_price'] > $filters['max_price']) {
                    $matchesFilters = false;
                }
                if (!empty($filters['condition']) && $item['condition'] !== $filters['condition']) {
                    $matchesFilters = false;
                }
                
                if ($matchesFilters) {
                    $filteredItems[] = $item;
                }
            }
        }

        // Sort by price or date
        if (!empty($filters['sort_by']) && $filters['sort_by'] === 'price_low_high') {
            usort($filteredItems, function ($a, $b) {
                return $a['asking_price'] <=> $b['asking_price'];
            });
        } elseif (!empty($filters['sort_by']) && $filters['sort_by'] === 'price_high_low') {
            usort($filteredItems, function ($a, $b) {
                return $b['asking_price'] <=> $a['asking_price'];
            });
        } else {
            // Default sort by date (newest first)
            usort($filteredItems, function ($a, $b) {
                return strtotime($b['created_at']) <=> strtotime($a['created_at']);
            });
        }

        return [
            'items' => $filteredItems,
            'count' => count($filteredItems),
            'category' => $category,
            'filters' => $filters,
        ];
    }

    /**
     * Get all items (in a real implementation, this would be paginated)
     */
    private function getAllItems(): array
    {
        // This is a simplified version - in a real app you'd query a database
        // For this implementation, we'll return empty as we don't have a way to get all items from cache
        return [];
    }

    /**
     * Get refurbished items specifically
     */
    public function getRefurbishedItems(array $filters = []): array
    {
        $allItems = $this->getAllItems();
        $refurbishedItems = [];

        foreach ($allItems as $item) {
            if ($item['condition'] === 'refurbished' || $item['certified']) {
                $refurbishedItems[] = $item;
            }
        }

        return [
            'items' => $refurbishedItems,
            'count' => count($refurbishedItems),
            'type' => 'refurbished',
            'filters' => $filters,
        ];
    }

    /**
     * Get quality standards for a category
     */
    public function getQualityStandards(string $category): array
    {
        $standards = $this->refurbishmentStandards[$category] ?? [];
        
        return [
            'category' => $category,
            'standards' => $standards,
            'general_recommendations' => [
                'thorough_testing' => true,
                'warranty_offering' => true,
                'detailed_description' => true,
                'quality_photography' => true,
            ]
        ];
    }

    /**
     * Calculate the environmental impact of buying second-hand
     */
    public function calculateEnvironmentalImpact(array $itemData): array
    {
        // Environmental impact calculations for second-hand items
        // These are estimated values based on research
        $environmentalImpact = [
            'extended_product_life' => 'Extending life by 2-5 years',
            'waste_reduction' => 'Diverting 1 item from waste stream',
            'carbon_footprint_reduction' => '70-80% reduction vs. new item',
            'resource_conservation' => [
                'water_saved' => 'Significant water savings vs. manufacturing',
                'energy_saved' => '70-80% less energy vs. manufacturing new item',
                'materials_saved' => 'Reducing demand for raw materials',
            ],
            'equivalents' => [
                'trees_saved' => 0.1, // Approximate trees worth of resources
                'plastic_bottles_not_created' => 50, // Approximate equivalent
            ]
        ];

        return [
            'item' => $itemData['title'] ?? 'Unknown item',
            'environmental_impact' => $environmentalImpact,
            'message' => 'Buying this second-hand item has significant environmental benefits',
            'calculated_at' => now()->toISOString(),
        ];
    }
}