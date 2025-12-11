<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'author',
        'author_id',
        'category',
        'status',
        'is_featured',
        'tags',
        'featured_image',
        'view_count',
        'like_count',
        'comment_count',
        'published_at',
        'featured_video_id',
        'seo_meta',
        'reading_stats',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'tags' => 'array',
        'seo_meta' => 'array',
        'reading_stats' => 'array',
        'view_count' => 'integer',
        'like_count' => 'integer',
        'comment_count' => 'integer',
        'is_featured' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Scope to get only published posts
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->where('published_at', '<=', now());
    }

    /**
     * Scope to get featured posts
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope to filter by category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Relationship to the author (user)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Scope to get published posts
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->where('published_at', '<=', now());
    }

    /**
     * Scope to get featured posts
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope to filter by category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }
}
