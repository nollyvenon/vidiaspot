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
        // Test that cache is configured to use database store
        $cacheStore = config('cache.default');
        $this->assertEquals('database', $cacheStore);

        // Test that database cache uses the correct connection
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