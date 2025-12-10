<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeliveryOrder extends Model
{
    protected $fillable = [
        'user_id',
        'order_id',
        'order_number',
        'pickup_address_id',
        'delivery_address_id',
        'courier_partner_id', // ID of the courier partner handling the delivery
        'delivery_status', // pending, assigned, picked_up, in_transit, out_for_delivery, delivered, failed, returned
        'delivery_type', // same_day, next_day, standard, express, scheduled
        'delivery_service_type', // standard, express, premium, eco-friendly, cold_chain
        'package_weight_kg',
        'package_dimensions', // {length, width, height} in cm
        'package_value',
        'estimated_delivery_time',
        'actual_delivery_time',
        'delivery_distance_km',
        'delivery_route_data', // Route information for optimization
        'delivery_cost',
        'carbon_emissions', // Calculated carbon emissions for eco-tracking
        'is_cash_on_delivery',
        'cod_amount',
        'recipient_signature_required',
        'delivery_instructions',
        'special_handling_notes', // Fragile, refrigerated, etc.
        'tracking_number',
        'current_location_latitude',
        'current_location_longitude',
        'last_known_location',
        'eta_timestamp',
        'delivery_window_start',
        'delivery_window_end',
        'delivery_confirmation_token',
        'signature_image_path', // Path to recipient signature
        'photo_on_delivery_path', // Path to delivery photo
        'delivery_attempt_count',
        'delivery_attempts_log',
        'delivery_partner_rating',
        'delivery_notes',
        'insurance_covered',
        'insurance_claim_initiated',
        'insurance_claim_amount',
        'insurance_claim_status', // 'pending', 'approved', 'rejected'
        'delivery_zone',
        'pickup_point_id', // If pickup point used
        'delivery_hub_id', // Which delivery hub handles this
        'delivery_partner_commission', // Commission for delivery partner
        'pickup_time',
        'delivery_deadline',
        'temperature_control_required', // For cold chain deliveries
        'temperature_log', // Temperature logs for cold chain
        'package_inspection_required',
        'package_inspection_result',
        'payment_status', // 'pending', 'completed', 'failed'
        'return_requested',
        'return_reason',
        'return_initiated_at',
        'return_shipped_at',
        'return_delivered_at',
        'return_tracking_number',
        'warehouse_id', // For items coming from specific warehouses
        'assigned_driver_id', // ID of the driver assigned
        'driver_name',
        'driver_phone',
        'driver_rating',
        'driver_vehicle_info', // Vehicle type and license plate
        'delivery_completed_by',
        'delivered_at',
        'custom_fields',
        'metadata',
    ];

    protected $casts = [
        'estimated_delivery_time' => 'datetime',
        'actual_delivery_time' => 'datetime',
        'delivery_distance_km' => 'decimal:2',
        'package_weight_kg' => 'decimal:2',
        'package_dimensions' => 'array',
        'package_value' => 'decimal:2',
        'delivery_cost' => 'decimal:2',
        'carbon_emissions' => 'decimal:2',
        'delivery_route_data' => 'array',
        'cod_amount' => 'decimal:2',
        'current_location_latitude' => 'decimal:8',
        'current_location_longitude' => 'decimal:8',
        'eta_timestamp' => 'datetime',
        'delivery_window_start' => 'datetime',
        'delivery_window_end' => 'datetime',
        'delivery_attempt_count' => 'integer',
        'delivery_attempts_log' => 'array',
        'delivery_partner_rating' => 'decimal:2',
        'insurance_covered' => 'boolean',
        'is_cash_on_delivery' => 'boolean',
        'recipient_signature_required' => 'boolean',
        'special_handling_notes' => 'array',
        'delivery_notes' => 'string',
        'insurance_claim_amount' => 'decimal:2',
        'temperature_control_required' => 'boolean',
        'temperature_log' => 'array',
        'package_inspection_required' => 'boolean',
        'package_inspection_result' => 'array',
        'payment_status' => 'string',
        'return_requested' => 'boolean',
        'return_initiated_at' => 'datetime',
        'return_shipped_at' => 'datetime',
        'return_delivered_at' => 'datetime',
        'pickup_time' => 'datetime',
        'delivery_deadline' => 'datetime',
        'delivered_at' => 'datetime',
        'custom_fields' => 'array',
        'metadata' => 'array',
    ];

    /**
     * Get the user who requested the delivery
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the order that this delivery is for
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Order::class);
    }

    /**
     * Get the pickup address
     */
    public function pickupAddress(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'pickup_address_id');
    }

    /**
     * Get the delivery address
     */
    public function deliveryAddress(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'delivery_address_id');
    }

    /**
     * Get the assigned delivery partner
     */
    public function courierPartner(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Logistics\CourierPartner::class, 'courier_partner_id');
    }

    /**
     * Get the warehouse if applicable
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'warehouse_id');
    }

    /**
     * Get the assigned driver
     */
    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_driver_id');
    }

    /**
     * Scope to get by delivery status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('delivery_status', $status);
    }

    /**
     * Scope to get by delivery type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('delivery_type', $type);
    }

    /**
     * Scope to get by delivery zone
     */
    public function scopeByZone($query, $zone)
    {
        return $query->where('delivery_zone', $zone);
    }

    /**
     * Scope to get by date range
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope to get for a specific user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to get same-day deliveries
     */
    public function scopeSameDay($query)
    {
        return $query->where('delivery_type', 'same_day');
    }

    /**
     * Scope to get cold chain deliveries
     */
    public function scopeColdChain($query)
    {
        return $query->where('temperature_control_required', true);
    }

    /**
     * Check if delivery is currently active (not completed or failed)
     */
    public function isActive(): bool
    {
        return !in_array($this->delivery_status, ['delivered', 'failed', 'returned', 'cancelled']);
    }

    /**
     * Check if delivery is in transit
     */
    public function isInTransit(): bool
    {
        return in_array($this->delivery_status, ['assigned', 'picked_up', 'in_transit', 'out_for_delivery']);
    }

    /**
     * Check if delivery requires special handling
     */
    public function requiresSpecialHandling(): bool
    {
        return !empty($this->special_handling_notes);
    }

    /**
     * Check if delivery is temperature controlled
     */
    public function isTemperatureControlled(): bool
    {
        return $this->temperature_control_required ?? false;
    }

    /**
     * Calculate delivery ETA based on current location and known factors
     */
    public function calculateETA(): \DateTime
    {
        // In a real implementation, this would connect to routing APIs
        // For simulation, we'll add time based on current status
        $currentTime = now();
        $additionalMinutes = 0;

        switch ($this->delivery_status) {
            case 'pending':
                $additionalMinutes = 15; // 15 mins for assignment
                break;
            case 'assigned':
                $additionalMinutes = 30; // 30 mins for driver to arrive at pickup
                break;
            case 'picked_up':
                // Calculate based on distance and delivery window
                $additionalMinutes = $this->delivery_distance_km ? $this->delivery_distance_km * 2 : 45; // ~2 mins per km
                break;
            case 'in_transit':
                // Calculate remaining time based on distance and progress
                $additionalMinutes = $this->delivery_distance_km ? ($this->delivery_distance_km / 2) : 30; // ~30 mins remaining
                break;
            case 'out_for_delivery':
                $additionalMinutes = 15; // 15 mins to reach destination
                break;
            default:
                $additionalMinutes = 30;
        }

        return $currentTime->addMinutes($additionalMinutes);
    }

    /**
     * Generate unique delivery token for confirmation
     */
    public static function generateDeliveryToken(): string
    {
        return 'DTK_' . time() . '_' . strtoupper(uniqid());
    }
}
