<?php

namespace App\Http\Controllers;

use App\Services\TrustSafetyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrustSafetyController extends Controller
{
    protected $trustSafetyService;

    public function __construct(TrustSafetyService $trustSafetyService)
    {
        $this->trustSafetyService = $trustSafetyService;
    }

    /**
     * Initiate biometric verification
     */
    public function initiateBiometricVerification(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User must be logged in'
            ], 401);
        }

        $request->validate([
            'type' => 'required|in:fingerprint,face_recognition',
            'subtype' => 'nullable|string',
        ]);

        try {
            $verification = $this->trustSafetyService->initiateBiometricVerification(
                $user->id,
                $request->type,
                $request->subtype
            );

            return response()->json([
                'success' => true,
                'message' => 'Biometric verification initiated',
                'verification' => $verification
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to initiate biometric verification: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Process biometric verification callback
     */
    public function processBiometricVerification(Request $request, $verificationId)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User must be logged in'
            ], 401);
        }

        $request->validate([
            'biometric_data' => 'required|array',
        ]);

        try {
            $verification = $this->trustSafetyService->processBiometricVerification(
                $verificationId,
                $request->biometric_data
            );

            return response()->json([
                'success' => true,
                'message' => 'Biometric verification processed',
                'verification' => $verification
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to process biometric verification: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Initiate video verification for high-value transactions
     */
    public function initiateVideoVerification(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User must be logged in'
            ], 401);
        }

        $request->validate([
            'transaction_id' => 'nullable|integer',
            'purpose' => 'in:identity_verification,high_value_transaction,dispute_resolution|default:identity_verification',
        ]);

        try {
            $verification = $this->trustSafetyService->initiateVideoVerification(
                $user->id,
                $request->transaction_id,
                $request->purpose
            );

            return response()->json([
                'success' => true,
                'message' => 'Video verification initiated',
                'verification' => $verification
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to initiate video verification: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Process video verification
     */
    public function processVideoVerification(Request $request, $verificationId)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User must be logged in'
            ], 401);
        }

        $request->validate([
            'video_path' => 'required|string',
            'metadata' => 'array',
        ]);

        try {
            $verification = $this->trustSafetyService->processVideoVerification(
                $verificationId,
                $request->video_path,
                $request->metadata
            );

            return response()->json([
                'success' => true,
                'message' => 'Video verification processed',
                'verification' => $verification
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to process video verification: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Create a new report
     */
    public function createReport(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User must be logged in'
            ], 401);
        }

        $request->validate([
            'entity_type' => 'required|in:user,ad,vendor_store,food_vendor,insurance_provider,order,review,message,post',
            'entity_id' => 'required|integer',
            'report_type' => 'required|in:fraud,inappropriate_content,scam,misleading_info,spam,harassment,other',
            'description' => 'required|string|max:2000',
            'evidence_attachments' => 'array',
            'severity_level' => 'in:low,medium,high,critical',
        ]);

        try {
            $report = $this->trustSafetyService->createReport(
                $user->id,
                $request->entity_type,
                $request->entity_id,
                $request->report_type,
                $request->description,
                $request->evidence_attachments
            );

            return response()->json([
                'success' => true,
                'message' => 'Report created successfully',
                'report' => $report
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create report: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get user's report history
     */
    public function getUserReports()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User must be logged in'
            ], 401);
        }

        $reports = \App\Models\Report::where('reporter_user_id', $user->id)
                                    ->orderBy('created_at', 'desc')
                                    ->get();

        return response()->json([
            'success' => true,
            'reports' => $reports
        ]);
    }

    /**
     * Get seller performance dashboard
     */
    public function getSellerPerformanceDashboard($userId = null)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User must be logged in'
            ], 401);
        }

        $targetUserId = $userId ?: $user->id;

        // Only allow viewing own dashboard or if user has permission
        if ($targetUserId != $user->id && !$user->hasRole('admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to view this dashboard'
            ], 403);
        }

        $dashboard = $this->trustSafetyService->getSellerPerformanceDashboard($targetUserId);

        return response()->json([
            'success' => true,
            'dashboard' => $dashboard
        ]);
    }

    /**
     * Purchase buyer protection for a transaction
     */
    public function purchaseBuyerProtection(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User must be logged in'
            ], 401);
        }

        $request->validate([
            'transaction_id' => 'required|integer',
            'transaction_type' => 'required|in:ad_purchase,food_order,insurance_purchase,service_booking',
            'transaction_reference' => 'required|string',
            'provider_id' => 'nullable|integer|exists:insurance_providers,id',
        ]);

        try {
            $protection = $this->trustSafetyService->purchaseBuyerProtection(
                $user->id,
                $request->transaction_id,
                $request->transaction_type,
                $request->transaction_reference,
                $request->provider_id
            );

            return response()->json([
                'success' => true,
                'message' => 'Buyer protection purchased successfully',
                'protection' => $protection
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to purchase protection: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * File a protection claim
     */
    public function fileProtectionClaim(Request $request, $protectionId)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User must be logged in'
            ], 401);
        }

        $request->validate([
            'claim_amount' => 'required|numeric|min:0',
            'reason' => 'required|string|max:1000',
            'evidence' => 'array',
        ]);

        try {
            $protection = $this->trustSafetyService->fileProtectionClaim(
                $protectionId,
                $request->claim_amount,
                $request->reason,
                $request->evidence
            );

            return response()->json([
                'success' => true,
                'message' => 'Protection claim filed successfully',
                'protection' => $protection
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to file claim: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get user's protection policies
     */
    public function getUserProtections()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User must be logged in'
            ], 401);
        }

        $protections = \App\Models\BuyerProtection::where('user_id', $user->id)
                                                  ->orderBy('created_at', 'desc')
                                                  ->get();

        return response()->json([
            'success' => true,
            'protections' => $protections
        ]);
    }

    /**
     * Perform background check for service providers
     */
    public function performBackgroundCheck(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User must be logged in'
            ], 401);
        }

        // Only allow admin or user performing check on themselves
        if ($user->id != $request->user_id && !$user->hasRole('admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to perform background check'
            ], 403);
        }

        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'check_type' => 'in:standard,enhanced,criminal,financial|default:standard',
        ]);

        try {
            $trustScore = $this->trustSafetyService->performBackgroundCheck(
                $request->user_id,
                $request->check_type
            );

            return response()->json([
                'success' => true,
                'message' => 'Background check completed',
                'trust_score' => $trustScore
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to perform background check: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get user verification status
     */
    public function getUserVerificationStatus($userId = null)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User must be logged in'
            ], 401);
        }

        $targetUserId = $userId ?: $user->id;

        // Only allow viewing own status or if user has permission
        if ($targetUserId != $user->id && !$user->hasRole('admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to view verification status'
            ], 403);
        }

        $status = $this->trustSafetyService->getUserVerificationStatus($targetUserId);

        return response()->json([
            'success' => true,
            'verification_status' => $status
        ]);
    }

    /**
     * Get trust score for user
     */
    public function getUserTrustScore($userId = null)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User must be logged in'
            ], 401);
        }

        $targetUserId = $userId ?: $user->id;

        // Only allow viewing own score or if user has permission
        if ($targetUserId != $user->id && !$user->hasRole('admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to view trust score'
            ], 403);
        }

        $trustScore = $this->trustSafetyService->getTrustScore($targetUserId);

        return response()->json([
            'success' => true,
            'trust_score' => $trustScore
        ]);
    }

    /**
     * Get reports for admin moderation
     */
    public function getReportsForModeration(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->hasRole('admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Admin access required'
            ], 403);
        }

        $status = $request->get('status', 'pending');
        $severity = $request->get('severity');
        $type = $request->get('type');

        $query = \App\Models\Report::query();

        if ($status) {
            $query->where('status', $status);
        }

        if ($severity) {
            $query->where('severity_level', $severity);
        }

        if ($type) {
            $query->where('report_type', $type);
        }

        $reports = $query->orderBy('created_at', 'desc')
                        ->get();

        return response()->json([
            'success' => true,
            'reports' => $reports,
            'filters' => [
                'status' => $status,
                'severity' => $severity,
                'type' => $type
            ]
        ]);
    }

    /**
     * Update report status (admin function)
     */
    public function updateReportStatus(Request $request, $reportId)
    {
        $user = Auth::user();
        if (!$user || !$user->hasRole('admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Admin access required'
            ], 403);
        }

        $request->validate([
            'status' => 'required|in:pending,under_review,resolved,dismissed,escalated',
            'resolution_notes' => 'nullable|string',
            'moderation_decision' => 'nullable|in:dismissed,warning_issued,account_suspended,account_terminated',
            'moderation_notes' => 'nullable|string',
        ]);

        try {
            $report = \App\Models\Report::findOrFail($reportId);
            $report->update([
                'status' => $request->status,
                'resolution_notes' => $request->resolution_notes,
                'moderation_decision' => $request->moderation_decision,
                'moderation_notes' => $request->moderation_notes,
                'resolved_by_admin_id' => $user->id,
                'resolved_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Report status updated',
                'report' => $report
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update report: ' . $e->getMessage()
            ], 400);
        }
    }
}
