<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AmlKycReport extends Model
{
    protected $fillable = [
        'verification_status',
        'suspicious_transactions',
        'customer_due_diligence',
        'pep_screening',
        'sanctions_screening',
        'period_start',
        'period_end',
        'metadata'
    ];

    protected $casts = [
        'verification_status' => 'array',
        'suspicious_transactions' => 'array',
        'customer_due_diligence' => 'array',
        'pep_screening' => 'array',
        'sanctions_screening' => 'array',
        'metadata' => 'array',
        'period_start' => 'datetime',
        'period_end' => 'datetime',
    ];
}