<?php

namespace App\Http\Controllers;

use App\Models\CryptoCurrency;
use App\Models\P2pCryptoOrder;
use App\Models\CryptoTransaction;
use App\Models\P2pCryptoEscrow;
use App\Models\P2pCryptoTradeDispute;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class P2pCryptoController extends Controller
{
    /**
     * Display available crypto currencies for P2P trading
     */
    public function getCryptoCurrencies(): JsonResponse
    {
        $currencies = CryptoCurrency::where('is_active', true)
            ->select('id', 'name', 'symbol', 'price', 'logo_url')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $currencies
        ]);
    }

    /**
     * Create a new P2P crypto order
     */
    public function createOrder(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'crypto_currency_id' => 'required|exists:crypto_currencies,id',
            'order_type' => 'required|in:buy,sell',
            'amount' => 'required|numeric|min:0.00000001',
            'price_per_unit' => 'required|numeric|min:0.00000001',
            'payment_method' => 'required|string|max:255',
            'terms_and_conditions' => 'nullable|string',
            'additional_notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();

        // For sell orders, check if user has sufficient crypto balance
        if ($request->order_type === 'sell') {
            // In a real implementation, we would check user's crypto wallet balance
            // For now, we'll just create the order
        }

        $order = P2pCryptoOrder::create([
            'seller_id' => $request->order_type === 'sell' ? $user->id : null,
            'buyer_id' => $request->order_type === 'buy' ? $user->id : null,
            'crypto_currency_id' => $request->crypto_currency_id,
            'order_type' => $request->order_type,
            'amount' => $request->amount,
            'price_per_unit' => $request->price_per_unit,
            'total_amount' => $request->amount * $request->price_per_unit,
            'payment_method' => $request->payment_method,
            'status' => 'active',
            'terms_and_conditions' => $request->terms_and_conditions,
            'additional_notes' => $request->additional_notes,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'P2P crypto order created successfully',
            'data' => $order
        ], 201);
    }

    /**
     * Get all active P2P crypto orders
     */
    public function getActiveOrders(Request $request): JsonResponse
    {
        $cryptoCurrencyId = $request->query('crypto_currency_id');
        $orderType = $request->query('order_type');

        $query = P2pCryptoOrder::with(['cryptoCurrency', 'seller', 'buyer'])
            ->where('status', 'active')
            ->orderBy('price_per_unit');

        if ($cryptoCurrencyId) {
            $query->where('crypto_currency_id', $cryptoCurrencyId);
        }

        if ($orderType) {
            $query->where('order_type', $orderType);
        }

        $orders = $query->get();

        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }

    /**
     * Get user's P2P orders
     */
    public function getUserOrders(Request $request): JsonResponse
    {
        $user = $request->user();
        $status = $request->query('status');

        $query = P2pCryptoOrder::with(['cryptoCurrency', 'seller', 'buyer'])
            ->where(function($q) use ($user) {
                $q->where('seller_id', $user->id)
                  ->orWhere('buyer_id', $user->id);
            });

        if ($status) {
            $query->where('status', $status);
        }

        $orders = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }

    /**
     * Match an order with the current user
     */
    public function matchOrder(Request $request, $orderId): JsonResponse
    {
        $user = $request->user();

        $order = P2pCryptoOrder::where('id', $orderId)
            ->where('status', 'active')
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found or not active'
            ], 404);
        }

        // Prevent user from matching their own order
        if ($user->id === $order->seller_id || $user->id === $order->buyer_id) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot match your own order'
            ], 400);
        }

        // Determine if this is a buyer matching a sell order or a seller matching a buy order
        if ($order->order_type === 'sell') {
            // Current user becomes the buyer
            $order->update([
                'buyer_id' => $user->id,
                'status' => 'matched',
                'matched_at' => now()
            ]);
        } else {
            // Current user becomes the seller
            $order->update([
                'seller_id' => $user->id,
                'status' => 'matched',
                'matched_at' => now()
            ]);
        }

        // Create escrow record
        P2pCryptoEscrow::create([
            'p2p_order_id' => $order->id,
            'crypto_transaction_id' => null, // Will be set when crypto is transferred
            'amount' => $order->amount,
            'status' => 'held'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Order matched successfully',
            'data' => $order->fresh()
        ]);
    }

    /**
     * Cancel a P2P order
     */
    public function cancelOrder(Request $request, $orderId): JsonResponse
    {
        $user = $request->user();

        $order = P2pCryptoOrder::findOrFail($orderId);

        // Only the seller can cancel a sell order, only the buyer can cancel a buy order
        $isOwner = ($order->order_type === 'sell' && $order->seller_id === $user->id) ||
                   ($order->order_type === 'buy' && $order->buyer_id === $user->id);

        if (!$isOwner) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to cancel this order'
            ], 403);
        }

        if (!in_array($order->status, ['active', 'matched'])) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot cancel order with current status'
            ], 400);
        }

        $order->update([
            'status' => 'cancelled',
            'cancelled_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Order cancelled successfully'
        ]);
    }

    /**
     * Create a trade dispute
     */
    public function createDispute(Request $request, $orderId): JsonResponse
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'dispute_type' => 'required|in:payment_not_received,payment_not_made,other',
            'description' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $order = P2pCryptoOrder::findOrFail($orderId);

        // Check if user is involved in the order
        if ($user->id !== $order->seller_id && $user->id !== $order->buyer_id) {
            return response()->json([
                'success' => false,
                'message' => 'User not involved in this order'
            ], 403);
        }

        $dispute = P2pCryptoTradeDispute::create([
            'p2p_order_id' => $order->id,
            'initiator_user_id' => $user->id,
            'dispute_type' => $request->dispute_type,
            'description' => $request->description,
            'status' => 'open'
        ]);

        // Update order status to indicate dispute
        $order->update(['status' => 'in_dispute']);

        return response()->json([
            'success' => true,
            'message' => 'Dispute created successfully',
            'data' => $dispute
        ], 201);
    }

    /**
     * Process payment for a matched P2P order
     */
    public function processPayment(Request $request, $orderId): JsonResponse
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'payment_method' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
            'proof_of_payment' => 'nullable|string', // For bank transfers, mobile money, etc.
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $order = P2pCryptoOrder::findOrFail($orderId);

        // Check if user is involved in the order
        if ($user->id !== $order->seller_id && $user->id !== $order->buyer_id) {
            return response()->json([
                'success' => false,
                'message' => 'User not involved in this order'
            ], 403);
        }

        // Check order status - payment can only be processed for matched orders
        if ($order->status !== 'matched') {
            return response()->json([
                'success' => false,
                'message' => 'Payment can only be processed for matched orders'
            ], 400);
        }

        // In a real implementation, we would integrate with payment gateways here
        // For now, we'll create a payment transaction record
        $paymentTransaction = \App\Models\PaymentTransaction::create([
            'user_id' => $user->id,
            'transaction_id' => 'P2P_' . strtoupper(uniqid()),
            'amount' => $order->total_amount,
            'currency' => 'USD', // Default currency for crypto transactions
            'status' => 'pending',
            'payment_method' => $request->payment_method,
            'description' => "P2P Crypto Order Payment - Order #{$order->id}",
            'payment_gateway' => $request->payment_method,
            'payment_reference' => $request->proof_of_payment ?? null,
        ]);

        // Update order with payment transaction ID
        $order->update([
            'payment_transaction_id' => $paymentTransaction->id,
            'status' => 'in_progress'
        ]);

        // Trigger notification to the counterparty
        // In a real app, we would send push notifications, emails, etc.

        return response()->json([
            'success' => true,
            'message' => 'Payment initiated successfully',
            'data' => [
                'payment_transaction' => $paymentTransaction,
                'order' => $order->fresh()
            ]
        ], 201);
    }

    /**
     * Release crypto from escrow after payment confirmation
     */
    public function releaseEscrow(Request $request, $orderId): JsonResponse
    {
        $user = $request->user();

        // Only the seller can release escrow in a buy order, only the buyer can release escrow in a sell order
        $order = P2pCryptoOrder::with('escrow')->findOrFail($orderId);

        if ($user->id !== $order->seller_id && $user->id !== $order->buyer_id) {
            return response()->json([
                'success' => false,
                'message' => 'User not involved in this order'
            ], 403);
        }

        // Check if the user has the right to release escrow
        $canRelease = false;
        if ($order->order_type === 'buy' && $user->id === $order->seller_id) {
            $canRelease = true;
        } elseif ($order->order_type === 'sell' && $user->id === $order->buyer_id) {
            $canRelease = true;
        }

        if (!$canRelease) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to release escrow'
            ], 403);
        }

        if ($order->status !== 'in_progress') {
            return response()->json([
                'success' => false,
                'message' => 'Order not in progress state'
            ], 400);
        }

        // Update the escrow status to released
        if ($order->escrow) {
            $order->escrow->update([
                'status' => 'released',
                'released_at' => now(),
                'release_notes' => $request->notes ?? 'Released by authorized party'
            ]);
        }

        // Update the order status to completed
        $order->update([
            'status' => 'completed',
            'completed_at' => now()
        ]);

        // Create crypto transaction record
        $cryptoTransaction = CryptoTransaction::create([
            'user_id' => $order->buyer_id ?? $order->seller_id,
            'crypto_currency_id' => $order->crypto_currency_id,
            'transaction_type' => 'buy',
            'amount' => $order->amount,
            'rate' => $order->price_per_unit,
            'total_value' => $order->total_amount,
            'status' => 'completed',
            'executed_at' => now()
        ]);

        // Update the order with the crypto transaction ID
        $order->update([
            'crypto_transaction_id' => $cryptoTransaction->id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Escrow released and crypto transferred successfully',
            'data' => $order->fresh()
        ]);
    }

    /**
     * Get a specific P2P order
     */
    public function show(Request $request, $orderId): JsonResponse
    {
        $user = $request->user();

        $order = P2pCryptoOrder::with(['cryptoCurrency', 'seller', 'buyer', 'escrow'])
            ->where('id', $orderId)
            ->where(function($q) use ($user) {
                $q->where('seller_id', $user->id)
                  ->orWhere('buyer_id', $user->id);
            })
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $order
        ]);
    }
}