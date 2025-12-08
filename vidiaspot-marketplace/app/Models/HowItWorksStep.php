<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HowItWorksStep extends Model
{
    protected $fillable = [
        'title',
        'description',
        'icon_class',
        'step_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'step_order' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('step_order');
    }
}
