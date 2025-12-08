<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FoodOrder extends Model
{
    protected $fillable = [
        'user_id',
        'food_vendor_id',
        'order_number',
        'status',
        'total_amount',
        'currency',
        'quantity',
        'payment_method',
        'payment_status',
        'delivery_address',
        'customer_email',
        'customer_phone',
        'customer_name',
        'delivery_instructions',
        'delivery_fee',
        'tax_amount',
        'discount_amount',
        'tip_amount',
        'notes',
        'order_items',
        'driver_id',
        'driver_name',
        'driver_phone',
        'estimated_delivery_time',
        'actual_delivery_time',
        'delivery_status',
        'order_type', // delivery, pickup
        'scheduled_time', // for scheduled orders
        'special_instructions',
        'packaging_fee',
        'service_fee',
        'order_rating',
        'order_feedback',
        'cancelled_at',
        'cancelled_reason',
        'refunded_at',
        'refunded_amount',
    ];

    protected $casts = [
        'order_items' => 'array',
        'delivery_address' => 'array',
        'total_amount' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tip_amount' => 'decimal:2',
        'packaging_fee' => 'decimal:2',
        'service_fee' => 'decimal:2',
        'refunded_amount' => 'decimal:2',
        'order_rating' => 'integer',
        'quantity' => 'integer',
        'estimated_delivery_time' => 'datetime',
        'actual_delivery_time' => 'datetime',
        'scheduled_time' => 'datetime',
        'cancelled_at' => 'datetime',
        'refunded_at' => 'datetime',
    ];

    /**
     * Get the user who placed the order
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the vendor who will fulfill the order
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(FoodVendor::class, 'food_vendor_id');
    }

    /**
     * Get the items in this order
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(FoodOrderItem::class);
    }

    /**
     * Scope to get orders for a specific user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to get orders for a specific vendor
     */
    public function scopeForVendor($query, $vendorId)
    {
        return $query->where('food_vendor_id', $vendorId);
    }

    /**
     * Scope to filter by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter by order type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('order_type', $type);
    }
}
