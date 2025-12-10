<?php

namespace App\Http\Controllers\Logistics;

use App\Services\LogisticsService;
use App\Models\Logistics\ShippingLabel;
use App\Models\Logistics\CourierPartner;
use App\Models\Logistics\ReturnRequest;
use App\Models\BuyerProtection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogisticsController extends Controller
{
    protected $logisticsService;

    public function __construct(LogisticsService $logisticsService)
    {
        $this->logisticsService = $logisticsService;
    }

    /**
     * Get available logistics partners
     */
    public function getLogisticsPartners(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User must be logged in'
            ], 401);
        }

        $filters = $request->only([
            'service_type', 'coverage_area', 'is_same_day',
            'delivery_timeframe', 'weight_range', 'price_range'
        ]);

        $partners = $this->logisticsService->getLogisticsPartners($filters);

        return response()->json([
            'success' => true,
            'partners' => $partners,
            'count' => count($partners)
        ]);
    }

    /**
     * Generate shipping label
     */
    public function generateShippingLabel(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User must be logged in'
            ], 401);
        }

        $request->validate([
            'ad_id' => 'required|integer|exists:ads,id',
            'from_address' => 'required|array',
            'to_address' => 'required|array',
            'package_weight' => 'required|numeric|min:0.1',
            'package_dimensions' => 'required|array',
            'package_value' => 'required|numeric|min:100',
            'carrier_code' => 'required|in:fedex,ups,dhl,poslaju,local_carrier',
            'service_type' => 'required|in:standard,express,overnight,freight',
            'insure_package' => 'boolean',
            'signature_required' => 'boolean',
            'delivery_instructions' => 'nullable|string',
        ]);

        try {
            $label = $this->logisticsService->generateShippingLabel(
                $user->id,
                $request->all()
            );

            return response()->json([
                'success' => true,
                'message' => 'Shipping label generated successfully',
                'label' => $label
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate shipping label: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Process return request
     */
    public function processReturnRequest(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User must be logged in'
            ], 401);
        }

        $request->validate([
            'order_id' => 'required|integer',
            'ad_id' => 'nullable|integer|exists:ads,id',
            'vendor_id' => 'required|integer|exists:users,id',
            'return_reason' => 'required|string|max:255',
            'return_description' => 'nullable|string',
            'return_type' => 'required|in:refund,exchange,repair,replacement',
            'return_method' => 'required|in:pickup,drop_off,courier_collection,self_delivery',
            'refund_amount' => 'required|numeric|min:0',
            'return_images' => 'array',
            'return_images.*' => 'string',
        ]);

        try {
            $return = $this->logisticsService->processReturn($user->id, $request->all());

            return response()->json([
                'success' => true,
                'message' => 'Return request processed successfully',
                'return' => $return
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to process return request: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get return management dashboard for sellers
     */
    public function getReturnManagementDashboard()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User must be logged in'
            ], 401);
        }

        $returns = ReturnRequest::where(function($query) use ($user) {
            // Get returns where user is either the vendor or the requester
            $query->where('vendor_id', $user->id)
                  ->orWhere('user_id', $user->id);
        })
        ->orderBy('created_at', 'desc')
        ->paginate(20);

        $stats = [
            'total_returns' => ReturnRequest::where('vendor_id', $user->id)->count(),
            'active_returns' => ReturnRequest::where('vendor_id', $user->id)
                                            ->whereIn('status', ['pending', 'processing', 'approved'])
                                            ->count(),
            'resolved_returns' => ReturnRequest::where('vendor_id', $user->id)
                                               ->where('status', 'resolved')
                                               ->count(),
            'return_rate' => $this->calculateReturnRate($user->id),
            'average_resolution_time' => $this->calculateAvgResolutionTime($user->id),
        ];

        return response()->json([
            'success' => true,
            'returns' => $returns,
            'statistics' => $stats,
            'filters_available' => [
                'status' => ['pending', 'processing', 'approved', 'rejected', 'resolved'],
                'type' => ['refund', 'exchange', 'repair', 'replacement'],
                'reason' => ['defective', 'not_as_described', 'wrong_item', 'changed_mind', 'other']
            ]
        ]);
    }

    /**
     * Generate package insurance
     */
    public function generatePackageInsurance(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User must be logged in'
            ], 401);
        }

        $request->validate([
            'package_value' => 'required|numeric|min:100',
            'carrier_id' => 'nullable|integer|exists:insurance_providers,id',
        ]);

        try {
            $insurance = $this->logisticsService->generatePackageInsurance(
                $user->id,
                $request->package_value,
                $request->carrier_id
            );

            return response()->json([
                'success' => true,
                'message' => 'Package insurance generated successfully',
                'insurance' => $insurance
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate package insurance: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Integrate with warehouse for large sellers
     */
    public function warehouseIntegration(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User must be logged in'
            ], 401);
        }

        $request->validate([
            'warehouse_id' => 'required|integer|exists:warehouses,id',
            'allocation_size' => 'required|integer|min:1', // in square meters
            'integration_type' => 'required|in:storage,fulfillment,hybrid',
            'storage_category' => 'required|in:general,cold_chain,fragile,hazardous',
            'contract_months' => 'required|integer|min:1|max:12',
            'estimated_inventory_value' => 'required|numeric|min:10000',
            'auto_fulfillment' => 'boolean',
            'auto_sync' => 'boolean',
        ]);

        try {
            $integration = $this->logisticsService->integrateWithWarehouse($user->id, $request->all());

            return response()->json([
                'success' => true,
                'message' => 'Warehouse integration initiated successfully',
                'integration' => $integration
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to integrate with warehouse: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Synchronize inventory across platforms
     */
    public function synchronizeInventory(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User must be logged in'
            ], 401);
        }

        $platforms = $request->get('platforms', 'all');
        $syncType = $request->get('sync_type', 'full'); // 'full', 'incremental', 'price_only', 'inventory_only'

        try {
            $result = $this->logisticsService->synchronizeInventoryAcrossPlatforms(
                $user->id,
                is_string($platforms) ? explode(',', $platforms) : $platforms
            );

            return response()->json([
                'success' => true,
                'message' => 'Inventory synchronized successfully',
                'result' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to synchronize inventory: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get user's protection policies
     */
    public function getUserProtections(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User must be logged in'
            ], 401);
        }

        $protections = BuyerProtection::where('user_id', $user->id)
                                    ->orderBy('created_at', 'desc')
                                    ->get();

        return response()->json([
            'success' => true,
            'protections' => $protections,
            'count' => $protections->count()
        ]);
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
            'reason' => 'required|string|max:500',
            'evidence' => 'array',
            'evidence.*' => 'string',
        ]);

        try {
            $result = $this->logisticsService->fileProtectionClaim(
                $protectionId,
                $request->claim_amount,
                $request->reason,
                $request->evidence
            );

            return response()->json([
                'success' => true,
                'message' => 'Protection claim filed successfully',
                'protection' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to file protection claim: ' . $e->getMessage()
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

        // Only allow viewing own status or if user is admin
        if ($targetUserId !== $user->id && !$user->hasRole('admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to view verification status'
            ], 403);
        }

        $verificationStatus = $this->logisticsService->getUserVerificationStatus($targetUserId);

        return response()->json([
            'success' => true,
            'verification_status' => $verificationStatus
        ]);
    }

    /**
     * Get user trust score
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

        // Only allow viewing own score or if user is admin
        if ($targetUserId !== $user->id && !$user->hasRole('admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to view trust score'
            ], 403);
        }

        $trustScore = $this->logisticsService->getTrustScore($targetUserId);

        return response()->json([
            'success' => true,
            'trust_score' => $trustScore
        ]);
    }

    /**
     * Get reports for admin moderation (placeholder)
     */
    public function getReportsForModeration()
    {
        $user = Auth::user();
        if (!$user || !$user->hasRole('admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Admin access required'
            ], 403);
        }

        // This would be implemented in ReportController in a real scenario
        $reports = Report::where('status', 'pending')
                        ->orWhere('status', 'under_review')
                        ->orderBy('created_at', 'desc')
                        ->get();

        return response()->json([
            'success' => true,
            'reports' => $reports
        ]);
    }

    /**
     * Update report status (placeholder)
     */
    public function updateReportStatus(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user || !$user->hasRole('admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Admin access required'
            ], 403);
        }

        $request->validate([
            'status' => 'required|in:resolved,dismissed,escalated,under_review'
        ]);

        $report = Report::findOrFail($id);
        $report->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Report status updated',
            'report' => $report
        ]);
    }

    /**
     * Calculate return rate for seller analytics
     */
    private function calculateReturnRate($userId)
    {
        $totalTransactions = \App\Models\Order::where('user_id', $userId)->count();
        $totalReturns = ReturnRequest::where('vendor_id', $userId)->count();

        if ($totalTransactions == 0) {
            return 0;
        }

        return ($totalReturns / $totalTransactions) * 100;
    }

    /**
     * Calculate average resolution time for returns
     */
    private function calculateAvgResolutionTime($userId)
    {
        $returns = ReturnRequest::where('vendor_id', $userId)
                               ->whereNotNull('resolution_date')
                               ->get();

        if ($returns->isEmpty()) {
            return 0;
        }

        $totalDays = 0;
        foreach ($returns as $return) {
            if ($return->created_at && $return->resolution_date) {
                $totalDays += $return->created_at->diffInDays($return->resolution_date);
            }
        }

        return round($totalDays / $returns->count(), 2);
    }
}
