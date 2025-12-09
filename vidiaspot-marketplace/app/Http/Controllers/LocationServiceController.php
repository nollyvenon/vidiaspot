<?php

namespace App\Http\Controllers;

use App\Services\LocationService;
use App\Models\Location;
use App\Models\CourierPartner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LocationServiceController extends Controller
{
    protected $locationService;

    public function __construct(LocationService $locationService)
    {
        $this->locationService = $locationService;
    }

    /**
     * Get optimal delivery route
     */
    public function calculateOptimalRoute(Request $request)
    {
        $request->validate([
            'pickup_location' => 'required|array',
            'pickup_location.latitude' => 'required|numeric',
            'pickup_location.longitude' => 'required|numeric',
            'delivery_location' => 'required|array',
            'delivery_location.latitude' => 'required|numeric',
            'delivery_location.longitude' => 'required|numeric',
            'additional_stops' => 'array',
            'additional_stops.*.latitude' => 'required|numeric',
            'additional_stops.*.longitude' => 'required|numeric',
        ]);

        $route = $this->locationService->calculateOptimalRoute(
            $request->pickup_location,
            $request->delivery_location,
            $request->additional_stops ?? []
        );

        return response()->json([
            'success' => true,
            'route' => $route
        ]);
    }

    /**
     * Get indoor mapping for a location
     */
    public function getIndoorMapping(Request $request, $locationId)
    {
        $request->validate([
            'floor_index' => 'integer|min:0|default:0',
        ]);

        $mapping = $this->locationService->generateIndoorMapping($locationId, $request->floor_index ?? 0);

        return response()->json([
            'success' => true,
            'indoor_mapping' => $mapping
        ]);
    }

    /**
     * Get real-time delivery tracking
     */
    public function getRealTimeTracking($orderId)
    {
        $tracking = $this->locationService->getRealTimeDeliveryTracking($orderId);

        return response()->json([
            'success' => true,
            'tracking' => $tracking
        ]);
    }

    /**
     * Get nearby pickup points
     */
    public function getNearbyPickupPoints(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius_km' => 'integer|min:1|max:50|default:10',
            'limit' => 'integer|min:1|max:50|default:10',
        ]);

        $pickupPoints = $this->locationService->getNearbyPickupPoints(
            $request->latitude,
            $request->longitude,
            $request->radius_km,
            $request->limit
        );

        return response()->json([
            'success' => true,
            'pickup_points' => $pickupPoints,
            'count' => count($pickupPoints)
        ]);
    }

    /**
     * Get local courier marketplace
     */
    public function getLocalCourierMarketplace(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius_km' => 'integer|min:1|max:100|default:25',
        ]);

        $couriers = $this->locationService->getLocalCourierMarketplace(
            $request->latitude,
            $request->longitude,
            $request->radius_km
        );

        return response()->json([
            'success' => true,
            'couriers' => $couriers,
            'count' => count($couriers)
        ]);
    }

    /**
     * Check if same-day delivery is available for location
     */
    public function checkSameDayDelivery(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $availability = $this->locationService->isSameDayDeliveryAvailable(
            $request->latitude,
            $request->longitude
        );

        return response()->json([
            'success' => true,
            'same_day_delivery' => $availability
        ]);
    }

    /**
     * Generate cold chain configuration
     */
    public function generateColdChainConfig(Request $request)
    {
        $request->validate([
            'item_type' => 'required|in:food,medical,pharmaceutical,perishable,dairy,frozen,electronics',
            'quantity' => 'required|integer|min:1',
            'delivery_options' => 'array',
        ]);

        $config = $this->locationService->generateColdChainConfig(
            $request->item_type,
            $request->quantity,
            $request->delivery_options ?? []
        );

        return response()->json([
            'success' => true,
            'cold_chain_config' => $config
        ]);
    }

    /**
     * Calculate international shipping costs
     */
    public function calculateInternationalShipping(Request $request)
    {
        $request->validate([
            'destination_country' => 'required|string',
            'weight_kg' => 'required|numeric|min:0.1',
            'dimensions' => 'required|array',
            'dimensions.length' => 'required|numeric|min:1',
            'dimensions.width' => 'required|numeric|min:1',
            'dimensions.height' => 'required|numeric|min:1',
            'item_value' => 'required|numeric|min:100',
            'shipping_class' => 'in:standard,express,economy,priority|default:standard',
        ]);

        $shippingCosts = $this->locationService->calculateInternationalShipping(
            $request->destination_country,
            $request->weight_kg,
            $request->dimensions,
            $request->item_value,
            $request->shipping_class
        );

        return response()->json([
            'success' => true,
            'shipping_costs' => $shippingCosts
        ]);
    }

    /**
     * Get all pickup points with filters
     */
    public function getAllPickupPoints(Request $request)
    {
        $filters = $request->only(['city', 'state', 'delivery_zone', 'features']);

        $pickupPoints = $this->locationService->getAllPickupPoints($filters);

        return response()->json([
            'success' => true,
            'pickup_points' => $pickupPoints,
            'count' => $pickupPoints->count()
        ]);
    }

    /**
     * Get location delivery availability
     */
    public function getLocationDeliveryAvailability($locationId)
    {
        $availability = $this->locationService->getLocationDeliveryAvailability($locationId);

        return response()->json([
            'success' => true,
            'availability' => $availability
        ]);
    }

    /**
     * Get delivery zones for an area
     */
    public function getDeliveryZones(Request $request)
    {
        $query = Location::select('delivery_zone')
                        ->where('delivery_zone', '!=', null)
                        ->where('is_active', true)
                        ->distinct();

        if ($request->has('city')) {
            $query = $query->where('city', $request->city);
        }

        if ($request->has('state')) {
            $query = $query->where('state', $request->state);
        }

        $zones = $query->pluck('delivery_zone')->toArray();

        return response()->json([
            'success' => true,
            'delivery_zones' => $zones
        ]);
    }

    /**
     * Get delivery time estimates for zones
     */
    public function getDeliveryTimeEstimates(Request $request)
    {
        $request->validate([
            'origin_zone' => 'required|string',
            'destination_zone' => 'required|string',
        ]);

        // In a real implementation, this would use actual delivery time estimates based on zones
        // For demonstration, we'll simulate data based on zone distance
        $timeEstimate = mt_rand(24, 72); // Hours between zones

        return response()->json([
            'success' => true,
            'origin_zone' => $request->origin_zone,
            'destination_zone' => $request->destination_zone,
            'estimated_delivery_time_hours' => $timeEstimate,
            'estimated_delivery_time_formatted' => $this->formatHoursToDays($timeEstimate),
            'shipping_options' => [
                'standard' => $timeEstimate,
                'express' => floor($timeEstimate / 2),
                'overnight' => 24,
            ],
        ]);
    }

    /**
     * Format hours to days/hours string
     */
    private function formatHoursToDays($hours)
    {
        $days = floor($hours / 24);
        $remainingHours = $hours % 24;
        $result = "";

        if ($days > 0) {
            $result .= "{$days} day" . ($days > 1 ? 's' : '');
        }

        if ($remainingHours > 0) {
            if ($result) {
                $result .= " ";
            }
            $result .= "{$remainingHours} hour" . ($remainingHours > 1 ? 's' : '');
        }

        return $result;
    }
}
