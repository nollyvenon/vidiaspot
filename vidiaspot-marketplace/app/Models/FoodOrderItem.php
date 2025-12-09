<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FoodOrderItem extends Model
{
    protected $fillable = [
        'food_order_id',
        'food_menu_item_id',
        'name',
        'price',
        'quantity',
        'total_price',
        'special_instructions',
        'customization_options',
        'item_addons',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'quantity' => 'integer',
        'special_instructions' => 'string',
        'customization_options' => 'array',
        'item_addons' => 'array',
    ];

    /**
     * Get the order that this item belongs to
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(FoodOrder::class, 'food_order_id');
    }

    /**
     * Get the menu item that this order item is based on
     */
    public function menuItem(): BelongsTo
    {
        return $this->belongsTo(FoodMenuItem::class, 'food_menu_item_id');
    }
}
