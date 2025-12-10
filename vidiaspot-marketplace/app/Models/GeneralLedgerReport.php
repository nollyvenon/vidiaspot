<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeneralLedgerReport extends Model
{
    protected $fillable = [
        'chart_of_accounts',
        'trial_balance',
        'journal_entries',
        'account_reconciliations',
        'accrued_items',
        'period_start',
        'period_end',
        'metadata'
    ];

    protected $casts = [
        'chart_of_accounts' => 'array',
        'trial_balance' => 'array',
        'journal_entries' => 'array',
        'account_reconciliations' => 'array',
        'accrued_items' => 'array',
        'metadata' => 'array',
        'period_start' => 'datetime',
        'period_end' => 'datetime',
    ];
}