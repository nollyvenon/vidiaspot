<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\User;
use App\Models\Ad;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class PaymentsController extends Controller
{
    /**
     * Get all payments for admin management.
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user || (!$user->hasRole('admin') && $user->email !== config('app.admin_email', 'admin@vidiaspot.com'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $query = Payment::with(['user', 'ad', 'subscription']);

        // Filter by status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Filter by payment gateway
        if ($request->payment_gateway) {
            $query->where('payment_gateway', $request->payment_gateway);
        }

        // Filter by user
        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by date range
        if ($request->date_from) {
            $query->where('created_at', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->where('created_at', '<=', $request->date_to);
        }

        // Search
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('transaction_id', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', function($q) use ($request) {
                      $q->where('name', 'like', '%' . $request->search . '%')
                        ->orWhere('email', 'like', '%' . $request->search . '%');
                  });
            });
        }

        $payments = $query->orderBy('created_at', 'desc')->paginate($request->per_page ?? 15);

        return response()->json([
            'success' => true,
            'data' => $payments,
            'message' => 'Payments list for admin management'
        ]);
    }

    /**
     * Get payment statistics.
     */
    public function stats(): JsonResponse
    {
        $user = Auth::user();

        if (!$user || (!$user->hasRole('admin') && $user->email !== config('app.admin_email', 'admin@vidiaspot.com'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $stats = [
            'total_payments' => Payment::count(),
            'total_revenue' => Payment::where('status', 'completed')->sum('amount'),
            'pending_payments' => Payment::where('status', 'pending')->count(),
            'failed_payments' => Payment::where('status', 'failed')->count(),
            'payments_by_gateway' => Payment::selectRaw('payment_gateway, COUNT(*) as count, SUM(amount) as total')
                ->groupBy('payment_gateway')
                ->get()
                ->map(function($item) {
                    return [
                        'gateway' => $item->payment_gateway,
                        'count' => $item->count,
                        'total' => $item->total
                    ];
                }),
            'payments_by_status' => Payment::selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status'),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
            'message' => 'Payment statistics'
        ]);
    }

    /**
     * Update payment status.
     */
    public function updateStatus(Request $request, string $id): JsonResponse
    {
        $user = Auth::user();

        if (!$user || (!$user->hasRole('admin') && $user->email !== config('app.admin_email', 'admin@vidiaspot.com'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'status' => 'required|in:completed,failed,refunded,pending'
        ]);

        $payment = Payment::findOrFail($id);
        $payment->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'data' => $payment->refresh(),
            'message' => 'Payment status updated successfully'
        ]);
    }

    /**
     * Get payment details.
     */
    public function show(string $id): JsonResponse
    {
        $user = Auth::user();

        if (!$user || (!$user->hasRole('admin') && $user->email !== config('app.admin_email', 'admin@vidiaspot.com'))) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $payment = Payment::with(['user', 'ad', 'subscription'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $payment,
            'message' => 'Payment details'
        ]);
    }
}
