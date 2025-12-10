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
        Schema::create('p2p_crypto_user_verifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('verification_type'); // kyc, biometric, video, etc.
            $table->string('verification_status'); // pending, approved, rejected, expired
            $table->string('verification_level'); // tier1, tier2, tier3, etc.
            $table->json('verification_data')->nullable(); // details for different verification types
            $table->string('document_type')->nullable(); // passport, driver_license, etc.
            $table->string('document_number')->nullable();
            $table->string('document_front_image')->nullable();
            $table->string('document_back_image')->nullable();
            $table->string('selfie_image')->nullable();
            $table->text('verification_notes')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->unsignedBigInteger('verified_by')->nullable(); // admin who verified
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('verified_by')->references('id')->on('users')->onDelete('set null');
            
            $table->index(['user_id', 'verification_status']);
            $table->index(['verification_type', 'verification_status']);
        });
        
        Schema::create('p2p_crypto_user_reputations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('counterparty_id'); // the user being rated
            $table->unsignedBigInteger('order_id'); // reference to the order that was traded
            $table->decimal('rating', 3, 2); // e.g., 4.75 out of 5
            $table->text('review')->nullable();
            $table->json('review_tags')->nullable(); // quick tags: reliable, fast, etc.
            $table->boolean('is_trusted')->default(false); // trusted flag based on interactions
            $table->integer('trade_count')->default(0); // successful trades with this user
            $table->decimal('completion_rate', 5, 2)->default(0); // completion percentage
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('counterparty_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('order_id')->references('id')->on('p2p_crypto_orders')->onDelete('cascade');
            
            $table->unique(['user_id', 'counterparty_id']); // one review per user pair
            $table->index(['counterparty_id', 'rating']);
        });
        
        // Add verification fields to the existing users table
        Schema::table('users', function (Blueprint $table) {
            $table->string('verification_level')->default('unverified'); // unverified, basic, verified, pro
            $table->decimal('reputation_score', 3, 2)->default(0); // overall reputation score
            $table->integer('total_trade_count')->default(0); // total number of trades
            $table->decimal('trade_completion_rate', 5, 2)->default(0); // overall completion rate
            $table->timestamp('last_trade_at')->nullable(); // last trade timestamp
            $table->boolean('is_trusted_seller')->default(false); // trusted seller flag
            $table->boolean('is_verified')->default(false); // verification status
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('p2p_crypto_user_reputations');
        Schema::dropIfExists('p2p_crypto_user_verifications');
        
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'verification_level',
                'reputation_score',
                'total_trade_count',
                'trade_completion_rate',
                'last_trade_at',
                'is_trusted_seller',
                'is_verified'
            ]);
        });
    }
};