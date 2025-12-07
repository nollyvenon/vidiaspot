<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Models\Currency;
use App\Models\ExchangeRate;
use App\Services\CurrencyService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Mockery;

class CurrencyServiceTest extends TestCase
{
    private $currencyService;
    private $redisServiceMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->redisServiceMock = Mockery::mock(\App\Services\RedisService::class);
        $this->currencyService = new CurrencyService();
    }

    public function test_convert_same_currency(): void
    {
        $result = $this->currencyService->convert(100.00, 'USD', 'USD');
        $this->assertEquals(100.00, $result);
    }

    public function test_convert_with_existing_exchange_rate(): void
    {
        $exchangeRate = ExchangeRate::factory()->create([
            'from_currency_code' => 'USD',
            'to_currency_code' => 'EUR',
            'rate' => 0.85
        ]);

        $result = $this->currencyService->convert(100.00, 'USD', 'EUR');
        $this->assertEquals(85.00, $result);
    }

    public function test_convert_calls_convert_method_on_exchange_rate(): void
    {
        $exchangeRate = ExchangeRate::factory()->create([
            'from_currency_code' => 'USD',
            'to_currency_code' => 'EUR',
            'rate' => 0.85
        ]);

        // Mock the convert method on the ExchangeRate model
        $this->mock(ExchangeRate::class, function ($mock) use ($exchangeRate) {
            $mock->shouldReceive('convert')->with(100.00)->andReturn(85.00);
        });

        $result = $this->currencyService->convert(100.00, 'USD', 'EUR');
        $this->assertEquals(85.00, $result);
    }

    public function test_convert_with_external_api_when_no_db_rate(): void
    {
        Config::set('services.exchange_rates.api_key', 'test_api_key');

        Http::fake([
            'https://api.exchangerate-api.com/v4/latest/USD' => Http::response([
                'rates' => [
                    'EUR' => 0.85,
                    'GBP' => 0.75
                ]
            ], 200)
        ]);

        $result = $this->currencyService->convert(100.00, 'USD', 'EUR');
        $this->assertEquals(85.00, $result);

        // Verify the rate was saved to the database
        $this->assertDatabaseHas('exchange_rates', [
            'from_currency_code' => 'USD',
            'to_currency_code' => 'EUR',
            'rate' => 0.85
        ]);
    }

    public function test_convert_throws_exception_when_no_rate_available(): void
    {
        Config::set('services.exchange_rates.api_key', null);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Exchange rate not available for USD to EUR');

        $this->currencyService->convert(100.00, 'USD', 'EUR');
    }

    public function test_format_method(): void
    {
        $currency = Currency::factory()->create([
            'code' => 'USD',
            'symbol' => '$',
            'format' => '$%s'
        ]);

        $result = $this->currencyService->format(100.00, 'USD');
        $this->assertEquals('$100.00', $result);
    }

    public function test_format_fallback_when_currency_not_found(): void
    {
        $result = $this->currencyService->format(100.00, 'NONEXISTENT');
        $this->assertEquals('NGN 100.00', $result);
    }

    public function test_get_all_currencies(): void
    {
        Currency::factory()->count(3)->create(['status' => 'active']);
        Currency::factory()->create(['status' => 'inactive']);

        $currencies = $this->currencyService->getAllCurrencies();
        $this->assertCount(3, $currencies);
        $this->assertTrue($currencies->every(fn($currency) => $currency->status === 'active'));
    }

    public function test_get_default_currency(): void
    {
        Currency::factory()->create(['code' => 'USD', 'is_default' => false]);
        $defaultCurrency = Currency::factory()->create(['code' => 'NGN', 'is_default' => true]);

        $result = $this->currencyService->getDefaultCurrency();
        $this->assertInstanceOf(Currency::class, $result);
        $this->assertEquals('NGN', $result->code);
    }

    public function test_fetch_exchange_rate_logs_error_when_api_fails(): void
    {
        Config::set('services.exchange_rates.api_key', 'test_api_key');

        Log::shouldReceive('warning')
           ->once()
           ->with(Mockery::type('string'));

        Http::fake([
            'https://api.exchangerate-api.com/v4/latest/USD' => Http::response([], 500)
        ]);

        $result = $this->currencyService->convert(100.00, 'USD', 'EUR');
        
        // Should throw exception since no rate was found
        $this->expectException(\Exception::class);
    }

    public function test_update_all_rates_with_api_key(): void
    {
        Config::set('services.exchange_rates.api_key', 'test_api_key');

        $currency1 = Currency::factory()->create(['code' => 'USD', 'status' => 'active']);
        $currency2 = Currency::factory()->create(['code' => 'EUR', 'status' => 'active']);
        $inactiveCurrency = Currency::factory()->create(['code' => 'GBP', 'status' => 'inactive']);

        Http::fake([
            'https://api.exchangerate-api.com/v4/latest/USD' => Http::response([
                'rates' => [
                    'USD' => 1.0,
                    'EUR' => 0.85,
                    'GBP' => 0.75
                ]
            ], 200),
            'https://api.exchangerate-api.com/v4/latest/EUR' => Http::response([
                'rates' => [
                    'USD' => 1.18,
                    'EUR' => 1.0,
                    'GBP' => 0.88
                ]
            ], 200)
        ]);

        // This should update rates for active currencies only
        $this->currencyService->updateAllRates();

        // Check that rates were updated for active currencies
        $this->assertDatabaseHas('exchange_rates', [
            'from_currency_code' => 'USD',
            'to_currency_code' => 'EUR',
        ]);

        $this->assertDatabaseHas('exchange_rates', [
            'from_currency_code' => 'EUR',
            'to_currency_code' => 'USD',
        ]);
    }

    public function test_update_all_rates_without_api_key(): void
    {
        Config::set('services.exchange_rates.api_key', null);

        // This should not make any API calls
        $this->currencyService->updateAllRates();

        // Should not throw any exceptions or create any records
        $this->assertEquals(0, ExchangeRate::count());
    }

    public function test_fetch_exchange_rate_returns_null_when_no_api_key(): void
    {
        Config::set('services.exchange_rates.api_key', null);

        $reflection = new \ReflectionClass($this->currencyService);
        $method = $reflection->getMethod('fetchExchangeRate');
        $method->setAccessible(true);

        $result = $method->invoke($this->currencyService, 'USD', 'EUR');
        $this->assertNull($result);
    }
}