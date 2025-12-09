<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShippingLabel extends Model
{
    protected $fillable = [
        'user_id',
        'order_id',
        'delivery_order_id',
        'shipment_id', // Unique shipment ID
        'tracking_number', // Tracking number from carrier
        'carrier_code', // 'fedex', 'ups', 'dhl', 'local_carrier', etc.
        'carrier_name', // Full carrier name
        'label_url', // URL to download the shipping label
        'label_file_path', // Path to the stored label file
        'shipping_cost',
        'currency_code',
        'package_weight_kg',
        'package_length_cm',
        'package_width_cm',
        'package_height_cm',
        'package_value',
        'declared_value', // Value declared for insurance purposes
        'shipping_address_json', // Sender address as JSON
        'delivery_address_json', // Recipient address as JSON
        'service_type', // 'standard', 'express', 'overnight', 'freight'
        'insurance_covered', // Whether insurance is included
        'insurance_amount', // Insurance coverage amount
        'signature_required', // Whether signature is required
        'adult_signature_required', // Whether adult signature is required
        'delivery_confirmation', // Type of delivery confirmation
        'status', // 'created', 'printed', 'shipped', 'delivered', 'cancelled', 'returned'
        'estimated_delivery_date',
        'actual_delivery_date',
        'generated_at',
        'printed_at',
        'shipped_at',
        'delivery_deadline',
        'delivery_instructions',
        'special_services', // Additional services like COD, delivery appointment
        'cod_amount', // Cash on delivery amount
        'cod_collect_mode', // 'cash', 'check'
        'return_label', // Whether return label is included
        'return_label_url',
        'return_label_file_path',
        'packages_count', // Number of packages in shipment
        'package_contents', // Description of package contents
        'weight_unit', // 'kg', 'lb'
        'dimension_unit', // 'cm', 'in'
        'origin_country',
        'destination_country',
        'customs_info', // Customs declaration for international shipments
        'commercial_invoice_required', // For international shipments
        'commercial_invoice_url',
        'commercial_invoice_file_path',
        'shipper_account_number', // Carrier account number
        'recipient_account_number', // If recipient pays (DAP, DDP)
        'billing_option', // 'shipper_pay', 'recipient_pay', 'third_party_pay'
        'delivery_time_preference', // 'morning', 'afternoon', 'any_time'
        'delivery_date_scheduled', // Scheduled delivery date
        'delivery_window', // Specific delivery window
        'package_identifier', // Package ID for multi-piece shipments
        'package_sequence', // Sequence number for multi-piece shipments
        'package_total_pieces', // Total number of pieces in shipment
        'label_template', // Template used for this label
        'label_metadata', // Additional metadata
        'custom_fields',
        'notes',
    ];

    protected $casts = [
        'shipping_address_json' => 'array',
        'delivery_address_json' => 'array',
        'shipping_cost' => 'decimal:2',
        'package_weight_kg' => 'decimal:2',
        'package_length_cm' => 'decimal:2',
        'package_width_cm' => 'decimal:2',
        'package_height_cm' => 'decimal:2',
        'package_value' => 'decimal:2',
        'declared_value' => 'decimal:2',
        'insurance_amount' => 'decimal:2',
        'cod_amount' => 'decimal:2',
        'signature_required' => 'boolean',
        'adult_signature_required' => 'boolean',
        'return_label' => 'boolean',
        'commercial_invoice_required' => 'boolean',
        'is_active' => 'boolean',
        'estimated_delivery_date' => 'date',
        'actual_delivery_date' => 'date',
        'generated_at' => 'datetime',
        'printed_at' => 'datetime',
        'shipped_at' => 'datetime',
        'delivery_deadline' => 'datetime',
        'special_services' => 'array',
        'package_contents' => 'array',
        'customs_info' => 'array',
        'delivery_instructions' => 'string',
        'label_template' => 'string',
        'label_metadata' => 'array',
        'custom_fields' => 'array',
        'notes' => 'string',
        'packages_count' => 'integer',
        'package_sequence' => 'integer',
        'package_total_pieces' => 'integer',
    ];

    /**
     * Get the user who created this shipping label
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the order associated with this label
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Order::class);
    }

    /**
     * Get the delivery order associated with this label
     */
    public function deliveryOrder(): BelongsTo
    {
        return $this->belongsTo(\App\Models\DeliveryOrder::class, 'delivery_order_id');
    }

    /**
     * Scope to get labels by carrier
     */
    public function scopeByCarrier($query, $carrierCode)
    {
        return $query->where('carrier_code', $carrierCode);
    }

    /**
     * Scope to get labels by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to get labels for a specific user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to get labels by service type
     */
    public function scopeByServiceType($query, $serviceType)
    {
        return $query->where('service_type', $serviceType);
    }

    /**
     * Scope to get labels within a date range
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope to get labels for international shipping
     */
    public function scopeInternational($query)
    {
        return $query->where('origin_country', '!=', DB::raw('destination_country'));
    }

    /**
     * Get the formatted address for display
     */
    public function getDisplayAddressAttribute()
    {
        $deliveryAddr = $this->delivery_address_json;
        return $deliveryAddr['address_line1'] . ', ' .
               ($deliveryAddr['address_line2'] ? $deliveryAddr['address_line2'] . ', ' : '') .
               $deliveryAddr['city'] . ', ' .
               $deliveryAddr['state'] . ', ' .
               $deliveryAddr['country'] . ' ' .
               ($deliveryAddr['postal_code'] ?? '');
    }

    /**
     * Calculate package dimensions
     */
    public function getPackageDimensionsAttribute()
    {
        return [
            'length' => $this->package_length_cm,
            'width' => $this->package_width_cm,
            'height' => $this->package_height_cm,
            'unit' => $this->dimension_unit,
            'volume' => $this->package_length_cm * $this->package_width_cm * $this->package_height_cm
        ];
    }

    /**
     * Check if label is ready for printing
     */
    public function isReadyForPrint(): bool
    {
        return $this->status === 'created' && !empty($this->label_url);
    }

    /**
     * Check if label has been printed
     */
    public function isPrinted(): bool
    {
        return !is_null($this->printed_at);
    }

    /**
     * Check if shipment is in transit
     */
    public function isInTransit(): bool
    {
        return in_array($this->status, ['shipped', 'in_transit']);
    }

    /**
     * Check if shipment is delivered
     */
    public function isDelivered(): bool
    {
        return $this->status === 'delivered';
    }
}
