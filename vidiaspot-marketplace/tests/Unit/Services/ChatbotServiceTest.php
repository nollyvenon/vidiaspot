<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\ChatbotService;
use App\Services\RedisService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Mockery;

class ChatbotServiceTest extends TestCase
{
    private $chatbotService;
    private $redisServiceMock;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->redisServiceMock = Mockery::mock(RedisService::class);
        $this->chatbotService = new ChatbotService($this->redisServiceMock);
    }

    public function test_process_input_returns_array(): void
    {
        // Mock Redis to return no cached response
        $this->redisServiceMock
            ->shouldReceive('get')
            ->with(Mockery::type('string'))
            ->andReturn(null);

        // Mock Redis to expect caching
        $this->redisServiceMock
            ->shouldReceive('put')
            ->with(Mockery::type('string'), Mockery::type('array'), 600)
            ->once();

        $result = $this->chatbotService->processInput('Hello');
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('intent', $result);
        $this->assertArrayHasKey('response', $result);
        $this->assertArrayHasKey('confidence', $result);
        $this->assertArrayHasKey('follow_up_suggestions', $result);
    }

    public function test_classify_intent_greeting(): void
    {
        $input = 'hello';

        $reflection = new \ReflectionClass($this->chatbotService);
        $method = $reflection->getMethod('classifyIntent');
        $method->setAccessible(true);

        $result = $method->invoke($this->chatbotService, $input);

        $this->assertEquals('greeting', $result['type']);
        $this->assertIsFloat($result['confidence']);
        $this->assertGreaterThanOrEqual(0, $result['confidence']);
        $this->assertLessThanOrEqual(1, $result['confidence']);
    }

    public function test_classify_intent_farewell(): void
    {
        $input = 'goodbye';

        $reflection = new \ReflectionClass($this->chatbotService);
        $method = $reflection->getMethod('classifyIntent');
        $method->setAccessible(true);

        $result = $method->invoke($this->chatbotService, $input);

        $this->assertEquals('farewell', $result['type']);
    }

    public function test_classify_intent_help(): void
    {
        $input = 'I need help with my order';

        $reflection = new \ReflectionClass($this->chatbotService);
        $method = $reflection->getMethod('classifyIntent');
        $method->setAccessible(true);

        $result = $method->invoke($this->chatbotService, $input);
        
        $this->assertEquals('help', $result['type']);
    }

    public function test_classify_intent_product_info(): void
    {
        $input = 'Tell me about this product';

        $reflection = new \ReflectionClass($this->chatbotService);
        $method = $reflection->getMethod('classifyIntent');
        $method->setAccessible(true);

        $result = $method->invoke($this->chatbotService, $input);
        
        $this->assertEquals('product_info', $result['type']);
    }

    public function test_classify_intent_default(): void
    {
        $input = 'This is a random sentence with no known patterns';

        $reflection = new \ReflectionClass($this->chatbotService);
        $method = $reflection->getMethod('classifyIntent');
        $method->setAccessible(true);

        $result = $method->invoke($this->chatbotService, $input);
        
        $this->assertEquals('default', $result['type']);
        $this->assertEquals(0.0, $result['confidence']);
    }

    public function test_generate_response(): void
    {
        $intent = [
            'type' => 'greeting',
            'confidence' => 1.0,
            'details' => []
        ];

        $reflection = new \ReflectionClass($this->chatbotService);
        $method = $reflection->getMethod('generateResponse');
        $method->setAccessible(true);

        $result = $method->invoke($this->chatbotService, $intent);
        
        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    public function test_get_follow_up_suggestions(): void
    {
        $reflection = new \ReflectionClass($this->chatbotService);
        $method = $reflection->getMethod('getFollowUpSuggestions');
        $method->setAccessible(true);

        $result = $method->invoke($this->chatbotService, 'greeting');
        
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
    }

    public function test_needs_human_assistance(): void
    {
        $reflection = new \ReflectionClass($this->chatbotService);
        $method = $reflection->getMethod('needsHumanAssistance');
        $method->setAccessible(true);

        $lowConfidenceIntent = [
            'confidence' => 0.2
        ];
        
        $highConfidenceIntent = [
            'confidence' => 0.8
        ];

        $this->assertTrue($method->invoke($this->chatbotService, $lowConfidenceIntent));
        $this->assertFalse($method->invoke($this->chatbotService, $highConfidenceIntent));
    }

    public function test_get_common_questions(): void
    {
        $result = $this->chatbotService->getCommonQuestions();
        
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
        $this->assertArrayHasKey('question', $result[0]);
        $this->assertArrayHasKey('answer', $result[0]);
    }

    public function test_get_analytics(): void
    {
        $result = $this->chatbotService->getAnalytics();
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('total_conversations', $result);
        $this->assertArrayHasKey('avg_response_time', $result);
        $this->assertArrayHasKey('resolution_rate', $result);
        $this->assertArrayHasKey('top_intents', $result);
    }

    public function test_handle_complex_query_with_openai(): void
    {
        Config::set('services.openai.api_key', 'test_key');

        Http::fake([
            'https://api.openai.com/v1/chat/completions' => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'content' => 'This is a response from OpenAI API'
                        ]
                    ]
                ]
            ], 200)
        ]);

        $result = $this->chatbotService->handleComplexQuery('What is the weather today?');
        
        $this->assertEquals('This is a response from OpenAI API', $result);
    }

    public function test_handle_complex_query_returns_null_when_no_api_key(): void
    {
        Config::set('services.openai.api_key', null);

        $result = $this->chatbotService->handleComplexQuery('What is the weather today?');
        
        $this->assertNull($result);
    }

    public function test_handle_complex_query_returns_null_on_api_error(): void
    {
        Config::set('services.openai.api_key', 'test_key');

        Http::fake([
            'https://api.openai.com/v1/chat/completions' => Http::response([], 500)
        ]);

        $result = $this->chatbotService->handleComplexQuery('What is the weather today?');
        
        $this->assertNull($result);
    }

    public function test_escalate_to_human(): void
    {
        Log::shouldReceive('info')
           ->once()
           ->with('Chatbot escalation to human agent', Mockery::type('array'));

        $result = $this->chatbotService->escalateToHuman('Complex issue', 1);
        
        $this->assertIsArray($result);
        $this->assertTrue($result['escalated']);
        $this->assertIsString($result['message']);
        $this->assertTrue($result['ticket_created']);
    }

    public function test_process_input_uses_cache(): void
    {
        $cachedResult = [
            'intent' => 'greeting',
            'response' => 'Hello! How can I help you today?',
            'confidence' => 1.0,
            'follow_up_suggestions' => [],
            'needs_human_assistance' => false,
        ];

        // Mock Redis to return cached response
        $this->redisServiceMock
            ->shouldReceive('get')
            ->with(Mockery::type('string'))
            ->andReturn($cachedResult);

        $result = $this->chatbotService->processInput('Hello');
        
        $this->assertEquals($cachedResult, $result);
    }

    public function test_train_bot(): void
    {
        Log::shouldReceive('info')
           ->once()
           ->with('Training chatbot with new data', Mockery::type('array'));

        $trainingData = [
            'intent' => 'new_intent',
            'patterns' => ['new pattern'],
            'responses' => ['new response']
        ];

        $result = $this->chatbotService->trainBot($trainingData);
        
        $this->assertTrue($result);
    }

    public function test_process_input_with_user_id_stores_conversation(): void
    {
        // Mock Redis to return no cached response
        $this->redisServiceMock
            ->shouldReceive('get')
            ->with(Mockery::type('string'))
            ->andReturn(null);

        // Mock Redis to expect caching
        $this->redisServiceMock
            ->shouldReceive('put')
            ->with(Mockery::type('string'), Mockery::type('array'), 600)
            ->once();

        Log::shouldReceive('info')
           ->once()
           ->with('Chatbot conversation', Mockery::type('array'));

        $result = $this->chatbotService->processInput('Hello', 1, 'session123');
        
        $this->assertIsArray($result);
    }
}