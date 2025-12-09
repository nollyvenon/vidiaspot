<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryTracking extends Model
{
    protected $fillable = [
        'user_id',
        'ad_id',
        'inventory_item_id',
        'initial_quantity',
        'current_quantity',
        'reserved_quantity',
        'sold_quantity',
        'damaged_quantity',
        'lost_quantity',
        'quantity_unit', // 'pieces', 'kg', 'liters', etc.
        'location_trackable', // Whether location tracking is enabled
        'location_coordinates', // Current location if trackable
        'last_updated_by',
        'last_updated_at',
        'automatic_updates_enabled', // Whether to automatically sync with POS/system
        'sync_with_vendor_store', // Whether to sync with vendor store inventory
        'sync_with_ads', // Whether to sync with classified ads
        'out_of_stock_notification',
        'low_stock_threshold',
        'reorder_threshold',
        'reorder_quantity',
        'restock_date',
        'inventory_history', // Track all movements as JSON
        'inventory_status', // 'in_stock', 'low_stock', 'out_of_stock', 'discontinued'
        'last_scanned_at',
        'scanned_by',
        'qr_code_enabled', // If QR code tracking is available
        'qr_code_url', // URL to the QR code for this item
        'rfid_enabled', // If RFID tracking is available
        'rfid_tag_id', // RFID tag ID
        'batch_tracking_enabled',
        'batch_number',
        'expiry_tracking_enabled',
        'expiry_date',
        'production_date',
        'best_before_date',
        'recall_tracking',
        'recall_dates', // Dates when item was recalled
        'quality_checks_enabled',
        'last_quality_check',
        'quality_status', // 'passed', 'pending', 'failed'
        'notes',
        'custom_fields',
    ];

    protected $casts = [
        'initial_quantity' => 'integer',
        'current_quantity' => 'integer',
        'reserved_quantity' => 'integer',
        'sold_quantity' => 'integer',
        'damaged_quantity' => 'integer',
        'lost_quantity' => 'integer',
        'location_trackable' => 'boolean',
        'location_coordinates' => 'array',
        'automatic_updates_enabled' => 'boolean',
        'sync_with_vendor_store' => 'boolean',
        'sync_with_ads' => 'boolean',
        'out_of_stock_notification_enabled' => 'boolean',
        'low_stock_threshold' => 'integer',
        'reorder_threshold' => 'integer',
        'reorder_quantity' => 'integer',
        'inventory_history' => 'array',
        'last_updated_at' => 'datetime',
        'last_scanned_at' => 'datetime',
        'qr_code_enabled' => 'boolean',
        'rfid_enabled' => 'boolean',
        'batch_tracking_enabled' => 'boolean',
        'expiry_tracking_enabled' => 'boolean',
        'production_date' => 'date',
        'expiry_date' => 'date',
        'best_before_date' => 'date',
        'recall_tracking' => 'array',
        'quality_checks_enabled' => 'boolean',
        'last_quality_check' => 'datetime',
        'custom_fields' => 'array',
    ];

    /**
     * Get the user associated with this inventory tracking
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the ad associated with this inventory tracking
     */
    public function ad(): BelongsTo
    {
        return $this->belongsTo(Ad::class);
    }

    /**
     * Get the inventory item associated with this tracking
     */
    public function inventoryItem(): BelongsTo
    {
        return $this->belongsTo(\App\Models\InventoryItem::class, 'inventory_item_id');
    }

    /**
     * Get the user who last updated this tracking record
     */
    public function lastUpdatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_updated_by');
    }

    /**
     * Scope to get only active inventory items
     */
    public function scopeActive($query)
    {
        return $query->where('inventory_status', '!=', 'discontinued');
    }

    /**
     * Scope to get items by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('inventory_status', $status);
    }

    /**
     * Scope to get out of stock items
     */
    public function scopeOutOfStock($query)
    {
        return $query->where('inventory_status', 'out_of_stock');
    }

    /**
     * Scope to get low stock items
     */
    public function scopeLowStock($query)
    {
        return $query->where('inventory_status', 'low_stock');
    }

    /**
     * Scope to get items for specific ad
     */
    public function scopeForAd($query, $adId)
    {
        return $query->where('ad_id', $adId);
    }

    /**
     * Update inventory quantity
     */
    public function updateQuantity($newQuantity, $updatedBy = null)
    {
        $oldQuantity = $this->current_quantity;
        $difference = $newQuantity - $oldQuantity;

        // Update the inventory record
        $this->update([
            'current_quantity' => $newQuantity,
            'last_updated_by' => $updatedBy,
            'last_updated_at' => now(),
        ]);

        // Add to inventory history
        $history = $this->inventory_history ?: [];
        $history[] = [
            'date' => now(),
            'previous_quantity' => $oldQuantity,
            'new_quantity' => $newQuantity,
            'difference' => $difference,
            'updated_by' => $updatedBy,
            'reason' => 'manual_update',
        ];

        $this->update(['inventory_history' => $history]);

        // Update inventory status based on new quantity
        $this->updateInventoryStatus();

        return $this;
    }

    /**
     * Record inventory movement
     */
    public function recordMovement($quantity, $action, $reason, $updatedBy = null)
    {
        $oldQuantity = $this->current_quantity;
        $newQuantity = match($action) {
            'sale' => $this->current_quantity - $quantity,
            'restock' => $this->current_quantity + $quantity,
            'damage' => $this->current_quantity - $quantity,
            'lost' => $this->current_quantity - $quantity,
            'reservation' => $this->current_quantity - $quantity,
            'reservation_release' => $this->current_quantity + $quantity,
            'return' => $this->current_quantity + $quantity,
            default => $this->current_quantity,
        };

        $movement = [
            'date' => now(),
            'action' => $action,
            'quantity' => $quantity,
            'previous_quantity' => $oldQuantity,
            'new_quantity' => $newQuantity,
            'difference' => $newQuantity - $oldQuantity,
            'reason' => $reason,
            'updated_by' => $updatedBy,
        ];

        // Update current quantity
        $this->update([
            'current_quantity' => $newQuantity,
            'last_updated_by' => $updatedBy,
            'last_updated_at' => now(),
        ]);

        // Add to inventory history
        $history = $this->inventory_history ?: [];
        $history[] = $movement;
        $this->update(['inventory_history' => $history]);

        // Update inventory status based on new quantity
        $this->updateInventoryStatus();

        return $this;
    }

    /**
     * Update inventory status based on current quantity
     */
    public function updateInventoryStatus()
    {
        $quantity = $this->current_quantity;
        $threshold = $this->low_stock_threshold ?? 5;

        if ($quantity <= 0) {
            $status = 'out_of_stock';
        } elseif ($quantity <= $threshold) {
            $status = 'low_stock';
        } else {
            $status = 'in_stock';
        }

        $this->update(['inventory_status' => $status]);
    }

    /**
     * Check if inventory is running low
     */
    public function isLowOnStock(): bool
    {
        return $this->current_quantity <= ($this->low_stock_threshold ?? 5);
    }

    /**
     * Check if inventory is out of stock
     */
    public function isOutOfStock(): bool
    {
        return $this->current_quantity <= 0;
    }

    /**
     * Check if inventory is in good stock
     */
    public function isInGoodStock(): bool
    {
        return $this->current_quantity > ($this->low_stock_threshold ?? 5);
    }

    /**
     * Check if ready for reorder
     */
    public function isReadyForReorder(): bool
    {
        return $this->current_quantity <= ($this->reorder_threshold ?? 10);
    }
}
