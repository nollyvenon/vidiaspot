<?php

namespace App\Http\Controllers;

use App\Services\CarbonFootprintCalculatorService;
use Illuminate\Http\Request;

class CarbonFootprintCalculatorController extends Controller
{
    private CarbonFootprintCalculatorService $calculatorService;

    public function __construct()
    {
        $this->calculatorService = new CarbonFootprintCalculatorService();
    }

    /**
     * Calculate carbon footprint for a single shipment.
     */
    public function calculateShippingFootprint(Request $request)
    {
        $request->validate([
            'weight' => 'required|numeric|min:0.01',
            'distance' => 'required|numeric|min:0.1',
            'method' => 'required|string|in:' . implode(',', array_keys($this->calculatorService->getShippingMethods())),
            'origin' => 'required|array',
            'origin.lat' => 'required_if:distance,null|numeric|between:-90,90',
            'origin.lng' => 'required_if:distance,null|numeric|between:-180,180',
            'destination' => 'required|array',
            'destination.lat' => 'required_if:distance,null|numeric|between:-90,90',
            'destination.lng' => 'required_if:distance,null|numeric|between:-180,180',
            'package_size' => 'string|in:small,medium,large,extra_large',
            'items' => 'integer|min:1',
            'temperature_controlled' => 'boolean',
        ]);

        try {
            $result = $this->calculatorService->calculateShippingFootprint($request->all());

            return response()->json([
                'success' => true,
                'data' => $result,
                'message' => 'Carbon footprint calculated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Calculate distance between two points.
     */
    public function calculateDistance(Request $request)
    {
        $request->validate([
            'origin' => 'required|array',
            'origin.lat' => 'required|numeric|between:-90,90',
            'origin.lng' => 'required|numeric|between:-180,180',
            'destination' => 'required|array',
            'destination.lat' => 'required|numeric|between:-90,90',
            'destination.lng' => 'required|numeric|between:-180,180',
        ]);

        $distance = $this->calculatorService->calculateDistance(
            $request->origin,
            $request->destination
        );

        return response()->json([
            'distance_km' => round($distance, 2),
            'distance_mi' => round($distance * 0.621371, 2),
            'origin' => $request->origin,
            'destination' => $request->destination,
        ]);
    }

    /**
     * Get available shipping methods.
     */
    public function getShippingMethods()
    {
        $methods = $this->calculatorService->getShippingMethods();

        return response()->json([
            'methods' => $methods,
            'message' => 'Shipping methods retrieved successfully'
        ]);
    }

    /**
     * Get package size options.
     */
    public function getPackageSizes()
    {
        $sizes = $this->calculatorService->getPackageSizes();

        return response()->json([
            'sizes' => $sizes,
            'message' => 'Package sizes retrieved successfully'
        ]);
    }

    /**
     * Suggest more eco-friendly shipping options.
     */
    public function suggestEcoOptions(Request $request)
    {
        $request->validate([
            'weight' => 'required|numeric|min:0.01',
            'distance' => 'required|numeric|min:0.1',
            'method' => 'required|string|in:' . implode(',', array_keys($this->calculatorService->getShippingMethods())),
            'origin' => 'required',
            'destination' => 'required',
            'package_size' => 'string|in:small,medium,large,extra_large',
            'items' => 'integer|min:1',
            'temperature_controlled' => 'boolean',
        ]);

        $suggestions = $this->calculatorService->suggestEcoOptions($request->all());

        return response()->json([
            'suggestions' => $suggestions,
            'current_method' => $request->method,
            'message' => 'Eco-friendly shipping options suggested'
        ]);
    }

    /**
     * Calculate carbon footprint for multiple shipments.
     */
    public function calculateMultipleShipments(Request $request)
    {
        $request->validate([
            'shipments' => 'required|array|min:1',
            'shipments.*.weight' => 'required|numeric|min:0.01',
            'shipments.*.distance' => 'required|numeric|min:0.1',
            'shipments.*.method' => 'required|string|in:' . implode(',', array_keys($this->calculatorService->getShippingMethods())),
            'shipments.*.origin' => 'required',
            'shipments.*.destination' => 'required',
            'shipments.*.package_size' => 'string|in:small,medium,large,extra_large',
            'shipments.*.items' => 'integer|min:1',
            'shipments.*.temperature_controlled' => 'boolean',
        ]);

        try {
            $result = $this->calculatorService->calculateMultipleShipments($request->shipments);

            return response()->json([
                'success' => true,
                'data' => $result,
                'message' => 'Multiple shipments carbon footprint calculated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get carbon footprint equivalents information.
     */
    public function getEquivalentsInfo()
    {
        // This endpoint provides information about how to interpret carbon footprint values
        $info = [
            'tree_absorption' => [
                'description' => 'Average tree absorbs 22kg CO2 per year',
                'calculation' => 'carbon_kg / 22 * 365 days',
            ],
            'car_emissions' => [
                'description' => 'Average car emits 120g CO2 per km driven',
                'calculation' => 'carbon_kg / 0.12 km',
            ],
            'gasoline' => [
                'description' => '1 gallon of gasoline produces 8.89kg CO2',
                'calculation' => 'carbon_kg / 8.89 gallons',
            ],
            'electricity' => [
                'description' => 'Average grid electricity (varies by region)',
                'calculation' => 'carbon_kg / 0.475 kWh',
            ],
        ];

        return response()->json([
            'equivalents_info' => $info,
            'message' => 'Carbon footprint equivalents information'
        ]);
    }
}