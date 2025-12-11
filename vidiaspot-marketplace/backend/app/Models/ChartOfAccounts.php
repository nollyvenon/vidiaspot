<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * ChartOfAccounts Model
 * Represents the chart of accounts for the accounting system
 */
class ChartOfAccounts extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'type', // asset, liability, equity, revenue, expense
        'description',
        'parent_id',
        'level',
        'is_active',
        'company_id',
        'balance',
        'normal_balance',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'balance' => 'decimal:2',
        'level' => 'integer',
        'parent_id' => 'integer',
        'company_id' => 'integer',
    ];

    /**
     * Get the parent account
     */
    public function parent()
    {
        return $this->belongsTo(ChartOfAccounts::class, 'parent_id');
    }

    /**
     * Get child accounts
     */
    public function children()
    {
        return $this->hasMany(ChartOfAccounts::class, 'parent_id');
    }

    /**
     * Get journal entries related to this account
     */
    public function journalEntries()
    {
        return $this->belongsToMany(JournalEntry::class, 'journal_entry_lines')
                    ->withPivot('debit', 'credit', 'description')
                    ->withTimestamps();
    }

    /**
     * Get the company that owns the account
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get account types
     */
    public static function getAccountTypes()
    {
        return [
            'asset' => 'Assets',
            'liability' => 'Liabilities',
            'equity' => 'Equity',
            'revenue' => 'Revenue',
            'expense' => 'Expenses',
            'cost_of_goods_sold' => 'Cost of Goods Sold',
            'other_income' => 'Other Income',
            'other_expense' => 'Other Expense',
        ];
    }

    /**
     * Get normal balance types
     */
    public static function getNormalBalanceTypes()
    {
        return [
            'debit' => 'Debit',
            'credit' => 'Credit',
        ];
    }

    /**
     * Calculate account balance
     */
    public function calculateBalance()
    {
        $journalEntries = $this->journalEntries()->where('posted', true)->get();
        $balance = 0;

        foreach ($journalEntries as $entry) {
            $line = $entry->pivot;
            if ($this->normal_balance === 'debit') {
                $balance += $line->debit - $line->credit;
            } else {
                $balance += $line->credit - $line->debit;
            }
        }

        return $balance;
    }

    /**
     * Scope for active accounts
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope by account type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope for asset accounts
     */
    public function scopeAssets($query)
    {
        return $query->where('type', 'asset');
    }

    /**
     * Scope for liability accounts
     */
    public function scopeLiabilities($query)
    {
        return $query->where('type', 'liability');
    }

    /**
     * Scope for equity accounts
     */
    public function scopeEquity($query)
    {
        return $query->where('type', 'equity');
    }

    /**
     * Scope for revenue accounts
     */
    public function scopeRevenue($query)
    {
        return $query->where('type', 'revenue');
    }

    /**
     * Scope for expense accounts
     */
    public function scopeExpenses($query)
    {
        return $query->where('type', 'expense');
    }
}