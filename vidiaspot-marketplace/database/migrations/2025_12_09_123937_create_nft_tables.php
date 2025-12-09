<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('nft_collections', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('banner_image_url')->nullable();
            $table->string('image_url')->nullable();
            $table->foreignId('creator_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
            $table->string('external_url')->nullable();
            $table->string('twitter_username')->nullable();
            $table->string('instagram_username')->nullable();
            $table->string('discord_url')->nullable();
            $table->string('category')->default('art');
            $table->string('status')->default('active');
            $table->boolean('verified')->default(false);
            $table->integer('total_supply')->default(0);
            $table->integer('minted_supply')->default(0);
            $table->string('contract_address')->nullable();
            $table->string('blockchain')->default('ethereum');
            $table->string('token_standard')->default('ERC-721');
            $table->decimal('royalty_percentage', 5, 2)->default(0);
            $table->foreignId('royalty_recipient')->nullable()->constrained('users')->onDelete('set null');
            $table->decimal('fees_on_sale', 8, 2)->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['creator_id', 'status']);
            $table->index('verified');
            $table->index('category');
        });

        Schema::create('nfts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('collection_id')->nullable()->constrained('nft_collections')->onDelete('set null');
            $table->string('external_url')->nullable();
            $table->string('image_url')->nullable();
            $table->string('animation_url')->nullable();
            $table->string('token_id')->unique();
            $table->string('contract_address')->nullable();
            $table->string('blockchain')->default('ethereum');
            $table->foreignId('creator_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
            $table->decimal('price', 15, 8)->default(0);
            $table->string('currency')->default('ETH');
            $table->string('status')->default('draft');
            $table->json('properties')->nullable();
            $table->json('levels')->nullable();
            $table->json('stats')->nullable();
            $table->boolean('is_listed')->default(false);
            $table->decimal('list_price', 15, 8)->default(0);
            $table->string('list_currency')->default('ETH');
            $table->decimal('royalty_percentage', 5, 2)->default(0);
            $table->foreignId('royalty_recipient')->nullable()->constrained('users')->onDelete('set null');
            $table->json('metadata')->nullable();
            $table->string('token_standard')->default('ERC-721');
            $table->integer('supply')->default(1);
            $table->integer('max_supply')->default(1);
            $table->boolean('is_soulbound')->default(false);
            $table->boolean('transferable')->default(true);
            $table->timestamps();

            $table->index(['owner_id', 'status']);
            $table->index('is_listed');
            $table->index('blockchain');
            $table->index('token_id');
        });

        Schema::create('nft_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nft_id')->constrained('nfts')->onDelete('cascade');
            $table->foreignId('from_user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('to_user_id')->constrained('users')->onDelete('cascade');
            $table->string('transaction_type')->default('sale'); // sale, transfer, mint, burn
            $table->decimal('price', 15, 8)->default(0);
            $table->string('currency')->default('ETH');
            $table->string('transaction_hash')->nullable();
            $table->string('blockchain')->default('ethereum');
            $table->bigInteger('block_number')->nullable();
            $table->decimal('gas_used', 15, 8)->nullable();
            $table->decimal('gas_price', 15, 8)->nullable();
            $table->decimal('fee', 15, 8)->default(0);
            $table->string('status')->default('pending'); // pending, success, failed
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['nft_id', 'transaction_type']);
            $table->index(['from_user_id', 'to_user_id']);
            $table->index('status');
            $table->index('transaction_hash');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nft_transactions');
        Schema::dropIfExists('nfts');
        Schema::dropIfExists('nft_collections');
    }
};
