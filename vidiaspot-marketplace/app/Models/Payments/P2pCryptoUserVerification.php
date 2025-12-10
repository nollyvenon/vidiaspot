<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class P2pCryptoUserVerification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'verification_type',
        'verification_status',
        'verification_level',
        'verification_data',
        'document_type',
        'document_number',
        'document_front_image',
        'document_back_image',
        'selfie_image',
        'verification_notes',
        'verified_at',
        'expires_at',
        'verified_by',
    ];

    protected $casts = [
        'verification_data' => 'array',
        'verified_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}