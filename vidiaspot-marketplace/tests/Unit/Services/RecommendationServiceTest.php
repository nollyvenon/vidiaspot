<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Models\Ad;
use App\Models\User;
use App\Models\Category;
use App\Services\RecommendationService;
use App\Services\RedisService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Mockery;

class RecommendationServiceTest extends TestCase
{
    private $recommendationService;
    private $redisServiceMock;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->redisServiceMock = Mockery::mock(RedisService::class);
        $this->recommendationService = new RecommendationService($this->redisServiceMock);
    }

    public function test_get_personalized_recommendations_returns_array(): void
    {
        $userId = 1;

        // Mock Redis to return no cached results
        $this->redisServiceMock
            ->shouldReceive('get')
            ->with("recommendations:user:{$userId}:limit:10")
            ->andReturn(null);

        // Mock user history in Redis
        $this->redisServiceMock
            ->shouldReceive('getUserHistory')
            ->with($userId)
            ->andReturn([1, 2, 3]);

        // Mock database queries
        $adMock = Mockery::mock('Eloquent');
        $adMock->shouldReceive('whereIn')->andReturnSelf();
        $adMock->shouldReceive('get')->andReturn(collect([Ad::factory()->make()]));

        $this->mock(Ad::class, function ($mock) use ($adMock) {
            $mock->shouldReceive('where')->andReturnSelf();
            $mock->shouldReceive('whereNotIn')->andReturnSelf();
            $mock->shouldReceive('limit')->andReturnSelf();
            $mock->shouldReceive('orderByRaw')->andReturnSelf();
            $mock->shouldReceive('orderBy')->andReturnSelf();
            $mock->shouldReceive('get')->andReturn(collect([
                [
                    'id' => 1,
                    'title' => 'Test Ad',
                    'description' => 'Test Description',
                    'price' => 100.00
                ]
            ]));
            $mock->shouldReceive('inRandomOrder')->andReturnSelf();
        });

        $result = $this->recommendationService->getPersonalizedRecommendations($userId, 10);
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('id', $result[0] ?? []);
    }

    public function test_get_personalized_recommendations_uses_cache(): void
    {
        $userId = 1;
        $cachedResult = [
            ['id' => 1, 'title' => 'Cached Ad'],
            ['id' => 2, 'title' => 'Another Cached Ad']
        ];

        // Mock Redis to return cached results
        $this->redisServiceMock
            ->shouldReceive('get')
            ->with("recommendations:user:{$userId}:limit:10")
            ->andReturn($cachedResult);

        $result = $this->recommendationService->getPersonalizedRecommendations($userId, 10);
        
        $this->assertEquals($cachedResult, $result);
        // Database should not be queried when cache is hit
    }

    public function test_get_personalized_recommendations_caches_results(): void
    {
        $userId = 1;

        // Mock Redis to return no cached results
        $this->redisServiceMock
            ->shouldReceive('get')
            ->with("recommendations:user:{$userId}:limit:10")
            ->andReturn(null);

        // Mock Redis to expect caching
        $this->redisServiceMock
            ->shouldReceive('put')
            ->with("recommendations:user:{$userId}:limit:10", Mockery::type('array'), 3600)
            ->once();

        // Mock user history in Redis
        $this->redisServiceMock
            ->shouldReceive('getUserHistory')
            ->with($userId)
            ->andReturn([1, 2, 3]);

        // Mock database queries
        $this->mock(Ad::class, function ($mock) {
            $mock->shouldReceive('where')->andReturnSelf();
            $mock->shouldReceive('whereNotIn')->andReturnSelf();
            $mock->shouldReceive('limit')->andReturnSelf();
            $mock->shouldReceive('orderByRaw')->andReturnSelf();
            $mock->shouldReceive('orderBy')->andReturnSelf();
            $mock->shouldReceive('get')->andReturn(collect([
                [
                    'id' => 1,
                    'title' => 'Test Ad',
                    'description' => 'Test Description',
                    'price' => 100.00
                ]
            ]));
            $mock->shouldReceive('inRandomOrder')->andReturnSelf();
        });

        $result = $this->recommendationService->getPersonalizedRecommendations($userId, 10);
        
        $this->assertIsArray($result);
    }

    public function test_get_collaborative_recommendations(): void
    {
        $userId = 1;
        $limit = 5;

        // Mock Redis to return no cached results
        $this->redisServiceMock
            ->shouldReceive('get')
            ->with("collab_rec:user:{$userId}:limit:{$limit}")
            ->andReturn(null);

        // Mock Redis to expect caching
        $this->redisServiceMock
            ->shouldReceive('put')
            ->with("collab_rec:user:{$userId}:limit:{$limit}", Mockery::type('array'), 3600)
            ->once();

        // Mock database queries
        $this->mock(Ad::class, function ($mock) {
            $mock->shouldReceive('whereIn')->andReturnSelf();
            $mock->shouldReceive('where')->andReturnSelf();
            $mock->shouldReceive('whereNotIn')->andReturnSelf();
            $mock->shouldReceive('limit')->andReturnSelf();
            $mock->shouldReceive('orderBy')->andReturnSelf();
            $mock->shouldReceive('get')->andReturn(collect([
                [
                    'id' => 1,
                    'title' => 'Collaborative Ad',
                    'description' => 'Collaborative Description',
                    'price' => 100.00
                ]
            ]));
        });

        $result = $this->recommendationService->getCollaborativeRecommendations($userId, $limit);
        
        $this->assertIsArray($result);
    }

    public function test_get_category_recommendations(): void
    {
        $categorySlug = 'electronics';
        $limit = 5;

        // Mock category
        $category = Category::factory()->create(['slug' => $categorySlug]);

        // Mock Redis to return no cached results
        $this->redisServiceMock
            ->shouldReceive('get')
            ->with("category_rec:{$categorySlug}:limit:{$limit}")
            ->andReturn(null);

        // Mock Redis to expect caching
        $this->redisServiceMock
            ->shouldReceive('put')
            ->with("category_rec:{$categorySlug}:limit:{$limit}", Mockery::type('array'), 3600)
            ->once();

        // Mock database queries
        $this->mock(Ad::class, function ($mock) use ($category) {
            $mock->shouldReceive('where')->with('category_id', $category->id)->andReturnSelf();
            $mock->shouldReceive('where')->with('status', 'active')->andReturnSelf();
            $mock->shouldReceive('limit')->andReturnSelf();
            $mock->shouldReceive('inRandomOrder')->andReturnSelf();
            $mock->shouldReceive('get')->andReturn(collect([
                [
                    'id' => 1,
                    'title' => 'Category Ad',
                    'description' => 'Category Description',
                    'price' => 100.00
                ]
            ]));
        });

        $result = $this->recommendationService->getCategoryRecommendations($categorySlug, $limit);
        
        $this->assertIsArray($result);
    }

    public function test_get_trending_items(): void
    {
        $limit = 5;
        $timeFrame = 'week';

        // Mock Redis to return no cached results
        $this->redisServiceMock
            ->shouldReceive('get')
            ->with("trending:{$timeFrame}:limit:{$limit}")
            ->andReturn(null);

        // Mock Redis to expect caching
        $this->redisServiceMock
            ->shouldReceive('put')
            ->with("trending:{$timeFrame}:limit:{$limit}", Mockery::type('array'), 3600)
            ->once();

        // Mock database queries
        $this->mock(Ad::class, function ($mock) {
            $mock->shouldReceive('where')->with('status', 'active')->andReturnSelf();
            $mock->shouldReceive('where')->andReturnSelf();
            $mock->shouldReceive('orderBy')->with('view_count', 'desc')->andReturnSelf();
            $mock->shouldReceive('orderBy')->with('created_at', 'desc')->andReturnSelf();
            $mock->shouldReceive('limit')->andReturnSelf();
            $mock->shouldReceive('get')->andReturn(collect([
                [
                    'id' => 1,
                    'title' => 'Trending Ad',
                    'description' => 'Trending Description',
                    'price' => 100.00,
                    'view_count' => 100
                ]
            ]));
        });

        $result = $this->recommendationService->getTrendingItems($limit, $timeFrame);
        
        $this->assertIsArray($result);
    }

    public function test_get_seasonal_recommendations(): void
    {
        $limit = 5;

        // Mock Redis to return no cached results
        $this->redisServiceMock
            ->shouldReceive('get')
            ->with("seasonal_rec:limit:{$limit}")
            ->andReturn(null);

        // Mock Redis to expect caching
        $this->redisServiceMock
            ->shouldReceive('put')
            ->with("seasonal_rec:limit:{$limit}", Mockery::type('array'), 3600)
            ->once();

        // Mock category
        $category = Category::factory()->create(['slug' => 'electronics']);

        // Mock database queries
        $this->mock(Ad::class, function ($mock) use ($category) {
            $mock->shouldReceive('where')->with('category_id', $category->id)->andReturnSelf();
            $mock->shouldReceive('where')->with('status', 'active')->andReturnSelf();
            $mock->shouldReceive('limit')->andReturnSelf();
            $mock->shouldReceive('get')->andReturn(collect([
                [
                    'id' => 1,
                    'title' => 'Seasonal Ad',
                    'description' => 'Seasonal Description',
                    'price' => 100.00
                ]
            ]));
        });

        $this->mock(Category::class, function ($mock) use ($category) {
            $mock->shouldReceive('where')->with('slug', 'LIKE', '%electronics%')->andReturnSelf();
            $mock->shouldReceive('first')->andReturn($category);
        });

        $result = $this->recommendationService->getSeasonalRecommendations($limit);
        
        $this->assertIsArray($result);
    }

    public function test_get_cold_start_recommendations(): void
    {
        $limit = 6;

        // Mock popular ads
        $this->mock(Ad::class, function ($mock) {
            $mock->shouldReceive('where')->with('status', 'active')->andReturnSelf();
            $mock->shouldReceive('where')->andReturnSelf();
            $mock->shouldReceive('orderBy')->with('view_count', 'desc')->andReturnSelf();
            $mock->shouldReceive('limit')->andReturnSelf();
            $mock->shouldReceive('get')->andReturn(collect([
                [
                    'id' => 1,
                    'title' => 'Popular Ad',
                    'description' => 'Popular Description',
                    'price' => 100.00
                ]
            ]));
        });

        // Mock trending items
        $this->mock(Ad::class, function ($mock) {
            $mock->shouldReceive('where')->with('status', 'active')->andReturnSelf();
            $mock->shouldReceive('where')->andReturnSelf();
            $mock->shouldReceive('orderBy')->with('view_count', 'desc')->andReturnSelf();
            $mock->shouldReceive('orderBy')->with('created_at', 'desc')->andReturnSelf();
            $mock->shouldReceive('limit')->andReturnSelf();
            $mock->shouldReceive('get')->andReturn(collect([
                [
                    'id' => 2,
                    'title' => 'Trending Ad',
                    'description' => 'Trending Description',
                    'price' => 150.00
                ]
            ]));
        });

        $result = $this->recommendationService->getColdStartRecommendations($limit);
        
        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
    }

    public function test_refresh_recommendations(): void
    {
        $userId = 1;

        $result = $this->recommendationService->refreshRecommendations($userId);
        
        $this->assertTrue($result);
    }

    public function test_track_recommendation_interaction(): void
    {
        $userId = 1;
        $adId = 2;
        $interactionType = 'click';

        // Mock Redis methods expected to be called
        $this->redisServiceMock
            ->shouldReceive('put')
            ->with("rec_interaction:user:{$userId}:ad:{$adId}", Mockery::type('array'), 2592000)
            ->once();

        $this->redisServiceMock
            ->shouldReceive('addToRecentlyViewed')
            ->with($userId, $adId)
            ->once();

        $result = $this->recommendationService->trackRecommendationInteraction($userId, $adId, $interactionType);
        
        $this->assertTrue($result);
    }

    public function test_find_similar_users(): void
    {
        $userId = 1;

        // Mock user with city and state
        $user = User::factory()->create([
            'city' => 'Lagos',
            'state' => 'Lagos'
        ]);

        $this->mock(User::class, function ($mock) use ($user) {
            $mock->shouldReceive('find')->with($userId)->andReturn($user);
            $mock->shouldReceive('where')->andReturnSelf();
            $mock->shouldReceive('orWhere')->andReturnSelf();
            $mock->shouldReceive('where')->with('id', '!=', $userId)->andReturnSelf();
            $mock->shouldReceive('limit')->andReturnSelf();
            $mock->shouldReceive('pluck')->with('id')->andReturn(collect([2, 3, 4]));
        });

        $reflection = new \ReflectionClass($this->recommendationService);
        $method = $reflection->getMethod('findSimilarUsers');
        $method->setAccessible(true);

        $result = $method->invoke($this->recommendationService, $userId);
        
        $this->assertIsArray($result);
    }
}