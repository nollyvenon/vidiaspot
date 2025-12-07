<?php

namespace App\Models\Traits;

trait DatabaseOptimizationTrait
{
    /**
     * Boot the trait for a model.
     */
    protected static function bootDatabaseOptimizationTrait()
    {
        // Add global scopes to optimize queries
        static::addGlobalScopes();
    }

    /**
     * Add global scopes to optimize queries.
     */
    protected static function addGlobalScopes()
    {
        // Common optimization scopes can be added here
    }

    /**
     * Define common indexes that should be present on models using this trait.
     */
    public function getCommonIndexes()
    {
        return [
            'created_at',
            'updated_at',
            'status',
        ];
    }

    /**
     * Scope to get records with specific status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to get active records.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to get records within date range.
     */
    public function scopeDateRange($query, $startDate, $endDate, $field = 'created_at')
    {
        return $query->whereBetween($field, [$startDate, $endDate]);
    }

    /**
     * Scope to eager load common relationships to prevent N+1 queries.
     */
    public function scopeWithCommonRelations($query)
    {
        return $query;
    }
}
