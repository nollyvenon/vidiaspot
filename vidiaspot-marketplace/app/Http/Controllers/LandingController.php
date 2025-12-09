<?php

namespace App\Http\Controllers;

use App\Models\CryptoCurrency;
use App\Models\P2pCryptoOrder;
use App\Models\Ad;
use App\Models\Category;
use App\Models\Blog;
use App\Models\Testimonial;
use App\Models\ContentPage;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class LandingController extends Controller
{
    /**
     * Display the landing page data
     */
    public function index(Request $request): JsonResponse
    {
        // Get featured crypto currencies for P2P marketplace
        $featuredCryptoCurrencies = CryptoCurrency::where('is_active', true)
            ->orderBy('market_cap', 'desc')
            ->limit(5)
            ->get(['id', 'name', 'symbol', 'price', 'logo_url']);

        // Get latest P2P crypto orders
        $latestP2pOrders = P2pCryptoOrder::with(['cryptoCurrency', 'seller'])
            ->whereIn('status', ['active', 'matched'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get featured ads for other marketplace features
        $featuredAds = Ad::with(['user', 'category'])
            ->where('is_featured', true)
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get();

        // Get categories for the marketplace
        $categories = Category::where('is_active', true)
            ->withCount('ads')
            ->orderBy('name')
            ->limit(10)
            ->get();

        // Get latest blog posts
        $latestBlogs = Blog::where('status', 'published')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get(['id', 'title', 'excerpt', 'created_at', 'author']);

        // Get testimonials
        $testimonials = Testimonial::where('is_approved', true)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get about us content if available
        $aboutPage = ContentPage::where('slug', 'about-us')->first();

        return response()->json([
            'success' => true,
            'data' => [
                'featured_crypto_currencies' => $featuredCryptoCurrencies,
                'latest_p2p_orders' => $latestP2pOrders,
                'featured_ads' => $featuredAds,
                'categories' => $categories,
                'latest_blogs' => $latestBlogs,
                'testimonials' => $testimonials,
                'about_content' => $aboutPage ? $aboutPage->content : null,
            ]
        ]);
    }

    /**
     * Display information about the P2P crypto marketplace
     */
    public function p2pCryptoMarketplace(): JsonResponse
    {
        // Get stats for P2P crypto marketplace
        $totalActiveOrders = P2pCryptoOrder::where('status', 'active')->count();
        $totalCompletedOrders = P2pCryptoOrder::where('status', 'completed')->count();
        $totalTradeVolume = P2pCryptoOrder::where('status', 'completed')
            ->sum('total_amount');

        // Get top crypto currencies for P2P trading
        $topCryptoCurrencies = CryptoCurrency::where('is_active', true)
            ->orderBy('market_cap', 'desc')
            ->limit(10)
            ->get(['id', 'name', 'symbol', 'price', 'logo_url']);

        // Get latest P2P orders
        $recentOrders = P2pCryptoOrder::with(['cryptoCurrency', 'seller', 'buyer'])
            ->whereIn('status', ['active', 'matched', 'completed'])
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'marketplace_stats' => [
                    'total_active_orders' => $totalActiveOrders,
                    'total_completed_orders' => $totalCompletedOrders,
                    'total_trade_volume' => $totalTradeVolume,
                ],
                'top_crypto_currencies' => $topCryptoCurrencies,
                'recent_orders' => $recentOrders,
            ],
            'message' => 'P2P Crypto Marketplace information retrieved successfully'
        ]);
    }

    /**
     * Display information about all marketplace modules
     */
    public function marketplaceModules(): JsonResponse
    {
        $modules = [
            [
                'name' => 'P2P Crypto Marketplace',
                'description' => 'Peer-to-peer cryptocurrency trading platform with escrow protection',
                'features' => [
                    'Secure escrow system',
                    'Multi-cryptocurrency support',
                    'Dispute resolution',
                    'Real-time trading',
                    'Advanced order matching'
                ],
                'status' => 'active',
                'api_endpoint' => '/api/p2p-crypto'
            ],
            [
                'name' => 'Shopify Clone',
                'description' => 'Complete e-commerce solution with inventory management',
                'features' => [
                    'Product management',
                    'Inventory tracking',
                    'Order processing',
                    'Payment integration',
                    'Customer management'
                ],
                'status' => 'planned',
                'api_endpoint' => '/api/ecommerce'
            ],
            [
                'name' => 'Food Vending',
                'description' => 'Food ordering and delivery system',
                'features' => [
                    'Restaurant listings',
                    'Menu management',
                    'Order tracking',
                    'Delivery coordination',
                    'Review system'
                ],
                'status' => 'planned',
                'api_endpoint' => '/api/food'
            ],
            [
                'name' => 'Logistics',
                'description' => 'Supply chain and delivery management platform',
                'features' => [
                    'Shipment tracking',
                    'Route optimization',
                    'Inventory management',
                    'Courier management',
                    'Real-time updates'
                ],
                'status' => 'planned',
                'api_endpoint' => '/api/logistics'
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'modules' => $modules,
            ],
            'message' => 'Marketplace modules information retrieved successfully'
        ]);
    }
}