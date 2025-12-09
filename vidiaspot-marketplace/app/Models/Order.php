<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'vendor_store_id',
        'order_number',
        'status',
        'total_amount',
        'currency',
        'quantity',
        'payment_method',
        'payment_status',
        'shipping_address',
        'billing_address',
        'customer_email',
        'customer_phone',
        'customer_name',
        'shipping_method',
        'shipping_cost',
        'tax_amount',
        'discount_amount',
        'notes',
        'order_items',
        'tracking_number',
        'estimated_delivery',
        'fulfillment_status',
        'cancelled_at',
        'cancelled_reason',
    ];

    protected $casts = [
        'order_items' => 'array',
        'shipping_address' => 'array',
        'billing_address' => 'array',
        'total_amount' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'quantity' => 'integer',
        'estimated_delivery' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    /**
     * Get the user that owns the order
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the vendor store associated with the order
     */
    public function vendorStore(): BelongsTo
    {
        return $this->belongsTo(VendorStore::class);
    }

    /**
     * Scope to get orders for a specific user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to get orders for a specific vendor store
     */
    public function scopeForVendorStore($query, $vendorStoreId)
    {
        return $query->where('vendor_store_id', $vendorStoreId);
    }

    /**
     * Scope to filter by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}
