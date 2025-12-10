<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Subscription;
use App\Models\Ad;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class TransactionReportController extends Controller
{
    /**
     * Get user's transaction history report.
     */
    public function getUserTransactions(Request $request): JsonResponse
    {
        $authenticatedUser = Auth::user();
        if (!$authenticatedUser) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $query = Payment::where('user_id', $authenticatedUser->id)
            ->with(['subscription', 'ad'])
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('gateway')) {
            $query->where('payment_gateway', $request->gateway);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $transactions = $query->paginate(50);

        return response()->json([
            'transactions' => $transactions,
        ]);
    }

    /**
     * Export user's transaction history as CSV.
     */
    public function exportUserTransactions(Request $request)
    {
        $authenticatedUser = Auth::user();
        if (!$authenticatedUser) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $query = Payment::where('user_id', $authenticatedUser->id)
            ->with(['subscription', 'ad'])
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('gateway')) {
            $query->where('payment_gateway', $request->gateway);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $transactions = $query->get();

        $filename = "transaction_history_" . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ];

        $callback = function() use ($transactions) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, [
                'ID', 
                'Transaction ID', 
                'Amount', 
                'Currency', 
                'Gateway', 
                'Method', 
                'Status', 
                'Ad Title', 
                'Subscription Name', 
                'Date'
            ]);

            foreach ($transactions as $transaction) {
                fputcsv($file, [
                    $transaction->id,
                    $transaction->transaction_id,
                    $transaction->amount,
                    $transaction->currency_code,
                    $transaction->payment_gateway,
                    $transaction->payment_method ?? 'N/A',
                    $transaction->status,
                    $transaction->ad ? $transaction->ad->title : 'N/A',
                    $transaction->subscription ? $transaction->subscription->name : 'N/A',
                    $transaction->created_at->format('Y-m-d H:i:s')
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get user's subscription history.
     */
    public function getUserSubscriptions(): JsonResponse
    {
        $authenticatedUser = Auth::user();
        if (!$authenticatedUser) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $subscriptions = Subscription::where('user_id', $authenticatedUser->id)
            ->with(['payments'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'subscriptions' => $subscriptions,
        ]);
    }

    /**
     * Get user's ad payment history.
     */
    public function getUserAdPayments(): JsonResponse
    {
        $authenticatedUser = Auth::user();
        if (!$authenticatedUser) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $adPayments = Payment::where('user_id', $authenticatedUser->id)
            ->whereNotNull('ad_id')
            ->with(['ad', 'ad.category'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'ad_payments' => $adPayments,
        ]);
    }

    /**
     * Get user's subscription payment history.
     */
    public function getUserSubscriptionPayments(): JsonResponse
    {
        $authenticatedUser = Auth::user();
        if (!$authenticatedUser) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $subscriptionPayments = Payment::where('user_id', $authenticatedUser->id)
            ->whereNotNull('subscription_id')
            ->with(['subscription'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'subscription_payments' => $subscriptionPayments,
        ]);
    }

    /**
     * Get user's transaction statistics.
     */
    public function getUserTransactionStats(): JsonResponse
    {
        $authenticatedUser = Auth::user();
        if (!$authenticatedUser) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $totalTransactions = Payment::where('user_id', $authenticatedUser->id)->count();
        $totalPaid = Payment::where('user_id', $authenticatedUser->id)
            ->where('status', 'completed')
            ->sum('amount');
        $totalPending = Payment::where('user_id', $authenticatedUser->id)
            ->where('status', 'pending')
            ->sum('amount');
        $totalFailed = Payment::where('user_id', $authenticatedUser->id)
            ->where('status', 'failed')
            ->sum('amount');

        return response()->json([
            'total_transactions' => $totalTransactions,
            'total_paid' => $totalPaid,
            'total_pending' => $totalPending,
            'total_failed' => $totalFailed,
        ]);
    }
}