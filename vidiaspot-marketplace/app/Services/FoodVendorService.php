<?php

namespace App\Services;

use App\Models\FoodVendor;
use App\Models\FoodMenuItem;
use App\Models\FoodOrder;
use App\Models\FoodOrderItem;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class FoodVendorService
{
    /**
     * Get all active food vendors
     */
    public function getActiveVendors($cuisineType = null, $latitude = null, $longitude = null, $radius = 10)
    {
        $query = FoodVendor::where('is_active', true)
                          ->where('accepting_orders', true);

        if ($cuisineType) {
            $query = $query->where('cuisine_type', $cuisineType);
        }

        if ($latitude && $longitude) {
            $query = $query->byLocation($latitude, $longitude, $radius);
        }

        return $query->orderBy('rating', 'desc')
                     ->orderBy('total_reviews', 'desc')
                     ->get();
    }

    /**
     * Get a specific vendor and its menu
     */
    public function getVendorWithMenu($vendorId)
    {
        return FoodVendor::with(['menuItems' => function($query) {
            $query->where('is_available', true)->orderBy('category');
        }])->findOrFail($vendorId);
    }

    /**
     * Get vendors by cuisine type
     */
    public function getVendorsByCuisine($cuisineType)
    {
        return FoodVendor::active()
                         ->byCuisine($cuisineType)
                         ->orderBy('rating', 'desc')
                         ->get();
    }

    /**
     * Search vendors by name or cuisine
     */
    public function searchVendors($searchTerm, $latitude = null, $longitude = null)
    {
        $query = FoodVendor::where('is_active', true)
                          ->where('accepting_orders', true);

        if ($searchTerm) {
            $query = $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('cuisine_type', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%");
            });
        }

        if ($latitude && $longitude) {
            $query = $query->byLocation($latitude, $longitude, 15); // 15km radius for search
        }

        return $query->orderBy('rating', 'desc')
                     ->orderBy('total_reviews', 'desc')
                     ->get();
    }

    /**
     * Create a new food vendor
     */
    public function createVendor($userId, $vendorData)
    {
        $vendorData['user_id'] = $userId;
        $vendorData['rating'] = 0.00;
        $vendorData['total_reviews'] = 0;

        return FoodVendor::create($vendorData);
    }

    /**
     * Update vendor information
     */
    public function updateVendor($vendorId, $vendorData)
    {
        $vendor = FoodVendor::findOrFail($vendorId);
        $vendor->update($vendorData);
        return $vendor;
    }

    /**
     * Add menu item to vendor
     */
    public function addMenuItem($vendorId, $itemData)
    {
        $itemData['food_vendor_id'] = $vendorId;
        return FoodMenuItem::create($itemData);
    }

    /**
     * Update menu item
     */
    public function updateMenuItem($itemId, $itemData)
    {
        $item = FoodMenuItem::findOrFail($itemId);
        $item->update($itemData);
        return $item;
    }

    /**
     * Place a food order
     */
    public function placeOrder($userId, $orderData)
    {
        // Validate menu items exist and are available
        foreach ($orderData['order_items'] as $item) {
            $menuItem = FoodMenuItem::findOrFail($item['menu_item_id']);
            if (!$menuItem->is_available) {
                throw new \Exception("Item {$menuItem->name} is not available");
            }
            if ($item['quantity'] > $menuItem->max_quantity_per_order) {
                throw new \Exception("Quantity exceeds maximum allowed for {$menuItem->name}");
            }
        }

        // Calculate total amount
        $totalAmount = 0;
        foreach ($orderData['order_items'] as $item) {
            $menuItem = FoodMenuItem::findOrFail($item['menu_item_id']);
            $itemTotal = $menuItem->price * $item['quantity'];
            $totalAmount += $itemTotal;
        }

        // Add additional fees
        $totalAmount += $orderData['delivery_fee'] ?? 0;
        $totalAmount += $orderData['packaging_fee'] ?? 0;
        $totalAmount += $orderData['service_fee'] ?? 0;
        $totalAmount += $orderData['tip_amount'] ?? 0;

        // Calculate tax
        $taxAmount = ($totalAmount - ($orderData['delivery_fee'] ?? 0)) * ($orderData['tax_percentage'] ?? 0) / 100;

        // Create order number
        $orderNumber = 'FOD-' . date('Ymd') . '-' . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);

        // Create the order
        $order = FoodOrder::create([
            'user_id' => $userId,
            'food_vendor_id' => $orderData['vendor_id'],
            'order_number' => $orderNumber,
            'total_amount' => $totalAmount + $taxAmount,
            'quantity' => collect($orderData['order_items'])->sum('quantity'),
            'customer_email' => $orderData['customer_email'],
            'customer_name' => $orderData['customer_name'],
            'customer_phone' => $orderData['customer_phone'] ?? null,
            'delivery_address' => $orderData['delivery_address'],
            'delivery_instructions' => $orderData['delivery_instructions'] ?? null,
            'payment_method' => $orderData['payment_method'] ?? null,
            'delivery_fee' => $orderData['delivery_fee'] ?? 0,
            'tax_amount' => $taxAmount,
            'tip_amount' => $orderData['tip_amount'] ?? 0,
            'packaging_fee' => $orderData['packaging_fee'] ?? 0,
            'service_fee' => $orderData['service_fee'] ?? 0,
            'notes' => $orderData['notes'] ?? null,
            'order_items' => $orderData['order_items'],
            'order_type' => $orderData['order_type'] ?? 'delivery',
            'scheduled_time' => $orderData['scheduled_time'] ?? null,
            'special_instructions' => $orderData['special_instructions'] ?? null,
        ]);

        // Create order items
        foreach ($orderData['order_items'] as $item) {
            FoodOrderItem::create([
                'food_order_id' => $order->id,
                'food_menu_item_id' => $item['menu_item_id'],
                'name' => $item['name'],
                'price' => $item['price'],
                'quantity' => $item['quantity'],
                'total_price' => $item['price'] * $item['quantity'],
                'special_instructions' => $item['special_instructions'] ?? null,
                'customization_options' => $item['customization_options'] ?? [],
                'item_addons' => $item['item_addons'] ?? [],
            ]);
        }

        return $order;
    }

    /**
     * Get user's order history
     */
    public function getUserOrderHistory($userId)
    {
        return FoodOrder::where('user_id', $userId)
                        ->orderBy('created_at', 'desc')
                        ->get();
    }

    /**
     * Get vendor's order list
     */
    public function getVendorOrders($vendorId, $status = null)
    {
        $query = FoodOrder::where('food_vendor_id', $vendorId);
        
        if ($status) {
            $query = $query->where('status', $status);
        }
        
        return $query->orderBy('created_at', 'desc')
                     ->get();
    }

    /**
     * Update order status
     */
    public function updateOrderStatus($orderNumber, $status, $deliveryStatus = null)
    {
        $order = FoodOrder::where('order_number', $orderNumber)->first();
        
        if (!$order) {
            return null;
        }

        $updateData = ['status' => $status];
        if ($deliveryStatus) {
            $updateData['delivery_status'] = $deliveryStatus;
        }

        $order->update($updateData);

        return $order;
    }

    /**
     * Get order by number
     */
    public function getOrderByNumber($orderNumber)
    {
        return FoodOrder::where('order_number', $orderNumber)->first();
    }

    /**
     * Get menu items by category
     */
    public function getMenuItemsByCategory($vendorId, $category)
    {
        return FoodMenuItem::where('food_vendor_id', $vendorId)
                          ->where('category', $category)
                          ->where('is_available', true)
                          ->orderBy('name')
                          ->get();
    }

    /**
     * Get popular menu items
     */
    public function getPopularMenuItems($vendorId, $limit = 10)
    {
        return FoodMenuItem::where('food_vendor_id', $vendorId)
                          ->where('is_popular', true)
                          ->where('is_available', true)
                          ->orderBy('name')
                          ->limit($limit)
                          ->get();
    }

    /**
     * Get new menu items
     */
    public function getNewMenuItems($vendorId, $limit = 10)
    {
        return FoodMenuItem::where('food_vendor_id', $vendorId)
                          ->where('is_new', true)
                          ->where('is_available', true)
                          ->orderBy('created_at', 'desc')
                          ->limit($limit)
                          ->get();
    }

    /**
     * Get menu items by dietary options
     */
    public function getMenuItemsByDietary($vendorId, $dietaryOption)
    {
        return FoodMenuItem::where('food_vendor_id', $vendorId)
                          ->whereJsonContains('dietary_options', $dietaryOption)
                          ->where('is_available', true)
                          ->orderBy('category')
                          ->get();
    }

    /**
     * Get vendor statistics
     */
    public function getVendorStats($vendorId)
    {
        $vendor = FoodVendor::findOrFail($vendorId);
        
        $totalOrders = FoodOrder::where('food_vendor_id', $vendorId)
                                ->count();
        
        $totalRevenue = FoodOrder::where('food_vendor_id', $vendorId)
                                 ->where('status', 'delivered')
                                 ->sum('total_amount');
        
        $avgOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;
        
        return [
            'vendor' => $vendor,
            'total_orders' => $totalOrders,
            'total_revenue' => $totalRevenue,
            'avg_order_value' => $avgOrderValue,
            'rating' => $vendor->rating,
            'total_reviews' => $vendor->total_reviews,
        ];
    }
}