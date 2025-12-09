<?php

namespace App\Http\Controllers;

use App\Services\CryptoP2PService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CryptoP2PController extends Controller
{
    protected $cryptoP2PService;

    public function __construct(CryptoP2PService $cryptoP2PService)
    {
        $this->cryptoP2PService = $cryptoP2PService;
    }

    /**
     * Display the main P2P crypto marketplace page
     */
    public function index(Request $request)
    {
        $filters = [
            'crypto_currency' => $request->crypto_currency,
            'fiat_currency' => $request->fiat_currency ?? 'NGN',
            'trade_type' => $request->trade_type,
            'search' => $request->search,
            'per_page' => $request->per_page ?? 12,
        ];

        $listings = $this->cryptoP2PService->getActiveListings($filters);

        return view('crypto-p2p.index', compact('listings'));
    }

    /**
     * Show create listing form
     */
    public function createListing()
    {
        $supportedCurrencies = $this->cryptoP2PService->getSupportedCryptocurrencies();
        
        return view('crypto-p2p.create-listing', compact('supportedCurrencies'));
    }

    /**
     * Store a new crypto listing
     */
    public function storeListing(Request $request)
    {
        $request->validate([
            'crypto_currency' => 'required|string|max:10',
            'fiat_currency' => 'required|string|max:10',
            'trade_type' => 'required|in:buy,sell',
            'price_per_unit' => 'required|numeric|min:0.00000001',
            'min_trade_amount' => 'required|numeric|min:0',
            'max_trade_amount' => 'required|numeric|min:0|gte:min_trade_amount',
            'available_amount' => 'required_if:trade_type,sell|numeric|min:0',
            'payment_methods' => 'required|array|min:1',
            'payment_methods.*' => 'string|in:bank_transfer,mobile_money,cash,online_wallet',
            'trading_fee_percent' => 'nullable|numeric|min:0|max:100',
            'trading_fee_fixed' => 'nullable|numeric|min:0',
            'negotiable' => 'boolean',
            'auto_accept' => 'boolean',
            'verification_level_required' => 'integer|min:1|max:3',
            'trade_security_level' => 'integer|min:1|max:3',
            'is_public' => 'boolean',
        ]);

        try {
            $listingData = $request->only([
                'crypto_currency',
                'fiat_currency',
                'trade_type',
                'price_per_unit',
                'min_trade_amount',
                'max_trade_amount',
                'available_amount',
                'payment_methods',
                'trading_fee_percent',
                'trading_fee_fixed',
                'trading_terms',
                'negotiable',
                'auto_accept',
                'auto_release_time_hours',
                'verification_level_required',
                'trade_security_level',
                'is_public',
                'featured',
                'location',
                'location_radius',
                'metadata',
            ]);

            $listing = $this->cryptoP2PService->createListing($listingData);

            return response()->json([
                'success' => true,
                'message' => 'Listing created successfully',
                'listing' => $listing
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Show a specific listing
     */
    public function showListing($id)
    {
        $listing = $this->cryptoP2PService->getListing($id);
        
        if (!$listing) {
            abort(404);
        }

        return view('crypto-p2p.show-listing', compact('listing'));
    }

    /**
     * Show initiate trade form for a listing
     */
    public function initiateTrade($listingId)
    {
        $listing = $this->cryptoP2PService->getListing($listingId);
        
        if (!$listing) {
            abort(404);
        }

        return view('crypto-p2p.initiate-trade', compact('listing'));
    }

    /**
     * Store a new trade for a listing
     */
    public function storeTrade(Request $request, $listingId)
    {
        $request->validate([
            'crypto_amount' => 'required|numeric|min:0.00000001',
            'payment_method' => 'required|string',
            'payment_details' => 'array',
        ]);

        try {
            $tradeData = $request->only([
                'crypto_amount', 
                'payment_method', 
                'payment_details'
            ]);

            $trade = $this->cryptoP2PService->initiateTrade($listingId, $tradeData);

            return response()->json([
                'success' => true,
                'message' => 'Trade initiated successfully',
                'trade' => $trade,
                'redirect_url' => route('crypto-p2p.trade.show', $trade->id)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Show a specific trade
     */
    public function showTrade($id)
    {
        $trade = $this->cryptoP2PService->getTrade($id);
        
        if (!$trade || ($trade->buyer_id !== Auth::id() && $trade->seller_id !== Auth::id())) {
            abort(404);
        }

        return view('crypto-p2p.show-trade', compact('trade'));
    }

    /**
     * Confirm payment for a trade
     */
    public function confirmPayment($tradeId)
    {
        try {
            $trade = $this->cryptoP2PService->confirmPayment($tradeId);

            return response()->json([
                'success' => true,
                'message' => 'Payment confirmed successfully',
                'trade' => $trade
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Release crypto for a trade (only seller can do this)
     */
    public function releaseCrypto($tradeId)
    {
        try {
            $trade = $this->cryptoP2PService->releaseCrypto($tradeId);

            return response()->json([
                'success' => true,
                'message' => 'Crypto released successfully',
                'trade' => $trade
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get user's listings
     */
    public function getUserListings()
    {
        $listings = $this->cryptoP2PService->getUserListings();
        
        return view('crypto-p2p.user-listings', compact('listings'));
    }

    /**
     * Get user's trades
     */
    public function getUserTrades(Request $request)
    {
        $filters = [
            'status' => $request->status,
            'trade_type' => $request->trade_type,
            'crypto_currency' => $request->crypto_currency,
            'per_page' => $request->per_page ?? 10,
        ];

        $trades = $this->cryptoP2PService->getUserTrades(null, $filters);
        
        return view('crypto-p2p.user-trades', compact('trades'));
    }

    /**
     * Get matching listings for a trade
     */
    public function getMatchingListings(Request $request)
    {
        $request->validate([
            'crypto_currency' => 'required|string',
            'fiat_currency' => 'required|string',
            'trade_type' => 'required|in:buy,sell',
            'amount' => 'required|numeric|min:0.00000001',
        ]);

        $listings = $this->cryptoP2PService->getMatchingListings(
            $request->crypto_currency,
            $request->fiat_currency,
            $request->trade_type,
            $request->amount
        );

        return response()->json([
            'success' => true,
            'listings' => $listings
        ]);
    }

    /**
     * Get trade statistics for a user
     */
    public function getTradeStatistics()
    {
        $stats = $this->cryptoP2PService->getTradeStatistics();
        
        return response()->json([
            'success' => true,
            'statistics' => $stats
        ]);
    }

    /**
     * Delete a listing
     */
    public function deleteListing($id)
    {
        $listing = $this->cryptoP2PService->updateListing($id, ['status' => 'inactive'], Auth::id());

        if (!$listing) {
            return response()->json([
                'success' => false,
                'message' => 'Listing not found or unauthorized'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Listing deleted successfully'
        ]);
    }
}