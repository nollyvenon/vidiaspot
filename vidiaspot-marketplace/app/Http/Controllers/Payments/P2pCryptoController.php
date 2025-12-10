<?php

namespace App\Http\Controllers\Payments;

use App\Http\Requests\StoreP2pCryptoOrderRequest;
use App\Http\Requests\StoreP2pCryptoTradingOrderRequest;
use App\Http\Resources\P2pCryptoOrderResource;
use App\Http\Resources\P2pCryptoTradingOrderResource;
use App\Models\CryptoCurrency;
use App\Models\P2pCryptoOrder;
use App\Models\CryptoTransaction;
use App\Models\P2pCryptoEscrow;
use App\Models\P2pCryptoTradeDispute;
use App\Models\P2pCryptoTradingOrder;
use App\Models\P2pCryptoTradingPair;
use App\Services\P2pCryptoSecurityService;
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
    public function createOrder(StoreP2pCryptoOrderRequest $request): JsonResponse
    {
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
            'payment_method_id' => $request->payment_method_id, // Use stored payment method if provided
            'status' => 'active',
            'terms_and_conditions' => $request->terms_and_conditions,
            'additional_notes' => $request->additional_notes,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'P2P crypto order created successfully',
            'data' => new P2pCryptoOrderResource($order)
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
     * Create an OCO (One-Cancels-Other) order pair
     */
    public function createOCOOrder(StoreP2pCryptoTradingOrderRequest $request): \Illuminate\Http\JsonResponse
    {
        $user = $request->user();

        // OCO orders require two orders: one limit and one stop-loss
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'trading_pair_id' => 'required|exists:p2p_crypto_trading_pairs,id',
            'side' => 'required|in:buy,sell',
            'quantity' => 'required|numeric|min:0.00000001',
            'limit_price' => 'required|numeric|min:0.00000001',
            'stop_price' => 'required|numeric|min:0.00000001', // Stop loss price
            'stop_limit_price' => 'nullable|numeric|min:0.00000001', // Optional stop-limit price
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $pair = P2pCryptoTradingPair::find($request->trading_pair_id);

        \DB::transaction(function () use ($request, $user, $pair) {
            // Create the primary order (limit order)
            $limitOrder = P2pCryptoTradingOrder::create([
                'user_id' => $user->id,
                'trading_pair_id' => $request->trading_pair_id,
                'order_type' => 'limit',
                'side' => $request->side,
                'quantity' => $request->quantity,
                'price' => $request->limit_price,
                'status' => 'pending',
                'time_in_force' => $request->time_in_force ?? 'GTC',
                'fee_currency' => $pair->quoteCurrency->symbol,
                'is_oco_group_member' => true,
                'oco_group_id' => \Illuminate\Support\Str::uuid(),
            ]);

            // Create the secondary order (stop-loss order)
            $stopOrderType = $request->side === 'buy' ? 'stop_limit' : 'stop_loss';
            $stopOrder = P2pCryptoTradingOrder::create([
                'user_id' => $user->id,
                'trading_pair_id' => $request->trading_pair_id,
                'order_type' => $stopOrderType,
                'side' => $request->side === 'buy' ? 'sell' : 'buy', // Opposite side for stop order
                'quantity' => $request->quantity,
                'price' => $request->stop_limit_price, // Use stop limit if provided, otherwise market
                'stop_price' => $request->stop_price,
                'status' => 'pending',
                'time_in_force' => 'GTC',
                'fee_currency' => $pair->quoteCurrency->symbol,
                'is_oco_group_member' => true,
                'oco_group_id' => $limitOrder->oco_group_id,
            ]);

            // When one order executes, cancel the other
            // In a real implementation, you'd set up listeners for this behavior
        });

        return response()->json([
            'success' => true,
            'message' => 'OCO order created successfully',
            'data' => [
                'limit_order' => new P2pCryptoTradingOrderResource($limitOrder),
                'stop_order' => new P2pCryptoTradingOrderResource($stopOrder),
            ]
        ], 201);
    }

    /**
     * Create a trailing stop order
     */
    public function createTrailingStopOrder(StoreP2pCryptoTradingOrderRequest $request): \Illuminate\Http\JsonResponse
    {
        $user = $request->user();

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'trading_pair_id' => 'required|exists:p2p_crypto_trading_pairs,id',
            'side' => 'required|in:buy,sell',
            'quantity' => 'required|numeric|min:0.00000001',
            'trailing_amount' => 'required|numeric|min:0.00000001', // Trail by this amount
            'trailing_percent' => 'nullable|numeric|min:0.0001|max:100', // Or trail by percentage
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $pair = P2pCryptoTradingPair::find($request->trading_pair_id);

        $order = P2pCryptoTradingOrder::create([
            'user_id' => $user->id,
            'trading_pair_id' => $request->trading_pair_id,
            'order_type' => 'trailing_stop',
            'side' => $request->side,
            'quantity' => $request->quantity,
            'price' => null, // Will be calculated dynamically
            'stop_price' => null, // Will be calculated dynamically based on current price and trail
            'status' => 'pending',
            'time_in_force' => $request->time_in_force ?? 'GTC',
            'fee_currency' => $pair->quoteCurrency->symbol,
            'metadata' => [
                'trailing_amount' => $request->trailing_amount,
                'trailing_percent' => $request->trailing_percent ?? null,
                'activation_price' => $request->activation_price ?? null, // Optional activation price
                'is_triggered' => false,
            ],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Trailing stop order created successfully',
            'data' => new P2pCryptoTradingOrderResource($order->load('tradingPair'))
        ], 201);
    }

    /**
     * Create a grid trading strategy
     */
    public function createGridOrder(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = $request->user();

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'trading_pair_id' => 'required|exists:p2p_crypto_trading_pairs,id',
            'side' => 'required|in:buy,sell,both',
            'upper_price' => 'required|numeric|min:0.00000001',
            'lower_price' => 'required|numeric|min:0.00000001|lt:upper_price',
            'grid_levels' => 'required|integer|min:2|max:100',
            'total_quantity' => 'required|numeric|min:0.00000001',
            'stop_loss_price' => 'nullable|numeric|min:0.00000001',
            'take_profit_price' => 'nullable|numeric|min:0.00000001',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $pair = P2pCryptoTradingPair::find($request->trading_pair_id);

        // Calculate grid prices
        $priceStep = ($request->upper_price - $request->lower_price) / ($request->grid_levels - 1);
        $quantityPerLevel = $request->total_quantity / $request->grid_levels;

        $orders = [];
        \DB::transaction(function () use ($request, $user, $pair, $priceStep, $quantityPerLevel, &$orders) {
            for ($i = 0; $i < $request->grid_levels; $i++) {
                $gridPrice = $request->lower_price + ($i * $priceStep);

                // Determine if this is a buy or sell order based on position
                // For a basic grid: buy at lower prices, sell at higher prices
                $levelSide = ($gridPrice < ($request->upper_price + $request->lower_price) / 2) ? 'buy' : 'sell';

                // If side is restricted to buy or sell only, respect that
                if ($request->side !== 'both') {
                    $levelSide = $request->side;
                }

                $order = P2pCryptoTradingOrder::create([
                    'user_id' => $user->id,
                    'trading_pair_id' => $request->trading_pair_id,
                    'order_type' => 'limit',
                    'side' => $levelSide,
                    'quantity' => $quantityPerLevel,
                    'price' => $gridPrice,
                    'status' => 'pending',
                    'time_in_force' => $request->time_in_force ?? 'GTC',
                    'fee_currency' => $pair->quoteCurrency->symbol,
                    'is_grid_member' => true,
                    'grid_group_id' => \Illuminate\Support\Str::uuid(),
                    'metadata' => [
                        'grid_level' => $i + 1,
                        'grid_strategy' => 'basic',
                    ],
                ]);

                $orders[] = $order;
            }

            // Create stop loss and take profit orders if specified
            if ($request->stop_loss_price) {
                P2pCryptoTradingOrder::create([
                    'user_id' => $user->id,
                    'trading_pair_id' => $request->trading_pair_id,
                    'order_type' => 'stop_loss',
                    'side' => 'sell', // Assuming we're closing a long position
                    'quantity' => $request->total_quantity,
                    'stop_price' => $request->stop_loss_price,
                    'status' => 'pending',
                    'time_in_force' => 'GTC',
                    'fee_currency' => $pair->quoteCurrency->symbol,
                    'is_grid_protection' => true,
                    'grid_group_id' => $orders[0]->grid_group_id,
                    'metadata' => [
                        'protection_type' => 'stop_loss',
                    ],
                ]);
            }

            if ($request->take_profit_price) {
                P2pCryptoTradingOrder::create([
                    'user_id' => $user->id,
                    'trading_pair_id' => $request->trading_pair_id,
                    'order_type' => 'limit',
                    'side' => 'sell', // Assuming we're closing a long position
                    'quantity' => $request->total_quantity,
                    'price' => $request->take_profit_price,
                    'status' => 'pending',
                    'time_in_force' => 'GTC',
                    'fee_currency' => $pair->quoteCurrency->symbol,
                    'is_grid_protection' => true,
                    'grid_group_id' => $orders[0]->grid_group_id,
                    'metadata' => [
                        'protection_type' => 'take_profit',
                    ],
                ]);
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Grid trading strategy created successfully',
            'data' => [
                'grid_group_id' => $orders[0]->grid_group_id,
                'orders' => P2pCryptoTradingOrderResource::collection(collect($orders)->load('tradingPair')),
            ]
        ], 201);
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

    /**
     * Get all available trading pairs
     */
    public function getTradingPairs(): \Illuminate\Http\JsonResponse
    {
        $tradingPairs = \App\Models\P2pCryptoTradingPair::with(['baseCurrency', 'quoteCurrency'])
            ->where('is_active', true)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $tradingPairs
        ]);
    }

    /**
     * Get order book for a trading pair
     */
    public function getOrderBook(Request $request, $pairId): \Illuminate\Http\JsonResponse
    {
        $pair = \App\Models\P2pCryptoTradingPair::find($pairId);
        if (!$pair) {
            return response()->json([
                'success' => false,
                'message' => 'Trading pair not found'
            ], 404);
        }

        // Get active buy orders (bids) - sorted by price descending
        $bids = \App\Models\P2pCryptoOrder::where([
                ['crypto_currency_id', $pair->base_currency_id],
                ['order_type', 'buy'],
                ['status', 'active']
            ])
            ->orderBy('price_per_unit', 'desc')
            ->limit(20)
            ->get();

        // Get active sell orders (asks) - sorted by price ascending
        $asks = \App\Models\P2pCryptoOrder::where([
                ['crypto_currency_id', $pair->base_currency_id],
                ['order_type', 'sell'],
                ['status', 'active']
            ])
            ->orderBy('price_per_unit', 'asc')
            ->limit(20)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'pair' => $pair,
                'bids' => $bids,
                'asks' => $asks
            ]
        ]);
    }

    /**
     * Create a new trading order (advanced order types)
     */
    public function createTradingOrder(StoreP2pCryptoTradingOrderRequest $request): \Illuminate\Http\JsonResponse
    {
        $user = $request->user();
        $pair = P2pCryptoTradingPair::find($request->trading_pair_id);

        // Validate order type specific requirements
        if (in_array($request->order_type, ['limit', 'stop_limit']) && !$request->price) {
            return response()->json([
                'success' => false,
                'message' => 'Price is required for limit orders'
            ], 400);
        }

        if (in_array($request->order_type, ['stop_loss', 'stop_limit']) && !$request->stop_price) {
            return response()->json([
                'success' => false,
                'message' => 'Stop price is required for stop orders'
            ], 400);
        }

        $order = P2pCryptoTradingOrder::create([
            'user_id' => $user->id,
            'trading_pair_id' => $request->trading_pair_id,
            'order_type' => $request->order_type,
            'side' => $request->side,
            'quantity' => $request->quantity,
            'price' => $request->price,
            'stop_price' => $request->stop_price,
            'status' => 'pending',
            'time_in_force' => $request->time_in_force ?? 'GTC',
            'good_till_date' => $request->good_till_date,
            'post_only' => $request->post_only ?? false,
            'reduce_only' => $request->reduce_only ?? false,
            'fee_currency' => $pair->quoteCurrency->symbol,
        ]);

        // For market orders, try to match immediately
        if ($request->order_type === 'market') {
            $this->processMarketOrder($order);
        }

        return response()->json([
            'success' => true,
            'message' => 'Trading order created successfully',
            'data' => new P2pCryptoTradingOrderResource($order->load('tradingPair'))
        ], 201);
    }

    /**
     * Process market order by matching with existing orders
     */
    private function processMarketOrder(\App\Models\P2pCryptoTradingOrder $order): void
    {
        // This is a simplified matching logic - in production, you'd have a more complex engine
        $pair = $order->tradingPair;
        $targetSide = $order->side === 'buy' ? 'sell' : 'buy';

        // Get matching orders from the opposite side
        $matchingOrders = \App\Models\P2pCryptoOrder::where([
                ['crypto_currency_id', $pair->base_currency_id],
                ['order_type', $targetSide],
                ['status', 'active']
            ])
            ->orderBy($order->side === 'buy' ? 'price_per_unit' : 'price_per_unit', $order->side === 'buy' ? 'asc' : 'desc')
            ->get();

        $remainingQuantity = $order->quantity;

        foreach ($matchingOrders as $matchingOrder) {
            if ($remainingQuantity <= 0) {
                break;
            }

            $executedQty = min($remainingQuantity, $matchingOrder->amount);
            $executionPrice = $matchingOrder->price_per_unit;

            // Create trade execution
            \App\Models\P2pCryptoTradeExecution::create([
                'trading_order_id' => $order->id,
                'maker_order_id' => $matchingOrder->id,
                'taker_order_id' => $order->id,
                'trading_pair_id' => $order->trading_pair_id,
                'side' => $order->side,
                'quantity' => $executedQty,
                'price' => $executionPrice,
                'fee' => 0, // Calculate based on fee structure
                'fee_currency' => $pair->quoteCurrency->symbol,
                'fee_payer' => 'taker',
                'executed_at' => now(),
            ]);

            // Update original orders
            $matchingOrder->update([
                'amount' => $matchingOrder->amount - $executedQty,
                'status' => ($matchingOrder->amount - $executedQty) <= 0 ? 'completed' : 'partially_filled'
            ]);

            $order->update([
                'executed_quantity' => $order->executed_quantity + $executedQty,
                'status' => ($order->executed_quantity + $executedQty >= $order->quantity) ? 'filled' : 'partially_filled',
                'avg_price' => (($order->avg_price * $order->executed_quantity) + ($executionPrice * $executedQty)) / ($order->executed_quantity + $executedQty)
            ]);

            $remainingQuantity -= $executedQty;
        }
    }

    /**
     * Get user's trading orders
     */
    public function getUserTradingOrders(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = $request->user();
        $status = $request->query('status');
        $pairId = $request->query('trading_pair_id');
        $orderType = $request->query('order_type');

        $query = \App\Models\P2pCryptoTradingOrder::with(['tradingPair', 'tradeExecutions'])
            ->where('user_id', $user->id);

        if ($status) {
            $query->where('status', $status);
        }

        if ($pairId) {
            $query->where('trading_pair_id', $pairId);
        }

        if ($orderType) {
            $query->where('order_type', $orderType);
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }

    /**
     * Get user's trade history
     */
    public function getUserTradeHistory(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = $request->user();

        $executions = \App\Models\P2pCryptoTradeExecution::with(['tradingPair', 'tradingOrder'])
            ->where(function($query) use ($user) {
                $query->whereHas('tradingOrder', function($q) use ($user) {
                    $q->where('user_id', $user->id);
                });
            })
            ->orderBy('executed_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $executions
        ]);
    }

    /**
     * Get user's payment methods
     */
    public function getUserPaymentMethods(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = $request->user();

        $paymentMethods = \App\Models\P2pCryptoPaymentMethod::where('user_id', $user->id)
            ->select('id', 'payment_type', 'payment_provider', 'name', 'account_number', 'account_name', 'bank_name', 'is_verified', 'is_active', 'is_default')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $paymentMethods
        ]);
    }

    /**
     * Get all disputes for a user
     */
    public function getUserDisputes(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = $request->user();
        $status = $request->query('status');

        $query = \App\Models\P2pCryptoTradeDispute::with(['p2pOrder', 'initiator'])
            ->where(function($query) use ($user) {
                $query->where('initiator_user_id', $user->id)
                      ->orWhereHas('p2pOrder', function($q) use ($user) {
                          $q->where('seller_id', $user->id)
                            ->orWhere('buyer_id', $user->id);
                      });
            });

        if ($status) {
            $query->where('status', $status);
        }

        $disputes = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $disputes
        ]);
    }

    /**
     * Get a specific dispute
     */
    public function getDispute(Request $request, $disputeId): \Illuminate\Http\JsonResponse
    {
        $user = $request->user();

        $dispute = \App\Models\P2pCryptoTradeDispute::with(['p2pOrder', 'initiator', 'resolver'])
            ->where('id', $disputeId)
            ->where(function($query) use ($user) {
                $query->where('initiator_user_id', $user->id)
                      ->orWhereHas('p2pOrder', function($q) use ($user) {
                          $q->where('seller_id', $user->id)
                            ->orWhere('buyer_id', $user->id);
                      });
            })
            ->first();

        if (!$dispute) {
            return response()->json([
                'success' => false,
                'message' => 'Dispute not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $dispute
        ]);
    }

    /**
     * Get user's verification status and level
     */
    public function getUserVerificationStatus(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = $request->user();

        $verification = \App\Models\P2pCryptoUserVerification::where('user_id', $user->id)
            ->latest()
            ->first();

        return response()->json([
            'success' => true,
            'data' => [
                'verification_level' => $user->verification_level ?? 'unverified',
                'verification_status' => $verification ? $verification->verification_status : 'unverified',
                'is_verified' => $user->is_verified ?? false,
                'reputation_score' => $user->reputation_score ?? 0,
                'trade_completion_rate' => $user->trade_completion_rate ?? 0,
                'total_trade_count' => $user->total_trade_count ?? 0,
                'last_trade_at' => $user->last_trade_at,
                'is_trusted_seller' => $user->is_trusted_seller ?? false,
                'daily_limit' => $this->getVerificationLevelLimit($user),
                'remaining_daily_limit' => $this->getRemainingDailyLimit($user),
            ]
        ]);
    }

    /**
     * Submit verification documents
     */
    public function submitVerification(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = $request->user();
        $securityService = new P2pCryptoSecurityService();

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'verification_type' => 'required|in:document,biometric_face,biometric_fingerprint,video',
            'document_type' => 'required_if:verification_type,document',
            'document_front' => 'required_if:verification_type,document|url',
            'document_back' => 'nullable|url',
            'face_image' => 'required_if:verification_type,biometric_face|url',
            'fingerprint_data' => 'required_if:verification_type,biometric_fingerprint',
            'session_id' => 'required_if:verification_type,video',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Process verification based on type
        $verificationResult = $securityService->verifyIdentity($user, $request->all());

        if ($verificationResult['success']) {
            // Save verification record
            $verification = \App\Models\P2pCryptoUserVerification::create([
                'user_id' => $user->id,
                'verification_type' => $request->verification_type,
                'verification_status' => $verificationResult['status'],
                'verification_level' => $this->getVerificationLevel($request->verification_type),
                'verification_data' => $request->except(['verification_type', 'document_front', 'document_back']),
                'document_type' => $request->document_type,
                'document_front_image' => $request->document_front,
                'document_back_image' => $request->document_back,
                'selfie_image' => $request->selfie_image,
                'verification_notes' => $verificationResult['details']['reason'] ?? null,
                'verified_at' => $verificationResult['status'] === 'verified' ? now() : null,
                'verified_by' => $verificationResult['status'] === 'verified' ? null : null, // Would be set by admin in real app
            ]);

            // Update user verification status if successful
            if ($verificationResult['status'] === 'verified') {
                $user->update([
                    'is_verified' => true,
                    'verification_level' => $this->getVerificationLevel($request->verification_type),
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Verification submitted successfully',
                'data' => $verification
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Verification failed',
                'data' => $verificationResult
            ], 400);
        }
    }

    /**
     * Calculate user's reputation score
     */
    public function calculateReputation(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = $request->user();
        $securityService = new P2pCryptoSecurityService();

        $reputationScore = $securityService->calculateReputationScore($user);

        return response()->json([
            'success' => true,
            'message' => 'Reputation calculated successfully',
            'data' => [
                'reputation_score' => $reputationScore,
                'user_id' => $user->id
            ]
        ]);
    }

    /**
     * Get risk assessment for a trading position
     */
    public function getRiskAssessment(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'leverage' => 'required|numeric|min:1',
            'margin' => 'required|numeric|min:0',
            'liquidation_price' => 'required|numeric|min:0',
            'current_price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $securityService = new P2pCryptoSecurityService();

        $riskAssessment = $securityService->assessPositionRisk($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Risk assessment completed',
            'data' => $riskAssessment
        ]);
    }

    /**
     * Get risk report for user
     */
    public function getRiskReport(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = $request->user();
        $securityService = new P2pCryptoSecurityService();

        $riskReport = $securityService->generateRiskReport($user);

        return response()->json([
            'success' => true,
            'message' => 'Risk report generated',
            'data' => $riskReport
        ]);
    }

    /**
     * Calculate portfolio diversification
     */
    public function getDiversificationScore(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'portfolio' => 'required|array',
            'portfolio.*.symbol' => 'required|string',
            'portfolio.*.value' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $securityService = new P2pCryptoSecurityService();

        $diversificationScore = $securityService->calculateDiversificationScore($request->portfolio);

        return response()->json([
            'success' => true,
            'message' => 'Diversification score calculated',
            'data' => [
                'diversification_score' => $diversificationScore,
                'portfolio_size' => count($request->portfolio)
            ]
        ]);
    }

    /**
     * Get user's verification level limit
     */
    private function getVerificationLevelLimit($user): float
    {
        $verificationLevel = $user->verification_level ?? 'unverified';
        $thresholds = config('p2p_crypto_security.verification.verification_thresholds', []);

        $levelConfig = $thresholds[$verificationLevel] ?? $thresholds['level_1'] ?? ['daily_limit' => 1000];
        return $levelConfig['daily_limit'];
    }

    /**
     * Get user's remaining daily limit
     */
    private function getRemainingDailyLimit($user): float
    {
        $dailyLimit = $this->getVerificationLevelLimit($user);
        $dailyVolume = P2pCryptoOrder::where(function($query) use ($user) {
                $query->where('seller_id', $user->id)
                      ->orWhere('buyer_id', $user->id);
            })
            ->where('created_at', '>=', now()->startOfDay())
            ->sum('total_amount');

        return max(0, $dailyLimit - $dailyVolume);
    }

    /**
     * Get verification level based on verification type
     */
    private function getVerificationLevel($verificationType): string
    {
        switch ($verificationType) {
            case 'document':
                return 'level_2';
            case 'biometric_face':
            case 'biometric_fingerprint':
                return 'level_2';
            case 'video':
                return 'level_3';
            default:
                return 'level_1';
        }
    }

    /**
     * Add evidence to a dispute
     */
    public function addDisputeEvidence(Request $request, $disputeId): \Illuminate\Http\JsonResponse
    {
        $user = $request->user();

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'evidence' => 'required|string',
            'evidence_type' => 'required|in:image,document,transaction_proof,communication_log,other',
            'evidence_url' => 'nullable|url',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $dispute = \App\Models\P2pCryptoTradeDispute::where('id', $disputeId)
            ->where('initiator_user_id', $user->id)
            ->orWhere(function($query) use ($user) {
                $query->whereHas('p2pOrder', function($q) use ($user) {
                    $q->where('seller_id', $user->id)
                      ->orWhere('buyer_id', $user->id);
                });
            })
            ->first();

        if (!$dispute) {
            return response()->json([
                'success' => false,
                'message' => 'Dispute not found or unauthorized access'
            ], 404);
        }

        if ($dispute->status !== 'open') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot add evidence to a dispute that is not open'
            ], 400);
        }

        // Add evidence to the dispute's metadata
        $evidence = [
            'type' => $request->evidence_type,
            'content' => $request->evidence,
            'url' => $request->evidence_url,
            'added_by' => $user->id,
            'added_at' => now()->toISOString(),
        ];

        $currentEvidence = $dispute->evidence ?? [];
        $currentEvidence[] = $evidence;

        $dispute->update([
            'evidence' => $currentEvidence,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Evidence added successfully',
            'data' => $dispute->fresh()
        ]);
    }

    /**
     * Resolve a dispute (admin/super user only)
     */
    public function resolveDispute(Request $request, $disputeId): \Illuminate\Http\JsonResponse
    {
        $user = $request->user();

        // Only admin or super users can resolve disputes
        if (!$user->hasRole('admin') && !$user->hasRole('super_admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to resolve disputes'
            ], 403);
        }

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'resolution' => 'required|in:buyer_favor,seller_favor,cancel_order,other',
            'resolution_notes' => 'required|string',
            'refund_amount' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $dispute = \App\Models\P2pCryptoTradeDispute::findOrFail($disputeId);

        if ($dispute->status !== 'open') {
            return response()->json([
                'success' => false,
                'message' => 'Dispute is not open for resolution'
            ], 400);
        }

        $dispute->update([
            'status' => 'resolved',
            'resolution' => $request->resolution,
            'resolution_notes' => $request->resolution_notes,
            'resolved_at' => now(),
            'resolver_id' => $user->id,
        ]);

        // Update the associated order status based on resolution
        $order = $dispute->p2pOrder;
        if ($order) {
            $order->update(['status' => 'dispute_resolved']);
            // In a real implementation, we might release or refund the escrow based on resolution
        }

        return response()->json([
            'success' => true,
            'message' => 'Dispute resolved successfully',
            'data' => $dispute->fresh()
        ]);
    }

    /**
     * Admin: Get all open disputes
     */
    public function getOpenDisputes(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = $request->user();

        if (!$user->hasRole('admin') && !$user->hasRole('super_admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to view disputes'
            ], 403);
        }

        $disputes = \App\Models\P2pCryptoTradeDispute::with(['p2pOrder', 'initiator'])
            ->where('status', 'open')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $disputes
        ]);
    }

    /**
     * Admin: Escrow management (release, refund, etc.)
     */
    public function manageEscrow(Request $request, $orderId): \Illuminate\Http\JsonResponse
    {
        $user = $request->user();

        if (!$user->hasRole('admin') && !$user->hasRole('super_admin')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to manage escrow'
            ], 403);
        }

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'action' => 'required|in:release,refund,hold',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $order = P2pCryptoOrder::with('escrow')->findOrFail($orderId);

        if (!$order->escrow) {
            return response()->json([
                'success' => false,
                'message' => 'No escrow found for this order'
            ], 404);
        }

        switch ($request->action) {
            case 'release':
                $order->escrow->update([
                    'status' => 'released',
                    'released_at' => now(),
                    'release_notes' => $request->notes ?? 'Released by admin'
                ]);

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

                $order->update([
                    'crypto_transaction_id' => $cryptoTransaction->id
                ]);

                break;

            case 'refund':
                $order->escrow->update([
                    'status' => 'refunded',
                    'refunded_at' => now(),
                    'refund_notes' => $request->notes ?? 'Refunded by admin'
                ]);

                $order->update([
                    'status' => 'cancelled',
                    'cancelled_at' => now()
                ]);
                break;

            case 'hold':
                $order->escrow->update([
                    'status' => 'held',
                    'release_notes' => $request->notes ?? 'Held by admin'
                ]);

                $order->update([
                    'status' => 'on_hold'
                ]);
                break;
        }

        return response()->json([
            'success' => true,
            'message' => 'Escrow action completed successfully',
            'data' => $order->fresh()
        ]);
    }

    /**
     * Add a payment method
     */
    public function addPaymentMethod(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = $request->user();

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'payment_type' => 'required|string',
            'name' => 'required|string|max:255',
            'payment_details' => 'required|array',
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'bank_name' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Set as default if this is the first payment method
        $isDefault = \App\Models\P2pCryptoPaymentMethod::where('user_id', $user->id)->count() === 0;

        $paymentMethod = \App\Models\P2pCryptoPaymentMethod::create([
            'user_id' => $user->id,
            'payment_type' => $request->payment_type,
            'payment_provider' => $request->payment_type, // default to type
            'name' => $request->name,
            'payment_details' => $request->payment_details, // In production, encrypt this data
            'account_name' => $request->account_name,
            'account_number' => $request->account_number,
            'bank_name' => $request->bank_name,
            'country_code' => $request->country_code ?? 'US', // default country
            'is_default' => $isDefault,
            'is_verified' => false, // Needs verification
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Payment method added successfully',
            'data' => $paymentMethod
        ], 201);
    }

    /**
     * Get user verification status
     */
    public function getUserVerificationStatus(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = $request->user();

        $verification = \App\Models\P2pCryptoUserVerification::where('user_id', $user->id)
            ->latest()
            ->first();

        return response()->json([
            'success' => true,
            'data' => [
                'verification_level' => $user->verification_level,
                'verification_status' => $verification ? $verification->verification_status : 'unverified',
                'is_verified' => $user->is_verified,
                'reputation_score' => $user->reputation_score,
                'trade_completion_rate' => $user->trade_completion_rate,
                'total_trade_count' => $user->total_trade_count,
                'last_trade_at' => $user->last_trade_at,
                'is_trusted_seller' => $user->is_trusted_seller,
            ]
        ]);
    }

    /**
     * Get user's reputation and reviews
     */
    public function getUserReputation(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = $request->user();

        $reputation = \App\Models\P2pCryptoUserReputation::where('counterparty_id', $user->id)
            ->selectRaw('AVG(rating) as avg_rating, COUNT(*) as review_count, SUM(trade_count) as total_trades')
            ->first();

        $recentReviews = \App\Models\P2pCryptoUserReputation::where('counterparty_id', $user->id)
            ->with(['user:id,name,avatar']) // Only select necessary fields
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'average_rating' => $reputation->avg_rating ? round($reputation->avg_rating, 2) : 0,
                'review_count' => $reputation->review_count ?? 0,
                'total_trades' => $reputation->total_trades ?? 0,
                'recent_reviews' => $recentReviews
            ]
        ]);
    }
}