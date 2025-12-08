<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryLocation extends Model
{
    protected $fillable = [
        'user_id',
        'vendor_store_id',
        'name',
        'address',
        'latitude',
        'longitude',
        'is_active',
        'is_primary',
        'contact_person',
        'contact_phone',
        'contact_email',
        'operating_hours',
        'capacity',
        'max_storage_units',
        'current_usage',
        'manager_id',
        'timezone',
        'settings',
    ];

    protected $casts = [
        'address' => 'array',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'is_active' => 'boolean',
        'is_primary' => 'boolean',
        'capacity' => 'integer',
        'max_storage_units' => 'integer',
        'current_usage' => 'integer',
        'operating_hours' => 'array',
        'settings' => 'array',
        'timezone' => 'string',
    ];

    /**
     * Get the user that owns this inventory location
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the vendor store this location belongs to
     */
    public function vendorStore(): BelongsTo
    {
        return $this->belongsTo(VendorStore::class);
    }

    /**
     * Get the manager of this location
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    /**
     * Get inventory items at this location
     */
    public function inventoryItems(): HasMany
    {
        return $this->hasMany(InventoryItem::class);
    }

    /**
     * Scope to get only active locations
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get primary location
     */
    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }
}
