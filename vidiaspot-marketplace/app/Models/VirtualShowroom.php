<?php

namespace App\Models;

class VirtualShowroom extends BaseModel
{
    protected $fillable = [
        'name',
        'description',
        'slug',
        'owner_id',
        'vendor_id',
        'platform',
        'url',
        'embed_code',
        'thumbnail_url',
        'background_image_url',
        'virtual_environment',
        'is_public',
        'is_active',
        'max_visitors',
        'current_visitors',
        'requires_reservation',
        'reservation_fee',
        'currency',
        'start_date',
        'end_date',
        'opening_hours',
        'features',
        'settings',
        'metadata',
    ];

    protected $casts = [
        'opening_hours' => 'array',
        'features' => 'array',
        'settings' => 'array',
        'metadata' => 'array',
        'is_public' => 'boolean',
        'is_active' => 'boolean',
        'requires_reservation' => 'boolean',
        'max_visitors' => 'integer',
        'current_visitors' => 'integer',
        'reservation_fee' => 'decimal:2',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function products()
    {
        return $this->belongsToMany(Ad::class, 'virtual_showroom_products');
    }

    public function visitors()
    {
        return $this->belongsToMany(User::class, 'virtual_showroom_visitors')
                    ->withPivot('visit_time', 'duration', 'status')
                    ->withTimestamps();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeForPlatform($query, $platform)
    {
        return $query->where('platform', $platform);
    }
}