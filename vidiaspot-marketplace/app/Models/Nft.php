<?php

namespace App\Models;

class Nft extends BaseModel
{
    protected $fillable = [
        'name',
        'description',
        'collection_id',
        'external_url',
        'image_url',
        'animation_url',
        'token_id',
        'contract_address',
        'blockchain',
        'creator_id',
        'owner_id',
        'price',
        'currency',
        'status',
        'properties',
        'levels',
        'stats',
        'is_listed',
        'list_price',
        'list_currency',
        'royalty_percentage',
        'royalty_recipient',
        'metadata',
        'token_standard',
        'supply',
        'max_supply',
        'is_soulbound',
        'transferable',
    ];

    protected $casts = [
        'properties' => 'array',
        'levels' => 'array',
        'stats' => 'array',
        'metadata' => 'array',
        'is_listed' => 'boolean',
        'is_soulbound' => 'boolean',
        'transferable' => 'boolean',
        'price' => 'decimal:2',
        'list_price' => 'decimal:2',
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

    public function collection()
    {
        return $this->belongsTo(NftCollection::class);
    }

    public function transactions()
    {
        return $this->hasMany(NftTransaction::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeListed($query)
    {
        return $query->where('is_listed', true);
    }

    public function scopeForBlockchain($query, $blockchain)
    {
        return $query->where('blockchain', $blockchain);
    }
}