<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdPlacement extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'location',
        'type',
        'size',
        'priority',
        'is_active',
        'settings',
        'user_id',
        'content',
        'starts_at',
        'expires_at',
        'target_pages',
        'targeting_rules',
        'view_count',
        'click_count',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'is_active' => 'boolean',
        'priority' => 'integer',
        'settings' => 'array',
        'content' => 'array',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'targeting_rules' => 'array',
        'view_count' => 'integer',
        'click_count' => 'integer',
    ];

    /**
     * Get the user who created this ad placement.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to get active ad placements.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                    ->where('starts_at', '<=', now())
                    ->where(function($q) {
                        $q->whereNull('expires_at')
                          ->orWhere('expires_at', '>', now());
                    });
    }

    /**
     * Scope to get by location.
     */
    public function scopeByLocation($query, $location)
    {
        return $query->where('location', $location);
    }

    /**
     * Scope to get by type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }
}
