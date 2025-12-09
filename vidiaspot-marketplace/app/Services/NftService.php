<?php

namespace App\Services;

use App\Models\Nft;
use App\Models\NftCollection;
use App\Models\NftTransaction;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class NftService
{
    public function createCollection($collectionData, $userId = null)
    {
        $userId = $userId ?: Auth::id();
        
        return NftCollection::create([
            'name' => $collectionData['name'],
            'description' => $collectionData['description'] ?? '',
            'slug' => \Str::slug($collectionData['name']),
            'creator_id' => $userId,
            'owner_id' => $userId,
            'external_url' => $collectionData['external_url'] ?? null,
            'twitter_username' => $collectionData['twitter_username'] ?? null,
            'instagram_username' => $collectionData['instagram_username'] ?? null,
            'discord_url' => $collectionData['discord_url'] ?? null,
            'category' => $collectionData['category'] ?? 'art',
            'status' => 'active',
            'verified' => false,
            'blockchain' => $collectionData['blockchain'] ?? 'ethereum',
            'token_standard' => $collectionData['token_standard'] ?? 'ERC-721',
            'royalty_percentage' => $collectionData['royalty_percentage'] ?? 0,
            'metadata' => $collectionData['metadata'] ?? [],
        ]);
    }

    public function createNft($nftData, $collectionId = null, $userId = null)
    {
        $userId = $userId ?: Auth::id();
        
        $nft = Nft::create([
            'name' => $nftData['name'],
            'description' => $nftData['description'] ?? '',
            'collection_id' => $collectionId,
            'external_url' => $nftData['external_url'] ?? null,
            'image_url' => $nftData['image_url'] ?? null,
            'animation_url' => $nftData['animation_url'] ?? null,
            'token_id' => $nftData['token_id'] ?? $this->generateTokenId(),
            'contract_address' => $nftData['contract_address'] ?? null,
            'blockchain' => $nftData['blockchain'] ?? 'ethereum',
            'creator_id' => $userId,
            'owner_id' => $userId,
            'price' => $nftData['price'] ?? 0,
            'currency' => $nftData['currency'] ?? 'ETH',
            'status' => 'draft',
            'properties' => $nftData['properties'] ?? [],
            'levels' => $nftData['levels'] ?? [],
            'stats' => $nftData['stats'] ?? [],
            'is_listed' => $nftData['is_listed'] ?? false,
            'list_price' => $nftData['list_price'] ?? 0,
            'list_currency' => $nftData['list_currency'] ?? 'ETH',
            'royalty_percentage' => $nftData['royalty_percentage'] ?? 0,
            'royalty_recipient' => $nftData['royalty_recipient'] ?? $userId,
            'metadata' => $nftData['metadata'] ?? [],
            'token_standard' => $nftData['token_standard'] ?? 'ERC-721',
            'supply' => $nftData['supply'] ?? 1,
            'max_supply' => $nftData['max_supply'] ?? 1,
            'is_soulbound' => $nftData['is_soulbound'] ?? false,
            'transferable' => $nftData['transferable'] ?? true,
        ]);

        return $nft;
    }

    public function listNftForSale($nftId, $price, $currency = 'ETH', $userId = null)
    {
        $userId = $userId ?: Auth::id();
        
        $nft = Nft::where('id', $nftId)
            ->where('owner_id', $userId)
            ->first();

        if ($nft) {
            $nft->update([
                'is_listed' => true,
                'list_price' => $price,
                'list_currency' => $currency,
                'status' => 'active',
            ]);
        }

        return $nft;
    }

    public function buyNft($nftId, $buyerId = null)
    {
        $buyerId = $buyerId ?: Auth::id();
        
        $nft = Nft::where('id', $nftId)
            ->where('is_listed', true)
            ->where('status', 'active')
            ->first();

        if (!$nft || $nft->owner_id === $buyerId) {
            return null;
        }

        $sellerId = $nft->owner_id;
        $price = $nft->list_price;
        $currency = $nft->list_currency;

        // In a real implementation, this would handle the blockchain transaction
        // For now, we'll simulate the transaction
        
        \DB::beginTransaction();
        
        try {
            // Update NFT ownership
            $nft->update([
                'owner_id' => $buyerId,
                'is_listed' => false,
            ]);

            // Record transaction
            $transaction = NftTransaction::create([
                'nft_id' => $nftId,
                'from_user_id' => $sellerId,
                'to_user_id' => $buyerId,
                'transaction_type' => 'sale',
                'price' => $price,
                'currency' => $currency,
                'status' => 'success',
                'metadata' => [
                    'sale_price' => $price,
                    'previous_owner' => $sellerId,
                    'new_owner' => $buyerId,
                ],
            ]);

            \DB::commit();
            
            return ['nft' => $nft, 'transaction' => $transaction];
        } catch (\Exception $e) {
            \DB::rollback();
            throw $e;
        }
    }

    public function getMarketplaceNfts($filters = [])
    {
        $query = Nft::active()->listed()->with(['creator', 'owner', 'collection']);

        if (isset($filters['collection_id'])) {
            $query->where('collection_id', $filters['collection_id']);
        }

        if (isset($filters['blockchain'])) {
            $query->forBlockchain($filters['blockchain']);
        }

        if (isset($filters['creator_id'])) {
            $query->where('creator_id', $filters['creator_id']);
        }

        if (isset($filters['price_min'])) {
            $query->where('list_price', '>=', $filters['price_min']);
        }

        if (isset($filters['price_max'])) {
            $query->where('list_price', '<=', $filters['price_max']);
        }

        if (isset($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('description', 'like', '%' . $filters['search'] . '%');
        }

        return $query->orderBy('created_at', 'desc')
                    ->paginate($filters['per_page'] ?? 20);
    }

    public function getNftDetails($nftId)
    {
        return Nft::with(['creator', 'owner', 'collection', 'transactions'])->find($nftId);
    }

    public function getUserNfts($userId = null)
    {
        $userId = $userId ?: Auth::id();
        
        return Nft::where('owner_id', $userId)
            ->with(['collection'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getUserCollections($userId = null)
    {
        $userId = $userId ?: Auth::id();
        
        return NftCollection::where('owner_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    private function generateTokenId()
    {
        return (string) time() . rand(1000, 9999);
    }
}