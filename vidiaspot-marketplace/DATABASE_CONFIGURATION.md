# Vidiaspot Marketplace - Database Configuration Guide (MySQL Primary with SQLite Cache)

## Table of Contents
1. [Overview](#overview)
2. [Database Architecture](#database-architecture)
3. [MySQL Configuration (Primary Database)](#mysql-configuration-primary-database)
4. [SQLite Configuration (Local Cache Layer)](#sqlite-configuration-local-cache-layer)
5. [Database Migrations](#database-migrations)
6. [Read Replica Configuration](#read-replica-configuration)
7. [Performance Optimization](#performance-optimization)
8. [Connection Pooling](#connection-pooling)
9. [Caching Strategy](#caching-strategy)
10. [Troubleshooting](#troubleshooting)

## Overview

Vidiaspot Marketplace is designed with SQLite as the default primary database for local development, with MySQL as the option for production. SQLite can also be used as a local cache layer to reduce operations from the main database. This architecture provides high performance and scalability while maintaining cost efficiency.

### Database Strategy
- **Default (Development)**: SQLite for all application data
- **Production Option**: MySQL as primary, SQLite for caching
- **Local Cache**: Can use SQLite for reducing read load from main database
- **Feature Parity**: Same functionality across environments

## Database Architecture

### Supported Databases
- **Primary Database**: MySQL 8.0+ (main application data)
- **Local Cache Layer**: SQLite 3.26+ (for reducing read load)
- **Alternative**: PostgreSQL, SQL Server (with additional configuration)

### Database Schema
The application uses a normalized schema with proper relationships and foreign key constraints (when supported).

### Key Database Tables
- `users`: User authentication and profiles
- `ads`: Classified ad listings
- `categories`: Ad category hierarchy
- `ad_images`: Ad image storage
- `payment_transactions`: Payment tracking
- `ad_placements`: Ad placement management
- `messages`: User-to-user messaging
- `reviews`: User ratings and reviews

## MySQL Configuration (Primary Database)

### Production Configuration
MySQL is configured as the primary database in the `.env` file:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=vidiaspot_marketplace
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### Setting Up MySQL Primary Database

#### 1. Create MySQL Database
```sql
CREATE DATABASE vidiaspot_marketplace CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'vidiaspot_user'@'localhost' IDENTIFIED BY 'your_secure_password';
GRANT ALL PRIVILEGES ON vidiaspot_marketplace.* TO 'vidiaspot_user'@'localhost';
FLUSH PRIVILEGES;
```

#### 2. Configure Environment
Ensure these settings in `.env`:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=vidiaspot_marketplace
DB_USERNAME=vidiaspot_user
DB_PASSWORD=your_secure_password
```

### MySQL Advantages as Primary Database
- **Performance**: Optimized for high-load applications
- **Scalability**: Horizontal and vertical scaling options
- **Features**: Full SQL standard compliance
- **Replication**: Master-slave replication support
- **Security**: Advanced user management and security features

## SQLite Configuration (Local Cache Layer)

### Cache Configuration
SQLite is used as a local cache layer to reduce read operations from the primary MySQL database:

```
# In cache configuration (config/cache.php)
'sqlite_cache' => [
    'driver' => 'database',
    'table' => 'cache',
    'connection' => 'sqlite_cache',
    'lock_connection' => 'sqlite_cache',
],
```

### Setting Up SQLite Cache Layer

#### 1. Create SQLite Cache Database
```bash
# Create SQLite cache file
touch database/cache.sqlite
# Or on Windows:
# type nul > database\cache.sqlite
```

#### 2. Configure Environment Variables
Add to your `.env` file:
```
SQLITE_CACHE_DATABASE=database/cache.sqlite
DB_CACHE_CONNECTION=sqlite_cache
DB_CACHE_LOCK_CONNECTION=sqlite_cache
```

#### 3. Configure Cache Store
The cache configuration is already set to use SQLite:
```php
// In config/cache.php
'database' => [
    'driver' => 'database',
    'connection' => env('DB_CACHE_CONNECTION', 'sqlite_cache'),  // Uses SQLite for cache
    'table' => env('DB_CACHE_TABLE', 'cache'),
    'lock_connection' => env('DB_CACHE_LOCK_CONNECTION', 'sqlite_cache'),
    'lock_table' => env('DB_CACHE_LOCK_TABLE'),
],
```

### Cache Advantages
- **Reduced Load**: Minimizes read queries to primary database
- **Faster Response**: Local cache provides faster access to frequently requested data
- **Cost Efficiency**: Reduces primary database load and costs
- **Resilience**: Application continues to work even if cache layer fails

## Caching Strategy

### Purpose
The SQLite cache layer is designed to dramatically reduce read operations from the primary MySQL database by storing frequently accessed data locally. This approach:

- **Minimizes Database Load**: Reduces the number of queries hitting the primary MySQL database
- **Improves Performance**: Provides faster access to frequently requested data
- **Reduces Costs**: Lower MySQL resource utilization means lower infrastructure costs
- **Increases Availability**: System remains functional with degraded performance if primary database has issues

### Data Caching Approach
- **Time-based Caching**: Cache entries expire after configurable time intervals
- **Tag-based Caching**: Related data grouped under cache tags for easy invalidation
- **Conditional Caching**: Only cache data that is frequently accessed
- **Cache Warming**: Proactively cache important data during low-traffic periods

### Implementation with Ad Placement Service
The AdPlacementService demonstrates how the caching strategy reduces database reads:
```php
class AdPlacementService
{
    public function getActivePlacementsByPosition($position, $filters = [])
    {
        // Try to get from cache first to reduce database reads
        $cacheKey = "ad_placements_{$position}_" . md5(serialize($filters));

        return Cache::remember($cacheKey, now()->addHours(1), function() use ($position, $filters) {
            // This expensive database query only runs when cache is expired or empty
            $query = AdPlacement::where('position', $position)
                              ->where('is_active', true)
                              ->where('starts_at', '<=', now())
                              ->where('expires_at', '>=', now());

            // Apply additional filters
            if (!empty($filters['category'])) {
                $query->whereJsonContains('target_audience', $filters['category']);
            }

            return $query->orderBy('priority', 'desc')
                         ->orderBy('created_at', 'desc')
                         ->get();
        });
    }
}
```

### Cache Invalidation Strategy
- **Event-driven Invalidation**: When ad placements are updated, corresponding cache entries are cleared
- **Time-based Expiration**: Automatic cache refresh after specified duration
- **Selective Invalidation**: Only invalidate caches for affected positions
- **Warm-up Process**: Pre-populate cache after invalidation

### Cache Configuration for Ad Placements
```php
// Cache configuration optimized for ad placements
[
    'database' => [
        'driver' => 'database',
        'connection' => 'sqlite_cache',  // Use SQLite for cache storage
        'table' => 'cache',
        'lock_connection' => 'sqlite_cache',
        'lock_table' => 'cache_locks',
    ],
]
```

### Benefits for Ad Placement System
- **Reduced Database Load**: Frequent ad placement queries served from local cache
- **Faster Page Loads**: Cached ad placements load instantly
- **Improved User Experience**: No delays waiting for ad placement queries
- **Scalability**: System can handle more concurrent users with reduced database load

### Performance Monitoring
Monitor the effectiveness of the caching strategy:
- Cache hit ratio: Percentage of requests served from cache
- Database query reduction: Count of queries avoided due to caching
- Response time improvements: Faster page loads for ad-heavy pages
- Resource utilization: Reduced MySQL server load

### Recommended MySQL Settings

#### my.cnf Configuration
```
[mysqld]
# Connection settings
max_connections = 200
max_user_connections = 150

# Performance settings
innodb_buffer_pool_size = 1G
innodb_log_file_size = 256M
query_cache_type = 1
query_cache_size = 64M

# Storage settings
innodb_file_per_table = 1
innodb_flush_log_at_trx_commit = 2
```

### MySQL Primary Database Advantages
- **Performance**: Optimized for high-load applications
- **Scalability**: Horizontal and vertical scaling options
- **Features**: Full SQL standard compliance
- **Replication**: Master-slave replication support
- **Security**: Advanced user management and security features

## Database Migrations

### Migration Compatibility
All migrations in the Vidiaspot Marketplace are designed to work with both SQLite and MySQL:

#### Compatible Migration Example:
```php
Schema::create('ads', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->text('description');
    $table->decimal('price', 10, 2);
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->foreignId('category_id')->constrained()->onDelete('cascade');
    $table->string('location');
    $table->boolean('is_featured')->default(false);
    $table->timestamps();
    
    $table->index(['category_id', 'location']);
    $table->index('created_at');
});
```

### Running Migrations
```bash
# Fresh install (deletes existing data)
php artisan migrate:fresh

# Regular migration (adds new changes)
php artisan migrate

# Rollback last migration batch
php artisan migrate:rollback

# Show migration status
php artisan migrate:status
```

### Migration Best Practices
- Use `constrained()` for foreign keys (Laravel handles cross-database compatibility)
- Use `morphs()` for polymorphic relationships
- Use appropriate index strategies
- Test migrations on both database types

## Read Replica Configuration

### Multi-Database Connection Setup
The application supports read-write separation for high-traffic scenarios:

#### .env Configuration:
```
# Primary (write) database
DB_HOST_WRITE=primary-db.example.com
DB_PORT_WRITE=3306
DB_USERNAME_WRITE=primary_user
DB_PASSWORD_WRITE=primary_password

# Replica (read) database(s)
DB_HOST_READ=replica1.example.com,replica2.example.com
DB_PORT_READ=3306,3306
DB_USERNAME_READ=replica_user
DB_PASSWORD_READ=replica_password

# Common settings
DB_CONNECTION=mysql
DB_DATABASE=vidiaspot_marketplace
```

### Connection Configuration in database.php
```php
'mysql' => [
    'driver' => 'mysql',
    'host' => [
        'write' => env('DB_HOST_WRITE', env('DB_HOST', '127.0.0.1')),
        'read' => env('DB_HOST_READ', env('DB_HOST', '127.0.0.1')),
    ],
    'port' => [
        'write' => env('DB_PORT_WRITE', env('DB_PORT', '3306')),
        'read' => env('DB_PORT_READ', env('DB_PORT', '3306')),
    ],
    'database' => env('DB_DATABASE', 'laravel'),
    'username' => [
        'write' => env('DB_USERNAME_WRITE', env('DB_USERNAME', 'root')),
        'read' => env('DB_USERNAME_READ', env('DB_USERNAME', 'root')),
    ],
    'password' => [
        'write' => env('DB_PASSWORD_WRITE', env('DB_PASSWORD', '')),
        'read' => env('DB_PASSWORD_READ', env('DB_PASSWORD', '')),
    ],
    // ... other configuration
],
```

### Read Replica Usage
- **Automatic**: Laravel automatically routes SELECT queries to read replicas
- **Writes**: All WRITE operations go to the primary database
- **Consistency**: Fresh data queries can be forced to use primary with `->useWritePdo()`

## Performance Optimization

### SQLite Optimization
```php
// In database configuration
'sqlite' => [
    'driver' => 'sqlite',
    'url' => env('DB_URL'),
    'database' => env('DB_DATABASE', database_path('database.sqlite')),
    'prefix' => '',
    'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
    'busy_timeout' => null,  // Increase if experiencing locking issues
    'journal_mode' => 'wal', // Better for concurrent access
    'synchronous' => 'normal', // Performance vs safety trade-off
    'transaction_mode' => 'DEFERRED',
],
```

### MySQL Performance Settings

#### Query Optimization
```php
// Use eager loading to prevent N+1 queries
$ads = Ad::with(['user', 'category', 'images'])->paginate(20);

// Use indexing properly
// Migration example with proper indexing
Schema::create('ads', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->foreignId('category_id');
    $table->string('location');
    $table->timestamps();
    
    // Composite index for common queries
    $table->index(['category_id', 'location']);
    
    // Single indexes
    $table->index('created_at');
    $table->index('title');
});
```

#### Connection Pooling
MySQL supports connection pooling through persistent connections:
```php
'options' => [
    PDO::ATTR_PERSISTENT => true, // Connection pooling
    // ... other options
],
```

### Caching Strategies
- **Redis**: For session and cache storage
- **Application Cache**: For frequently accessed data
- **Database Cache**: Query result caching

## Connection Pooling

### Configuration
Connection pooling is enabled by default for MySQL:
```php
'options' => [
    PDO::ATTR_PERSISTENT => true, // Enable connection pooling
],
```

### Pooling Benefits
- **Performance**: Reduced connection overhead
- **Scalability**: Handle more concurrent requests
- **Resource Usage**: Reduced database server load

### Considerations
- **Connection Limits**: Respect database connection limits
- **Memory Usage**: Persistent connections use more memory
- **Transaction Safety**: Be careful with transaction isolation

## Database Seeding

### Running Seeders
```bash
# Run all seeders
php artisan db:seed

# Run specific seeder
php artisan db:seed --class=UserSeeder

# Run with fresh migration
php artisan migrate:fresh --seed
```

### Seeder Compatibility
Seeders are compatible with both SQLite and MySQL:
```php
// Example seeder that works with both databases
public function run()
{
    User::create([
        'name' => 'Admin User',
        'email' => 'admin@example.com',
        'password' => bcrypt('password'),
        'email_verified_at' => now(),
    ]);
    
    // Categories
    Category::create([
        'name' => 'Electronics',
        'slug' => 'electronics',
        'parent_id' => null,
    ]);
}
```

## Troubleshooting

### Common Issues

#### Issue: "database is locked" (SQLite)
**Cause**: Multiple processes trying to write simultaneously
**Solution**: 
```bash
# Increase busy timeout in configuration
'busy_timeout' => 5000, // 5 seconds
```

#### Issue: "SQLSTATE[HY000] [2002] Connection refused" (MySQL)
**Cause**: MySQL server not running or wrong configuration
**Solution**:
1. Verify MySQL is running
2. Check connection parameters in `.env`
3. Ensure firewall allows connections

#### Issue: Foreign Key Constraint Errors
**Solution**: 
```bash
# Temporarily disable foreign key checks if needed
DB_FOREIGN_KEYS=false  # In .env
```

### Environment Switching
Easily switch between databases:
```bash
# For local development
DB_CONNECTION=sqlite

# For production
DB_CONNECTION=mysql
```

### Performance Monitoring
```bash
# Monitor query performance
DB_LOG_SQL=true  # In .env for debugging
```

### Database Health Checks
```php
// Example health check controller
public function healthCheck()
{
    try {
        DB::connection()->getPdo();
        return response()->json(['status' => 'healthy', 'database' => true]);
    } catch (\Exception $e) {
        return response()->json(['status' => 'unhealthy', 'database' => false], 500);
    }
}
```

## Production Deployment Considerations

### Database Preparation
1. **Backup**: Always backup before migrating
2. **Maintenance Mode**: Enable during major updates
3. **Testing**: Test migrations on staging first
4. **Monitoring**: Monitor during and after deployment

### Scaling Strategies
- **Read Replicas**: Distribute read load across multiple servers
- **Connection Pooling**: Optimize connection usage
- **Caching**: Reduce database load through caching
- **Indexing**: Proper indexing for query optimization

## Security Considerations

### Database Security
- **User Permissions**: Use least-privilege principle
- **Connection Security**: Use SSL/TLS for connections
- **SQL Injection**: Laravel's query builder prevents injection
- **Environment Variables**: Never commit credentials

### Configuration Security
```bash
# Ensure .env file is not accessible
# Set proper file permissions
chmod 600 .env
```

## Migration from SQLite to MySQL

### Steps to Migrate
1. **Backup SQLite Database**: Copy the database file
2. **Set Up MySQL Database**: Create new MySQL database
3. **Update Configuration**: Change `.env` settings
4. **Run Migrations**: Create schema in MySQL
5. **Data Migration**: Export/import data between databases

### Tool Recommendation
For large datasets, consider using Laravel's built-in database facilities or external tools like:
- `mysqldump` + `sqlite3` command line tools
- Database management GUIs
- Specialized migration tools

---

*This database configuration guide is updated with each release. Ensure you test database changes in a staging environment before production deployment.*