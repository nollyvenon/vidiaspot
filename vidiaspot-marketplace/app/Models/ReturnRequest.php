<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReturnRequest extends Model
{
    protected $fillable = [
        'user_id',
        'order_id',
        'delivery_order_id',
        'ad_id',
        'vendor_id',
        'return_reason',
        'return_description',
        'return_images', // Photos of the item being returned
        'return_reason_category', // 'defective', 'wrong_item', 'changed_mind', 'not_as_described', 'damaged', 'other'
        'return_type', // 'refund', 'exchange', 'repair', 'replacement'
        'return_method', // 'pickup', 'drop_off', 'courier_collection', 'self_delivery'
        'return_status', // 'pending', 'approved', 'rejected', 'processing', 'shipped_back', 'received', 'refunded', 'cancelled'
        'refund_amount',
        'exchange_item_id', // If exchanging for another item
        'exchange_item_details', // Details of exchange item
        'return_shipping_label_id', // Link to the return shipping label
        'return_tracking_number',
        'return_shipped_date',
        'return_delivered_date',
        'return_address_json', // Where to return the item
        'original_delivery_address_json', // Original delivery address
        'is_return_insured', // Whether return is insured
        'return_insurance_amount', // Insurance coverage for return
        'return_insurance_claim_number', // If claim was made
        'return_insurance_status', // 'active', 'claimed', 'pending'
        'restocking_fee', // Fee for restocking
        'return_fee', // Fee for return processing (if applicable)
        'return_notes',
        'admin_notes',
        'return_deadline', // Deadline for return
        'is_return_eligible', // Whether return is eligible
        'return_eligibility_reasons', // Reasons for eligibility/ineligibility
        'return_condition_required', // 'unused', 'unused_like_new', 'any_condition'
        'item_verification_status', // 'pending', 'verified', 'rejected'
        'item_inspection_details',
        'quality_check_status', // 'not_started', 'in_progress', 'completed'
        'quality_check_notes',
        'resolution_type', // 'refund', 'exchange', 'store_credit', 'replacement'
        'resolution_details',
        'customer_satisfaction_score', // After resolution
        'return_label_generated', // Whether return label was generated
        'return_label_url',
        'return_label_cost',
        'return_pickup_scheduled_date',
        'return_pickup_scheduled_time',
        'return_pickup_confirmed',
        'return_pickup_confirmed_at',
        'return_pickup_confirmed_by',
        'return_courier_assigned',
        'return_courier_name',
        'return_courier_phone',
        'return_courier_tracking_url',
        'return_pickup_instructions',
        'return_picked_up_at',
        'return_out_for_delivery_at',
        'return_delivered_to_warehouse_at',
        'reimbursement_processed_at',
        'reimbursement_method', // 'original_payment_method', 'bank_transfer', 'wallet_credit'
        'reimbursement_transaction_id',
        'return_cost_deduction', // Cost deduction from seller
        'return_cost_recovery', // Cost recovery from customer (if applicable)
        'return_disposal_method', // 'donation', 'recycling', 'landfill', 'resale'
        'return_disposal_notes',
        'return_audit_log', // Log of all return activities
        'return_custom_fields',
        'return_metadata',
    ];

    protected $casts = [
        'return_images' => 'array',
        'return_address_json' => 'array',
        'original_delivery_address_json' => 'array',
        'return_insurance_amount' => 'decimal:2',
        'refund_amount' => 'decimal:2',
        'restocking_fee' => 'decimal:2',
        'return_fee' => 'decimal:2',
        'return_cost_deduction' => 'decimal:2',
        'return_cost_recovery' => 'decimal:2',
        'return_eligibility_reasons' => 'array',
        'return_eligibility' => 'boolean',
        'is_return_insured' => 'boolean',
        'return_label_generated' => 'boolean',
        'return_pickup_confirmed' => 'boolean',
        'return_courier_assigned' => 'boolean',
        'return_picked_up_at' => 'datetime',
        'return_out_for_delivery_at' => 'datetime',
        'return_delivered_to_warehouse_at' => 'datetime',
        'reimbursement_processed_at' => 'datetime',
        'return_pickup_scheduled_date' => 'date',
        'return_pickup_scheduled_time' => 'time',
        'return_deadline' => 'datetime',
        'quality_check_notes' => 'string',
        'resolution_details' => 'array',
        'item_inspection_details' => 'array',
        'return_audit_log' => 'array',
        'return_custom_fields' => 'array',
        'return_metadata' => 'array',
        'refund_amount' => 'decimal:2',
    ];

    /**
     * Get the user who requested the return
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the order associated with this return
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Order::class);
    }

    /**
     * Get the delivery order associated with this return
     */
    public function deliveryOrder(): BelongsTo
    {
        return $this->belongsTo(\App\Models\DeliveryOrder::class, 'delivery_order_id');
    }

    /**
     * Get the ad that was returned
     */
    public function ad(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Ad::class);
    }

    /**
     * Get the vendor who sold the item
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    /**
     * Get the return shipping label
     */
    public function returnShippingLabel(): BelongsTo
    {
        return $this->belongsTo(\App\Models\ShippingLabel::class, 'return_shipping_label_id');
    }

    /**
     * Scope to get returns by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('return_status', $status);
    }

    /**
     * Scope to get returns for a specific user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to get returns for a specific vendor
     */
    public function scopeForVendor($query, $vendorId)
    {
        return $query->where('vendor_id', $vendorId);
    }

    /**
     * Scope to get returns by reason category
     */
    public function scopeByReason($query, $reason)
    {
        return $query->where('return_reason_category', $reason);
    }

    /**
     * Scope to get returns within date range
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Check if return is still eligible
     */
    public function isEligible(): bool
    {
        if (!$this->is_return_eligible) {
            return false;
        }

        if ($this->return_deadline && now() > $this->return_deadline) {
            return false;
        }

        if (in_array($this->return_status, ['refunded', 'rejected', 'cancelled'])) {
            return false;
        }

        return true;
    }

    /**
     * Check if return is in progress
     */
    public function isInProgress(): bool
    {
        return in_array($this->return_status, ['approved', 'processing', 'shipped_back', 'in_transit']);
    }

    /**
     * Check if return is completed
     */
    public function isCompleted(): bool
    {
        return in_array($this->return_status, ['refunded', 'exchange_completed']);
    }

    /**
     * Get return timeline
     */
    public function getTimelineAttribute()
    {
        $timeline = [
            ['stage' => 'initiated', 'date' => $this->created_at, 'status' => 'Return Request Initiated'],
        ];

        if ($this->return_status === 'approved') {
            $timeline[] = ['stage' => 'approved', 'date' => $this->updated_at, 'status' => 'Return Approved'];
        }

        if ($this->return_picked_up_at) {
            $timeline[] = ['stage' => 'picked_up', 'date' => $this->return_picked_up_at, 'status' => 'Item Picked Up for Return'];
        }

        if ($this->return_delivered_to_warehouse_at) {
            $timeline[] = ['stage' => 'received', 'date' => $this->return_delivered_to_warehouse_at, 'status' => 'Item Received at Warehouse'];
        }

        if ($this->reimbursement_processed_at) {
            $timeline[] = ['stage' => 'refunded', 'date' => $this->reimbursement_processed_at, 'status' => 'Refund Processed'];
        }

        return $timeline;
    }

    /**
     * Get the return shipping cost calculation
     */
    public function calculateReturnShippingCost($distance = null)
    {
        // If distance isn't provided, make an estimate based on original delivery
        if (!$distance) {
            $distance = 15; // Default to 15km if unknown
        }

        // Base cost: 200 NGN + 10 NGN per km
        $baseCost = 200;
        $distanceCost = $distance * 10;

        return $baseCost + $distanceCost;
    }
}
