<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class P2pCryptoPaymentMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'payment_type',
        'payment_provider',
        'name',
        'payment_details',
        'account_number',
        'account_name',
        'bank_name',
        'branch_code',
        'swift_code',
        'country_code',
        'is_default',
        'is_verified',
        'is_active',
        'usage_count',
        'verified_at',
        'last_used_at',
    ];

    protected $casts = [
        'payment_details' => 'array',
        'is_default' => 'boolean',
        'is_verified' => 'boolean',
        'is_active' => 'boolean',
        'usage_count' => 'integer',
        'verified_at' => 'datetime',
        'last_used_at' => 'datetime',
    ];

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function p2pOrders()
    {
        return $this->hasMany(P2pCryptoOrder::class, 'payment_method_id');
    }
}