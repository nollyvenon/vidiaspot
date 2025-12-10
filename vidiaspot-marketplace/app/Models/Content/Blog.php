<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Blog extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'excerpt',
        'content',
        'featured_image',
        'status',
        'is_featured',
        'is_published',
        'published_at',
        'view_count',
        'tags',
        'meta',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'is_featured' => 'boolean',
        'is_published' => 'boolean',
        'view_count' => 'integer',
        'tags' => 'array',
        'meta' => 'array',
        'published_at' => 'datetime',
    ];

    /**
     * Get the author of this blog.
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Scope to get published blogs.
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true)
                    ->where('status', 'published');
    }

    /**
     * Scope to get featured blogs.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope to get blogs by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}
