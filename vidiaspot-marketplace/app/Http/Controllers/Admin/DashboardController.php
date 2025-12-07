<?php

namespace App\Http\Controllers\Admin;

use App\Models\Ad;
use App\Models\Vendor;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Comment;
use App\Models\StatusLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    /**
     * Get dashboard statistics for widgets.
     */
    public function getStats(): JsonResponse
    {
        $this->checkAdminAccess();

        $stats = [
            'total_users' => User::count(),
            'total_vendors' => Vendor::count(),
            'total_ads' => Ad::count(),
            'total_payments' => Payment::count(),
            'total_revenue' => Payment::where('status', 'completed')->sum('amount'),
            'recent_users' => User::latest()->take(5)->get(['id', 'name', 'email', 'created_at']),
            'recent_ads' => Ad::with('user')->latest()->take(5)->get(['id', 'title', 'user_id', 'created_at']),
            'recent_payments' => Payment::with('user')->latest()->take(5)->get(['id', 'transaction_id', 'user_id', 'amount', 'status', 'created_at']),
            'recent_comments' => Comment::with('user')->latest()->take(5)->get(['id', 'user_id', 'content', 'created_at']),
            'recent_status_logs' => StatusLog::with('user')->latest()->take(5)->get(['id', 'status', 'statusable_type', 'statusable_id', 'created_at']),
        ];

        return response()->json($stats);
    }

    /**
     * Get monthly statistics for chart.
     */
    public function getMonthlyStats(): JsonResponse
    {
        $this->checkAdminAccess();

        $currentYear = now()->year;
        
        $monthlyStats = [];
        for ($month = 1; $month <= 12; $month++) {
            $startDate = now()->year($currentYear)->month($month)->startOfMonth();
            $endDate = now()->year($currentYear)->month($month)->endOfMonth();
            
            $monthlyStats[] = [
                'month' => $startDate->format('M'),
                'new_users' => User::whereBetween('created_at', [$startDate, $endDate])->count(),
                'new_ads' => Ad::whereBetween('created_at', [$startDate, $endDate])->count(),
                'new_payments' => Payment::whereBetween('created_at', [$startDate, $endDate])->count(),
                'total_revenue' => Payment::whereBetween('created_at', [$startDate, $endDate])
                    ->where('status', 'completed')
                    ->sum('amount'),
            ];
        }
        
        return response()->json([
            'monthly_stats' => $monthlyStats,
        ]);
    }

    /**
     * Get quick actions.
     */
    public function getQuickActions(): JsonResponse
    {
        $this->checkAdminAccess();

        // Get counts of items needing attention
        $pendingVendors = Vendor::where('status', 'pending')->count();
        $pendingAds = Ad::where('status', 'pending')->count();
        $pendingPayments = Payment::where('status', 'pending')->count();
        $unapprovedComments = Comment::where('is_approved', false)->count();

        $quickActions = [
            'pending_vendors' => $pendingVendors,
            'pending_ads' => $pendingAds,
            'pending_payments' => $pendingPayments,
            'unapproved_comments' => $unapprovedComments,
        ];

        return response()->json($quickActions);
    }
}