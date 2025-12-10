<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaxReport extends Model
{
    protected $fillable = [
        'user_tax_forms',
        'taxable_events',
        'jurisdictional_taxes',
        'withholding_taxes',
        'audit_trails',
        'period_start',
        'period_end',
        'metadata'
    ];

    protected $casts = [
        'user_tax_forms' => 'array',
        'taxable_events' => 'array',
        'jurisdictional_taxes' => 'array',
        'withholding_taxes' => 'array',
        'audit_trails' => 'array',
        'metadata' => 'array',
        'period_start' => 'datetime',
        'period_end' => 'datetime',
    ];
}