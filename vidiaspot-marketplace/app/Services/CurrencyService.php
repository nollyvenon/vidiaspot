<?php

namespace App\Services;

use App\Models\Currency;
use App\Models\ExchangeRate;
use Illuminate\Support\Facades\Http;

class CurrencyService
{
    /**
     * Convert an amount from one currency to another.
     *
     * @param float $amount
     * @param string $fromCurrency
     * @param string $toCurrency
     * @return float
     */
    public function convert($amount, $fromCurrency, $toCurrency)
    {
        if ($fromCurrency === $toCurrency) {
            return $amount;
        }

        // Try to find the exchange rate in the database first
        $exchangeRate = ExchangeRate::forConversion($fromCurrency, $toCurrency)->first();

        if ($exchangeRate) {
            return $exchangeRate->convert($amount);
        }

        // If not found in DB, get from external service (as fallback)
        $rate = $this->fetchExchangeRate($fromCurrency, $toCurrency);
        
        if ($rate === null) {
            // If still no rate, throw an exception or return the original amount
            throw new \Exception("Exchange rate not available for {$fromCurrency} to {$toCurrency}");
        }

        return $amount * $rate;
    }

    /**
     * Format an amount with the specified currency format.
     *
     * @param float $amount
     * @param string $currencyCode
     * @return string
     */
    public function format($amount, $currencyCode)
    {
        $currency = Currency::where('code', $currencyCode)->first();
        
        if (!$currency) {
            // If currency not found, use defaults
            return 'NGN ' . number_format($amount, 2);
        }

        return $currency->formatAmount($amount);
    }

    /**
     * Get all available currencies.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllCurrencies()
    {
        return Currency::active()->get();
    }

    /**
     * Get the default currency.
     *
     * @return \App\Models\Currency
     */
    public function getDefaultCurrency()
    {
        return Currency::default()->first();
    }

    /**
     * Fetch exchange rate from external API (if configured).
     *
     * @param string $fromCurrency
     * @param string $toCurrency
     * @return float|null
     */
    private function fetchExchangeRate($fromCurrency, $toCurrency)
    {
        $apiKey = config('services.exchange_rates.api_key', env('EXCHANGE_RATE_API_KEY'));
        
        if (!$apiKey) {
            // If no API key is configured, return null to use saved rates
            return null;
        }

        try {
            // Example with a common exchange rate API
            $response = Http::get("https://api.exchangerate-api.com/v4/latest/{$fromCurrency}");
            
            if ($response->successful()) {
                $data = $response->json();
                $rate = $data['rates'][$toCurrency] ?? null;

                if ($rate) {
                    // Update the database with the new rate
                    ExchangeRate::updateOrCreate([
                        'from_currency_code' => $fromCurrency,
                        'to_currency_code' => $toCurrency,
                    ], [
                        'rate' => $rate,
                        'last_updated' => now(),
                        'provider' => 'exchangerate-api.com',
                    ]);
                    
                    return $rate;
                }
            }
        } catch (\Exception $e) {
            // Log the error but continue with existing data
            \Log::warning('Failed to fetch exchange rate: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Update all exchange rates from external API.
     *
     * @return void
     */
    public function updateAllRates()
    {
        $apiKey = config('services.exchange_rates.api_key', env('EXCHANGE_RATE_API_KEY'));
        
        if (!$apiKey) {
            return;
        }

        try {
            // Get all active currencies
            $currencies = Currency::active()->get();
            
            foreach ($currencies as $fromCurrency) {
                $response = Http::get("https://api.exchangerate-api.com/v4/latest/{$fromCurrency->code}");
                
                if ($response->successful()) {
                    $data = $response->json();
                    
                    foreach ($currencies as $toCurrency) {
                        $rate = $data['rates'][$toCurrency->code] ?? null;

                        if ($rate !== null) {
                            ExchangeRate::updateOrCreate([
                                'from_currency_code' => $fromCurrency->code,
                                'to_currency_code' => $toCurrency->code,
                            ], [
                                'rate' => $rate,
                                'last_updated' => now(),
                                'provider' => 'exchangerate-api.com',
                            ]);
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            \Log::error('Failed to update all exchange rates: ' . $e->getMessage());
        }
    }
}