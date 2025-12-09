<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryItem extends Model
{
    protected $fillable = [
        'inventory_location_id',
        'ad_id', // References the ad/item in the marketplace
        'sku',
        'barcode',
        'name',
        'description',
        'category',
        'brand',
        'quantity_on_hand',
        'quantity_reserved',
        'quantity_available',
        'minimum_stock_level',
        'maximum_stock_level',
        'cost_price',
        'selling_price',
        'currency',
        'weight',
        'dimensions',
        'color',
        'size',
        'material',
        'condition',
        'serial_number',
        'batch_number',
        'production_date',
        'expiry_date',
        'supplier_id',
        'reorder_point',
        'reorder_quantity',
        'warehouse_position',
        'is_active',
        'is_archived',
        'tags',
        'custom_attributes',
        'seasonal_adjustments',
    ];

    protected $casts = [
        'dimensions' => 'array',
        'quantity_on_hand' => 'integer',
        'quantity_reserved' => 'integer',
        'quantity_available' => 'integer',
        'minimum_stock_level' => 'integer',
        'maximum_stock_level' => 'integer',
        'cost_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'weight' => 'decimal:2',
        'reorder_point' => 'integer',
        'reorder_quantity' => 'integer',
        'is_active' => 'boolean',
        'is_archived' => 'boolean',
        'production_date' => 'date',
        'expiry_date' => 'date',
        'tags' => 'array',
        'custom_attributes' => 'array',
        'seasonal_adjustments' => 'array',
    ];

    /**
     * Get the inventory location this item belongs to
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(InventoryLocation::class, 'inventory_location_id');
    }

    /**
     * Get the ad that this inventory item is linked to
     */
    public function ad(): BelongsTo
    {
        return $this->belongsTo(Ad::class);
    }

    /**
     * Get supplier of this inventory item
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supplier_id');
    }

    /**
     * Scope to get only active items
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get items with low stock
     */
    public function scopeLowStock($query)
    {
        return $query->whereColumn('quantity_available', '<=', 'minimum_stock_level');
    }

    /**
     * Scope to get items by category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Check if item is low on stock
     */
    public function isLowOnStock(): bool
    {
        return $this->quantity_available <= $this->minimum_stock_level;
    }

    /**
     * Check if item is in stock
     */
    public function isInStock(): bool
    {
        return $this->quantity_available > 0;
    }

    /**
     * Calculate profit margin
     */
    public function getProfitMargin(): float
    {
        if ($this->selling_price == 0) return 0;
        return (($this->selling_price - $this->cost_price) / $this->selling_price) * 100;
    }
}
