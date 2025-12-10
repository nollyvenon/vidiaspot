<?php

namespace App\Models;

class SmartContractTransaction extends BaseModel
{
    protected $fillable = [
        'smart_contract_id',
        'user_id',
        'ad_id',
        'transaction_type',
        'function_name',
        'parameters',
        'transaction_hash',
        'blockchain',
        'block_number',
        'from_address',
        'to_address',
        'value',
        'gas_used',
        'gas_price',
        'status',
        'error_message',
        'confirmed_at',
        'confirmation_blocks',
        'events_logs',
        'receipt',
        'metadata',
    ];

    protected $casts = [
        'parameters' => 'array',
        'events_logs' => 'array',
        'receipt' => 'array',
        'metadata' => 'array',
        'value' => 'decimal:8',
        'gas_used' => 'decimal:2',
        'gas_price' => 'decimal:2',
        'confirmed_at' => 'datetime',
        'confirmation_blocks' => 'integer',
        'status' => 'string', // pending, confirmed, failed, reverted
    ];

    public function smartContract()
    {
        return $this->belongsTo(SmartContract::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ad()
    {
        return $this->belongsTo(Ad::class);
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('transaction_type', $type);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeForAd($query, $adId)
    {
        return $query->where('ad_id', $adId);
    }
}