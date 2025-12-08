<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Report extends Model
{
    protected $fillable = [
        'reporter_user_id', // Who made the report
        'reported_entity_type', // 'user', 'ad', 'vendor_store', 'insurance_provider', 'order', 'review', 'message', 'post'
        'reported_entity_id', // ID of the reported entity
        'report_type', // 'fraud', 'inappropriate_content', 'scam', 'misleading_info', 'spam', 'harassment', 'other'
        'severity_level', // 'low', 'medium', 'high', 'critical'
        'description',
        'evidence_attachments', // Paths to evidence files
        'status', // 'pending', 'under_review', 'resolved', 'dismissed', 'escalated'
        'resolution_notes',
        'resolved_by_admin_id', // Admin who resolved the report
        'resolved_at',
        'ai_analysis_results', // Results from AI analysis of the report
        'automated_response', // Action taken by automated system
        'manual_review_required', // Whether manual review is required
        'escalation_reason', // Reason for escalating to human review
        'moderation_decision', // 'dismissed', 'warning_issued', 'account_suspended', 'account_terminated'
        'moderation_notes',
        'trust_score_impact', // Impact on trust scores of involved parties
        'reputation_point_change', // Change to reputation scores
    ];

    protected $casts = [
        'evidence_attachments' => 'array',
        'ai_analysis_results' => 'array',
        'automated_response' => 'array',
        'manual_review_required' => 'boolean',
        'resolved_at' => 'datetime',
        'trust_score_impact' => 'array',
        'reputation_point_change' => 'integer',
    ];

    /**
     * Get the user who reported
     */
    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_user_id');
    }

    /**
     * Get the admin who resolved the report
     */
    public function resolver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by_admin_id');
    }

    /**
     * Scope to get reports by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to get reports by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('report_type', $type);
    }

    /**
     * Scope to get reports by severity
     */
    public function scopeBySeverity($query, $level)
    {
        return $query->where('severity_level', $level);
    }

    /**
     * Scope to get pending reports
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending')
                    ->orWhere('status', 'under_review');
    }

    /**
     * Scope to get reports by entity type and ID
     */
    public function scopeForEntity($query, $type, $id)
    {
        return $query->where('reported_entity_type', $type)
                    ->where('reported_entity_id', $id);
    }
}
