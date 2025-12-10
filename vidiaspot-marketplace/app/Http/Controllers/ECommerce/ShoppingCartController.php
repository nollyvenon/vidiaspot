<?php

namespace App\Http\Controllers;

use App\Services\ShoppingCartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShoppingCartController extends Controller
{
    protected $shoppingCartService;

    public function __construct(ShoppingCartService $shoppingCartService)
    {
        $this->shoppingCartService = $shoppingCartService;
    }

    /**
     * Add item to cart
     */
    public function addToCart(Request $request)
    {
        $request->validate([
            'ad_id' => 'required|exists:ads,id',
            'quantity' => 'required|integer|min:1',
            'selected_options' => 'array',
        ]);

        $user = Auth::user();
        $sessionId = session()->getId();

        try {
            $cartItem = $this->shoppingCartService->addToCart(
                $user?->id,
                $sessionId,
                $request->ad_id,
                $request->quantity,
                $request->selected_options ?? []
            );

            return response()->json([
                'success' => true,
                'message' => 'Item added to cart successfully',
                'cart_item' => $cartItem,
                'cart_count' => $this->getCartCount($user?->id, $sessionId)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add item to cart: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get cart items
     */
    public function getCart()
    {
        $user = Auth::user();
        $sessionId = session()->getId();

        $cartItems = $this->shoppingCartService->getCartItems($user?->id, $sessionId);
        $cartTotal = $this->shoppingCartService->getCartTotal($user?->id, $sessionId);

        return response()->json([
            'success' => true,
            'cart_items' => $cartItems,
            'cart_total' => $cartTotal,
            'cart_count' => $cartTotal['quantity']
        ]);
    }

    /**
     * Update cart item quantity
     */
    public function updateCartItem(Request $request, $adId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:0',
        ]);

        $user = Auth::user();
        $sessionId = session()->getId();

        try {
            $cartItem = $this->shoppingCartService->updateCartItem(
                $user?->id,
                $sessionId,
                $adId,
                $request->quantity
            );

            $cartTotal = $this->shoppingCartService->getCartTotal($user?->id, $sessionId);

            return response()->json([
                'success' => true,
                'message' => 'Cart updated successfully',
                'cart_item' => $cartItem,
                'cart_total' => $cartTotal,
                'cart_count' => $cartTotal['quantity']
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update cart: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Remove item from cart
     */
    public function removeFromCart($adId)
    {
        $user = Auth::user();
        $sessionId = session()->getId();

        $success = $this->shoppingCartService->removeFromCart($user?->id, $sessionId, $adId);

        $cartTotal = $this->shoppingCartService->getCartTotal($user?->id, $sessionId);

        if ($success) {
            return response()->json([
                'success' => true,
                'message' => 'Item removed from cart',
                'cart_total' => $cartTotal,
                'cart_count' => $cartTotal['quantity']
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Item not found in cart'
            ], 404);
        }
    }

    /**
     * Clear cart
     */
    public function clearCart()
    {
        $user = Auth::user();
        $sessionId = session()->getId();

        $deletedCount = $this->shoppingCartService->clearCart($user?->id, $sessionId);

        return response()->json([
            'success' => true,
            'message' => 'Cart cleared',
            'deleted_items' => $deletedCount
        ]);
    }

    /**
     * Create order from cart
     */
    public function createOrder(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User must be logged in to place an order'
            ], 401);
        }

        $request->validate([
            'customer_email' => 'required|email',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'shipping_address' => 'required|array',
            'shipping_address.address' => 'required|string',
            'shipping_address.city' => 'required|string',
            'shipping_address.state' => 'required|string',
            'shipping_address.country' => 'required|string',
            'shipping_address.postal_code' => 'required|string',
            'payment_method' => 'required|string',
        ]);

        try {
            $order = $this->shoppingCartService->createOrderFromCart($user->id, $request->all());

            return response()->json([
                'success' => true,
                'message' => 'Order created successfully',
                'order' => $order,
                'order_number' => $order->order_number
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create order: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get user's order history
     */
    public function getOrderHistory()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User must be logged in'
            ], 401);
        }

        $orders = $this->shoppingCartService->getUserOrders($user->id);

        return response()->json([
            'success' => true,
            'orders' => $orders
        ]);
    }

    /**
     * Get specific order
     */
    public function getOrder($orderNumber)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User must be logged in'
            ], 401);
        }

        $order = $this->shoppingCartService->getOrderByNumber($orderNumber);

        if (!$order || $order->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found or unauthorized'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'order' => $order
        ]);
    }

    /**
     * Get cart count helper
     */
    private function getCartCount($userId, $sessionId)
    {
        $total = $this->shoppingCartService->getCartTotal($userId, $sessionId);
        return $total['quantity'];
    }
}
