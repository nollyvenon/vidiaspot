<?php

namespace App\Http\Controllers;

use App\Services\EcoFriendlyPackagingService;
use Illuminate\Http\Request;

class EcoFriendlyPackagingController extends Controller
{
    private EcoFriendlyPackagingService $packagingService;

    public function __construct()
    {
        $this->packagingService = new EcoFriendlyPackagingService();
    }

    /**
     * Get all available eco-friendly packaging options.
     */
    public function getPackagingOptions()
    {
        $options = $this->packagingService->getPackagingOptions();

        return response()->json([
            'options' => $options,
            'message' => 'Eco-friendly packaging options retrieved successfully'
        ]);
    }

    /**
     * Get packaging sizes.
     */
    public function getPackagingSizes()
    {
        $sizes = $this->packagingService->getPackagingSizes();

        return response()->json([
            'sizes' => $sizes,
            'message' => 'Packaging sizes retrieved successfully'
        ]);
    }

    /**
     * Get a specific packaging option.
     */
    public function getPackagingOption(Request $request, string $type)
    {
        $option = $this->packagingService->getPackagingOption($type);

        if (!$option) {
            return response()->json([
                'error' => 'Packaging option not found'
            ], 404);
        }

        return response()->json([
            'option' => $option,
            'message' => 'Packaging option retrieved successfully'
        ]);
    }

    /**
     * Calculate the best packaging option for an item.
     */
    public function calculateBestPackaging(Request $request)
    {
        $request->validate([
            'weight' => 'required|numeric|min:0.01',
            'fragility' => 'required|string|in:low,medium,high',
            'preferred_eco_level' => 'string|in:low,medium,high',
            'budget_constraint' => 'numeric|min:0',
        ]);

        $result = $this->packagingService->calculateBestPackaging($request->all());

        return response()->json([
            'recommendation' => $result,
            'message' => 'Best packaging option calculated successfully'
        ]);
    }

    /**
     * Calculate packaging recommendations for multiple items.
     */
    public function calculateMultipleItemPackaging(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.weight' => 'required|numeric|min:0.01',
            'items.*.fragility' => 'required|string|in:low,medium,high',
            'items.*.preferred_eco_level' => 'string|in:low,medium,high',
            'items.*.budget_constraint' => 'numeric|min:0',
        ]);

        $result = $this->packagingService->calculateMultipleItemPackaging($request->items);

        return response()->json([
            'recommendations' => $result,
            'message' => 'Packaging recommendations for multiple items calculated successfully'
        ]);
    }

    /**
     * Get carbon footprint reduction by using eco packaging.
     */
    public function getCarbonFootprintReduction(Request $request)
    {
        $request->validate([
            'packaging_type' => 'required|string',
            'standard_type' => 'string',
        ]);

        $reduction = $this->packagingService->calculateCarbonFootprintReduction(
            $request->packaging_type,
            $request->standard_type ?? 'standard_cardboard'
        );

        if (!$reduction) {
            return response()->json([
                'error' => 'Invalid packaging type(s) specified'
            ], 400);
        }

        return response()->json([
            'reduction' => $reduction,
            'message' => 'Carbon footprint reduction calculated successfully'
        ]);
    }

    /**
     * Generate packaging sustainability report.
     */
    public function generateSustainabilityReport(Request $request)
    {
        $request->validate([
            'packaging_selections' => 'required|array|min:1',
            'packaging_selections.*.type' => 'required|string',
            'packaging_selections.*.environmental_score' => 'required|numeric',
            'packaging_selections.*.cost' => 'required|numeric',
        ]);

        $report = $this->packagingService->generateSustainabilityReport($request->packaging_selections);

        return response()->json([
            'report' => $report,
            'message' => 'Sustainability report generated successfully'
        ]);
    }
}