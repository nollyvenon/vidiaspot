<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create NFT collections table
        Schema::create('nft_collections', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('symbol')->nullable(); // Collection symbol
            $table->string('banner_image_url')->nullable(); // Collection banner
            $table->string('logo_image_url')->nullable(); // Collection logo
            $table->string('external_url')->nullable(); // External website
            $table->json('social_links')->nullable(); // Social media links
            $table->foreignId('owner_id')->nullable()->constrained('users')->onDelete('set null'); // Collection owner
            $table->string('contract_address')->unique(); // Smart contract address
            $table->string('contract_type')->default('erc721'); // ERC-721, ERC-1155, etc.
            $table->string('blockchain')->default('ethereum'); // Blockchain network
            $table->integer('total_supply')->default(0); // Total NFTs in collection
            $table->integer('total_transfers')->default(0); // Total transfers
            $table->decimal('floor_price', 20, 8)->default(0.00000000); // Floor price in ETH/BNB/MATIC
            $table->decimal('volume_24h', 20, 8)->default(0.00000000); // Volume in last 24h
            $table->decimal('volume_7d', 20, 8)->default(0.00000000); // Volume in last 7 days
            $table->decimal('volume_30d', 20, 8)->default(0.00000000); // Volume in last 30 days
            $table->json('royalties')->nullable(); // Royalty percentages
            $table->decimal('royalty_percentage', 5, 2)->default(0.00); // Standard royalty percentage
            $table->json('metadata')->nullable(); // Additional collection metadata
            $table->boolean('is_verified')->default(false); // Whether collection is verified
            $table->boolean('is_active')->default(true);
            $table->boolean('is_public')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->json('filters')->nullable(); // Filter options for the collection
            $table->json('attributes')->nullable(); // Common attributes in this collection
            $table->timestamp('verified_at')->nullable(); // When collection was verified
            $table->string('verification_status')->default('pending'); // pending, approved, rejected
            $table->text('verification_notes')->nullable(); // Verification notes
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            // Indexes for performance
            $table->index('is_active');
            $table->index('is_verified');
            $table->index('floor_price');
            $table->index('volume_24h');
            $table->index('total_supply');
            $table->index('slug');
            $table->index('contract_address');
        });

        // Create NFT tokens table
        Schema::create('nft_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // NFT name
            $table->string('token_id'); // Token ID on blockchain
            $table->string('slug')->unique(); // URL-friendly slug
            $table->text('description')->nullable(); // NFT description
            $table->foreignId('collection_id')->constrained('nft_collections')->onDelete('cascade');
            $table->string('image_url'); // Main image URL
            $table->string('animation_url')->nullable(); // Animation/video URL
            $table->string('external_url')->nullable(); // External URL
            $table->json('attributes')->nullable(); // NFT attributes/properties
            $table->json('properties')->nullable(); // Additional properties
            $table->json('levels')->nullable(); // Level-based attributes
            $table->json('stats')->nullable(); // Stat-based attributes
            $table->string('background_color')->nullable(); // Background color
            $table->string('animation_original_url')->nullable(); // Original animation URL
            $table->string('image_original_url')->nullable(); // Original image URL
            $table->foreignId('current_owner_id')->constrained('users')->onDelete('cascade'); // Current owner
            $table->string('blockchain')->default('ethereum'); // Blockchain network
            $table->string('contract_address'); // Contract address
            $table->decimal('price', 20, 8)->nullable(); // Current price
            $table->decimal('last_sale_price', 20, 8)->nullable(); // Last sale price
            $table->timestamp('last_sale_date')->nullable(); // Last sale date
            $table->integer('number_of_owners')->default(1); // Number of owners (for ERC-1155)
            $table->integer('transfer_count')->default(0); // Number of transfers
            $table->decimal('creator_royalty', 5, 2)->default(0.00); // Creator royalty percentage
            $table->decimal('seller_royalty', 5, 2)->default(0.00); // Seller royalty percentage
            $table->json('royalty_recipients')->nullable(); // Royalty payment recipients
            $table->boolean('is_listed')->default(false); // Whether NFT is currently listed
            $table->boolean('is_burned')->default(false); // Whether NFT is burned
            $table->boolean('is_soulbound')->default(false); // Whether NFT is soulbound
            $table->boolean('is_staked')->default(false); // Whether NFT is staked
            $table->boolean('is_featured')->default(false); // Whether NFT is featured
            $table->timestamp('list_date')->nullable(); // When listed for sale
            $table->timestamp('unlist_date')->nullable(); // When unlisted from sale
            $table->json('metadata')->nullable(); // Additional metadata
            $table->json('traits')->nullable(); // Traits for rarity calculations
            $table->decimal('rarity_score', 10, 4)->nullable(); // Rarity score
            $table->integer('rarity_rank')->nullable(); // Rarity rank in collection
            $table->integer('times_viewed')->default(0); // Number of times viewed
            $table->text('provenance')->nullable(); // Provenance history
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            // Indexes for performance
            $table->index(['collection_id', 'is_listed']);
            $table->index(['current_owner_id', 'is_burned']);
            $table->index('price');
            $table->index('is_listed');
            $table->index('is_burned');
            $table->index('token_id');
            $table->index('rarity_score');
            $table->index('last_sale_date');
            $table->index('created_at');
            $table->index('slug');
        });

        // Create NFT listings table
        Schema::create('nft_listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nft_token_id')->constrained('nft_tokens')->onDelete('cascade');
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('collection_id')->constrained('nft_collections')->onDelete('cascade');
            $table->decimal('price', 20, 8); // Listing price
            $table->string('currency')->default('ETH'); // Currency for price
            $table->string('blockchain')->default('ethereum'); // Blockchain
            $table->enum('listing_type', ['fixed_price', 'auction', 'dutch_auction', 'bundle'])->default('fixed_price');
            $table->enum('status', ['active', 'sold', 'cancelled', 'expired'])->default('active');
            $table->timestamp('starts_at')->nullable(); // When listing starts (for auctions)
            $table->timestamp('ends_at')->nullable(); // When listing ends
            $table->decimal('reserve_price', 20, 8)->nullable(); // Reserve price for auctions
            $table->decimal('min_bid_increment', 20, 8)->default(0.01000000); // Minimum bid increment
            $table->decimal('current_bid', 20, 8)->default(0.00000000); // Current highest bid
            $table->foreignId('current_bidder_id')->nullable()->constrained('users')->onDelete('set null'); // Current highest bidder
            $table->decimal('buy_now_price', 20, 8)->nullable(); // Buy now price for auction
            $table->integer('bid_count')->default(0); // Number of bids
            $table->boolean('is_english_auction')->default(true); // Whether it's an English auction
            $table->boolean('has_reserve')->default(false); // Whether listing has reserve
            $table->boolean('auto_relist')->default(false); // Whether to relist after expiration
            $table->json('metadata')->nullable(); // Additional listing metadata
            $table->json('payment_methods')->nullable(); // Supported payment methods
            $table->json('bundle_items')->nullable(); // For bundle listings
            $table->timestamps();

            // Indexes for performance
            $table->index(['status', 'listing_type']);
            $table->index(['seller_id', 'status']);
            $table->index(['collection_id', 'status']);
            $table->index('price');
            $table->index('current_bid');
            $table->index('ends_at');
            $table->index('starts_at');
        });

        // Create NFT sales table
        Schema::create('nft_sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nft_token_id')->constrained('nft_tokens')->onDelete('cascade');
            $table->foreignId('collection_id')->constrained('nft_collections')->onDelete('cascade');
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('buyer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('listing_id')->nullable()->constrained('nft_listings')->onDelete('set null'); // Associated listing
            $table->decimal('sale_price', 20, 8); // Sale price
            $table->string('currency')->default('ETH'); // Currency used
            $table->string('blockchain')->default('ethereum'); // Blockchain
            $table->decimal('seller_royalty', 20, 8)->default(0.00000000); // Royalty paid to seller
            $table->decimal('creator_royalty', 20, 8)->default(0.00000000); // Royalty paid to creator
            $table->decimal('platform_fee', 20, 8)->default(0.00000000); // Platform fee
            $table->timestamp('sale_date')->useCurrent(); // When sale occurred
            $table->string('transaction_hash')->unique(); // Blockchain transaction hash
            $table->json('payment_details')->nullable(); // Payment details
            $table->json('metadata')->nullable(); // Additional sale metadata
            $table->timestamps();

            // Indexes for performance
            $table->index(['buyer_id', 'sale_date']);
            $table->index(['seller_id', 'sale_date']);
            $table->index(['nft_token_id', 'sale_date']);
            $table->index('sale_price');
            $table->index('sale_date');
            $table->index('transaction_hash');
        });

        // Create NFT bids table
        Schema::create('nft_bids', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nft_token_id')->constrained('nft_tokens')->onDelete('cascade');
            $table->foreignId('listing_id')->constrained('nft_listings')->onDelete('cascade');
            $table->foreignId('bidder_id')->constrained('users')->onDelete('cascade');
            $table->decimal('bid_amount', 20, 8); // Bid amount
            $table->string('currency')->default('ETH'); // Currency used
            $table->timestamp('bid_time')->useCurrent(); // When bid was placed
            $table->boolean('is_winning_bid')->default(false); // Whether this is the winning bid
            $table->boolean('is_cancelled')->default(false); // Whether bid was cancelled
            $table->timestamp('cancelled_at')->nullable(); // When bid was cancelled
            $table->json('metadata')->nullable(); // Additional bid metadata
            $table->timestamps();

            // Indexes for performance
            $table->index(['bidder_id', 'bid_time']);
            $table->index(['listing_id', 'bid_time']);
            $table->index(['nft_token_id', 'bid_time']);
            $table->index('bid_amount');
            $table->index('bid_time');
            $table->index('is_winning_bid');
        });

        // Create NFT auctions table
        Schema::create('nft_auctions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nft_token_id')->constrained('nft_tokens')->onDelete('cascade');
            $table->foreignId('collection_id')->constrained('nft_collections')->onDelete('cascade');
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
            $table->decimal('starting_price', 20, 8); // Starting price
            $table->decimal('reserve_price', 20, 8)->nullable(); // Reserve price
            $table->decimal('buy_now_price', 20, 8)->nullable(); // Buy now price
            $table->decimal('current_price', 20, 8)->default(0.00000000); // Current price
            $table->foreignId('highest_bidder_id')->nullable()->constrained('users')->onDelete('set null'); // Highest bidder
            $table->timestamp('starts_at')->nullable(); // Auction start time
            $table->timestamp('ends_at')->nullable(); // Auction end time
            $table->timestamp('ended_at')->nullable(); // When auction actually ended
            $table->boolean('is_finished')->default(false); // Whether auction is finished
            $table->boolean('is_cancelled')->default(false); // Whether auction was cancelled
            $table->timestamp('cancelled_at')->nullable(); // When auction was cancelled
            $table->string('currency')->default('ETH'); // Currency used
            $table->string('blockchain')->default('ethereum'); // Blockchain
            $table->decimal('min_bid_increment', 20, 8)->default(0.01000000); // Minimum bid increment
            $table->integer('bid_count')->default(0); // Number of bids
            $table->json('metadata')->nullable(); // Additional auction metadata
            $table->timestamps();

            // Indexes for performance
            $table->index(['seller_id', 'is_finished']);
            $table->index(['is_finished', 'ends_at']);
            $table->index('current_price');
            $table->index('starts_at');
            $table->index('ends_at');
        });

        // Create NFT marketplace settings table
        Schema::create('nft_marketplace_settings', function (Blueprint $table) {
            $table->id();
            $table->string('setting_key')->unique(); // e.g., 'marketplace_fee_percent', 'min_royalty_percent'
            $table->text('setting_value'); // Setting value
            $table->string('description')->nullable(); // Description of the setting
            $table->string('type')->default('string'); // Data type: string, integer, decimal, json, boolean
            $table->boolean('is_active')->default(true); // Whether setting is active
            $table->timestamps();

            // Indexes for performance
            $table->index('setting_key');
            $table->index('is_active');
        });

        // Insert default marketplace settings
        DB::table('nft_marketplace_settings')->insert([
            [
                'setting_key' => 'marketplace_fee_percent',
                'setting_value' => '2.5',
                'description' => 'Default marketplace fee percentage',
                'type' => 'decimal',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'setting_key' => 'min_royalty_percent',
                'setting_value' => '0.0',
                'description' => 'Minimum creator royalty percentage',
                'type' => 'decimal',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'setting_key' => 'max_royalty_percent',
                'setting_value' => '15.0',
                'description' => 'Maximum creator royalty percentage',
                'type' => 'decimal',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'setting_key' => 'enable_royalties',
                'setting_value' => 'true',
                'description' => 'Whether to enable creator royalties',
                'type' => 'boolean',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'setting_key' => 'enable_auctions',
                'setting_value' => 'true',
                'description' => 'Whether to enable auction listings',
                'type' => 'boolean',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nft_marketplace_settings');
        Schema::dropIfExists('nft_auctions');
        Schema::dropIfExists('nft_bids');
        Schema::dropIfExists('nft_sales');
        Schema::dropIfExists('nft_listings');
        Schema::dropIfExists('nft_tokens');
        Schema::dropIfExists('nft_collections');
    }
};
