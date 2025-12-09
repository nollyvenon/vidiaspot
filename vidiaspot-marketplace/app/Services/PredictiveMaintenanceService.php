<?php

namespace App\Services;

use App\Models\PredictiveMaintenanceRecord;
use App\Models\IoTDevice;
use App\Models\Ad;
use App\Models\User;
use App\Services\AIService;
use App\Services\AnomalyDetectionService;
use Illuminate\Support\Facades\Auth;

class PredictiveMaintenanceService
{
    protected $aiService;
    protected $anomalyDetectionService;

    public function __construct(AIService $aiService, AnomalyDetectionService $anomalyDetectionService)
    {
        $this->aiService = $aiService;
        $this->anomalyDetectionService = $anomalyDetectionService;
    }

    public function analyzeDeviceForMaintenance($deviceId, $sensorData = null)
    {
        // Get the IoT device
        $iotDevice = IoTDevice::find($deviceId);
        if (!$iotDevice) {
            return null;
        }

        // If no sensor data provided, get it from the device
        if (!$sensorData) {
            $sensorData = $this->getDeviceSensorData($iotDevice);
        }

        // Use anomaly detection to identify unusual patterns
        $anomalies = $this->anomalyDetectionService->detectAnomalies($sensorData, $iotDevice->device_type);

        // Use AI to predict potential failures and maintenance needs
        $aiAnalysis = $this->aiService->analyzeDevicePerformance([
            'device_type' => $iotDevice->device_type,
            'brand' => $iotDevice->brand,
            'model' => $iotDevice->model,
            'age' => $this->calculateDeviceAge($iotDevice),
            'sensor_data' => $sensorData,
            'anomalies' => $anomalies,
            'model_type' => 'predictive_maintenance',
        ]);

        // Calculate maintenance priority based on AI analysis
        $maintenancePriority = $this->calculateMaintenancePriority($aiAnalysis);

        // Create a predictive maintenance record
        $maintenanceRecord = PredictiveMaintenanceRecord::create([
            'device_id' => $iotDevice->device_id, // External device ID
            'iot_device_id' => $iotDevice->id,
            'ad_id' => $iotDevice->ad_id,
            'user_id' => $iotDevice->user_id,
            'maintenance_type' => $this->determineMaintenanceType($anomalies),
            'predicted_failure_date' => $aiAnalysis['predicted_failure_date'] ?? null,
            'confidence_level' => $aiAnalysis['confidence_level'] ?? 0.7,
            'maintenance_priority' => $maintenancePriority,
            'status' => 'pending',
            'symptoms' => $anomalies['symptoms'] ?? [],
            'detected_anomalies' => $anomalies['anomalies'] ?? [],
            'maintenance_suggestions' => $aiAnalysis['suggestions'] ?? [],
            'maintenance_cost_estimate' => $aiAnalysis['cost_estimate'] ?? 0,
            'maintenance_duration_estimate' => $aiAnalysis['duration_estimate'] ?? 0,
            'recommended_parts' => $aiAnalysis['recommended_parts'] ?? [],
            'sensor_data' => $sensorData,
            'ai_analysis' => $aiAnalysis,
        ]);

        // If high priority, notify user immediately
        if ($maintenancePriority > 7) {
            $this->notifyUserOfUrgentMaintenance($maintenanceRecord);
        }

        return $maintenanceRecord;
    }

    public function scheduleMaintenance($maintenanceRecordId, $scheduleDate, $userId = null)
    {
        $userId = $userId ?: Auth::id();
        
        $record = PredictiveMaintenanceRecord::where('id', $maintenanceRecordId)
            ->where('user_id', $userId)
            ->first();

        if ($record) {
            $record->update([
                'maintenance_schedule_date' => $scheduleDate,
                'status' => 'scheduled',
            ]);
        }

        return $record;
    }

    public function markMaintenanceCompleted($maintenanceRecordId, $performedBy = null, $cost = null, $feedback = null, $userId = null)
    {
        $userId = $userId ?: Auth::id();
        
        $record = PredictiveMaintenanceRecord::where('id', $maintenanceRecordId)
            ->where('user_id', $userId)
            ->first();

        if ($record) {
            $record->update([
                'actual_maintenance_date' => now(),
                'maintenance_performed_by' => $performedBy,
                'maintenance_cost_actual' => $cost,
                'resolved' => true,
                'feedback' => $feedback,
                'status' => 'completed',
            ]);
        }

        return $record;
    }

