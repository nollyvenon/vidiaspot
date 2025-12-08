<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SellerTool extends Model
{
    protected $fillable = [
        'user_id',
        'tool_type', // 'bulk_editor', 'pricing_analyzer', 'repricing_tool', 'crm_integration', 'loyalty_program', 'cross_platform_sync', 'seasonal_planner'
        'name',
        'description',
        'is_active',
        'settings',
        'integration_config',
        'usage_stats',
        'last_used_at',
        'permissions',
        'access_level',
        'trial_expires_at',
        'subscription_status',
        'custom_fields',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'settings' => 'array',
        'integration_config' => 'array',
        'usage_stats' => 'array',
        'permissions' => 'array',
        'access_level' => 'string',
        'trial_expires_at' => 'datetime',
        'subscription_status' => 'string',
        'custom_fields' => 'array',
        'last_used_at' => 'datetime',
    ];

    /**
     * Get the user who owns this tool
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to get only active tools
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get tools by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('tool_type', $type);
    }

    /**
     * Check if tool is in trial period
     */
    public function isInTrialPeriod(): bool
    {
        return $this->trial_expires_at && $this->trial_expires_at->isFuture();
    }

    /**
     * Check if user has access to this tool
     */
    public function userHasAccess($userId): bool
    {
        return $this->user_id === $userId || $this->permissions && in_array($userId, $this->permissions);
    }
}
