<?php

namespace App\Services;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;

class SessionManager
{
    protected RedisService $redisService;

    public function __construct(RedisService $redisService)
    {
        $this->redisService = $redisService;
    }

    /**
     * Create a new session
     *
     * @param array $data
     * @param int $ttl
     * @return string
     */
    public function createSession(array $data, int $ttl = 3600): string
    {
        $sessionId = Str::uuid()->toString();
        
        // Store session data
        $this->redisService->storeSession($sessionId, $data, $ttl);
        
        // Set cookie
        Cookie::queue(Cookie::make('session_id', $sessionId, $ttl / 60));
        
        return $sessionId;
    }

    /**
     * Get session data by ID
     *
     * @param string $sessionId
     * @return array|null
     */
    public function getSessionData(string $sessionId): ?array
    {
        if (empty($sessionId)) {
            return null;
        }
        
        return $this->redisService->getSession($sessionId);
    }

    /**
     * Update session data
     *
     * @param string $sessionId
     * @param array $data
     * @param int $ttl
     * @return bool
     */
    public function updateSession(string $sessionId, array $data, int $ttl = 3600): bool
    {
        if (empty($sessionId)) {
            return false;
        }
        
        return $this->redisService->storeSession($sessionId, $data, $ttl);
    }

    /**
     * Invalidate a session
     *
     * @param string $sessionId
     * @return bool
     */
    public function invalidateSession(string $sessionId): bool
    {
        if (empty($sessionId)) {
            return false;
        }
        
        return $this->redisService->deleteSession($sessionId);
    }

    /**
     * Extend session expiry
     *
     * @param string $sessionId
     * @param int $ttl
     * @return bool
     */
    public function extendSession(string $sessionId, int $ttl = 3600): bool
    {
        if (empty($sessionId)) {
            return false;
        }
        
        $currentData = $this->redisService->getSession($sessionId);
        if (!$currentData) {
            return false;
        }
        
        return $this->redisService->storeSession($sessionId, $currentData, $ttl);
    }

    /**
     * Get session ID from request
     *
     * @return string|null
     */
    public function getSessionId(): ?string
    {
        return Cookie::get('session_id');
    }

    /**
     * Check if session exists and is valid
     *
     * @param string $sessionId
     * @return bool
     */
    public function isValidSession(string $sessionId): bool
    {
        return $this->redisService->getSession($sessionId) !== null;
    }

    /**
     * Store user-specific data in session
     *
     * @param string $sessionId
     * @param string $key
     * @param mixed $value
     * @param int $ttl
     * @return bool
     */
    public function storeUserSessionData(string $sessionId, string $key, $value, int $ttl = 3600): bool
    {
        if (empty($sessionId)) {
            return false;
        }
        
        $sessionData = $this->redisService->getSession($sessionId) ?: [];
        $sessionData[$key] = $value;
        
        return $this->redisService->storeSession($sessionId, $sessionData, $ttl);
    }

    /**
     * Get user-specific data from session
     *
     * @param string $sessionId
     * @param string $key
     * @return mixed
     */
    public function getUserSessionData(string $sessionId, string $key)
    {
        if (empty($sessionId)) {
            return null;
        }
        
        $sessionData = $this->redisService->getSession($sessionId);
        return $sessionData[$key] ?? null;
    }

    /**
     * Remove user-specific data from session
     *
     * @param string $sessionId
     * @param string $key
     * @return bool
     */
    public function removeUserSessionData(string $sessionId, string $key): bool
    {
        if (empty($sessionId)) {
            return false;
        }
        
        $sessionData = $this->redisService->getSession($sessionId) ?: [];
        unset($sessionData[$key]);
        
        $ttl = 3600; // Get original TTL somehow, defaulting to 1 hour
        return $this->redisService->storeSession($sessionId, $sessionData, $ttl);
    }

    /**
     * Get or create user's cart in session
     *
     * @param string $sessionId
     * @return array
     */
    public function getUserCart(string $sessionId): array
    {
        return $this->getUserSessionData($sessionId, 'cart') ?: [];
    }

    /**
     * Add item to user's cart in session
     *
     * @param string $sessionId
     * @param int $itemId
     * @param int $quantity
     * @return bool
     */
    public function addToCart(string $sessionId, int $itemId, int $quantity = 1): bool
    {
        $cart = $this->getUserCart($sessionId);
        $cart[$itemId] = ($cart[$itemId] ?? 0) + $quantity;
        
        return $this->storeUserSessionData($sessionId, 'cart', $cart);
    }

    /**
     * Remove item from user's cart in session
     *
     * @param string $sessionId
     * @param int $itemId
     * @return bool
     */
    public function removeFromCart(string $sessionId, int $itemId): bool
    {
        $cart = $this->getUserCart($sessionId);
        unset($cart[$itemId]);
        
        return $this->storeUserSessionData($sessionId, 'cart', $cart);
    }

    /**
     * Clear user's cart in session
     *
     * @param string $sessionId
     * @return bool
     */
    public function clearCart(string $sessionId): bool
    {
        return $this->removeUserSessionData($sessionId, 'cart');
    }

    /**
     * Get user's recently viewed items from session
     *
     * @param string $sessionId
     * @return array
     */
    public function getRecentlyViewed(string $sessionId): array
    {
        return $this->getUserSessionData($sessionId, 'recently_viewed') ?: [];
    }

    /**
     * Add item to user's recently viewed in session
     *
     * @param string $sessionId
     * @param int $itemId
     * @return bool
     */
    public function addToRecentlyViewed(string $sessionId, int $itemId): bool
    {
        $recentlyViewed = $this->getRecentlyViewed($sessionId);
        
        // Add to beginning of array
        array_unshift($recentlyViewed, $itemId);
        
        // Keep only last 10 items
        $recentlyViewed = array_slice($recentlyViewed, 0, 10);
        
        return $this->storeUserSessionData($sessionId, 'recently_viewed', $recentlyViewed);
    }
}