    public function getMaintenanceRecommendations($userId = null, $filters = [])
    {
        $userId = $userId ?: Auth::id();
        
        $query = PredictiveMaintenanceRecord::where('user_id', $userId)
            ->with(['iotDevice', 'ad']);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['priority_min'])) {
            $query->where('maintenance_priority', '>=', $filters['priority_min']);
        }

        if (isset($filters['device_type'])) {
            $query->whereHas('iotDevice', function($q) use ($filters) {
                $q->where('device_type', $filters['device_type']);
            });
        }

        return $query->orderBy('maintenance_priority', 'desc')
            ->orderBy('predicted_failure_date', 'asc')
            ->get();
    }

    public function getUrgentMaintenanceNeeds($userId = null)
    {
        $userId = $userId ?: Auth::id();
        
        return PredictiveMaintenanceRecord::where('user_id', $userId)
            ->where('maintenance_priority', '>', 7)
            ->where('status', 'pending')
            ->with(['iotDevice', 'ad'])
            ->orderBy('maintenance_priority', 'desc')
            ->get();
    }

    public function getMaintenanceHistory($userId = null, $deviceId = null)
    {
        $userId = $userId ?: Auth::id();
        
        $query = PredictiveMaintenanceRecord::where('user_id', $userId);

        if ($deviceId) {
            $query->where('iot_device_id', $deviceId);
        }

        return $query->where('resolved', true)
            ->with(['iotDevice', 'ad'])
            ->orderBy('actual_maintenance_date', 'desc')
            ->get();
    }

    public function getPredictiveInsights($userId = null)
    {
        $userId = $userId ?: Auth::id();
        
        $records = PredictiveMaintenanceRecord::where('user_id', $userId)
            ->with(['iotDevice', 'ad'])
            ->get();

        // Group by device type
        $byType = $records->groupBy('iotDevice.device_type');

        // Calculate statistics
        $stats = [
            'total_devices_monitored' => $records->unique('iot_device_id')->count(),
            'devices_needing_maintenance' => $records->where('status', 'pending')->unique('iot_device_id')->count(),
            'high_priority_issues' => $records->where('maintenance_priority', '>', 8)->count(),
            'average_confidence_level' => $records->avg('confidence_level'),
            'estimated_cost' => $records->where('status', 'pending')->sum('maintenance_cost_estimate'),
            'by_type' => $byType->map(function($items) {
                return [
                    'count' => $items->count(),
                    'avg_priority' => $items->avg('maintenance_priority'),
                    'high_priority' => $items->where('maintenance_priority', '>', 7)->count(),
                ];
            }),
        ];

        return $stats;
    }

    public function predictMaintenanceForAd($adId)
    {
        $ad = Ad::find($adId);
        if (!$ad) {
            return null;
        }

        // Find IoT devices associated with this ad
        $iotDevices = IoTDevice::where('ad_id', $adId)->get();

        $allPredictions = collect();
        foreach ($iotDevices as $device) {
            $sensorData = $this->getDeviceSensorData($device);
            $prediction = $this->analyzeDeviceForMaintenance($device->id, $sensorData);
            if ($prediction) {
                $allPredictions->push($prediction);
            }
        }

        return $allPredictions;
    }

    public function getMaintenanceAlerts($userId = null)
    {
        $userId = $userId ?: Auth::id();
        
        return PredictiveMaintenanceRecord::where('user_id', $userId)
            ->where('status', 'pending')
            ->where('maintenance_priority', '>', 6)
            ->where('predicted_failure_date', '<=', now()->addDays(30))
            ->with(['iotDevice', 'ad'])
            ->orderBy('maintenance_priority', 'desc')
            ->get();
    }

    private function getDeviceSensorData($iotDevice)
    {
        // In a real implementation, this would fetch actual sensor data
        // from the IoT device or its connected sensors
        // For simulation purposes, we'll return sample data based on device type
        
        $baseData = [
            'timestamp' => now(),
            'device_id' => $iotDevice->device_id,
            'device_type' => $iotDevice->device_type,
        ];

        switch ($iotDevice->device_type) {
            case 'refrigerator':
                $baseData = array_merge($baseData, [
                    'temperature_internal' => rand(35, 40), // in Fahrenheit
                    'temperature_external' => rand(65, 75),
                    'compressor_runtime' => rand(70, 90), // percentage
                    'energy_consumption' => rand(1.0, 1.8), // kWh per day
                    'defrost_cycles' => rand(0, 2),
                    'vibration_level' => rand(0, 10),
                    'door_open_events' => rand(5, 15),
                ]);
                break;
            case 'washing_machine':
                $baseData = array_merge($baseData, [
                    'vibration_level' => rand(0, 10),
                    'water_level' => rand(80, 100),
                    'spin_speed' => rand(800, 1200), // RPM
                    'temperature' => rand(60, 90), // in Fahrenheit
                    'detergent_usage' => rand(0, 100),
                    'cycle_duration' => rand(30, 90), // minutes
                    'load_balance' => rand(0, 10),
                    'energy_consumption' => rand(0.5, 1.2),
                ]);
                break;
            case 'air_conditioner':
                $baseData = array_merge($baseData, [
                    'temperature_internal' => rand(68, 78),
                    'temperature_external' => rand(75, 100),
                    'compressor_runtime' => rand(20, 80),
                    'fan_speed' => rand(1, 5),
                    'filter_status' => rand(0, 100), // percentage remaining
                    'refrigerant_level' => rand(80, 100),
                    'energy_consumption' => rand(2.0, 5.0),
                    'humidity_level' => rand(30, 60),
                ]);
                break;
            default:
                $baseData = array_merge($baseData, [
                    'general_performance' => rand(0, 100),
                    'energy_consumption' => rand(0.1, 2.0),
                    'operational_time' => rand(8, 16), // hours per day
                    'temperature' => rand(20, 40), // internal temperature
                ]);
        }

        return $baseData;
    }

    private function calculateDeviceAge($iotDevice)
    {
        if ($iotDevice->registration_date) {
            return now()->diffInMonths($iotDevice->registration_date);
        }
        
        return 0;
    }

    private function calculateMaintenancePriority($aiAnalysis)
    {
        $priority = 5; // Base priority

        // Adjust based on confidence level
        if (isset($aiAnalysis['confidence_level'])) {
            $priority += ($aiAnalysis['confidence_level'] * 3);
        }

        // Adjust based on predicted failure urgency
        if (isset($aiAnalysis['predicted_failure_date'])) {
            $daysToFailure = now()->diffInDays($aiAnalysis['predicted_failure_date'], false);
            
            if ($daysToFailure <= 7) {
                $priority += 5; // Very urgent
            } elseif ($daysToFailure <= 30) {
                $priority += 3; // Somewhat urgent
            }
        }

        // Adjust based on anomaly severity
        if (isset($aiAnalysis['anomaly_severity'])) {
            $priority += ($aiAnalysis['anomaly_severity'] * 2);
        }

        return min(10, max(1, (int)round($priority)));
    }

    private function determineMaintenanceType($anomalies)
    {
        if (empty($anomalies['anomalies'])) {
            return 'routine';
        }

        $types = [];
        foreach ($anomalies['anomalies'] as $anomaly) {
            if (strpos(strtolower($anomaly), 'temperature') !== false) {
                $types[] = 'temperature';
            } elseif (strpos(strtolower($anomaly), 'vibration') !== false) {
                $types[] = 'mechanical';
            } elseif (strpos(strtolower($anomaly), 'energy') !== false) {
                $types[] = 'electrical';
            } elseif (strpos(strtolower($anomaly), 'filter') !== false) {
                $types[] = 'filter';
            }
        }

        if (empty($types)) {
            return 'general';
        }

        return implode(',', array_unique($types));
    }

    private function notifyUserOfUrgentMaintenance($maintenanceRecord)
    {
        // In a real implementation, this would send a push notification or email
        // For now, we'll log the notification
        
        \Log::info("Urgent maintenance notification sent", [
            'user_id' => $maintenanceRecord->user_id,
            'device_id' => $maintenanceRecord->iot_device_id,
            'priority' => $maintenanceRecord->maintenance_priority,
            'predicted_failure' => $maintenanceRecord->predicted_failure_date,
        ]);
    }
}