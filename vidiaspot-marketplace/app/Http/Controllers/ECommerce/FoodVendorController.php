<?php

namespace App\Http\Controllers;

use App\Services\FoodVendorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FoodVendorController extends Controller
{
    protected $foodVendorService;

    public function __construct(FoodVendorService $foodVendorService)
    {
        $this->foodVendorService = $foodVendorService;
    }

    /**
     * Get all active food vendors
     */
    public function index(Request $request)
    {
        $cuisineType = $request->get('cuisine');
        $latitude = $request->get('lat');
        $longitude = $request->get('lng');
        $radius = $request->get('radius', 10); // default 10km

        $vendors = $this->foodVendorService->getActiveVendors($cuisineType, $latitude, $longitude, $radius);

        return response()->json([
            'success' => true,
            'vendors' => $vendors
        ]);
    }

    /**
     * Get a specific vendor and its menu
     */
    public function show($vendorId)
    {
        $vendor = $this->foodVendorService->getVendorWithMenu($vendorId);

        return response()->json([
            'success' => true,
            'vendor' => $vendor
        ]);
    }

    /**
     * Search vendors
     */
    public function search(Request $request)
    {
        $searchTerm = $request->get('q');
        $cuisineType = $request->get('cuisine');
        $latitude = $request->get('lat');
        $longitude = $request->get('lng');

        $vendors = $this->foodVendorService->searchVendors($searchTerm, $latitude, $longitude);

        if ($cuisineType) {
            $vendors = $vendors->filter(function ($vendor) use ($cuisineType) {
                return stripos($vendor->cuisine_type, $cuisineType) !== false;
            });
        }

        return response()->json([
            'success' => true,
            'vendors' => $vendors
        ]);
    }

    /**
     * Get vendors by cuisine type
     */
    public function byCuisine($cuisineType)
    {
        $vendors = $this->foodVendorService->getVendorsByCuisine($cuisineType);

        return response()->json([
            'success' => true,
            'vendors' => $vendors,
            'cuisine_type' => $cuisineType
        ]);
    }

    /**
     * Get menu for a vendor
     */
    public function getMenu($vendorId)
    {
        $vendor = $this->foodVendorService->getVendorWithMenu($vendorId);

        return response()->json([
            'success' => true,
            'vendor' => [
                'id' => $vendor->id,
                'name' => $vendor->name,
                'description' => $vendor->description,
                'cuisine_type' => $vendor->cuisine_type,
                'image_url' => $vendor->image_url,
                'rating' => $vendor->rating,
                'total_reviews' => $vendor->total_reviews,
                'delivery_fee' => $vendor->delivery_fee,
                'estimated_delivery_time' => $vendor->estimated_delivery_time,
                'min_order_amount' => $vendor->min_order_amount,
                'opening_time' => $vendor->opening_time,
                'closing_time' => $vendor->closing_time,
                'contact_phone' => $vendor->contact_phone,
            ],
            'menu_items' => $vendor->menuItems
        ]);
    }

    /**
     * Get menu items by category
     */
    public function getMenuByCategory($vendorId, $category)
    {
        $items = $this->foodVendorService->getMenuItemsByCategory($vendorId, $category);

        return response()->json([
            'success' => true,
            'category' => $category,
            'items' => $items
        ]);
    }

    /**
     * Get popular menu items
     */
    public function getPopularMenuItems($vendorId)
    {
        $items = $this->foodVendorService->getPopularMenuItems($vendorId);

        return response()->json([
            'success' => true,
            'items' => $items,
            'message' => 'Popular items'
        ]);
    }

    /**
     * Get new menu items
     */
    public function getNewMenuItems($vendorId)
    {
        $items = $this->foodVendorService->getNewMenuItems($vendorId);

        return response()->json([
            'success' => true,
            'items' => $items,
            'message' => 'New items'
        ]);
    }

    /**
     * Get menu items by dietary option
     */
    public function getMenuByDietary($vendorId, $dietaryOption)
    {
        $items = $this->foodVendorService->getMenuItemsByDietary($vendorId, $dietaryOption);

        return response()->json([
            'success' => true,
            'dietary_option' => $dietaryOption,
            'items' => $items
        ]);
    }

    /**
     * Place an order
     */
    public function placeOrder(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User must be logged in to place an order'
            ], 401);
        }

        $request->validate([
            'vendor_id' => 'required|exists:food_vendors,id',
            'customer_email' => 'required|email',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'delivery_address' => 'required|array',
            'delivery_address.street' => 'required|string',
            'delivery_address.city' => 'required|string',
            'delivery_address.state' => 'required|string',
            'delivery_address.country' => 'required|string',
            'delivery_address.postal_code' => 'required|string',
            'order_items' => 'required|array|min:1',
            'order_items.*.menu_item_id' => 'required|exists:food_menu_items,id',
            'order_items.*.name' => 'required|string',
            'order_items.*.price' => 'required|numeric|min:0',
            'order_items.*.quantity' => 'required|integer|min:1',
            'payment_method' => 'required|string',
            'delivery_fee' => 'required|numeric|min:0',
            'order_type' => 'required|in:delivery,pickup',
            'special_instructions' => 'nullable|string',
            'tip_amount' => 'nullable|numeric|min:0',
        ]);

        try {
            $order = $this->foodVendorService->placeOrder($user->id, $request->all());

            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully',
                'order' => $order,
                'order_number' => $order->order_number
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to place order: ' . $e->getMessage()
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

        $orders = $this->foodVendorService->getUserOrderHistory($user->id);

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

        $order = $this->foodVendorService->getOrderByNumber($orderNumber);

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
     * Get vendor's orders (for vendors to see their orders)
     */
    public function getVendorOrders(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User must be logged in'
            ], 401);
        }

        // Find the vendor associated with this user
        $vendor = $user->foodVendor; // Assuming there's a relationship
        if (!$vendor) {
            return response()->json([
                'success' => false,
                'message' => 'No vendor account found for this user'
            ], 404);
        }

        $status = $request->get('status');
        $orders = $this->foodVendorService->getVendorOrders($vendor->id, $status);

        return response()->json([
            'success' => true,
            'orders' => $orders
        ]);
    }

    /**
     * Update order status (for vendors)
     */
    public function updateOrderStatus(Request $request, $orderNumber)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User must be logged in'
            ], 401);
        }

        $request->validate([
            'status' => 'required|string|in:pending,confirmed,preparing,ready,out_for_delivery,delivered,cancelled',
            'delivery_status' => 'nullable|string|in:pending,assigned,picked_up,on_the_way,delivered'
        ]);

        $order = $this->foodVendorService->updateOrderStatus($orderNumber, $request->status, $request->delivery_status);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Order status updated successfully',
            'order' => $order
        ]);
    }

    /**
     * Get vendor statistics
     */
    public function getVendorStats($vendorId)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User must be logged in'
            ], 401);
        }

        $vendor = $user->foodVendor; // This assumes a user can only have one vendor
        if (!$vendor || $vendor->id != $vendorId) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized or vendor not found'
            ], 403);
        }

        $stats = $this->foodVendorService->getVendorStats($vendorId);

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }
}
