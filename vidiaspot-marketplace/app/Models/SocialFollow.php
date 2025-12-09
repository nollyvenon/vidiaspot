<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SocialFollow extends Model
{
    protected $fillable = [
        'follower_id', // who follows
        'followed_id', // who is being followed
        'follow_type', // 'user', 'vendor_store', 'insurance_provider'
        'reputation_points',
        'is_approved', // for follow requests if needed
    ];

    /**
     * Get the follower user
     */
    public function follower(): BelongsTo
    {
        return $this->belongsTo(User::class, 'follower_id');
    }

    /**
     * Get the followed user/entity
     */
    public function followed(): BelongsTo
    {
        if ($this->follow_type === 'user') {
            return $this->belongsTo(User::class, 'followed_id');
        } elseif ($this->follow_type === 'vendor_store') {
            return $this->belongsTo(VendorStore::class, 'followed_id');
        } elseif ($this->follow_type === 'insurance_provider') {
            return $this->belongsTo(InsuranceProvider::class, 'followed_id');
        } else {
            // Generic relation for others
            return $this->belongsTo(User::class, 'followed_id');
        }
    }
}
