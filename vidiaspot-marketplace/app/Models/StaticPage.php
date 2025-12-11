<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaticPage extends Model
{
    use HasFactory;

    protected $fillable = [
        'page_key',
        'title',
        'content',
        'locale',
        'status',
        'order',
        'author_id',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Scope to get only active pages
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to get pages by key
     */
    public function scopeByKey($query, $pageKey)
    {
        return $query->where('page_key', $pageKey);
    }

    /**
     * Scope to get pages by locale
     */
    public function scopeByLocale($query, $locale)
    {
        return $query->where('locale', $locale);
    }

    /**
     * Get page content by key
     */
    public static function getContentByKey($pageKey, $locale = 'en', $defaultValue = '')
    {
        $page = static::active()
            ->byKey($pageKey)
            ->byLocale($locale)
            ->first();

        return $page ? $page->content : $defaultValue;
    }

    /**
     * Get page title by key
     */
    public static function getTitleByKey($pageKey, $locale = 'en', $defaultValue = '')
    {
        $page = static::active()
            ->byKey($pageKey)
            ->byLocale($locale)
            ->first();

        return $page ? $page->title : $defaultValue;
    }
}
