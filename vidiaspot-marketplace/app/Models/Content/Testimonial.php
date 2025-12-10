<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Testimonial extends Model
{
    protected $fillable = [
        'name',
        'position',
        'company',
        'testimonial',
        'avatar_url',
        'source',
        'rating',
        'is_featured',
        'is_active',
        'user_id',
        'published_at',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'rating' => 'array',
        'published_at' => 'datetime',
    ];

    /**
     * Get the user who added this testimonial.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to get active testimonials.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get featured testimonials.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope to get published testimonials.
     */
    public function scopePublished($query)
    {
        return $query->where('published_at', '<=', now());
    }
}
