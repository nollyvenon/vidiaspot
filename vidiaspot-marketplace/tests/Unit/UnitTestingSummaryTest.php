<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\CurrencyService;
use App\Services\ChatbotService;
use App\Models\Currency;
use App\Models\ExchangeRate;
use Mockery;

class UnitTestingSummaryTest extends TestCase
{
    /**
     * This test suite demonstrates proper unit testing practices for the VidiaSpot Marketplace application.
     * 
     * Unit tests should focus on testing individual components in isolation without external dependencies
     * such as databases, file systems, or network calls when possible.
     */
    
    public function test_currency_format_amount_method(): void
    {
        $currency = new Currency([
            'symbol' => '$',
            'precision' => 2
        ]);

        $result = $currency->formatAmount(123.45);
        $this->assertEquals('$123.45', $result);
    }

    public function test_exchange_rate_convert_method(): void
    {
        $exchangeRate = new ExchangeRate([
            'rate' => 0.85
        ]);

        $result = $exchangeRate->convert(100.00);
        $this->assertEquals(85.00, $result);
    }

    public function test_chatbot_intent_classification(): void
    {
        $chatbotService = new ChatbotService(Mockery::mock(\App\Services\RedisService::class));
        
        $reflection = new \ReflectionClass($chatbotService);
        $method = $reflection->getMethod('classifyIntent');
        $method->setAccessible(true);

        $result = $method->invoke($chatbotService, 'hello');
        $this->assertEquals('greeting', $result['type']);
    }

    public function test_chatbot_response_generation(): void
    {
        $chatbotService = new ChatbotService(Mockery::mock(\App\Services\RedisService::class));
        
        $reflection = new \ReflectionClass($chatbotService);
        $method = $reflection->getMethod('generateResponse');
        $method->setAccessible(true);

        $intent = ['type' => 'greeting', 'confidence' => 1.0, 'details' => []];
        $result = $method->invoke($chatbotService, $intent);
        
        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    public function test_model_fillable_properties(): void
    {
        $ad = new \App\Models\Ad();
        $fillable = $ad->getFillable();
        
        $this->assertContains('title', $fillable);
        $this->assertContains('description', $fillable);
        $this->assertContains('price', $fillable);
        $this->assertContains('status', $fillable);
    }

    public function test_model_casts(): void
    {
        $ad = new \App\Models\Ad();
        $casts = $ad->getCasts();
        
        $this->assertArrayHasKey('price', $casts);
        $this->assertArrayHasKey('negotiable', $casts);
        $this->assertArrayHasKey('view_count', $casts);
        
        $this->assertEquals('decimal:2', $casts['price']);
        $this->assertEquals('boolean', $casts['negotiable']);
        $this->assertEquals('integer', $casts['view_count']);
    }

    public function test_currency_scope_methods(): void
    {
        $currency = new Currency();
        
        // Test that scope methods exist and return query builders
        $this->assertInstanceOf(
            \Illuminate\Database\Eloquent\Builder::class, 
            $currency->newQuery()->active()
        );
        $this->assertInstanceOf(
            \Illuminate\Database\Eloquent\Builder::class, 
            $currency->newQuery()->default()
        );
    }

    public function test_exchange_rate_scope_methods(): void
    {
        $exchangeRate = new ExchangeRate();
        
        // Test that scope methods exist and return query builders
        $this->assertInstanceOf(
            \Illuminate\Database\Eloquent\Builder::class, 
            $exchangeRate->newQuery()->fromCurrency('USD')
        );
        $this->assertInstanceOf(
            \Illuminate\Database\Eloquent\Builder::class, 
            $exchangeRate->newQuery()->toCurrency('EUR')
        );
        $this->assertInstanceOf(
            \Illuminate\Database\Eloquent\Builder::class, 
            $exchangeRate->newQuery()->forConversion('USD', 'EUR')
        );
    }
}