<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class City extends Model
{
    protected $fillable = [
        'name',
        'state_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'state_id' => 'integer',
    ];

    /**
     * Get the state this city belongs to.
     */
    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    /**
     * Get the vendors in this city.
     */
    public function vendors(): HasMany
    {
        return $this->hasMany(Vendor::class);
    }

    /**
     * Scope to get active cities.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
