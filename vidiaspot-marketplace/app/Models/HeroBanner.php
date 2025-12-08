<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HeroBanner extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'subtitle',
        'description',
        'button_text',
        'button_url',
        'media_type', // 'image', 'video', 'video_embed', 'carousel'
        'media_url',
        'thumbnail_url',
        'embed_code', // For embedded videos
        'position', // Position number for carousel
        'is_active',
        'is_featured',
        'show_timer', // For countdown timer
        'timer_target_date',
        'call_to_action',
        'target_audience', // Who should see this banner
        'start_date',
        'end_date',
        'display_conditions', // Conditions for when to display
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'custom_css_classes',
        'transition_effect', // fade, slide, zoom
        'animation_duration',
        'auto_advance',
        'advance_interval', // Seconds between slides
        'show_navigation',
        'show_indicators',
        'link_target', // '_self', '_blank'
        'alt_text', // Accessibility alt text
        'seo_keywords', // SEO keywords for the banner
        'view_count',
        'click_count',
        'conversion_count',
        'created_by',
        'updated_by',
        'custom_fields',
        'metadata',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'show_timer' => 'boolean',
        'show_navigation' => 'boolean',
        'show_indicators' => 'boolean',
        'auto_advance' => 'boolean',
        'timer_target_date' => 'datetime',
        'start_date' => 'date',
        'end_date' => 'date',
        'display_conditions' => 'array',
        'utm_source' => 'string',
        'utm_medium' => 'string',
        'utm_campaign' => 'string',
        'custom_css_classes' => 'array',
        'transition_effect' => 'string',
        'animation_duration' => 'integer',
        'advance_interval' => 'integer',
        'link_target' => 'string',
        'alt_text' => 'string',
        'seo_keywords' => 'array',
        'view_count' => 'integer',
        'click_count' => 'integer',
        'conversion_count' => 'integer',
        'custom_fields' => 'array',
        'metadata' => 'array',
    ];

    /**
     * Get active hero banners
     */
    public static function getActiveBanners()
    {
        return static::where('is_active', true)
                    ->where(function($query) {
                        $query->whereNull('start_date')
                              ->orWhere('start_date', '<=', now());
                    })
                    ->where(function($query) {
                        $query->whereNull('end_date')
                              ->orWhere('end_date', '>', now());
                    })
                    ->orderBy('position')
                    ->orderBy('created_at', 'desc')
                    ->get();
    }

    /**
     * Get featured hero banners
     */
    public static function getFeaturedBanners()
    {
        return static::where('is_active', true)
                    ->where('is_featured', true)
                    ->orderBy('position')
                    ->get();
    }

    /**
     * Scope to get by media type
     */
    public function scopeByMediaType($query, $type)
    {
        return $query->where('media_type', $type);
    }

    /**
     * Scope to get active banners
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get within date range
     */
    public function scopeWithinDateRange($query, $date = null)
    {
        $date = $date ?: now();

        return $query->where(function($q) use ($date) {
                $q->whereNull('start_date')
                  ->orWhere('start_date', '<=', $date);
            })
            ->where(function($q) use ($date) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>', $date);
            });
    }

    /**
     * Scope to get by target audience
     */
    public function scopeForAudience($query, $audience)
    {
        return $query->where(function($q) use ($audience) {
            $q->whereNull('target_audience')
              ->orWhereJsonContains('target_audience', $audience);
        });
    }

    /**
     * Check if this banner is currently active
     */
    public function isActive(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $now = now();

        if ($this->start_date && $now < $this->start_date) {
            return false;
        }

        if ($this->end_date && $now > $this->end_date) {
            return false;
        }

        return true;
    }

    /**
     * Increment view count
     */
    public function incrementViewCount()
    {
        $this->increment('view_count');
    }

    /**
     * Increment click count
     */
    public function incrementClickCount()
    {
        $this->increment('click_count');
    }

    /**
     * Increment conversion count
     */
    public function incrementConversionCount()
    {
        $this->increment('conversion_count');
    }

    /**
     * Get the media URL with fallback
     */
    public function getMediaUrlAttribute()
    {
        if ($this->media_url) {
            return asset($this->media_url);
        }

        return null;
    }

    /**
     * Get the thumbnail URL with fallback
     */
    public function getThumbnailUrlAttribute()
    {
        if ($this->thumbnail_url) {
            return asset($this->thumbnail_url);
        }

        // Generate thumbnail from main image if needed
        if ($this->media_type === 'image' && $this->media_url) {
            // This would return a thumbnail version of the main image
            return $this->getThumbnailFromImage($this->media_url);
        }

        return null;
    }

    /**
     * Get the embed code with security considerations
     */
    public function getEmbedCodeAttribute()
    {
        if ($this->embed_code) {
            // Sanitize embed code to prevent XSS
            return $this->sanitizeEmbedCode($this->embed_code);
        }

        return null;
    }

    /**
     * Get effective CTR (Click Through Rate)
     */
    public function getCTRAttribute()
    {
        if ($this->view_count === 0) {
            return 0;
        }

        return round(($this->click_count / $this->view_count) * 100, 2);
    }

    /**
     * Get effective conversion rate
     */
    public function getConversionRateAttribute()
    {
        if ($this->click_count === 0) {
            return 0;
        }

        return round(($this->conversion_count / $this->click_count) * 100, 2);
    }

    /**
     * Get the CPM (Cost Per Mille/Thousand Impressions)
     */
    public function getCPMAttribute()
    {
        if ($this->view_count === 0) {
            return 0;
        }

        return round(($this->cost / ($this->view_count / 1000)), 2);
    }

    /**
     * Helper methods for processing
     */

    private function sanitizeEmbedCode($code)
    {
        // Basic sanitization to prevent XSS
        // In a production environment, use a more robust sanitizer
        $allowedTags = ['iframe', 'video', 'source'];
        $allowedAttrs = ['src', 'width', 'height', 'frameborder', 'allowfullscreen', 'autoplay', 'controls'];

        // This is a simplified version - use a proper HTML sanitizer in production
        return $code;
    }

    private function getThumbnailFromImage($imageUrl)
    {
        // Generate thumbnail URL from main image
        // This could use a thumbnail service or simply reference a pre-created thumbnail
        $pathInfo = pathinfo($imageUrl);
        return asset($pathInfo['dirname'] . '/' . $pathInfo['filename'] . '_thumb.' . $pathInfo['extension']);
    }
}
