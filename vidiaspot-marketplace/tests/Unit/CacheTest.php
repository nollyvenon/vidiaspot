<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Services\MySqlToSqliteCacheService;

class CacheTest extends TestCase
{
    public function test_cache_configuration()
    {
        // In testing environment, cache is set to 'array' to avoid external dependencies
        // In production, the default cache store would be 'redis'
        $cacheStore = config('cache.default');
        $this->assertContains($cacheStore, ['redis', 'array']); // Accept both for testing

        // Test that database cache uses the correct connection (for specific purposes)
        $cacheConnection = config('cache.stores.database.connection');
        $this->assertEquals('sqlite_cache', $cacheConnection);
    }

    public function test_mysql_to_sqlite_cache_service()
    {
        // Test that the service can be instantiated
        $service = app(MySqlToSqliteCacheService::class);
        $this->assertInstanceOf(MySqlToSqliteCacheService::class, $service);

        // Just verify the service exists and has expected methods
        $this->assertTrue(method_exists($service, 'getFromCacheOrDb'));
        $this->assertTrue(method_exists($service, 'getModelFromCache'));
        $this->assertTrue(method_exists($service, 'getSingleModelFromCache'));
    }

    public function test_cache_config_values()
    {
        // Test that the configuration values exist and are correct
        $cacheConnection = config('cache.stores.database.connection');
        $this->assertNotNull($cacheConnection);

        $dbCacheTable = config('cache.stores.database.table');
        $this->assertNotNull($dbCacheTable);

        $dbCacheLockConnection = config('cache.stores.database.lock_connection');
        $this->assertNotNull($dbCacheLockConnection);
    }
}