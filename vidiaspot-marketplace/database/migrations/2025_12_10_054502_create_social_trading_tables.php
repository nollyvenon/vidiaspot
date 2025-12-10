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
        Schema::create('social_trading', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('trader_id'); // The trader being followed
            $table->unsignedBigInteger('follower_id'); // The user following the trader
            $table->string('status')->default('active'); // active, inactive, blocked
            $table->decimal('performance_fee_rate', 5, 2)->default(0.00); // Percentage of profits shared
            $table->decimal('management_fee_rate', 5, 2)->default(0.00); // Percentage of funds managed
            $table->decimal('allocation_amount', 15, 2)->default(0.00); // Amount allocated to copy trades
            $table->string('allocation_currency')->default('USD');
            $table->boolean('auto_copy')->default(true); // Whether to auto-copy trades
            $table->json('copy_settings')->nullable(); // Settings for copying (max_amount, leverage_limit, etc.)
            $table->timestamp('followed_at')->nullable();
            $table->timestamp('unfollowed_at')->nullable();
            $table->timestamps();

            $table->index(['trader_id', 'status']);
            $table->index(['follower_id', 'status']);
            $table->index(['trader_id', 'follower_id']); // Unique combination
            $table->index('followed_at');
            
            $table->foreign('trader_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('follower_id')->references('id')->on('users')->onDelete('cascade');
            
            // Ensure a user can't follow the same trader multiple times
            $table->unique(['trader_id', 'follower_id']);
        });
        
        Schema::create('trading_leagues', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('type')->default('performance'); // performance, volume, risk-adjusted
            $table->string('period')->default('weekly'); // daily, weekly, monthly, quarterly
            $table->json('eligibility_criteria')->nullable(); // Criteria to join the league
            $table->boolean('is_active')->default(true);
            $table->timestamp('start_date');
            $table->timestamp('end_date');
            $table->timestamps();

            $table->index(['is_active', 'type']);
            $table->index('start_date');
            $table->index('end_date');
        });
        
        Schema::create('league_participants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('league_id');
            $table->unsignedBigInteger('user_id');
            $table->integer('position')->nullable(); // Current position in the league
            $table->decimal('score', 15, 2)->default(0.00);
            $table->json('performance_metrics')->nullable(); // Metrics like return, risk_ratio, etc.
            $table->timestamps();

            $table->index(['league_id', 'user_id']);
            $table->index(['league_id', 'position']);
            $table->index('score');
            
            $table->foreign('league_id')->references('id')->on('trading_leagues')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('league_participants');
        Schema::dropIfExists('trading_leagues');
        Schema::dropIfExists('social_trading');
    }
};