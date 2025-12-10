<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserTradeHistoryReport extends Model
{
    protected $fillable = [
        'user_id',
        'trade_history',
        'deposit_withdrawal_history',
        'fee_breakdown',
        'balance_snapshots',
        'tax_report',
        'report_date',
        'period_start',
        'period_end',
        'metadata'
    ];

    protected $casts = [
        'trade_history' => 'array',
        'deposit_withdrawal_history' => 'array',
        'fee_breakdown' => 'array',
        'balance_snapshots' => 'array',
        'tax_report' => 'array',
        'metadata' => 'array',
        'report_date' => 'datetime',
        'period_start' => 'datetime',
        'period_end' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}