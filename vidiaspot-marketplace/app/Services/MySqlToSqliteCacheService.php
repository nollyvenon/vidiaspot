<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

/**
 * Service to handle reading from MySQL and caching to SQLite
 * This service ensures that frequently accessed data from MySQL is cached in SQLite
 * to reduce read operations from the primary MySQL database and improve performance.
 * In this architecture, MySQL is the primary data source and SQLite serves as a
 * local cache layer to reduce read load.
 */
class MySqlToSqliteCacheService
{
    /**
     * Get data from cache or fetch from MySQL if not cached
     *
     * @param string $cacheKey The key to use for caching
     * @param callable $mysqlQuery The function that performs the MySQL query
     * @param int $ttl Cache time-to-live in seconds
     * @return mixed The cached or freshly fetched data
     */
    public function getFromCacheOrDb(string $cacheKey, callable $mysqlQuery, int $ttl = 3600)
    {
        return Cache::remember($cacheKey, $ttl, function () use ($mysqlQuery) {
            // Execute the MySQL query
            return $mysqlQuery();
        });
    }

    /**
     * Get model data from cache or fetch from MySQL if not cached
     *
     * @param string $cacheKey The key to use for caching
     * @param Model|string $model The Eloquent model or model class name
     * @param callable|null $queryBuilder Optional query builder modifications
     * @param int $ttl Cache time-to-live in seconds
     * @return mixed The cached or freshly fetched model data
     */
    public function getModelFromCache(string $cacheKey, $model, callable $queryBuilder = null, int $ttl = 3600)
    {
        return Cache::remember($cacheKey, $ttl, function () use ($model, $queryBuilder) {
            $query = is_string($model) ? $model::query() : $model->newQuery();
            
            if ($queryBuilder) {
                $query = $queryBuilder($query);
            }
            
            return $query->get();
        });
    }

    /**
     * Get a single model instance from cache or fetch from MySQL if not cached
     *
     * @param string $cacheKey The key to use for caching
     * @param Model|string $model The Eloquent model or model class name
     * @param mixed $id The ID of the record to fetch
     * @param int $ttl Cache time-to-live in seconds
     * @return mixed The cached or freshly fetched model instance
     */
    public function getSingleModelFromCache(string $cacheKey, $model, $id, int $ttl = 3600)
    {
        return Cache::remember($cacheKey, $ttl, function () use ($model, $id) {
            if (is_string($model)) {
                return $model::find($id);
            }
            return $model->find($id);
        });
    }

    /**
     * Clear specific cache entry
     *
     * @param string $cacheKey The key to clear from cache
     * @return bool Whether the cache entry was cleared
     */
    public function clearCache(string $cacheKey): bool
    {
        return Cache::forget($cacheKey);
    }

    /**
     * Clear cache entries by tag
     *
     * @param string $tag The tag to clear
     * @return bool Whether the tagged cache entries were cleared
     */
    public function clearTaggedCache(string $tag): bool
    {
        return Cache::tags([$tag])->flush();
    }

    /**
     * Get data with tagging capability for easier invalidation
     *
     * @param string $tag Cache tag for group invalidation
     * @param string $cacheKey The key to use for caching
     * @param callable $mysqlQuery The function that performs the MySQL query
     * @param int $ttl Cache time-to-live in seconds
     * @return mixed The cached or freshly fetched data
     */
    public function getFromTaggedCache(string $tag, string $cacheKey, callable $mysqlQuery, int $ttl = 3600)
    {
        return Cache::tags([$tag])->remember($cacheKey, $ttl, function () use ($mysqlQuery) {
            return $mysqlQuery();
        });
    }

    /**
     * Cache relationship data (e.g., eager loading)
     *
     * @param string $cacheKey The key to use for caching
     * @param Model $model The model to fetch with relationships
     * @param array $relationships The relationships to eager load
     * @param callable|null $queryBuilder Optional query builder modifications
     * @param int $ttl Cache time-to-live in seconds
     * @return mixed The cached or freshly fetched model data with relationships
     */
    public function getWithRelationships(string $cacheKey, $model, array $relationships = [], callable $queryBuilder = null, int $ttl = 3600)
    {
        return Cache::remember($cacheKey, $ttl, function () use ($model, $relationships, $queryBuilder) {
            $query = is_string($model) ? $model::with($relationships) : $model->with($relationships);
            
            if ($queryBuilder) {
                $query = $queryBuilder($query);
            }
            
            return $query->get();
        });
    }

    /**
     * Get paginated results from cache
     *
     * @param string $cacheKey The key to use for caching
     * @param Model|string $model The Eloquent model or model class name
     * @param int $perPage Number of items per page
     * @param callable|null $queryBuilder Optional query builder modifications
     * @param int $ttl Cache time-to-live in seconds
     * @return mixed The cached or freshly fetched paginated results
     */
    public function getPaginatedFromCache(string $cacheKey, $model, int $perPage = 15, callable $queryBuilder = null, int $ttl = 3600)
    {
        return Cache::remember($cacheKey, $ttl, function () use ($model, $perPage, $queryBuilder) {
            $query = is_string($model) ? $model::query() : $model->newQuery();
            
            if ($queryBuilder) {
                $query = $queryBuilder($query);
            }
            
            return $query->paginate($perPage);
        });
    }

    /**
     * Cache expensive count queries
     *
     * @param string $cacheKey The key to use for caching
     * @param Model|string $model The Eloquent model or model class name
     * @param callable|null $queryBuilder Optional query builder modifications
     * @param int $ttl Cache time-to-live in seconds
     * @return int The cached or freshly fetched count
     */
    public function getCachedCount(string $cacheKey, $model, callable $queryBuilder = null, int $ttl = 3600): int
    {
        return Cache::remember($cacheKey, $ttl, function () use ($model, $queryBuilder) {
            $query = is_string($model) ? $model::query() : $model->newQuery();
            
            if ($queryBuilder) {
                $query = $queryBuilder($query);
            }
            
            return $query->count();
        });
    }

    /**
     * Cache complex queries with multiple conditions
     *
     * @param string $cacheKey The key to use for caching
     * @param callable $complexQuery The complex query to execute
     * @param int $ttl Cache time-to-live in seconds
     * @return mixed The cached or freshly fetched results
     */
    public function getComplexQueryResult(string $cacheKey, callable $complexQuery, int $ttl = 3600)
    {
        return Cache::remember($cacheKey, $ttl, function () use ($complexQuery) {
            return $complexQuery();
        });
    }
}