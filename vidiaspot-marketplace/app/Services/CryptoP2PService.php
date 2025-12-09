<?php

namespace App\Services;

use App\Models\CryptoListing;
use App\Models\CryptoTrade;
use App\Models\CryptoTradeTransaction;
use App\Models\User;
use App\Services\PaymentService;
use App\Services\BlockchainService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CryptoP2PService
{
    protected $paymentService;
    protected $blockchainService;

    public function __construct(PaymentService $paymentService, BlockchainService $blockchainService)
    {
        $this->paymentService = $paymentService;
        $this->blockchainService = $blockchainService;
    }

    public function createListing($listingData, $userId = null)
    {
        $userId = $userId ?: Auth::id();
        
        // Validate listing data
        $this->validateListingData($listingData);
        
        // Create the crypto listing
        $listing = CryptoListing::create([
            'user_id' => $userId,
            'crypto_currency' => $listingData['crypto_currency'],
            'fiat_currency' => $listingData['fiat_currency'] ?? 'NGN',
            'trade_type' => $listingData['trade_type'], // 'buy' or 'sell'
            'price_per_unit' => $listingData['price_per_unit'],
            'min_trade_amount' => $listingData['min_trade_amount'] ?? 0,
            'max_trade_amount' => $listingData['max_trade_amount'] ?? 0,
            'available_amount' => $listingData['available_amount'] ?? 0,
            'payment_methods' => $listingData['payment_methods'] ?? [],
            'trading_fee_percent' => $listingData['trading_fee_percent'] ?? 0,
            'trading_fee_fixed' => $listingData['trading_fee_fixed'] ?? 0,
            'location' => $listingData['location'] ?? null,
            'location_radius' => $listingData['location_radius'] ?? 0,
            'trading_terms' => $listingData['trading_terms'] ?? [],
            'negotiable' => $listingData['negotiable'] ?? false,
            'auto_accept' => $listingData['auto_accept'] ?? false,
            'auto_release_time_hours' => $listingData['auto_release_time_hours'] ?? 24,
            'verification_level_required' => $listingData['verification_level_required'] ?? 1,
            'trade_security_level' => $listingData['trade_security_level'] ?? 1,
            'status' => 'active',
            'is_public' => $listingData['is_public'] ?? true,
            'featured' => $listingData['featured'] ?? false,
            'metadata' => $listingData['metadata'] ?? [],
        ]);

        return $listing;
    }

    public function updateListing($listingId, $listingData, $userId = null)
    {
        $userId = $userId ?: Auth::id();

        $listing = CryptoListing::where('id', $listingId)
            ->where('user_id', $userId)
            ->first();

        if (!$listing) {
            return null; // Return null instead of throwing exception for delete operation
        }

        $listing->update($listingData);
        return $listing;
    }

    public function initiateTrade($listingId, $tradeData, $userId = null)
    {
        $userId = $userId ?: Auth::id();
        
        // Get the listing
        $listing = CryptoListing::find($listingId);
        if (!$listing) {
            throw new \Exception('Listing not found');
        }

        // Check if it's a valid trade type (opposite of listing type)
        $tradeType = $listing->trade_type === 'buy' ? 'sell' : 'buy';
        $userRole = $listing->trade_type === 'buy' ? 'buyer' : 'seller';
        $counterpartyRole = $listing->trade_type === 'buy' ? 'seller' : 'buyer';

        // Validate trade amount against listing limits
        $cryptoAmount = $tradeData['crypto_amount'];
        $fiatAmount = $cryptoAmount * $listing->price_per_unit;

        if ($fiatAmount < $listing->min_trade_amount || $fiatAmount > $listing->max_trade_amount) {
            throw new \Exception('Trade amount outside listing limits');
        }

        // Check availability if it's a sell listing
        if ($listing->trade_type === 'sell' && $cryptoAmount > $listing->available_amount) {
            throw new \Exception('Insufficient crypto amount available in listing');
        }

        // Generate escrow address for this trade
        $escrowAddress = $this->generateEscrowAddress($listing->crypto_currency);

        // Create the trade
        $trade = CryptoTrade::create([
            'listing_id' => $listingId,
            $userRole . '_id' => $userId,
            $counterpartyRole . '_id' => $listing->user_id,
            'trade_type' => $tradeType,
            'crypto_currency' => $listing->crypto_currency,
            'fiat_currency' => $listing->fiat_currency,
            'crypto_amount' => $cryptoAmount,
            'fiat_amount' => $fiatAmount,
            'exchange_rate' => $listing->price_per_unit,
            'payment_method' => $tradeData['payment_method'],
            'status' => 'pending',
            'escrow_address' => $escrowAddress,
            'trade_reference' => $this->generateTradeReference(),
            'payment_details' => $tradeData['payment_details'] ?? [],
            'escrow_status' => 'awaiting_deposit',
            'security_level' => $listing->trade_security_level,
            'metadata' => [
                'original_listing_data' => $listing->toArray(),
                'trade_initiation_data' => $tradeData,
            ],
        ]);

        // Update listing reserved amount
        $listing->increment('reserved_amount', $cryptoAmount);

        return $trade;
    }

    public function confirmPayment($tradeId, $userId = null)
    {
        $userId = $userId ?: Auth::id();
        
        $trade = CryptoTrade::find($tradeId);
        if (!$trade) {
            throw new \Exception('Trade not found');
        }

        // Check if the user is the buyer
        if ($trade->buyer_id !== $userId) {
            throw new \Exception('Only the buyer can confirm payment');
        }

        // Update trade status
        $trade->update([
            'status' => 'payment_confirmed',
            'payment_confirmed_at' => now(),
            'escrow_status' => 'awaiting_release_approval',
        ]);

        return $trade;
    }

    public function releaseCrypto($tradeId, $userId = null)
    {
        $userId = $userId ?: Auth::id();
        
        $trade = CryptoTrade::find($tradeId);
        if (!$trade) {
            throw new \Exception('Trade not found');
        }

        // Check if the user is the seller
        if ($trade->seller_id !== $userId) {
            throw new \Exception('Only the seller can release crypto');
        }

        // Verify payment confirmation
        if ($trade->status !== 'payment_confirmed') {
            throw new \Exception('Payment must be confirmed before releasing crypto');
        }

        // Create blockchain transaction to release crypto from escrow to buyer
        $releaseTx = $this->blockchainService->transferFromEscrow(
            $trade->crypto_currency,
            $trade->escrow_address,
            $this->getUserBlockchainAddress($trade->buyer_id),
            $trade->crypto_amount
        );

        if (!$releaseTx['success']) {
            throw new \Exception('Failed to release crypto: ' . $releaseTx['error']);
        }

        // Update trade status
        $trade->update([
            'status' => 'completed',
            'crypto_released_at' => now(),
            'trade_completed_at' => now(),
            'escrow_status' => 'released',
        ]);

        // Record the transaction
        CryptoTradeTransaction::create([
            'trade_id' => $trade->id,
            'user_id' => $userId,
            'transaction_type' => 'crypto_release',
            'crypto_currency' => $trade->crypto_currency,
            'crypto_amount' => $trade->crypto_amount,
            'fiat_amount' => $trade->fiat_amount,
            'exchange_rate' => $trade->exchange_rate,
            'transaction_hash' => $releaseTx['transaction_hash'] ?? null,
            'status' => 'confirmed',
            'confirmed_at' => now(),
        ]);

        // Update listing available amount
        $listing = $trade->listing;
        $listing->decrement('reserved_amount', $trade->crypto_amount);

        return $trade;
    }

    public function getActiveListings($filters = [])
    {
        $query = CryptoListing::active()->with(['user']);

        if (isset($filters['crypto_currency'])) {
            $query->byCryptoCurrency($filters['crypto_currency']);
        }

        if (isset($filters['fiat_currency'])) {
            $query->byFiatCurrency($filters['fiat_currency']);
        }

        if (isset($filters['trade_type'])) {
            $query->byTradeType($filters['trade_type']);
        }

        if (isset($filters['min_amount'])) {
            $query->where('min_trade_amount', '>=', $filters['min_amount']);
        }

        if (isset($filters['max_amount'])) {
            $query->where('max_trade_amount', '<=', $filters['max_amount']);
        }

        if (isset($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('crypto_currency', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('fiat_currency', 'like', '%' . $filters['search'] . '%');
            });
        }

        return $query->orderBy('featured', 'desc')
                    ->orderBy('price_per_unit')
                    ->paginate($filters['per_page'] ?? 20);
    }

    public function getUserListings($userId = null)
    {
        $userId = $userId ?: Auth::id();
        
        return CryptoListing::where('user_id', $userId)
            ->with(['user'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getUserTrades($userId = null, $filters = [])
    {
        $userId = $userId ?: Auth::id();
        
        $query = CryptoTrade::where(function($q) use ($userId) {
            $q->where('buyer_id', $userId)->orWhere('seller_id', $userId);
        })
        ->with(['listing', 'buyer', 'seller']);

        if (isset($filters['status'])) {
            $query->byStatus($filters['status']);
        }

        if (isset($filters['trade_type'])) {
            $query->byTradeType($filters['trade_type']);
        }

        if (isset($filters['crypto_currency'])) {
            $query->byCryptoCurrency($filters['crypto_currency']);
        }

        return $query->orderBy('created_at', 'desc')
                    ->paginate($filters['per_page'] ?? 20);
    }

    public function getMatchingListings($cryptoCurrency, $fiatCurrency, $tradeType, $amount, $userId = null)
    {
        $userId = $userId ?: Auth::id();
        
        // Find listings that match the trade requirements
        $query = CryptoListing::active()
            ->where('crypto_currency', $cryptoCurrency)
            ->where('fiat_currency', $fiatCurrency)
            ->where('trade_type', $tradeType) // Opposite of the user's trade type
            ->where('user_id', '!=', $userId) // Exclude user's own listings
            ->where('min_trade_amount', '<=', $amount)
            ->where('max_trade_amount', '>=', $amount);

        // If user wants to sell, check availability
        if ($tradeType === 'buy') { // User wants to sell, so find buy listings
            $query->where('available_amount', '>=', $amount);
        }

        return $query->orderBy('price_per_unit', $tradeType === 'buy' ? 'asc' : 'desc') // Best price first
                    ->get();
    }

    public function calculateTradeFees($listingId, $cryptoAmount)
    {
        $listing = CryptoListing::find($listingId);
        if (!$listing) {
            return 0;
        }

        $fiatAmount = $cryptoAmount * $listing->price_per_unit;
        $percentFee = ($fiatAmount * $listing->trading_fee_percent) / 100;
        $fixedFee = $listing->trading_fee_fixed;

        return $percentFee + $fixedFee;
    }

    public function getUserReputation($userId)
    {
        $completedTrades = CryptoTrade::where(function($q) use ($userId) {
            $q->where('buyer_id', $userId)->orWhere('seller_id', $userId);
        })
        ->where('status', 'completed')
        ->get();

        if ($completedTrades->isEmpty()) {
            return [
                'trade_count' => 0,
                'completion_rate' => 0,
                'avg_rating' => 0,
            ];
        }

        $totalTrades = $completedTrades->count();
        $completedTradesCount = $completedTrades->count();
        $totalRating = $completedTrades->sum(function($trade) use ($userId) {
            return $trade->buyer_id === $userId ? $trade->seller_rating : $trade->buyer_rating;
        });
        $avgRating = $totalRating > 0 ? $totalRating / $completedTradesCount : 0;

        return [
            'trade_count' => $totalTrades,
            'completion_rate' => 100, // Assuming all retrieved trades are completed
            'avg_rating' => round($avgRating, 2),
        ];
    }

    public function getTradeStatistics($userId = null)
    {
        $userId = $userId ?: Auth::id();
        
        $trades = CryptoTrade::where(function($q) use ($userId) {
            $q->where('buyer_id', $userId)->orWhere('seller_id', $userId);
        })
        ->selectRaw('status, COUNT(*) as count, SUM(fiat_amount) as total_amount')
        ->groupBy('status')
        ->get();

        $stats = [
            'total_trades' => 0,
            'total_volume' => 0,
            'by_status' => [],
        ];

        foreach ($trades as $trade) {
            $stats['by_status'][$trade->status] = [
                'count' => $trade->count,
                'amount' => $trade->total_amount,
            ];
            $stats['total_trades'] += $trade->count;
            $stats['total_volume'] += $trade->total_amount;
        }

        return $stats;
    }

    private function validateListingData($listingData)
    {
        if (empty($listingData['crypto_currency'])) {
            throw new \Exception('Crypto currency is required');
        }

        if (empty($listingData['trade_type']) || !in_array($listingData['trade_type'], ['buy', 'sell'])) {
            throw new \Exception('Trade type must be either "buy" or "sell"');
        }

        if (empty($listingData['price_per_unit']) || $listingData['price_per_unit'] <= 0) {
            throw new \Exception('Price per unit must be greater than 0');
        }

        if (isset($listingData['min_trade_amount']) && isset($listingData['max_trade_amount'])) {
            if ($listingData['min_trade_amount'] > $listingData['max_trade_amount']) {
                throw new \Exception('Minimum trade amount cannot exceed maximum trade amount');
            }
        }
    }

    private function generateTradeReference()
    {
        return 'CT' . strtoupper(Str::random(8)) . time();
    }

    private function generateEscrowAddress($cryptoCurrency)
    {
        // In a real implementation, this would generate a unique escrow address
        // using the blockchain service
        return 'escrow_' . strtolower($cryptoCurrency) . '_' . Str::random(10);
    }

    public function getListing($listingId)
    {
        return CryptoListing::with(['user'])->find($listingId);
    }

    public function getTrade($tradeId)
    {
        return CryptoTrade::with(['listing', 'buyer', 'seller'])->find($tradeId);
    }

    public function getSupportedCryptocurrencies()
    {
        // Return supported cryptocurrencies
        return [
            'BTC' => 'Bitcoin',
            'ETH' => 'Ethereum',
            'USDT' => 'Tether',
            'USDC' => 'USD Coin',
            'BNB' => 'Binance Coin',
            'ADA' => 'Cardano',
            'SOL' => 'Solana',
            'DOT' => 'Polkadot',
            'DOGE' => 'Dogecoin',
            'LTC' => 'Litecoin'
        ];
    }

    private function getUserBlockchainAddress($userId)
    {
        // In a real implementation, this would retrieve the user's blockchain address
        // from their profile or wallet service
        return 'user_' . $userId . '_wallet';
    }
}