<?php

namespace App\Http\Controllers;

use App\Services\GreenDeliveryOptionsService;
use Illuminate\Http\Request;

class GreenDeliveryOptionsController extends Controller
{
    private GreenDeliveryOptionsService $deliveryService;

    public function __construct()
    {
        $this->deliveryService = new GreenDeliveryOptionsService();
    }

    /**
     * Calculate green delivery options for an order.
     */
    public function calculateGreenOptions(Request $request)
    {
        $request->validate([
            'order_id' => 'string',
            'weight_kg' => 'required|numeric|min:0.1',
            'volume_m3' => 'required|numeric|min:0.001',
            'origin' => 'required|array',
            'origin.lat' => 'required|numeric|between:-90,90',
            'origin.lng' => 'required|numeric|between:-180,180',
            'destination' => 'required|array',
            'destination.lat' => 'required|numeric|between:-90,90',
            'destination.lng' => 'required|numeric|between:-180,180',
            'delivery_window' => 'string',
            'item_category' => 'string',
        ]);

        try {
            $options = $this->deliveryService->calculateGreenOptions($request->all());

            return response()->json([
                'options' => $options,
                'message' => 'Green delivery options calculated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get all green delivery methods.
     */
    public function getDeliveryMethods()
    {
        $methods = $this->deliveryService->getDeliveryMethods();

        return response()->json([
            'methods' => $methods,
            'message' => 'Green delivery methods retrieved successfully'
        ]);
    }

    /**
     * Get eco-friendly packaging options.
     */
    public function getEcoPackagingOptions()
    {
        $options = $this->deliveryService->getEcoPackagingOptions();

        return response()->json([
            'packaging_options' => $options,
            'message' => 'Eco-friendly packaging options retrieved successfully'
        ]);
    }

    /**
     * Get zone-specific delivery recommendations.
     */
    public function getZoneRecommendations(Request $request)
    {
        $request->validate([
            'density' => 'required|string|in:high,medium,low',
            'distance' => 'required|string|in:short,medium,long',
            'location_type' => 'string',
        ]);

        $locationData = $request->all();
        $recommendations = $this->deliveryService->getZoneRecommendations($locationData);

        return response()->json([
            'recommendations' => $recommendations,
            'message' => 'Zone-specific delivery recommendations retrieved successfully'
        ]);
    }

    /**
     * Calculate environmental benefit of green delivery.
     */
    public function calculateEnvironmentalBenefit(Request $request)
    {
        $request->validate([
            'order_id' => 'string',
            'weight_kg' => 'required|numeric|min:0.1',
            'origin' => 'required|array',
            'origin.lat' => 'required|numeric|between:-90,90',
            'origin.lng' => 'required|numeric|between:-180,180',
            'destination' => 'required|array',
            'destination.lat' => 'required|numeric|between:-90,90',
            'destination.lng' => 'required|numeric|between:-180,180',
            'green_method' => 'required|string',
            'standard_method' => 'string',
        ]);

        try {
            $benefit = $this->deliveryService->calculateEnvironmentalBenefit(
                $request->all(),
                $request->green_method,
                $request->standard_method ?? 'standard_truck'
            );

            return response()->json([
                'benefit' => $benefit,
                'message' => 'Environmental benefit calculated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get carbon offset options.
     */
    public function getCarbonOffsetOptions(Request $request)
    {
        $request->validate([
            'co2_kg' => 'required|numeric|min:0',
        ]);

        $offsets = $this->deliveryService->getCarbonOffsetOptions($request->co2_kg);

        return response()->json([
            'offsets' => $offsets,
            'message' => 'Carbon offset options retrieved successfully'
        ]);
    }

    /**
     * Create a green delivery promise.
     */
    public function createDeliveryPromise(Request $request)
    {
        $request->validate([
            'order_id' => 'string',
            'weight_kg' => 'required|numeric|min:0.1',
            'origin' => 'required|array',
            'origin.lat' => 'required|numeric|between:-90,90',
            'origin.lng' => 'required|numeric|between:-180,180',
            'destination' => 'required|array',
            'destination.lat' => 'required|numeric|between:-90,90',
            'destination.lng' => 'required|numeric|between:-180,180',
            'delivery_method' => 'required|string',
        ]);

        try {
            $promise = $this->deliveryService->createDeliveryPromise($request->all(), $request->delivery_method);

            return response()->json([
                'promise' => $promise,
                'message' => 'Delivery promise created successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get delivery method by ID.
     */
    public function getDeliveryMethod(Request $request, string $methodId)
    {
        $method = $this->deliveryService->getDeliveryMethod($methodId);

        if (!$method) {
            return response()->json([
                'error' => 'Delivery method not found'
            ], 404);
        }

        return response()->json([
            'method' => $method,
            'message' => 'Delivery method retrieved successfully'
        ]);
    }
}