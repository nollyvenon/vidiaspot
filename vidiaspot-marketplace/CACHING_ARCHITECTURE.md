# Vidiaspot Marketplace - Caching Architecture

## Overview

This document describes the caching architecture of the Vidiaspot Marketplace application, which uses MySQL as the primary database and SQLite as a local cache layer to reduce read operations.

## Architecture

### Primary Database: MySQL
- **Purpose**: Primary data storage for all application data
- **Usage**: All data writes and main data operations
- **Environments**: Both development and production

### Cache Layer: SQLite  
- **Purpose**: Local cache to reduce read operations from MySQL
- **Usage**: Frequently accessed data is cached in SQLite to improve performance
- **Location**: Local `database/cache.sqlite` file
- **Mechanism**: Uses Laravel's database cache driver with SQLite connection

## Configuration

### Database Connections
```php
// config/database.php
'mysql' => [
    // MySQL primary database configuration
    'driver' => 'mysql',
    // ... configuration
],

'sqlite_cache' => [
    'driver' => 'sqlite',
    'database' => env('SQLITE_CACHE_DATABASE', database_path('cache.sqlite')),
    // ... configuration
],
```

### Cache Configuration
```php
// config/cache.php
'database' => [
    'driver' => 'database',
    'connection' => env('DB_CACHE_CONNECTION', 'sqlite_cache'),  // Uses SQLite
    'table' => env('DB_CACHE_TABLE', 'cache'),
    'lock_connection' => env('DB_CACHE_LOCK_CONNECTION', 'sqlite_cache'),
    'lock_table' => env('DB_CACHE_LOCK_TABLE'),
],
```

### Environment Variables
```bash
# .env
DB_CONNECTION=mysql                    # Primary database
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

# SQLite cache configuration
SQLITE_CACHE_DATABASE=database/cache.sqlite
DB_CACHE_CONNECTION=sqlite_cache
DB_CACHE_LOCK_CONNECTION=sqlite_cache
CACHE_STORE=database
```

## Implementation

### Using the Cache

The application uses Laravel's Cache facade throughout, which automatically uses SQLite as configured:

```php
use Illuminate\Support\Facades\Cache;

// Basic caching
Cache::put('key', 'value', $ttl);
$value = Cache::get('key');

// Cache with expiration
Cache::remember('key', now()->addHours(1), function() {
    return $this->getExpensiveDataFromMysql();
});
```

### Custom Cache Service

The application includes a custom service for explicit MySQL -> SQLite caching:

```php
use App\Services\MySqlToSqliteCacheService;

$cacheService = app(MySqlToSqliteCacheService::class);

$result = $cacheService->getFromCacheOrDb(
    'cache_key', 
    function() {
        // This query hits MySQL
        return SomeModel::where('active', true)->get();
    },
    3600 // TTL in seconds
);
```

### Service Integration

Many services in the application already implement caching patterns:

- `AdPlacementService` - Caches ad placement queries
- `RedisService` - Provides cache management functions
- Other services - Use Cache facade for frequent data access

## Benefits

1. **Reduced MySQL Load**: Frequently accessed data served from local SQLite cache
2. **Improved Performance**: Faster response times for cached data
3. **Cost Efficiency**: Lower MySQL resource utilization
4. **Scalability**: System can handle more concurrent users
5. **Resilience**: Application continues to work with degraded performance if primary database has issues

## Usage Guidelines

### When to Cache
- Frequently accessed data (e.g., user profiles, category lists, settings)
- Expensive queries that are run often
- Data that doesn't change frequently

### Cache Keys
- Use descriptive, hierarchical keys: `user:123:profile`, `category:electronics:ads`
- Include relevant parameters in keys to prevent stale data

### Cache Invalidation
- Clear cache when underlying data changes
- Use cache tags for related data invalidation
- Consider TTL settings based on data update frequency

## Performance Monitoring

Monitor the effectiveness of the caching strategy:
- Cache hit ratio: Percentage of requests served from cache
- Database query reduction: Count of queries avoided due to caching
- Response time improvements: Faster page loads for cached data
- Resource utilization: Reduced MySQL server load

## Troubleshooting

### Cache Not Working
- Verify `DB_CACHE_CONNECTION` is set to `sqlite_cache`
- Check that `database/cache.sqlite` file exists and is writable
- Ensure cache table migrations have been run

### Performance Issues
- Monitor cache hit/miss ratios
- Adjust TTL values based on data access patterns
- Consider adding more caching for frequently accessed data

---

*This caching architecture is designed to work with both development and production environments using MySQL as the primary database and SQLite as the caching layer.*