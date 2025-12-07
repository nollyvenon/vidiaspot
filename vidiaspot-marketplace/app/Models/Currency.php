<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Currency extends Model
{
    protected $fillable = [
        'code',
        'name',
        'symbol',
        'country',
        'precision',
        'format',
        'is_active',
        'is_default',
        'minor_unit',
    ];

    protected $casts = [
        'precision' => 'integer',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'minor_unit' => 'integer',
    ];

    /**
     * Get the exchange rates where this currency is the source.
     */
    public function exchangeRatesFrom(): HasMany
    {
        return $this->hasMany(ExchangeRate::class, 'from_currency_code', 'code');
    }

    /**
     * Get the exchange rates where this currency is the target.
     */
    public function exchangeRatesTo(): HasMany
    {
        return $this->hasMany(ExchangeRate::class, 'to_currency_code', 'code');
    }

    /**
     * Scope to get only active currencies.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get the default currency.
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Format a monetary amount with this currency's format.
     */
    public function formatAmount($amount)
    {
        return $this->symbol . number_format($amount, $this->precision);
    }
}
