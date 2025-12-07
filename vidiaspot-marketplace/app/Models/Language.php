<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Language extends Model
{
    protected $fillable = [
        'code',
        'name',
        'native_name',
        'flag_icon',
        'is_default',
        'is_rtl',
        'dialects',
        'is_active',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_rtl' => 'boolean',
        'is_active' => 'boolean',
        'dialects' => 'array',
    ];

    /**
     * Get the translations associated with this language.
     */
    public function translations(): HasMany
    {
        return $this->hasMany(Translation::class, 'locale', 'code');
    }

    /**
     * Scope to get only active languages.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get the default language.
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }
}
