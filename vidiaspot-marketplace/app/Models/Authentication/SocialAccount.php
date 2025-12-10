<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SocialAccount extends Model
{
    protected $fillable = [
        'user_id',
        'provider',
        'provider_user_id',
        'provider_username',
        'email',
        'avatar',
        'access_token',
        'refresh_token',
        'expires_at',
        'last_login_at',
        'metadata',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'last_login_at' => 'datetime',
        'metadata' => 'array',
    ];

    /**
     * Get the user that owns this social account.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to get accounts by provider.
     */
    public function scopeByProvider($query, string $provider)
    {
        return $query->where('provider', $provider);
    }

    /**
     * Scope to get accounts by provider user ID.
     */
    public function scopeByProviderId($query, string $providerUserId)
    {
        return $query->where('provider_user_id', $providerUserId);
    }

    /**
     * Check if access token is expired.
     */
    public function isAccessTokenExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Refresh the access token if expired.
     */
    public function refreshToken(): bool
    {
        if (!$this->refresh_token || !$this->isAccessTokenExpired()) {
            return !$this->isAccessTokenExpired();
        }

        // This would implement the token refresh logic for each provider
        // Implementation depends on the specific social provider's API
        return false;
    }
}
