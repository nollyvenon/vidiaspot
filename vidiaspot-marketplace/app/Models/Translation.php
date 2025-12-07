<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Translation extends Model
{
    protected $fillable = [
        'table_name',
        'record_id',
        'column_name',
        'locale',
        'value',
    ];

    protected $casts = [
        'record_id' => 'integer',
    ];

    /**
     * Get the language associated with this translation.
     */
    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class, 'locale', 'code');
    }

    /**
     * Get the translated value.
     */
    public function getValueAttribute($value)
    {
        return $value;
    }

    /**
     * Scope to get translation for a specific table and record.
     */
    public function scopeForRecord($query, $table, $recordId)
    {
        return $query->where('table_name', $table)
                    ->where('record_id', $recordId);
    }

    /**
     * Scope to get translation for a specific column.
     */
    public function scopeForColumn($query, $column)
    {
        return $query->where('column_name', $column);
    }

    /**
     * Scope to get translation for a specific locale.
     */
    public function scopeForLocale($query, $locale)
    {
        return $query->where('locale', $locale);
    }
}
