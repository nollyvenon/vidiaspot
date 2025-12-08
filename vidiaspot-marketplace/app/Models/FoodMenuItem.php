<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FoodMenuItem extends Model
{
    protected $fillable = [
        'food_vendor_id',
        'name',
        'description',
        'category',
        'price',
        'original_price', // for discounts
        'image_url',
        'ingredients',
        'allergens',
        'dietary_options', // vegetarian, vegan, gluten-free, etc.
        'spice_level', // mild, medium, hot
        'is_available',
        'is_popular',
        'is_new',
        'max_quantity_per_order',
        'preparation_time', // in minutes
        'customization_options',
        'serving_size',
        'calories',
        'item_settings',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'original_price' => 'decimal:2',
        'ingredients' => 'array',
        'allergens' => 'array',
        'dietary_options' => 'array',
        'customization_options' => 'array',
        'is_available' => 'boolean',
        'is_popular' => 'boolean',
        'is_new' => 'boolean',
        'max_quantity_per_order' => 'integer',
        'preparation_time' => 'integer', // in minutes
        'serving_size' => 'string',
        'calories' => 'integer',
        'item_settings' => 'array',
    ];

    /**
     * Get the vendor that owns this menu item
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(FoodVendor::class, 'food_vendor_id');
    }

    /**
     * Get order items for this menu item
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(FoodOrderItem::class);
    }

    /**
     * Scope to get only available items
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    /**
     * Scope to filter by category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope to filter by dietary option
     */
    public function scopeByDietary($query, $dietaryOption)
    {
        return $query->whereJsonContains('dietary_options', $dietaryOption);
    }
}
