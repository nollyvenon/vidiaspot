<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\ExchangeRate;
use App\Models\Currency;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExchangeRateTest extends TestCase
{
    use RefreshDatabase;

    public function test_exchange_rate_belongs_to_from_currency(): void
    {
        $fromCurrency = Currency::factory()->create(['code' => 'USD']);
        $toCurrency = Currency::factory()->create(['code' => 'EUR']);
        
        $exchangeRate = ExchangeRate::factory()->create([
            'from_currency_code' => 'USD',
            'to_currency_code' => 'EUR',
            'rate' => 0.85
        ]);

        $this->assertInstanceOf(Currency::class, $exchangeRate->fromCurrency);
        $this->assertEquals('USD', $exchangeRate->fromCurrency->code);
    }

    public function test_exchange_rate_belongs_to_to_currency(): void
    {
        $fromCurrency = Currency::factory()->create(['code' => 'USD']);
        $toCurrency = Currency::factory()->create(['code' => 'EUR']);
        
        $exchangeRate = ExchangeRate::factory()->create([
            'from_currency_code' => 'USD',
            'to_currency_code' => 'EUR',
            'rate' => 0.85
        ]);

        $this->assertInstanceOf(Currency::class, $exchangeRate->toCurrency);
        $this->assertEquals('EUR', $exchangeRate->toCurrency->code);
    }

    public function test_convert_method(): void
    {
        $exchangeRate = ExchangeRate::factory()->create([
            'from_currency_code' => 'USD',
            'to_currency_code' => 'EUR',
            'rate' => 0.85
        ]);

        $result = $exchangeRate->convert(100.00);
        $this->assertEquals(85.00, $result);
    }

    public function test_scope_from_currency(): void
    {
        ExchangeRate::factory()->create([
            'from_currency_code' => 'USD',
            'to_currency_code' => 'EUR',
            'rate' => 0.85
        ]);
        
        ExchangeRate::factory()->create([
            'from_currency_code' => 'GBP',
            'to_currency_code' => 'EUR',
            'rate' => 1.15
        ]);

        $usdRates = ExchangeRate::fromCurrency('USD')->get();
        
        $this->assertCount(1, $usdRates);
        $this->assertEquals('USD', $usdRates->first()->from_currency_code);
    }

    public function test_scope_to_currency(): void
    {
        ExchangeRate::factory()->create([
            'from_currency_code' => 'USD',
            'to_currency_code' => 'EUR',
            'rate' => 0.85
        ]);
        
        ExchangeRate::factory()->create([
            'from_currency_code' => 'USD',
            'to_currency_code' => 'GBP',
            'rate' => 0.75
        ]);

        $eurRates = ExchangeRate::toCurrency('EUR')->get();
        
        $this->assertCount(1, $eurRates);
        $this->assertEquals('EUR', $eurRates->first()->to_currency_code);
    }

    public function test_scope_for_conversion(): void
    {
        ExchangeRate::factory()->create([
            'from_currency_code' => 'USD',
            'to_currency_code' => 'EUR',
            'rate' => 0.85
        ]);
        
        ExchangeRate::factory()->create([
            'from_currency_code' => 'USD',
            'to_currency_code' => 'GBP',
            'rate' => 0.75
        ]);

        $conversionRate = ExchangeRate::forConversion('USD', 'EUR')->first();
        
        $this->assertInstanceOf(ExchangeRate::class, $conversionRate);
        $this->assertEquals('USD', $conversionRate->from_currency_code);
        $this->assertEquals('EUR', $conversionRate->to_currency_code);
        $this->assertEquals(0.85, $conversionRate->rate);
    }

    public function test_exchange_rate_casts(): void
    {
        $exchangeRate = ExchangeRate::factory()->create([
            'rate' => '1.23456789',
            'last_updated' => '2023-10-15 10:30:00'
        ]);

        $this->assertIsFloat($exchangeRate->rate);
        $this->assertInstanceOf(\Carbon\Carbon::class, $exchangeRate->last_updated);
        
        // Check that rate is cast to 8 decimal places
        $this->assertEquals(1.23456789, $exchangeRate->rate);
    }

    public function test_exchange_rate_fillable_attributes(): void
    {
        $fillable = [
            'from_currency_code',
            'to_currency_code',
            'rate',
            'last_updated',
            'provider',
        ];

        $exchangeRate = new ExchangeRate();
        $this->assertEquals($fillable, $exchangeRate->getFillable());
    }

    public function test_exchange_rate_default_values(): void
    {
        $exchangeRate = ExchangeRate::factory()->make();

        $this->assertNull($exchangeRate->id);
        $this->assertNull($exchangeRate->from_currency_code);
        $this->assertNull($exchangeRate->to_currency_code);
        $this->assertNull($exchangeRate->rate);
        $this->assertNull($exchangeRate->last_updated);
        $this->assertNull($exchangeRate->provider);
    }
}