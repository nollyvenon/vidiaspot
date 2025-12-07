<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExchangeRate extends Model
{
    protected $fillable = [
        'from_currency_code',
        'to_currency_code',
        'rate',
        'last_updated',
        'provider',
    ];

    protected $casts = [
        'rate' => 'decimal:8',
        'last_updated' => 'datetime',
    ];

    /**
     * Get the currency this rate is from.
     */
    public function fromCurrency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'from_currency_code', 'code');
    }

    /**
     * Get the currency this rate is to.
     */
    public function toCurrency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'to_currency_code', 'code');
    }

    /**
     * Convert an amount from the source currency to the target currency.
     */
    public function convert($amount)
    {
        return $amount * $this->rate;
    }

    /**
     * Scope to get rates for a specific from currency.
     */
    public function scopeFromCurrency($query, $currencyCode)
    {
        return $query->where('from_currency_code', $currencyCode);
    }

    /**
     * Scope to get rates for a specific to currency.
     */
    public function scopeToCurrency($query, $currencyCode)
    {
        return $query->where('to_currency_code', $currencyCode);
    }

    /**
     * Scope to get a specific conversion rate.
     */
    public function scopeForConversion($query, $fromCurrencyCode, $toCurrencyCode)
    {
        return $query->where('from_currency_code', $fromCurrencyCode)
                    ->where('to_currency_code', $toCurrencyCode);
    }
}
