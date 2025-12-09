<?php

namespace App\Models;

class NftTransaction extends BaseModel
{
    protected $fillable = [
        'nft_id',
        'from_user_id',
        'to_user_id',
        'transaction_type',
        'price',
        'currency',
        'transaction_hash',
        'blockchain',
        'block_number',
        'gas_used',
        'gas_price',
        'fee',
        'status',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'price' => 'decimal:2',
        'gas_used' => 'decimal:2',
        'gas_price' => 'decimal:2',
        'fee' => 'decimal:2',
    ];

    public function nft()
    {
        return $this->belongsTo(Nft::class);
    }

    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function toUser()
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }

    public function scopeSuccessful($query)
    {
        return $query->where('status', 'success');
    }

    public function scopeByTransactionType($query, $type)
    {
        return $query->where('transaction_type', $type);
    }
}