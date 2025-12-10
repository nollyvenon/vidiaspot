<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ListingMedia extends Model
{
    protected $fillable = [
        'ad_id',
        'user_id',
        'media_type', // 'image', 'video', '360_image', 'vr_tour', 'interactive_demo', 'documentation'
        'file_path',
        'file_url',
        'thumbnail_url',
        'original_filename',
        'media_caption',
        'media_alt_text',
        'is_primary', // Is this the primary media for the listing
        'display_order',
        'is_active',
        'view_count',
        'interaction_count', // For interactive media
        'duration_seconds', // For videos
        'width_pixels',
        'height_pixels',
        'file_size_bytes',
        'media_metadata', // Additional metadata specific to media type
        'upload_ip_address',
        'upload_user_agent',
        'uploaded_by_admin', // If uploaded by admin on behalf of user
        'is_approved', // For moderation
        'approved_by',
        'approved_at',
        'notes',
        'custom_fields', // For extended functionality
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'is_active' => 'boolean',
        'is_approved' => 'boolean',
        'media_metadata' => 'array',
        'custom_fields' => 'array',
        'view_count' => 'integer',
        'interaction_count' => 'integer',
        'duration_seconds' => 'integer',
        'width_pixels' => 'integer',
        'height_pixels' => 'integer',
        'file_size_bytes' => 'integer',
        'upload_ip_address' => 'ipv4',
        'approved_at' => 'datetime',
    ];

    /**
     * Get the ad this media belongs to
     */
    public function ad(): BelongsTo
    {
        return $this->belongsTo(Ad::class);
    }

    /**
     * Get the user who uploaded this media
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the admin who approved this media (if applicable)
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Scope to get only active media
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get only approved media
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    /**
     * Scope to get media by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('media_type', $type);
    }

    /**
     * Scope to get primary media for listings
     */
    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    /**
     * Scope to get media for specific ad
     */
    public function scopeForAd($query, $adId)
    {
        return $query->where('ad_id', $adId);
    }

    /**
     * Check if this is 360-degree media
     */
    public function is360Media(): bool
    {
        return $this->media_type === '360_image' || $this->media_type === '360_video';
    }

    /**
     * Check if this is VR media
     */
    public function isVrMedia(): bool
    {
        return $this->media_type === 'vr_tour' || $this->media_type === 'vr_experience';
    }

    /**
     * Check if this is interactive
     */
    public function isInteractive(): bool
    {
        return $this->media_type === 'interactive_demo' || $this->media_type === 'interactive_360';
    }

    /**
     * Check if this is video
     */
    public function isVideo(): bool
    {
        return $this->media_type === 'video' || $this->media_type === '360_video';
    }
}
