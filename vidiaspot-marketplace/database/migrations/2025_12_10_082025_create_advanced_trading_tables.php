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
        // Create trading portfolios table
        Schema::create('trading_portfolios', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Portfolio name
            $table->string('slug')->unique(); // URL-friendly slug
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Portfolio owner
            $table->text('description')->nullable(); // Portfolio description
            $table->enum('type', ['crypto', 'stocks', 'forex', 'commodities', 'hybrid'])->default('crypto'); // Portfolio type
            $table->enum('strategy', [
                'long_term', 'day_trading', 'swing_trading', 'scalping', 'hodling',
                'arbitrage', 'algorithmic', 'social_trading', 'copy_trading'
            ])->default('long_term'); // Trading strategy
            $table->decimal('initial_capital', 15, 2)->default(0.00); // Initial capital invested
            $table->decimal('current_value', 15, 2)->default(0.00); // Current portfolio value
            $table->decimal('total_profit_loss', 15, 2)->default(0.00); // Total P&L
            $table->decimal('total_profit_loss_percentage', 8, 4)->default(0.0000); // Total P&L percentage
            $table->decimal('daily_profit_loss', 15, 2)->default(0.00); // Daily P&L
            $table->decimal('weekly_profit_loss', 15, 2)->default(0.00); // Weekly P&L
            $table->decimal('monthly_profit_loss', 15, 2)->default(0.00); // Monthly P&L
            $table->decimal('yearly_profit_loss', 15, 2)->default(0.00); // Yearly P&L
            $table->decimal('max_drawdown', 8, 4)->default(0.0000); // Maximum drawdown
            $table->decimal('sharpe_ratio', 8, 4)->default(0.0000); // Sharpe ratio
            $table->decimal('sortino_ratio', 8, 4)->default(0.0000); // Sortino ratio
            $table->decimal('volatility', 8, 4)->default(0.0000); // Portfolio volatility
            $table->integer('total_trades')->default(0); // Total number of trades
            $table->integer('winning_trades')->default(0); // Number of winning trades
            $table->integer('losing_trades')->default(0); // Number of losing trades
            $table->decimal('win_rate', 5, 2)->default(0.00); // Win rate percentage
            $table->decimal('avg_win_amount', 15, 2)->default(0.00); // Average winning trade amount
            $table->decimal('avg_loss_amount', 15, 2)->default(0.00); // Average losing trade amount
            $table->decimal('best_trade', 15, 2)->default(0.00); // Best trade return
            $table->decimal('worst_trade', 15, 2)->default(0.00); // Worst trade return
            $table->integer('allocation_count')->default(0); // Number of allocations
            $table->json('asset_allocation')->nullable(); // Asset allocation breakdown
            $table->json('risk_metrics')->nullable(); // Additional risk metrics
            $table->json('performance_history')->nullable(); // Historical performance data
            $table->json('metadata')->nullable(); // Additional portfolio metadata
            $table->json('tags')->nullable(); // Portfolio tags
            $table->boolean('is_active')->default(true);
            $table->boolean('is_public')->default(false); // Whether portfolio is public
            $table->boolean('is_featured')->default(false);
            $table->boolean('auto_rebalance')->default(false); // Whether portfolio auto-rebalances
            $table->decimal('rebalance_threshold', 5, 2)->default(5.00); // Rebalance threshold percentage
            $table->timestamp('last_rebalanced_at')->nullable(); // When last rebalanced
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });

        // Create portfolio allocations table
        Schema::create('portfolio_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portfolio_id')->constrained('trading_portfolios')->onDelete('cascade');
            $table->string('asset_symbol'); // Asset symbol (e.g., BTC, ETH, AAPL)
            $table->string('asset_name'); // Asset name
            $table->string('asset_type')->default('crypto'); // Asset type: crypto, stock, forex, commodity
            $table->decimal('quantity', 20, 8)->default(0.00000000); // Quantity held
            $table->decimal('average_buy_price', 20, 8)->default(0.00000000); // Average buy price
            $table->decimal('current_price', 20, 8)->default(0.00000000); // Current market price
            $table->decimal('current_value', 20, 8)->default(0.00000000); // Current value (quantity * current_price)
            $table->decimal('cost_basis', 20, 8)->default(0.00000000); // Total cost basis (quantity * avg_buy_price)
            $table->decimal('unrealized_pnl', 20, 8)->default(0.00000000); // Unrealized profit/loss
            $table->decimal('unrealized_pnl_percentage', 8, 4)->default(0.0000); // Unrealized P&L percentage
            $table->decimal('allocation_percentage', 5, 2); // Percentage of portfolio allocated
            $table->decimal('target_allocation_percentage', 5, 2)->default(0.00); // Target allocation percentage
            $table->timestamp('first_bought_at')->nullable(); // When first bought
            $table->timestamp('last_updated_at')->nullable(); // When last updated
            $table->boolean('is_active')->default(true);
            $table->json('transaction_history')->nullable(); // Transaction history for this allocation
            $table->json('metadata')->nullable(); // Additional allocation metadata
            $table->timestamps();

            // Indexes for performance
            $table->index(['portfolio_id', 'is_active']);
            $table->index('asset_symbol');
            $table->index('current_value');
            $table->index('allocation_percentage');
        });

        // Create trading transactions table
        Schema::create('trading_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portfolio_id')->constrained('trading_portfolios')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Transaction owner
            $table->string('asset_symbol'); // Asset symbol
            $table->string('asset_name'); // Asset name
            $table->enum('transaction_type', ['buy', 'sell', 'transfer', 'dividend', 'airdrop', 'fork'])->default('buy');
            $table->enum('order_type', ['market', 'limit', 'stop_loss', 'take_profit', 'oco', 'iceberg'])->default('market');
            $table->decimal('quantity', 20, 8)->default(0.00000000); // Quantity traded
            $table->decimal('price', 20, 8)->default(0.00000000); // Price per unit
            $table->decimal('total_amount', 20, 8)->default(0.00000000); // Total transaction amount (quantity * price)
            $table->decimal('fee', 20, 8)->default(0.00000000); // Transaction fee
            $table->string('fee_currency')->default('USD'); // Fee currency
            $table->decimal('exchange_rate', 20, 8)->default(1.00000000); // Exchange rate if different currency
            $table->string('exchange')->nullable(); // Exchange where transaction occurred
            $table->string('transaction_id')->nullable(); // Exchange transaction ID
            $table->string('blockchain_transaction_hash')->nullable(); // For crypto transactions
            $table->string('status', 20)->default('completed'); // pending, completed, cancelled, failed
            $table->timestamp('executed_at')->useCurrent(); // When transaction was executed
            $table->string('notes')->nullable(); // Transaction notes
            $table->json('metadata')->nullable(); // Additional transaction metadata
            $table->timestamps();

            // Indexes for performance
            $table->index(['portfolio_id', 'executed_at']);
            $table->index(['user_id', 'executed_at']);
            $table->index('asset_symbol');
            $table->index('transaction_type');
            $table->index('executed_at');
            $table->index('total_amount');
        });

        // Create trading alerts table
        Schema::create('trading_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('asset_symbol'); // Asset symbol to monitor
            $table->string('alert_type'); // price_above, price_below, volume_spike, rsi_oversold, rsi_overbought, etc.
            $table->decimal('trigger_value', 20, 8); // Value that triggers the alert
            $table->decimal('current_value', 20, 8)->default(0.00000000); // Current value being monitored
            $table->string('condition'); // above, below, equals, crosses, etc.
            $table->string('timeframe')->default('any'); // any, 1m, 5m, 15m, 1h, 4h, 1d, 1w
            $table->boolean('is_active')->default(true);
            $table->boolean('is_triggered')->default(false);
            $table->timestamp('triggered_at')->nullable();
            $table->timestamp('last_triggered_at')->nullable();
            $table->integer('trigger_count')->default(0); // Number of times triggered
            $table->json('notification_preferences')->nullable(); // Notification preferences
            $table->string('name'); // Alert name
            $table->text('description')->nullable();
            $table->json('source_data')->nullable(); // Source data for the alert
            $table->json('metadata')->nullable(); // Additional alert metadata
            $table->timestamps();

            // Indexes for performance
            $table->index(['user_id', 'is_active']);
            $table->index('asset_symbol');
            $table->index('alert_type');
            $table->index('is_triggered');
            $table->index('triggered_at');
        });

        // Create market analysis reports table
        Schema::create('market_analysis_reports', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Report title
            $table->string('slug')->unique(); // URL-friendly slug
            $table->text('summary')->nullable(); // Report summary
            $table->longText('content'); // Report content (HTML/Markdown)
            $table->string('report_type'); // technical, fundamental, sentiment, news, prediction
            $table->enum('timeframe', ['intraday', 'daily', 'weekly', 'monthly', 'quarterly', 'yearly'])->default('daily');
            $table->string('market'); // crypto, stocks, forex, commodities
            $table->string('asset_symbol')->nullable(); // Specific asset if applicable
            $table->json('assets_covered')->nullable(); // Multiple assets covered in the report
            $table->text('key_insights')->nullable(); // Key insights from the report
            $table->text('risks')->nullable(); // Risks mentioned in the report
            $table->text('recommendations')->nullable(); // Trading recommendations
            $table->json('technical_indicators')->nullable(); // Technical indicators used
            $table->json('fundamental_metrics')->nullable(); // Fundamental metrics analyzed
            $table->json('sentiment_data')->nullable(); // Sentiment analysis data
            $table->json('predicted_movement')->nullable(); // Predicted price movement
            $table->decimal('confidence_level', 5, 2)->nullable(); // Confidence level in prediction
            $table->foreignId('author_id')->nullable()->constrained('users')->onDelete('set null'); // Author of the report
            $table->string('author_name')->nullable(); // Name of the author (for non-user authors)
            $table->integer('view_count')->default(0); // Number of views
            $table->integer('like_count')->default(0); // Number of likes
            $table->integer('share_count')->default(0); // Number of shares
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_premium')->default(false); // Whether report is premium content
            $table->boolean('is_published')->default(true);
            $table->timestamp('published_at')->nullable();
            $table->json('metadata')->nullable(); // Additional report metadata
            $table->json('tags')->nullable(); // Report tags
            $table->json('sources')->nullable(); // Sources used in analysis
            $table->timestamps();

            // Indexes for performance
            $table->index('is_published');
            $table->index('report_type');
            $table->index('market');
            $table->index('asset_symbol');
            $table->index('view_count');
            $table->index('published_at');
            $table->index('slug');
        });

        // Create trading signals table
        Schema::create('trading_signals', function (Blueprint $table) {
            $table->id();
            $table->string('signal_name'); // Name of the signal
            $table->string('asset_symbol'); // Asset symbol
            $table->enum('signal_type', ['buy', 'sell', 'strong_buy', 'strong_sell', 'hold', 'reduce_position', 'increase_position'])->default('hold');
            $table->decimal('entry_price', 20, 8)->nullable(); // Suggested entry price
            $table->decimal('target_price', 20, 8)->nullable(); // Target price
            $table->decimal('stop_loss_price', 20, 8)->nullable(); // Stop loss price
            $table->decimal('expected_return', 5, 2)->nullable(); // Expected return percentage
            $table->decimal('confidence_level', 5, 2)->default(50.00); // Confidence level
            $table->enum('timeframe', ['short', 'medium', 'long'])->default('medium');
            $table->string('strategy_source')->default('algorithmic'); // algorithmic, technical_analysis, fundamental_analysis, community
            $table->text('analysis')->nullable(); // Analysis behind the signal
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null'); // Signal creator
            $table->timestamp('valid_from')->nullable(); // When signal becomes valid
            $table->timestamp('valid_until')->nullable(); // When signal expires
            $table->boolean('is_active')->default(true);
            $table->boolean('is_executed')->default(false); // Whether signal was executed
            $table->timestamp('executed_at')->nullable(); // When signal was executed
            $table->string('exchange')->nullable(); // Exchange where signal should be executed
            $table->json('technical_indicators')->nullable(); // Technical indicators used for signal
            $table->json('metadata')->nullable(); // Additional signal metadata
            $table->timestamps();

            // Indexes for performance
            $table->index('asset_symbol');
            $table->index('signal_type');
            $table->index('is_active');
            $table->index('is_executed');
            $table->index('confidence_level');
            $table->index('valid_from');
            $table->index('valid_until');
        });

        // Create user trading signal subscriptions table
        Schema::create('user_signal_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('signal_id')->constrained('trading_signals')->onDelete('cascade');
            $table->boolean('is_subscribed')->default(true);
            $table->json('notification_settings')->nullable(); // Notification preferences
            $table->timestamp('subscribed_at')->useCurrent();
            $table->timestamp('unsubscribed_at')->nullable();
            $table->json('metadata')->nullable(); // Additional subscription metadata
            $table->timestamps();

            $table->unique(['user_id', 'signal_id']); // Each user can only subscribe to a signal once

            // Indexes for performance
            $table->index(['user_id', 'is_subscribed']);
            $table->index(['signal_id', 'is_subscribed']);
        });

        // Create portfolio rebalancing history table
        Schema::create('portfolio_rebalancing_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portfolio_id')->constrained('trading_portfolios')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->json('before_allocation')->nullable(); // Allocation before rebalancing
            $table->json('after_allocation')->nullable(); // Allocation after rebalancing
            $table->json('rebalance_actions')->nullable(); // Actions taken during rebalancing
            $table->decimal('total_value', 20, 8)->default(0.00000000); // Portfolio total value at rebalance time
            $table->decimal('rebalance_cost', 20, 8)->default(0.00000000); // Cost of rebalancing
            $table->text('reason')->nullable(); // Reason for rebalancing
            $table->timestamp('rebalanced_at')->useCurrent();
            $table->json('metadata')->nullable(); // Additional rebalancing metadata
            $table->timestamps();

            // Indexes for performance
            $table->index(['portfolio_id', 'rebalanced_at']);
            $table->index('rebalanced_at');
        });

        // Create tax-loss harvesting opportunities table
        Schema::create('tax_loss_harvesting_opportunities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('portfolio_id')->constrained('trading_portfolios')->onDelete('cascade');
            $table->string('asset_symbol'); // Asset symbol
            $table->string('asset_name'); // Asset name
            $table->decimal('quantity', 20, 8)->default(0.00000000); // Quantity
            $table->decimal('purchase_price', 20, 8)->default(0.00000000); // Purchase price per unit
            $table->decimal('current_price', 20, 8)->default(0.00000000); // Current price per unit
            $table->decimal('total_cost_basis', 20, 8)->default(0.00000000); // Total cost basis
            $table->decimal('current_value', 20, 8)->default(0.00000000); // Current value
            $table->decimal('unrealized_loss', 20, 8)->default(0.00000000); // Unrealized loss (potential tax benefit)
            $table->decimal('potential_tax_savings', 20, 8)->default(0.00000000); // Potential tax savings
            $table->timestamp('purchase_date')->nullable(); // Date of purchase
            $table->integer('holding_period_days')->default(0); // Number of days held
            $table->boolean('is_long_term'); // Whether it's a long-term holding
            $table->string('tax_rate_applied')->default('0.00'); // Tax rate that would apply
            $table->json('alternatives')->nullable(); // Alternative assets for replacement
            $table->text('notes')->nullable(); // Additional notes
            $table->boolean('is_processed')->default(false); // Whether opportunity has been processed
            $table->timestamp('processed_at')->nullable(); // When processed
            $table->json('metadata')->nullable(); // Additional metadata
            $table->timestamps();

            // Indexes for performance
            $table->index(['user_id', 'is_processed']);
            $table->index(['portfolio_id', 'is_processed']);
            $table->index('asset_symbol');
            $table->index('unrealized_loss');
            $table->index('potential_tax_savings');
            $table->index('processed_at');
        });

        // Create DCA (Dollar Cost Averaging) plans table
        Schema::create('dca_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name'); // Plan name
            $table->string('asset_symbol'); // Asset to buy in DCA
            $table->string('asset_name'); // Asset name
            $table->decimal('amount_per_period', 20, 8)->default(0.00000000); // Amount to invest per period
            $table->string('amount_currency')->default('USD'); // Currency for the amount
            $table->enum('frequency', ['daily', 'weekly', 'biweekly', 'monthly', 'quarterly'])->default('monthly'); // Investment frequency
            $table->decimal('total_invested', 20, 8)->default(0.00000000); // Total amount invested so far
            $table->decimal('total_quantity_acquired', 20, 8)->default(0.00000000); // Total quantity acquired
            $table->integer('investments_made')->default(0); // Number of investments made
            $table->timestamp('start_date')->useCurrent(); // When DCA started
            $table->timestamp('end_date')->nullable(); // When DCA should end (null for indefinite)
            $table->timestamp('next_investment_date')->nullable(); // When next investment is scheduled
            $table->boolean('is_active')->default(true); // Whether DCA is active
            $table->boolean('is_completed')->default(false); // Whether DCA is completed
            $table->boolean('auto_reinvest')->default(false); // Whether to reinvest dividends/earnings
            $table->json('investment_history')->nullable(); // History of investments
            $table->json('performance_metrics')->nullable(); // Performance metrics
            $table->string('exchange')->nullable(); // Exchange for DCA orders
            $table->text('notes')->nullable(); // Additional notes
            $table->json('metadata')->nullable(); // Additional metadata
            $table->timestamps();

            // Indexes for performance
            $table->index(['user_id', 'is_active']);
            $table->index('asset_symbol');
            $table->index('is_active');
            $table->index('next_investment_date');
        });

        // Create recurring buy/sell orders table
        Schema::create('recurring_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('portfolio_id')->constrained('trading_portfolios')->onDelete('cascade');
            $table->string('asset_symbol'); // Asset to trade
            $table->string('asset_name'); // Asset name
            $table->enum('order_type', ['buy', 'sell'])->default('buy'); // buy or sell
            $table->enum('frequency', ['daily', 'weekly', 'biweekly', 'monthly', 'quarterly'])->default('monthly'); // Order frequency
            $table->string('amount_type')->default('fixed'); // fixed, percentage_portfolio, percentage_cash
            $table->decimal('amount', 20, 8)->default(0.00000000); // Order amount
            $table->string('amount_currency')->default('USD'); // Currency for the amount
            $table->decimal('percentage', 5, 2)->default(0.00); // If amount_type is percentage
            $table->decimal('min_price', 20, 8)->nullable(); // Minimum price trigger (optional)
            $table->decimal('max_price', 20, 8)->nullable(); // Maximum price trigger (optional)
            $table->string('condition')->default('time_based'); // time_based, price_based, technical
            $table->json('technical_condition')->nullable(); // Technical condition for execution
            $table->decimal('total_executed', 20, 8)->default(0.00000000); // Total amount executed
            $table->integer('orders_executed')->default(0); // Number of orders executed
            $table->timestamp('start_date')->useCurrent(); // When recurring orders start
            $table->timestamp('end_date')->nullable(); // When recurring orders end
            $table->timestamp('next_execution_date')->nullable(); // When next order should execute
            $table->boolean('is_active')->default(true); // Whether recurring orders are active
            $table->boolean('is_completed')->default(false); // Whether recurring orders are completed
            $table->string('status')->default('pending'); // pending, active, paused, completed, cancelled
            $table->string('exchange')->nullable(); // Exchange for recurring orders
            $table->json('execution_history')->nullable(); // History of executions
            $table->json('metadata')->nullable(); // Additional metadata
            $table->timestamps();

            // Indexes for performance
            $table->index(['user_id', 'is_active']);
            $table->index(['portfolio_id', 'is_active']);
            $table->index('asset_symbol');
            $table->index('is_active');
            $table->index('next_execution_date');
        });

        // Create trading journal table
        Schema::create('trading_journals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('portfolio_id')->nullable()->constrained('trading_portfolios')->onDelete('set null');
            $table->string('trade_id')->nullable(); // Reference to specific trade
            $table->string('asset_symbol'); // Asset symbol
            $table->string('asset_name'); // Asset name
            $table->enum('trade_type', ['long', 'short', 'swing', 'day', 'scalp'])->default('long');
            $table->decimal('entry_price', 20, 8)->default(0.00000000); // Entry price
            $table->decimal('exit_price', 20, 8)->default(0.00000000); // Exit price
            $table->decimal('quantity', 20, 8)->default(0.00000000); // Quantity traded
            $table->decimal('position_size', 20, 8)->default(0.00000000); // Position size in currency
            $table->decimal('profit_loss', 20, 8)->default(0.00000000); // Profit or loss
            $table->decimal('profit_loss_percentage', 8, 4)->default(0.0000); // Profit or loss percentage
            $table->decimal('fees', 20, 8)->default(0.00000000); // Total fees paid
            $table->string('timeframe'); // 1m, 5m, 15m, 1h, 4h, 1d, etc.
            $table->text('entry_reason')->nullable(); // Why the trade was entered
            $table->text('exit_reason')->nullable(); // Why the trade was exited
            $table->text('setup_description')->nullable(); // Description of the trade setup
            $table->text('mistakes_made')->nullable(); // Mistakes made during the trade
            $table->text('lessons_learned')->nullable(); // Lessons learned from the trade
            $table->text('what_went_well')->nullable(); // What went well with the trade
            $table->text('what_to_improve')->nullable(); // What to improve for next time
            $table->json('technical_indicators_used')->nullable(); // Technical indicators used
            $table->json('risk_management')->nullable(); // Risk management applied
            $table->integer('risk_level')->unsigned()->nullable(); // Risk level 1-10
            $table->integer('emotional_state_entry')->unsigned()->nullable(); // Emotional state at entry 1-10
            $table->integer('emotional_state_exit')->unsigned()->nullable(); // Emotional state at exit 1-10
            $table->json('screenshots')->nullable(); // Screenshots of trade setup
            $table->json('metadata')->nullable(); // Additional metadata
            $table->timestamp('entry_date')->nullable(); // When trade was entered
            $table->timestamp('exit_date')->nullable(); // When trade was exited
            $table->timestamps();

            // Indexes for performance
            $table->index(['user_id', 'entry_date']);
            $table->index(['portfolio_id', 'entry_date']);
            $table->index('asset_symbol');
            $table->index('profit_loss');
            $table->index('entry_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trading_journals');
        Schema::dropIfExists('recurring_orders');
        Schema::dropIfExists('dca_plans');
        Schema::dropIfExists('tax_loss_harvesting_opportunities');
        Schema::dropIfExists('portfolio_rebalancing_history');
        Schema::dropIfExists('user_signal_subscriptions');
        Schema::dropIfExists('trading_signals');
        Schema::dropIfExists('market_analysis_reports');
        Schema::dropIfExists('trading_alerts');
        Schema::dropIfExists('trading_transactions');
        Schema::dropIfExists('portfolio_allocations');
        Schema::dropIfExists('trading_portfolios');
    }
};
