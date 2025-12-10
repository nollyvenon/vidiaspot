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
        Schema::create('trading_journals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('trading_pair_id')->nullable(); // The pair being traded
            $table->string('entry_type'); // long, short
            $table->string('strategy_used')->nullable(); // Name of the strategy
            $table->decimal('entry_price', 16, 8);
            $table->decimal('exit_price', 16, 8)->nullable();
            $table->decimal('quantity', 16, 8);
            $table->decimal('position_size', 15, 2); // Position size in currency
            $table->string('timeframe'); // 1m, 5m, 15m, 1h, 4h, 1d
            $table->timestamp('entry_time');
            $table->timestamp('exit_time')->nullable();
            $table->decimal('profit_loss', 15, 2)->default(0.00); // Profit or loss amount
            $table->decimal('profit_loss_percentage', 8, 4)->default(0.0000); // Profit or loss percentage
            $table->string('status')->default('open'); // open, closed, cancelled
            $table->decimal('stop_loss_price', 16, 8)->nullable();
            $table->decimal('take_profit_price', 16, 8)->nullable();
            $table->string('trade_setup')->nullable(); // Description of the trade setup
            $table->string('outcome')->nullable(); // win, loss, breakeven
            $table->text('notes')->nullable(); // Trader's notes about the trade
            $table->text('lessons_learned')->nullable(); // Lessons learned from the trade
            $table->json('trade_analysis')->nullable(); // Technical/fundamental analysis
            $table->json('emotional_state')->nullable(); // Emotional state during trade
            $table->string('emotional_state_before')->nullable(); // Emotional state before trade
            $table->string('emotional_state_during')->nullable(); // Emotional state during trade
            $table->string('emotional_state_after')->nullable(); // Emotional state after trade
            $table->boolean('reviewed')->default(false); // Whether the trade has been reviewed
            $table->timestamp('reviewed_at')->nullable(); // When the trade was reviewed
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'entry_time']);
            $table->index(['trading_pair_id', 'entry_time']);
            $table->index('profit_loss');
            $table->index('entry_time');
            $table->index('outcome');
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('trading_pair_id')->references('id')->on('p2p_crypto_trading_pairs')->onDelete('set null');
        });
        
        Schema::create('trading_tags', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('color')->nullable(); // Color for UI display
            $table->unsignedBigInteger('user_id'); // Tags are user-specific
            $table->timestamps();

            $table->index(['user_id', 'name']);
            $table->unique(['user_id', 'name']); // Each user can't have duplicate tag names
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
        
        Schema::create('journal_tag', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('trading_journal_id');
            $table->unsignedBigInteger('trading_tag_id');
            
            $table->foreign('trading_journal_id')->references('id')->on('trading_journals')->onDelete('cascade');
            $table->foreign('trading_tag_id')->references('id')->on('trading_tags')->onDelete('cascade');
            
            $table->unique(['trading_journal_id', 'trading_tag_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journal_tag');
        Schema::dropIfExists('trading_tags');
        Schema::dropIfExists('trading_journals');
    }
};