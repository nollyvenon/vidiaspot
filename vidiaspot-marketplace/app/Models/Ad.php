<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ad extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'description',
        'price',
        'currency_code',
        'condition',
        'status',
        'location',
        'latitude',
        'longitude',
        'contact_phone',
        'negotiable',
        'view_count',
        'expires_at',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'negotiable' => 'boolean',
        'view_count' => 'integer',
        'expires_at' => 'datetime',
    ];

    // Relationship: Ad belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relationship: Ad belongs to a category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Relationship: Ad has many images (to be defined later)
    public function images()
    {
        return $this->hasMany(AdImage::class);
    }

    // Relationship: Ad belongs to a currency
    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_code', 'code');
    }

    // Accessor: Get the formatted price with currency symbol
    public function getFormattedPriceAttribute()
    {
        $currency = $this->currency;
        if ($currency) {
            return $currency->formatAmount($this->price);
        }

        // Fallback if no currency is set
        return 'NGN ' . number_format($this->price, 2);
    }

    /**
     * Convert the price to a different currency.
     *
     * @param string $toCurrencyCode
     * @return string
     */
    public function convertPriceTo($toCurrencyCode)
    {
        $currencyService = new \App\Services\CurrencyService();

        try {
            $convertedAmount = $currencyService->convert($this->price, $this->currency_code, $toCurrencyCode);
            return $currencyService->format($convertedAmount, $toCurrencyCode);
        } catch (\Exception $e) {
            // If conversion fails, return the original price with original currency
            return $this->formatted_price;
        }
    }
}
