<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\CurrencyService;
use App\Services\RecommendationService;
use App\Services\ChatbotService;
use App\Models\Currency;
use App\Models\ExchangeRate;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;
use Mockery;

class ServiceUnitTest extends TestCase
{
    public function test_currency_service_format_amount(): void
    {
        $currency = new Currency([
            'code' => 'USD',
            'symbol' => '$',
            'precision' => 2
        ]);

        $result = $currency->formatAmount(123.45);
        $this->assertEquals('$123.45', $result);
    }

    public function test_currency_service_format_amount_with_different_precision(): void
    {
        $currency = new Currency([
            'code' => 'NGN',
            'symbol' => '₦',
            'precision' => 0
        ]);

        $result = $currency->formatAmount(123.45);
        $this->assertEquals('₦123', $result);
    }

    public function test_exchange_rate_convert_method(): void
    {
        $exchangeRate = new ExchangeRate([
            'rate' => 0.85
        ]);

        $result = $exchangeRate->convert(100.00);
        $this->assertEquals(85.00, $result);
    }

    public function test_chatbot_intent_classification_greeting(): void
    {
        $chatbotService = new ChatbotService(Mockery::mock(\App\Services\RedisService::class));
        
        $reflection = new \ReflectionClass($chatbotService);
        $method = $reflection->getMethod('classifyIntent');
        $method->setAccessible(true);

        $result = $method->invoke($chatbotService, 'hello');
        
        $this->assertEquals('greeting', $result['type']);
    }

    public function test_chatbot_intent_classification_farewell(): void
    {
        $chatbotService = new ChatbotService(Mockery::mock(\App\Services\RedisService::class));
        
        $reflection = new \ReflectionClass($chatbotService);
        $method = $reflection->getMethod('classifyIntent');
        $method->setAccessible(true);

        $result = $method->invoke($chatbotService, 'goodbye');
        
        $this->assertEquals('farewell', $result['type']);
    }

    public function test_chatbot_intent_classification_default(): void
    {
        $chatbotService = new ChatbotService(Mockery::mock(\App\Services\RedisService::class));
        
        $reflection = new \ReflectionClass($chatbotService);
        $method = $reflection->getMethod('classifyIntent');
        $method->setAccessible(true);

        $result = $method->invoke($chatbotService, 'random text with no matching patterns');
        
        $this->assertEquals('default', $result['type']);
    }

    public function test_chatbot_generate_response(): void
    {
        $chatbotService = new ChatbotService(Mockery::mock(\App\Services\RedisService::class));
        
        $reflection = new \ReflectionClass($chatbotService);
        $method = $reflection->getMethod('generateResponse');
        $method->setAccessible(true);

        $intent = [
            'type' => 'greeting',
            'confidence' => 1.0,
            'details' => []
        ];

        $result = $method->invoke($chatbotService, $intent);
        
        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    public function test_chatbot_needs_human_assistance(): void
    {
        $chatbotService = new ChatbotService(Mockery::mock(\App\Services\RedisService::class));
        
        $reflection = new \ReflectionClass($chatbotService);
        $method = $reflection->getMethod('needsHumanAssistance');
        $method->setAccessible(true);

        $lowConfidenceIntent = ['confidence' => 0.2];
        $highConfidenceIntent = ['confidence' => 0.8];

        $this->assertTrue($method->invoke($chatbotService, $lowConfidenceIntent));
        $this->assertFalse($method->invoke($chatbotService, $highConfidenceIntent));
    }

    public function test_chatbot_get_common_questions(): void
    {
        $chatbotService = new ChatbotService(Mockery::mock(\App\Services\RedisService::class));
        
        $result = $chatbotService->getCommonQuestions();

        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
        $this->assertArrayHasKey('question', $result[0]);
        $this->assertArrayHasKey('answer', $result[0]);
    }

    public function test_chatbot_get_analytics(): void
    {
        $chatbotService = new ChatbotService(Mockery::mock(\App\Services\RedisService::class));
        
        $result = $chatbotService->getAnalytics();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('total_conversations', $result);
        $this->assertArrayHasKey('avg_response_time', $result);
        $this->assertArrayHasKey('resolution_rate', $result);
        $this->assertArrayHasKey('top_intents', $result);
    }
}