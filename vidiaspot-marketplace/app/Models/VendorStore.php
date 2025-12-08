<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VendorStore extends Model
{
    protected $fillable = [
        'user_id',
        'store_name',
        'store_slug',
        'description',
        'theme',
        'theme_config',
        'logo_url',
        'banner_url',
        'contact_email',
        'contact_phone',
        'business_hours',
        'social_links',
        'is_active',
        'is_verified',
        'verified_at',
        'settings',
    ];

    protected $casts = [
        'business_hours' => 'array',
        'social_links' => 'array',
        'theme_config' => 'array',
        'settings' => 'array',
        'is_active' => 'boolean',
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function ads(): HasMany
    {
        return $this->hasMany(Ad::class, 'user_id'); // Vendors own ads through their user account
    }
}
