<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Currency;
use App\Models\ExchangeRate;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CurrencyTest extends TestCase
{
    use RefreshDatabase;

    public function test_currency_has_many_exchange_rates(): void
    {
        $currency = Currency::factory()->create(['code' => 'USD']);
        $exchangeRates = ExchangeRate::factory()->count(2)->create([
            'from_currency_code' => 'USD'
        ]);

        // Test the fromCurrency relationship
        $this->assertInstanceOf(ExchangeRate::class, $exchangeRates->first());
        $this->assertEquals('USD', $exchangeRates->first()->from_currency_code);
    }

    public function test_format_amount_method(): void
    {
        $currency = Currency::factory()->create([
            'code' => 'USD',
            'symbol' => '$',
            'format' => '$%s'
        ]);

        $result = $currency->formatAmount(123.45);
        $this->assertEquals('$123.45', $result);
    }

    public function test_format_amount_with_different_formats(): void
    {
        $currency = Currency::factory()->create([
            'code' => 'EUR',
            'symbol' => '€',
            'format' => '%s€'
        ]);

        $result = $currency->formatAmount(123.45);
        $this->assertEquals('123.45€', $result);
    }

    public function test_format_amount_with_number_formatting(): void
    {
        $currency = Currency::factory()->create([
            'code' => 'NGN',
            'symbol' => '₦',
            'format' => '₦%s',
            'thousand_separator' => ',',
            'decimal_places' => 2,
            'decimal_separator' => '.'
        ]);

        $result = $currency->formatAmount(1234.567);
        $this->assertEquals('₦1,234.57', $result);
    }

    public function test_format_amount_fallback(): void
    {
        $currency = Currency::factory()->create([
            'code' => 'GBP',
            'symbol' => '£'
        ]);

        $result = $currency->formatAmount(123.45);
        $this->assertEquals('£123.45', $result);
    }

    public function test_currency_scope_active(): void
    {
        Currency::factory()->create(['status' => 'active']);
        Currency::factory()->create(['status' => 'inactive']);
        Currency::factory()->create(['status' => 'active']);

        $activeCurrencies = Currency::active()->get();

        $this->assertCount(2, $activeCurrencies);
        $this->assertTrue($activeCurrencies->every(fn($currency) => $currency->status === 'active'));
    }

    public function test_currency_scope_default(): void
    {
        Currency::factory()->create(['code' => 'USD', 'is_default' => false]);
        Currency::factory()->create(['code' => 'NGN', 'is_default' => true]);
        Currency::factory()->create(['code' => 'EUR', 'is_default' => false]);

        $defaultCurrency = Currency::default()->first();

        $this->assertInstanceOf(Currency::class, $defaultCurrency);
        $this->assertEquals('NGN', $defaultCurrency->code);
        $this->assertTrue($defaultCurrency->is_default);
    }

    public function test_currency_fillable_attributes(): void
    {
        $fillable = [
            'code',
            'name',
            'symbol',
            'format',
            'thousand_separator',
            'decimal_places',
            'decimal_separator',
            'status',
            'is_default',
            'exchange_rate',
            'position',
            'active',
        ];

        $currency = new Currency();
        $this->assertEquals($fillable, $currency->getFillable());
    }

    public function test_currency_casts(): void
    {
        $currency = Currency::factory()->create([
            'decimal_places' => '2',
            'is_default' => '1',
            'active' => '1',
            'exchange_rate' => '1.2345'
        ]);

        $this->assertIsInt($currency->decimal_places);
        $this->assertIsBool($currency->is_default);
        $this->assertIsBool($currency->active);
        $this->assertIsFloat($currency->exchange_rate);

        $this->assertEquals(2, $currency->decimal_places);
        $this->assertTrue($currency->is_default);
        $this->assertTrue($currency->active);
        $this->assertEquals(1.2345, $currency->exchange_rate);
    }

    public function test_currency_default_values(): void
    {
        $currency = Currency::factory()->make();

        $this->assertNull($currency->id);
        $this->assertEquals('active', $currency->status);
        $this->assertFalse($currency->is_default);
        $this->assertTrue($currency->active);
        $this->assertEquals(2, $currency->decimal_places);
        $this->assertEquals(',', $currency->thousand_separator);
        $this->assertEquals('.', $currency->decimal_separator);
    }
}