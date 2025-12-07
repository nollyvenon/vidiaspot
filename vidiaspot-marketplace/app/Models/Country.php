<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    protected $fillable = [
        'name',
        'code',
        'phone_code',
        'currency_code',
        'is_active',
        'flag_icon',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the states for this country.
     */
    public function states(): HasMany
    {
        return $this->hasMany(State::class);
    }

    /**
     * Get the vendors in this country.
     */
    public function vendors(): HasMany
    {
        return $this->hasMany(Vendor::class);
    }

    /**
     * Scope to get active countries.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
