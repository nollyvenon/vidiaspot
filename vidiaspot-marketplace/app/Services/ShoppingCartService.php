<?php

namespace App\Services;

use App\Models\ShoppingCart;
use App\Models\Ad;
use App\Models\Order;
use Illuminate\Support\Str;

class ShoppingCartService
{
    /**
     * Add item to cart
     */
    public function addToCart($userId, $sessionId, $adId, $quantity = 1, $selectedOptions = [])
    {
        $ad = Ad::findOrFail($adId);

        // Check if item already exists in cart
        $cartItem = ShoppingCart::where(function ($query) use ($userId, $sessionId, $adId) {
            if ($userId) {
                $query->where('user_id', $userId);
            } else {
                $query->where('session_id', $sessionId);
            }
            $query->where('ad_id', $adId);
        })->first();

        if ($cartItem) {
            // Update existing item
            $newQuantity = $cartItem->quantity + $quantity;
            $cartItem->update([
                'quantity' => $newQuantity,
                'total_price' => $ad->price * $newQuantity,
                'selected_options' => array_merge($cartItem->selected_options ?? [], $selectedOptions)
            ]);
        } else {
            // Create new cart item
            $cartItem = ShoppingCart::create([
                'user_id' => $userId,
                'session_id' => $sessionId,
                'ad_id' => $adId,
                'quantity' => $quantity,
                'price' => $ad->price,
                'total_price' => $ad->price * $quantity,
                'selected_options' => $selectedOptions,
            ]);
        }

        return $cartItem;
    }

    /**
     * Update cart item quantity
     */
    public function updateCartItem($userId, $sessionId, $adId, $quantity)
    {
        $cartItem = ShoppingCart::where(function ($query) use ($userId, $sessionId, $adId) {
            if ($userId) {
                $query->where('user_id', $userId);
            } else {
                $query->where('session_id', $sessionId);
            }
            $query->where('ad_id', $adId);
        })->first();

        if (!$cartItem) {
            return null;
        }

        if ($quantity <= 0) {
            $cartItem->delete();
            return null;
        }

        $ad = Ad::findOrFail($adId);
        $cartItem->update([
            'quantity' => $quantity,
            'total_price' => $ad->price * $quantity,
        ]);

        return $cartItem;
    }

    /**
     * Remove item from cart
     */
    public function removeFromCart($userId, $sessionId, $adId)
    {
        $cartItem = ShoppingCart::where(function ($query) use ($userId, $sessionId, $adId) {
            if ($userId) {
                $query->where('user_id', $userId);
            } else {
                $query->where('session_id', $sessionId);
            }
            $query->where('ad_id', $adId);
        })->first();

        if ($cartItem) {
            $cartItem->delete();
            return true;
        }

        return false;
    }

    /**
     * Get cart items for user/session
     */
    public function getCartItems($userId = null, $sessionId = null)
    {
        $query = ShoppingCart::with(['ad', 'ad.user', 'ad.images']);

        if ($userId) {
            $query = $query->where('user_id', $userId);
        } else {
            $query = $query->where('session_id', $sessionId);
        }

        return $query->get();
    }

    /**
     * Get cart total
     */
    public function getCartTotal($userId = null, $sessionId = null)
    {
        $query = ShoppingCart::selectRaw('SUM(total_price) as total, SUM(quantity) as quantity');

        if ($userId) {
            $query = $query->where('user_id', $userId);
        } else {
            $query = $query->where('session_id', $sessionId);
        }

        $result = $query->first();

        return [
            'total' => $result->total ?? 0,
            'quantity' => $result->quantity ?? 0,
        ];
    }

    /**
     * Clear cart
     */
    public function clearCart($userId = null, $sessionId = null)
    {
        $query = ShoppingCart::query();

        if ($userId) {
            $query = $query->where('user_id', $userId);
        } else {
            $query = $query->where('session_id', $sessionId);
        }

        return $query->delete();
    }

    /**
     * Create order from cart
     */
    public function createOrderFromCart($userId, $orderData)
    {
        $cartItems = $this->getCartItems($userId);
        
        if ($cartItems->isEmpty()) {
            throw new \Exception('Cart is empty');
        }

        // Calculate total
        $totalAmount = $cartItems->sum('total_price');
        
        // Extract vendor store ID from first item (assuming all items are from same store for simplicity)
        $vendorStoreId = $cartItems->first()->ad->user->vendorStore->id ?? null;

        // Create order number
        $orderNumber = 'ORD-' . date('Ymd') . '-' . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);

        // Prepare order items
        $orderItems = [];
        foreach ($cartItems as $item) {
            $orderItems[] = [
                'ad_id' => $item->ad_id,
                'title' => $item->ad->title,
                'price' => $item->price,
                'quantity' => $item->quantity,
                'total_price' => $item->total_price,
                'selected_options' => $item->selected_options,
            ];
        }

        // Create order
        $order = Order::create([
            'user_id' => $userId,
            'vendor_store_id' => $vendorStoreId,
            'order_number' => $orderNumber,
            'total_amount' => $totalAmount,
            'quantity' => $cartItems->sum('quantity'),
            'customer_email' => $orderData['customer_email'],
            'customer_name' => $orderData['customer_name'],
            'customer_phone' => $orderData['customer_phone'] ?? null,
            'shipping_address' => $orderData['shipping_address'] ?? null,
            'billing_address' => $orderData['billing_address'] ?? $orderData['shipping_address'] ?? null,
            'payment_method' => $orderData['payment_method'] ?? null,
            'order_items' => $orderItems,
            'status' => 'pending',
            'payment_status' => 'pending',
            'fulfillment_status' => 'pending',
        ]);

        // Clear the cart after order creation
        $this->clearCart($userId);

        return $order;
    }

    /**
     * Get user's order history
     */
    public function getUserOrders($userId)
    {
        return Order::where('user_id', $userId)
                    ->orderBy('created_at', 'desc')
                    ->get();
    }

    /**
     * Get order by number
     */
    public function getOrderByNumber($orderNumber)
    {
        return Order::where('order_number', $orderNumber)->first();
    }

    /**
     * Update order status
     */
    public function updateOrderStatus($orderNumber, $status, $fulfillmentStatus = null)
    {
        $order = Order::where('order_number', $orderNumber)->first();
        
        if (!$order) {
            return null;
        }

        $updateData = ['status' => $status];
        if ($fulfillmentStatus) {
            $updateData['fulfillment_status'] = $fulfillmentStatus;
        }

        $order->update($updateData);

        return $order;
    }
}