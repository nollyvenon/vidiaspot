<?php

namespace App\Services;

use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Cache;

class RedisService
{
    /**
     * Cache a value with expiration
     * Uses SQLite cache layer to reduce reads from primary MySQL database
     *
     * @param string $key
     * @param mixed $value
     * @param int $ttl Time to live in seconds
     * @return bool
     */
    public function put(string $key, $value, int $ttl = 3600): bool
    {
        return Cache::put($key, $value, $ttl);
    }

    /**
     * Get a cached value
     *
     * @param string $key
     * @return mixed
     */
    public function get(string $key)
    {
        return Cache::get($key);
    }

    /**
     * Check if a key exists
     *
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return Cache::has($key);
    }

    /**
     * Remove a key from cache
     *
     * @param string $key
     * @return bool
     */
    public function forget(string $key): bool
    {
        return Cache::forget($key);
    }

    /**
     * Increment a value
     *
     * @param string $key
     * @param int $value
     * @return int
     */
    public function increment(string $key, int $value = 1): int
    {
        return Cache::increment($key, $value);
    }

    /**
     * Decrement a value
     *
     * @param string $key
     * @param int $value
     * @return int
     */
    public function decrement(string $key, int $value = 1): int
    {
        return Cache::decrement($key, $value);
    }

    /**
     * Store multiple key-value pairs
     *
     * @param array $data
     * @param int $ttl
     * @return bool
     */
    public function putMultiple(array $data, int $ttl = 3600): bool
    {
        return Cache::putMany($data, $ttl);
    }

    /**
     * Get multiple values by keys
     *
     * @param array $keys
     * @return array
     */
    public function getMultiple(array $keys): array
    {
        return Cache::many($keys);
    }

    /**
     * Cache data with tags (for invalidation)
     *
     * @param string $tag
     * @param string $key
     * @param mixed $value
     * @param int $ttl
     * @return mixed
     */
    public function putWithTag(string $tag, string $key, $value, int $ttl = 3600)
    {
        return Cache::tags([$tag])->put($key, $value, $ttl);
    }

    /**
     * Get data with tag
     *
     * @param string $tag
     * @param string $key
     * @return mixed
     */
    public function getWithTag(string $tag, string $key)
    {
        return Cache::tags([$tag])->get($key);
    }

    /**
     * Flush all items with specific tag
     *
     * @param string $tag
     * @return bool
     */
    public function flushTag(string $tag): bool
    {
        return Cache::tags([$tag])->flush();
    }

    /**
     * Store session data in Redis
     *
     * @param string $sessionId
     * @param array $data
     * @param int $ttl Session timeout in seconds
     * @return bool
     */
    public function storeSession(string $sessionId, array $data, int $ttl = 3600): bool
    {
        $key = "session:{$sessionId}";
        return $this->put($key, $data, $ttl);
    }

    /**
     * Retrieve session data from Redis
     *
     * @param string $sessionId
     * @return array|null
     */
    public function getSession(string $sessionId): ?array
    {
        $key = "session:{$sessionId}";
        return $this->get($key);
    }

    /**
     * Delete session data from Redis
     *
     * @param string $sessionId
     * @return bool
     */
    public function deleteSession(string $sessionId): bool
    {
        $key = "session:{$sessionId}";
        return $this->forget($key);
    }

    /**
     * Cache user's browsing history
     *
     * @param int $userId
     * @param array $itemIds
     * @param int $ttl
     * @return bool
     */
    public function saveUserHistory(int $userId, array $itemIds, int $ttl = 86400): bool
    {
        $key = "user_history:{$userId}";
        
        // Get existing history
        $history = $this->get($key) ?: [];
        
        // Add new items to history (limit to last 50 items)
        $history = array_slice([...$itemIds, ...$history], 0, 50);
        
        return $this->put($key, $history, $ttl);
    }

    /**
     * Get user's browsing history
     *
     * @param int $userId
     * @return array
     */
    public function getUserHistory(int $userId): array
    {
        $key = "user_history:{$userId}";
        return $this->get($key) ?: [];
    }

    /**
     * Store search queries with expiry
     *
     * @param string $queryHash
     * @param array $results
     * @param int $ttl
     * @return bool
     */
    public function cacheSearchResults(string $queryHash, array $results, int $ttl = 1800): bool
    {
        $key = "search:{$queryHash}";
        return $this->put($key, $results, $ttl);
    }

    /**
     * Get cached search results
     *
     * @param string $queryHash
     * @return array|null
     */
    public function getSearchResults(string $queryHash): ?array
    {
        $key = "search:{$queryHash}";
        return $this->get($key);
    }

    /**
     * Cache frequently accessed data (like categories, currencies, etc.)
     *
     * @param string $type
     * @param mixed $data
     * @param int $ttl
     * @return bool
     */
    public function cacheReferenceData(string $type, $data, int $ttl = 7200): bool
    {
        $key = "reference:{$type}";
        return $this->put($key, $data, $ttl);
    }

    /**
     * Get cached reference data
     *
     * @param string $type
     * @return mixed
     */
    public function getReferenceData(string $type)
    {
        $key = "reference:{$type}";
        return $this->get($key);
    }

    /**
     * Store temporary tokens (like OTPs, password reset tokens)
     *
     * @param string $token
     * @param string $value
     * @param int $ttl
     * @return bool
     */
    public function storeTemporaryToken(string $token, string $value, int $ttl = 300): bool
    {
        $key = "token:{$token}";
        return $this->put($key, $value, $ttl);
    }

    /**
     * Validate temporary token
     *
     * @param string $token
     * @return string|bool Value if valid, false if invalid/expired
     */
    public function validateToken(string $token)
    {
        $key = "token:{$token}";
        $value = $this->get($key);
        
        if ($value !== null) {
            return $value;
        }
        
        return false;
    }
}