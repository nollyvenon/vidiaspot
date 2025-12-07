<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Faq extends Model
{
    protected $fillable = [
        'question',
        'answer',
        'category_id',
        'parent_id',
        'order',
        'is_active',
        'is_featured',
        'metadata',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'order' => 'integer',
        'metadata' => 'array',
    ];

    /**
     * Get the category this FAQ belongs to.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class); // Using the existing Category model
    }

    /**
     * Get the parent FAQ (if this is a sub-FAQ).
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Faq::class, 'parent_id');
    }

    /**
     * Get the sub-FAQs.
     */
    public function children(): HasMany
    {
        return $this->hasMany(Faq::class, 'parent_id');
    }

    /**
     * Scope to get active FAQs.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get featured FAQs.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope to get FAQs by category.
     */
    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }
}
