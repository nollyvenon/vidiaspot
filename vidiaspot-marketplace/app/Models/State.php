<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class State extends Model
{
    protected $fillable = [
        'name',
        'code',
        'country_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'country_id' => 'integer',
    ];

    /**
     * Get the country this state belongs to.
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Get the cities in this state.
     */
    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }

    /**
     * Get the vendors in this state.
     */
    public function vendors(): HasMany
    {
        return $this->hasMany(Vendor::class);
    }

    /**
     * Scope to get active states.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
