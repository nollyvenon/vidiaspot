<?php

namespace App\Http\Controllers\Admin;

use App\Models\Payment;
use App\Models\User;
use App\Models\Subscription;
use App\Models\Ad;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class PaymentController extends Controller
{
    /**
     * Display a listing of payments.
     */
    public function index(Request $request): View
    {
        $this->checkAdminAccess();

        $query = Payment::with(['user', 'subscription', 'ad']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('gateway')) {
            $query->where('payment_gateway', $request->gateway);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $payments = $query->latest()->paginate(25);

        $filters = [
            'status' => $request->status,
            'gateway' => $request->gateway,
            'user_id' => $request->user_id,
            'date_from' => $request->date_from,
            'date_to' => $request->date_to,
        ];

        return $this->adminView('admin.payments.index', [
            'payments' => $payments,
            'gateways' => Payment::getSupportedGateways(),
            'filters' => $filters,
        ]);
    }

    /**
     * Display the specified payment.
     */
    public function show(Payment $payment): View
    {
        $this->checkAdminAccess();

        $payment->load(['user', 'subscription', 'ad']);

        return $this->adminView('admin.payments.show', [
            'payment' => $payment,
        ]);
    }

    /**
     * Update the specified payment status.
     */
    public function updateStatus(Request $request, Payment $payment): JsonResponse
    {
        $this->checkAdminAccess();

        $request->validate([
            'status' => 'required|in:pending,completed,failed,refunded',
        ]);

        $oldStatus = $payment->status;
        $payment->update([
            'status' => $request->status,
        ]);

        // If changing to completed, update completed_at
        if ($request->status === 'completed' && $oldStatus !== 'completed') {
            $payment->update([
                'completed_at' => now(),
            ]);
        }

        return response()->json([
            'message' => 'Payment status updated successfully',
            'payment' => $payment->refresh(),
        ]);
    }

    /**
     * Process a refund for a payment.
     */
    public function processRefund(Request $request, Payment $payment): JsonResponse
    {
        $this->checkAdminAccess();

        $request->validate([
            'refund_reason' => 'required|string|max:500',
        ]);

        if ($payment->status !== 'completed') {
            return response()->json([
                'error' => 'Cannot refund a payment that is not completed',
            ], 400);
        }

        // Here we would typically call the payment gateway API to process the refund
        // For now, we'll just update the payment status and log the refund
        
        $payment->update([
            'status' => 'refunded',
            'metadata' => array_merge($payment->metadata ?? [], [
                'refunded_by' => auth()->id(),
                'refund_reason' => $request->refund_reason,
                'refunded_at' => now(),
            ]),
        ]);

        return response()->json([
            'message' => 'Payment refunded successfully',
            'payment' => $payment->refresh(),
        ]);
    }

    /**
     * Get payment statistics for admin dashboard.
     */
    public function stats(): JsonResponse
    {
        $this->checkAdminAccess();

        $totalPayments = Payment::count();
        $totalCompleted = Payment::where('status', 'completed')->count();
        $totalFailed = Payment::where('status', 'failed')->count();
        $totalRefunded = Payment::where('status', 'refunded')->count();
        $totalAmount = Payment::where('status', 'completed')->sum('amount');
        $todayPayments = Payment::whereDate('created_at', today())->count();
        $todayAmount = Payment::whereDate('created_at', today())->where('status', 'completed')->sum('amount');

        // Get recent payments
        $recentPayments = Payment::with(['user', 'subscription', 'ad'])
            ->latest()
            ->limit(10)
            ->get();

        return response()->json([
            'total_payments' => $totalPayments,
            'total_completed' => $totalCompleted,
            'total_failed' => $totalFailed,
            'total_refunded' => $totalRefunded,
            'total_amount' => $totalAmount,
            'today_payments' => $todayPayments,
            'today_amount' => $todayAmount,
            'recent_payments' => $recentPayments,
        ]);
    }
}