<?php

namespace App\Services;

use App\Models\Drone;
use App\Models\DroneMission;
use App\Models\Order;
use App\Models\CourierPartner;
use Illuminate\Support\Facades\Auth;

class DroneDeliveryService
{
    public function assignDroneToOrder($orderId, $droneId = null)
    {
        $order = Order::find($orderId);
        if (!$order) {
            return null;
        }

        // If no drone specified, find an available one
        if (!$droneId) {
            $drone = $this->findAvailableDrone($order);
            if (!$drone) {
                return null;
            }
            $droneId = $drone->id;
        } else {
            $drone = Drone::find($droneId);
            if (!$drone || !$drone->is_available) {
                return null;
            }
        }

        // Create a new mission
        $mission = DroneMission::create([
            'drone_id' => $droneId,
            'order_id' => $orderId,
            'delivery_address_id' => $order->delivery_address_id, // assuming this exists
            'mission_type' => 'delivery',
            'status' => 'pending',
            'origin_lat' => $order->store_location_lat, // assuming these exist
            'origin_lng' => $order->store_location_lng,
            'destination_lat' => $order->delivery_address->lat, // assuming address model
            'destination_lng' => $order->delivery_address->lng,
            'payload_weight' => $this->calculatePayloadWeight($order),
            'estimated_duration' => $this->calculateEstimatedDuration($order),
            'distance' => $this->calculateDistance($order),
            'weather_conditions' => $this->getWeatherConditions($order->delivery_address),
            'battery_at_departure' => $drone->battery_level,
        ]);

        // Update drone status
        $drone->update([
            'is_available' => false,
            'current_mission_id' => $mission->id,
        ]);

        return $mission;
    }

    public function startMission($missionId)
    {
        $mission = DroneMission::find($missionId);
        if (!$mission || $mission->status !== 'pending') {
            return null;
        }

        $mission->update([
            'status' => 'in_progress',
            'actual_departure_time' => now(),
            'estimated_arrival_time' => now()->addMinutes($mission->estimated_duration),
        ]);

        return $mission;
    }

    public function updateMissionTracking($missionId, $trackingData)
    {
        $mission = DroneMission::find($missionId);
        if (!$mission) {
            return null;
        }

        $trackingData['timestamp'] = now();
        
        if (!is_array($mission->tracking_data)) {
            $mission->tracking_data = [];
        }
        
        $mission->tracking_data[] = $trackingData;
        $mission->save();

        return $mission;
    }

    public function completeMission($missionId)
    {
        $mission = DroneMission::find($missionId);
        if (!$mission) {
            return null;
        }

        $drone = $mission->drone;

        $mission->update([
            'status' => 'completed',
            'actual_arrival_time' => now(),
            'actual_duration' => $this->calculateActualDuration($mission),
            'battery_at_arrival' => $drone->battery_level,
            'completed_at' => now(),
        ]);

        // Update drone status
        $drone->update([
            'is_available' => true,
            'current_mission_id' => null,
        ]);

        return $mission;
    }

    public function cancelMission($missionId)
    {
        $mission = DroneMission::find($missionId);
        if (!$mission) {
            return null;
        }

        $drone = $mission->drone;

        $mission->update([
            'status' => 'cancelled',
        ]);

        // Update drone status
        $drone->update([
            'is_available' => true,
            'current_mission_id' => null,
        ]);

        return $mission;
    }

    public function getAvailableDrones($locationLat = null, $locationLng = null)
    {
        $query = Drone::available();

        if ($locationLat && $locationLng) {
            // In a real implementation, you'd calculate distance from the location
            // For now, we'll just return all available drones
        }

        return $query->with(['assignedCourierPartner'])->get();
    }

    public function getActiveMissions()
    {
        return DroneMission::active()
            ->with(['drone', 'order', 'deliveryAddress'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getMissionById($missionId)
    {
        return DroneMission::with(['drone', 'order', 'deliveryAddress'])->find($missionId);
    }

    public function getDroneById($droneId)
    {
        return Drone::with(['assignedCourierPartner', 'currentMission'])->find($droneId);
    }

    public function getDroneStatus($droneId)
    {
        $drone = Drone::find($droneId);
        
        if (!$drone) {
            return null;
        }

        return [
            'id' => $drone->id,
            'name' => $drone->name,
            'status' => $drone->status,
            'is_available' => $drone->is_available,
            'battery_level' => $drone->battery_level,
            'current_location' => [
                'lat' => $drone->current_location_lat,
                'lng' => $drone->current_location_lng,
            ],
            'current_mission' => $drone->current_mission_id ? $this->getMissionById($drone->current_mission_id) : null,
        ];
    }

    public function getMissionsForOrder($orderId)
    {
        return DroneMission::where('order_id', $orderId)
            ->with(['drone', 'deliveryAddress'])
            ->get();
    }

    public function getMissionTrackingData($missionId)
    {
        $mission = DroneMission::find($missionId);
        
        return $mission ? $mission->tracking_data : null;
    }

    public function scheduleMaintenance($droneId, $maintenanceData)
    {
        $drone = Drone::find($droneId);
        
        if ($drone) {
            $drone->update([
                'status' => 'maintenance',
                'is_available' => false,
                'next_maintenance_date' => $maintenanceData['date'],
                'maintenance_hours' => $drone->maintenance_hours + ($maintenanceData['hours'] ?? 0),
            ]);
        }

        return $drone;
    }

    private function findAvailableDrone($order)
    {
        // Find an available drone near the store location
        // In a real implementation, we'd calculate distances
        return Drone::available()
            ->where('max_payload', '>=', $this->calculatePayloadWeight($order))
            ->first();
    }

    private function calculatePayloadWeight($order)
    {
        // Calculate order weight based on items
        $weight = 0;
        if ($order->items) {
            foreach ($order->items as $item) {
                $weight += $item->weight * $item->quantity;
            }
        }
        return $weight;
    }

    private function calculateEstimatedDuration($order)
    {
        // Calculate estimated duration based on distance and drone speed
        // This is a simplified calculation
        $distance = $this->calculateDistance($order);
        $droneSpeed = 30; // Assuming 30 km/h average speed
        
        return ($distance / $droneSpeed) * 60; // Convert to minutes
    }

    private function calculateDistance($order)
    {
        // Calculate distance between store and delivery location
        // In a real implementation, you'd use the Haversine formula
        // For now, we'll simulate a distance calculation
        $lat1 = $order->store_location_lat;
        $lng1 = $order->store_location_lng;
        $lat2 = $order->delivery_address->lat; // Assuming address model
        $lng2 = $order->delivery_address->lng;

        // Simplified distance calculation
        $earthRadius = 6371; // km
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng/2) * sin($dLng/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        $distance = $earthRadius * $c;

        return $distance;
    }

    private function getWeatherConditions($address)
    {
        // In a real implementation, this would fetch actual weather data
        // For simulation purposes
        return [
            'temperature' => rand(15, 30),
            'wind_speed' => rand(0, 20),
            'visibility' => rand(5, 10),
            'precipitation' => rand(0, 1) > 0.5 ? 'none' : 'light',
        ];
    }

    private function calculateActualDuration($mission)
    {
        if ($mission->actual_departure_time && $mission->actual_arrival_time) {
            return $mission->actual_departure_time->diffInMinutes($mission->actual_arrival_time);
        }
        
        return 0;
    }
}