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
        Schema::create('crypto_listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('crypto_currency'); // BTC, ETH, USDT, etc.
            $table->string('fiat_currency')->default('NGN');
            $table->enum('trade_type', ['buy', 'sell']);
            $table->decimal('price_per_unit', 15, 8);
            $table->decimal('min_trade_amount', 10, 2)->default(0);
            $table->decimal('max_trade_amount', 10, 2)->default(0);
            $table->decimal('available_amount', 15, 8)->default(0);
            $table->decimal('reserved_amount', 15, 8)->default(0);
            $table->json('payment_methods'); // ['bank_transfer', 'mobile_money', 'cash']
            $table->json('trade_limits')->nullable(); // Limits for different verification levels
            $table->decimal('trading_fee_percent', 5, 2)->default(0);
            $table->decimal('trading_fee_fixed', 10, 2)->default(0);
            $table->string('location')->nullable(); // City, state, country
            $table->decimal('location_radius', 8, 2)->default(0); // in km
            $table->json('trading_terms')->nullable(); // Terms and conditions for the listing
            $table->boolean('negotiable')->default(false);
            $table->boolean('auto_accept')->default(false);
            $table->integer('auto_release_time_hours')->default(24); // Hours before auto-release
            $table->tinyInteger('verification_level_required')->default(1); // 1-3 levels
            $table->tinyInteger('trade_security_level')->default(1); // 1-3 levels
            $table->decimal('reputation_score', 5, 2)->default(0);
            $table->integer('trade_count')->default(0);
            $table->decimal('completion_rate', 5, 2)->default(0);
            $table->boolean('online_status')->default(true);
            $table->string('status')->default('active'); // active, inactive, suspended
            $table->boolean('is_public')->default(true);
            $table->boolean('featured')->default(false);
            $table->boolean('pinned')->default(false);
            $table->timestamp('expires_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['crypto_currency', 'trade_type']);
            $table->index(['crypto_currency', 'fiat_currency']);
            $table->index('status');
            $table->index('is_public');
            $table->index('featured');
            $table->index('expires_at');
        });

        Schema::create('crypto_trades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrained()->onDelete('cascade');
            $table->foreignId('buyer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade');
            $table->enum('trade_type', ['buy', 'sell']); // Trade type from buyer perspective
            $table->string('crypto_currency');
            $table->string('fiat_currency')->default('NGN');
            $table->decimal('crypto_amount', 15, 8);
            $table->decimal('fiat_amount', 10, 2);
            $table->decimal('exchange_rate', 15, 8);
            $table->string('payment_method'); // bank_transfer, mobile_money, cash, etc.
            $table->string('status')->default('pending'); // pending, in_escrow, payment_confirmed, completed, cancelled, disputed
            $table->string('escrow_address')->nullable();
            $table->string('trade_reference')->unique();
            $table->json('payment_details')->nullable(); // Details about the payment method used
            $table->string('escrow_status')->default('awaiting_deposit'); // awaiting_deposit, deposited, awaiting_release_approval, released, refunded
            $table->timestamp('payment_confirmed_at')->nullable();
            $table->timestamp('crypto_released_at')->nullable();
            $table->timestamp('trade_completed_at')->nullable();
            $table->foreignId('dispute_id')->nullable()->constrained('disputes')->onDelete('set null');
            $table->timestamp('dispute_resolved_at')->nullable();
            $table->string('dispute_resolution')->nullable(); // buyer_favored, seller_favored, split, cancelled
            $table->tinyInteger('buyer_rating')->nullable(); // 1-5 rating
            $table->tinyInteger('seller_rating')->nullable(); // 1-5 rating
            $table->text('buyer_review')->nullable();
            $table->text('seller_review')->nullable();
            $table->tinyInteger('security_level')->default(1); // 1-3 levels
            $table->json('trade_limits')->nullable();
            $table->boolean('verification_required')->default(false);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['listing_id', 'status']);
            $table->index(['buyer_id', 'status']);
            $table->index(['seller_id', 'status']);
            $table->index(['crypto_currency', 'status']);
            $table->index('status');
            $table->index('trade_reference');
            $table->index('trade_completed_at');
        });

        Schema::create('crypto_trade_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trade_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('transaction_type'); // crypto_deposit, crypto_release, fee_payment
            $table->string('crypto_currency');
            $table->string('fiat_currency')->default('NGN');
            $table->decimal('crypto_amount', 15, 8)->default(0);
            $table->decimal('fiat_amount', 10, 2)->default(0);
            $table->decimal('exchange_rate', 15, 8)->default(0);
            $table->decimal('transaction_fee', 10, 8)->default(0);
            $table->string('payment_method')->nullable();
            $table->json('payment_details')->nullable();
            $table->string('transaction_hash')->nullable();
            $table->string('blockchain')->default('ethereum');
            $table->bigInteger('block_number')->nullable();
            $table->string('from_address');
            $table->string('to_address');
            $table->string('status')->default('pending'); // pending, confirmed, failed, reverted
            $table->timestamp('confirmed_at')->nullable();
            $table->integer('confirmation_blocks')->default(0);
            $table->text('error_message')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['trade_id', 'status']);
            $table->index(['user_id', 'transaction_type']);
            $table->index('transaction_hash');
            $table->index('status');
            $table->index('block_number');
            $table->index(['crypto_currency', 'transaction_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crypto_trade_transactions');
        Schema::dropIfExists('crypto_trades');
        Schema::dropIfExists('crypto_listings');
    }
};
