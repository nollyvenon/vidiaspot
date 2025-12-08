<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomAdField extends Model
{
    protected $fillable = [
        'ad_id',
        'field_key',
        'field_label',
        'field_type',
        'field_options',
        'field_value',
        'field_config',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'field_options' => 'array',
        'field_config' => 'array',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function ad(): BelongsTo
    {
        return $this->belongsTo(Ad::class);
    }
}
