<?php

namespace App\Models;

class NftCollection extends BaseModel
{
    protected $fillable = [
        'name',
        'description',
        'slug',
        'banner_image_url',
        'image_url',
        'creator_id',
        'owner_id',
        'external_url',
        'twitter_username',
        'instagram_username',
        'discord_url',
        'description',
        'category',
        'status',
        'verified',
        'total_supply',
        'minted_supply',
        'contract_address',
        'blockchain',
        'token_standard',
        'royalty_percentage',
        'royalty_recipient',
        'fees_on_sale',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'verified' => 'boolean',
        'fees_on_sale' => 'decimal:2',
        'royalty_percentage' => 'decimal:2',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function nfts()
    {
        return $this->hasMany(Nft::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeVerified($query)
    {
        return $query->where('verified', true);
    }
}