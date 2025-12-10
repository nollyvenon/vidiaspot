<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContentPage extends Model
{
    protected $fillable = [
        'slug',
        'title',
        'content',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'page_type',
        'is_active',
        'is_featured',
        'view_count',
        'updated_by',
        'published_at',
    ];

    protected $casts = [
        'updated_by' => 'integer',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'view_count' => 'integer',
        'meta_keywords' => 'array',
        'published_at' => 'datetime',
    ];

    /**
     * Get the user who last updated this page.
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope to get active pages.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                    ->whereNotNull('published_at')
                    ->where('published_at', '<=', now());
    }

    /**
     * Scope to get by page type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('page_type', $type);
    }

    /**
     * Scope to get by slug.
     */
    public function scopeBySlug($query, $slug)
    {
        return $query->where('slug', $slug);
    }

    /**
     * Scope to get by page type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('page_type', $type);
    }
}
