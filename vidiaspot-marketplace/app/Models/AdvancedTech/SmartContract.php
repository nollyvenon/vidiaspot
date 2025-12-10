<?php

namespace App\Models;

class SmartContract extends BaseModel
{
    protected $fillable = [
        'name',
        'description',
        'contract_address',
        'blockchain',
        'abi',
        'bytecode',
        'contract_type',
        'status',
        'creator_id',
        'owner_id',
        'version',
        'gas_limit',
        'gas_price',
        'is_active',
        'deployed_at',
        'last_interaction_at',
        'transaction_count',
        'parameters',
        'functions',
        'events',
        'metadata',
    ];

    protected $casts = [
        'abi' => 'array',
        'bytecode' => 'array', // Could be large
        'parameters' => 'array',
        'functions' => 'array',
        'events' => 'array',
        'metadata' => 'array',
        'is_active' => 'boolean',
        'deployed_at' => 'datetime',
        'last_interaction_at' => 'datetime',
        'gas_limit' => 'integer',
        'gas_price' => 'decimal:2',
        'transaction_count' => 'integer',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function transactions()
    {
        return $this->hasMany(SmartContractTransaction::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByBlockchain($query, $blockchain)
    {
        return $query->where('blockchain', $blockchain);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('contract_type', $type);
    }
}