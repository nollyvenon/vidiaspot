<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VerificationRecord extends Model
{
    protected $fillable = [
        'user_id',
        'verification_type', // 'biometric', 'fingerprint', 'face_recognition', 'video', 'document'
        'verification_subtype', // 'fingerprint_left_thumb', 'face_front', 'video_introduction', etc.
        'verification_data', // Encrypted verification data
        'verification_metadata', // Additional metadata like confidence score, device info
        'result', // 'success', 'failed', 'pending', 'flagged'
        'confidence_score', // Confidence level of verification (0-100)
        'status', // 'active', 'expired', 'revoked'
        'verified_at',
        'expires_at',
        'verification_session_id', // Unique session for the verification
        'device_info', // Information about the device used
        'ip_address', // IP address of the verifier
        'location_data', // GPS coordinates if available
        'file_path', // Path to uploaded verification file (for biometrics/videos)
        'hash_verification', // Hash of the verification data
        'notes', // Internal notes
        'verified_by_admin', // If verified by an admin
        'admin_notes', // Notes from admin verification
    ];

    protected $casts = [
        'verification_metadata' => 'array',
        'device_info' => 'array',
        'location_data' => 'array',
        'confidence_score' => 'decimal:2',
        'verified_at' => 'datetime',
        'expires_at' => 'datetime',
        'ip_address' => 'ipv4',
    ];

    /**
     * Get the user who was verified
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to get only successful verifications
     */
    public function scopeSuccessful($query)
    {
        return $query->where('result', 'success');
    }

    /**
     * Scope to get active verifications
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                    ->where(function($q) {
                        $q->whereNull('expires_at')
                          ->orWhere('expires_at', '>', now());
                    });
    }

    /**
     * Scope by verification type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('verification_type', $type);
    }

    /**
     * Check if verification is still valid
     */
    public function isValid(): bool
    {
        return $this->status === 'active' &&
               ($this->expires_at === null || $this->expires_at > now());
    }
}
