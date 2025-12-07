<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Career extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'department',
        'job_type',
        'location',
        'salary_range',
        'description',
        'requirements',
        'benefits',
        'user_id',
        'status',
        'is_active',
        'published_at',
        'application_deadline',
        'meta',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'is_active' => 'boolean',
        'published_at' => 'datetime',
        'application_deadline' => 'datetime',
        'meta' => 'array',
    ];

    /**
     * Get the user who created this career post.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to get active careers.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                    ->where('status', 'published');
    }

    /**
     * Scope to get by department.
     */
    public function scopeByDepartment($query, $department)
    {
        return $query->where('department', $department);
    }

    /**
     * Scope to get by job type.
     */
    public function scopeByJobType($query, $jobType)
    {
        return $query->where('job_type', $jobType);
    }

    /**
     * Scope to get by location.
     */
    public function scopeByLocation($query, $location)
    {
        return $query->where('location', $location);
    }
}
