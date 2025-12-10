<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Escrow extends Model
{
    protected $fillable = [
        'transaction_id',
        'ad_id',
        'buyer_user_id',
        'seller_user_id',
        'amount',
        'currency',
        'status',
        'dispute_status',
        'release_date',
        'dispute_resolved_at',
        'dispute_details',
        'release_conditions',
        'notes',
        'blockchain_transaction_hash',
        'blockchain_contract_address',
        'blockchain_status',
        'blockchain_verification_data',
    ];

    protected $casts = [
        'transaction_id' => 'integer',
        'ad_id' => 'integer',
        'buyer_user_id' => 'integer',
        'seller_user_id' => 'integer',
        'amount' => 'decimal:2',
        'currency' => 'string',
        'status' => 'string',
        'dispute_status' => 'string',
        'release_date' => 'datetime',
        'dispute_resolved_at' => 'datetime',
        'dispute_details' => 'array',
        'release_conditions' => 'array',
        'notes' => 'string',
        'blockchain_transaction_hash' => 'string',
        'blockchain_contract_address' => 'string',
        'blockchain_status' => 'string',
        'blockchain_verification_data' => 'array',
    ];

    // Relationships
    public function ad()
    {
        return $this->belongsTo(Ad::class);
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_user_id');
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_user_id');
    }
}
