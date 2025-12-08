<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreTemplate extends Model
{
    protected $fillable = [
        'key',
        'name',
        'description',
        'features',
        'config',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'features' => 'array',
        'config' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get all active store templates ordered by sort_order
     */
    public static function getActiveTemplates()
    {
        return self::where('is_active', true)
                   ->orderBy('sort_order')
                   ->orderBy('created_at')
                   ->get();
    }

    /**
     * Scope to get only active templates
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to order by sort_order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')
                     ->orderBy('created_at');
    }
}
