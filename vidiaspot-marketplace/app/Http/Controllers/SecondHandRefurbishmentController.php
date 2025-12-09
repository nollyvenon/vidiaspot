<?php

namespace App\Http\Controllers;

use App\Services\SecondHandRefurbishmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SecondHandRefurbishmentController extends Controller
{
    private SecondHandRefurbishmentService $marketplaceService;

    public function __construct()
    {
        $this->marketplaceService = new SecondHandRefurbishmentService();
    }

    /**
     * Get condition ratings for second-hand items.
     */
    public function getConditionRatings()
    {
        $ratings = $this->marketplaceService->getConditionRatings();

        return response()->json([
            'ratings' => $ratings,
            'message' => 'Condition ratings retrieved successfully'
        ]);
    }

    /**
     * Validate an item for the second-hand marketplace.
     */
    public function validateItem(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string',
            'original_price' => 'required|numeric|min:0',
            'condition' => 'required|string',
            'description' => 'required|string',
            'brand' => 'string',
            'model' => 'string',
            'year' => 'integer|min:1900|max:' . (date('Y') + 1),
            'included_items' => 'array',
            'photos' => 'array',
        ]);

        $validationResult = $this->marketplaceService->validateItemForMarketplace($request->all());

        return response()->json([
            'validation' => $validationResult,
            'message' => 'Item validated successfully'
        ]);
    }

    /**
     * List an item on the second-hand marketplace.
     */
    public function listItem(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string',
            'original_price' => 'required|numeric|min:0',
            'condition' => 'required|string',
            'description' => 'required|string',
            'asking_price' => 'numeric|min:0',
            'brand' => 'string',
            'model' => 'string',
            'year' => 'integer|min:1900|max:' . (date('Y') + 1),
            'included_items' => 'array',
            'warranty_info' => 'string',
            'photos' => 'array',
            'location' => 'array',
        ]);

        $userId = Auth::id();
        $result = $this->marketplaceService->createSecondHandItem($request->all(), $userId);

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'errors' => $result['errors'],
                'warnings' => $result['warnings'],
                'message' => $result['message']
            ], 400);
        }

        return response()->json([
            'success' => true,
            'item' => $result['item'],
            'validation' => $result['validation'],
            'message' => $result['message']
        ]);
    }

    /**
     * Inspect and certify a second-hand item.
     */
    public function inspectItem(Request $request, string $itemId)
    {
        $request->validate([
            'condition_verified' => 'string',
            'functional_status' => 'string',
            'cosmetic_status' => 'string',
            'defects' => 'array',
            'report' => 'string',
            'inspector_id' => 'string',
        ]);

        $result = $this->marketplaceService->inspectItem($itemId, $request->all());

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'error' => $result['error']
            ], 400);
        }

        return response()->json([
            'success' => true,
            'inspection' => $result['inspection'],
            'updated_item' => $result['updated_item'],
            'message' => $result['message']
        ]);
    }

    /**
     * Get items for the current user.
     */
    public function getUserItems()
    {
        $userId = Auth::id();
        $items = $this->marketplaceService->getUserItems($userId);

        return response()->json([
            'items' => $items,
            'message' => 'User items retrieved successfully'
        ]);
    }

    /**
     * Get items by category.
     */
    public function getItemsByCategory(Request $request, string $category)
    {
        $request->validate([
            'min_price' => 'numeric|min:0',
            'max_price' => 'numeric|min:0',
            'condition' => 'string',
            'sort_by' => 'string|in:price_low_high,price_high_low,date_new_old,date_old_new',
        ]);

        $filters = [
            'min_price' => $request->min_price,
            'max_price' => $request->max_price,
            'condition' => $request->condition,
            'sort_by' => $request->sort_by,
        ];

        $items = $this->marketplaceService->getItemsByCategory($category, array_filter($filters));

        return response()->json([
            'items' => $items,
            'message' => 'Items retrieved successfully'
        ]);
    }

    /**
     * Get refurbished items.
     */
    public function getRefurbishedItems(Request $request)
    {
        $request->validate([
            'min_price' => 'numeric|min:0',
            'max_price' => 'numeric|min:0',
            'category' => 'string',
            'sort_by' => 'string|in:price_low_high,price_high_low,date_new_old,date_old_new',
        ]);

        $filters = [
            'min_price' => $request->min_price,
            'max_price' => $request->max_price,
            'category' => $request->category,
            'sort_by' => $request->sort_by,
        ];

        $items = $this->marketplaceService->getRefurbishedItems(array_filter($filters));

        return response()->json([
            'items' => $items,
            'message' => 'Refurbished items retrieved successfully'
        ]);
    }

    /**
     * Get quality standards for a category.
     */
    public function getQualityStandards(Request $request, string $category)
    {
        $standards = $this->marketplaceService->getQualityStandards($category);

        return response()->json([
            'standards' => $standards,
            'message' => 'Quality standards retrieved successfully'
        ]);
    }

    /**
     * Calculate environmental impact of buying second-hand.
     */
    public function calculateEnvironmentalImpact(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
        ]);

        $impact = $this->marketplaceService->calculateEnvironmentalImpact($request->all());

        return response()->json([
            'environmental_impact' => $impact,
            'message' => 'Environmental impact calculated successfully'
        ]);
    }

    /**
     * Get all second-hand items (with pagination).
     */
    public function getAllItems(Request $request)
    {
        $request->validate([
            'category' => 'string',
            'min_price' => 'numeric|min:0',
            'max_price' => 'numeric|min:0',
            'condition' => 'string',
            'sort_by' => 'string|in:price_low_high,price_high_low,date_new_old,date_old_new',
            'page' => 'integer|min:1',
            'per_page' => 'integer|min:1|max:100',
        ]);

        $page = $request->page ?? 1;
        $perPage = $request->per_page ?? 15;

        // In a real implementation, this would query the database with pagination
        // For this implementation, we'll return sample data

        return response()->json([
            'items' => [], // Will be populated in a real implementation
            'pagination' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => 0, // Will be populated in a real implementation
            ],
            'message' => 'All second-hand items retrieved successfully'
        ]);
    }
}