<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StatusLog extends Model
{
    protected $fillable = [
        'statusable_type',
        'statusable_id',
        'status',
        'previous_status',
        'reason',
        'changed_by',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    /**
     * Get the model that the status log belongs to (polymorphic relationship).
     */
    public function statusable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the user who changed the status.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    /**
     * Scope to get logs by statusable type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('statusable_type', $type);
    }

    /**
     * Scope to get logs by statusable ID.
     */
    public function scopeById($query, $id)
    {
        return $query->where('statusable_id', $id);
    }

    /**
     * Scope to get logs by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}
