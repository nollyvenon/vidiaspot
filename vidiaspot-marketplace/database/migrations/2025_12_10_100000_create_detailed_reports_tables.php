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
        // Financial Reports Tables
        Schema::create('balance_sheet_reports', function (Blueprint $table) {
            $table->id();
            $table->string('report_type'); // assets, liabilities, equity, working_capital, fixed_assets
            $table->json('assets')->nullable(); // Cryptocurrency holdings, fiat balances, escrow funds, cash equivalents
            $table->json('liabilities')->nullable(); // User deposits, pending settlements, obligations to users
            $table->json('equity')->nullable(); // Owner's equity, retained earnings, accumulated profits/losses
            $table->json('working_capital')->nullable(); // Current assets vs current liabilities
            $table->json('fixed_assets')->nullable(); // Servers, equipment, software licenses
            $table->decimal('total_assets', 20, 8)->default(0);
            $table->decimal('total_liabilities', 20, 8)->default(0);
            $table->decimal('total_equity', 20, 8)->default(0);
            $table->timestamp('period_start');
            $table->timestamp('period_end');
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('income_statement_reports', function (Blueprint $table) {
            $table->id();
            $table->json('revenue')->nullable(); // Trading fees, withdrawal fees, deposit fees, premium service revenue
            $table->json('cost_of_goods_sold')->nullable(); // Payment processing fees, blockchain fees, exchange rate spreads
            $table->json('operating_expenses')->nullable(); // Personnel, infrastructure, marketing, compliance costs
            $table->json('other_income_expenses')->nullable();
            $table->decimal('total_revenue', 20, 8)->default(0);
            $table->decimal('total_cost_of_goods_sold', 20, 8)->default(0);
            $table->decimal('total_operating_expenses', 20, 8)->default(0);
            $table->decimal('gross_profit', 20, 8)->default(0);
            $table->decimal('operating_income', 20, 8)->default(0);
            $table->decimal('net_income', 20, 8)->default(0);
            $table->timestamp('period_start');
            $table->timestamp('period_end');
            $table->json('revenue_by_currency')->nullable(); // Income breakdown by cryptocurrency type
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('cash_flow_reports', function (Blueprint $table) {
            $table->id();
            $table->json('operating_cash_flow')->nullable(); // Daily trading activity cash flow
            $table->json('investing_cash_flow')->nullable(); // Infrastructure investments, technology purchases
            $table->json('financing_cash_flow')->nullable(); // Funding activities, debt payments
            $table->json('free_cash_flow')->nullable(); // Available cash after operational expenses
            $table->decimal('net_operating_cash_flow', 20, 8)->default(0);
            $table->decimal('net_investing_cash_flow', 20, 8)->default(0);
            $table->decimal('net_financing_cash_flow', 20, 8)->default(0);
            $table->decimal('net_free_cash_flow', 20, 8)->default(0);
            $table->timestamp('period_start');
            $table->timestamp('period_end');
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        // Trading Activity Tables
        Schema::create('daily_trading_reports', function (Blueprint $table) {
            $table->id();
            $table->json('volume_by_pair')->nullable(); // Trading volume by pair
            $table->json('volume_by_currency')->nullable(); // Trading volume by currency
            $table->json('transaction_counts')->nullable(); // Number of trades, unique users, new registrations
            $table->json('fee_revenue')->nullable(); // Commission income by currency pair and user tier
            $table->json('settlement_status')->nullable(); // Successful, pending, failed transactions
            $table->json('average_order_size')->nullable(); // By currency pair and user segment
            $table->decimal('total_volume', 20, 8)->default(0);
            $table->integer('total_transactions')->default(0);
            $table->decimal('total_fees', 20, 8)->default(0);
            $table->timestamp('date');
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('user_activity_reports', function (Blueprint $table) {
            $table->id();
            $table->json('dau_mau_data')->nullable(); // Daily/Monthly Active Users (DAU/MAU)
            $table->json('retention_data')->nullable(); // User retention rates, churn analysis
            $table->json('conversion_data')->nullable(); // Registration to first trade, deposit to trade
            $table->json('geographic_data')->nullable(); // Trading activity by country/region
            $table->json('usage_patterns')->nullable(); // Peak Usage Times: Hourly and daily usage patterns
            $table->integer('dau')->default(0); // Daily Active Users
            $table->integer('mau')->default(0); // Monthly Active Users
            $table->decimal('retention_rate', 8, 4)->default(0); // Retention rate percentage
            $table->timestamp('period_start');
            $table->timestamp('period_end');
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        // User Account Reports Tables
        Schema::create('user_trade_history_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->json('trade_history')->nullable(); // Complete transaction history with timestamps
            $table->json('deposit_withdrawal_history')->nullable(); // All funding and withdrawal activities
            $table->json('fee_breakdown')->nullable(); // All fees charged to individual users
            $table->json('balance_snapshots')->nullable(); // Current and historical balance snapshots
            $table->json('tax_report')->nullable(); // Capital gains, income, and expense reports for users
            $table->timestamp('report_date');
            $table->timestamp('period_start');
            $table->timestamp('period_end');
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('user_segmentation_reports', function (Blueprint $table) {
            $table->id();
            $table->json('tier_based_data')->nullable(); // Activity and revenue by user verification level
            $table->json('vip_user_data')->nullable(); // High-volume trader activity and preferences
            $table->json('regional_user_data')->nullable(); // Behavior patterns by geographic location
            $table->json('new_returning_data')->nullable(); // New vs. Returning Users activity comparison
            $table->json('power_user_data')->nullable(); // Top 1% traders and their impact
            $table->timestamp('period_start');
            $table->timestamp('period_end');
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        // Risk Management Tables
        Schema::create('security_reports', function (Blueprint $table) {
            $table->id();
            $table->json('suspicious_activities')->nullable(); // Unusual trading patterns, security alerts
            $table->json('fraud_detection')->nullable(); // Flagged transactions and outcomes
            $table->json('kyc_aml_compliance')->nullable(); // Verification completion rates, compliance issues
            $table->json('system_security')->nullable(); // Breach attempts, security incidents
            $table->json('dispute_resolutions')->nullable(); // Trade disputes and resolution outcomes
            $table->integer('suspicious_activity_count')->default(0);
            $table->integer('fraud_detection_count')->default(0);
            $table->integer('dispute_count')->default(0);
            $table->timestamp('period_start');
            $table->timestamp('period_end');
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('market_risk_reports', function (Blueprint $table) {
            $table->id();
            $table->json('volatility_analysis')->nullable(); // Price volatility impact on trading volumes
            $table->json('liquidity_data')->nullable(); // Market depth and liquidity provision
            $table->json('counterparty_risk')->nullable(); // Exposure to individual traders
            $table->json('margin_call_data')->nullable(); // Leverage trading risk assessment
            $table->json('stop_loss_analysis')->nullable(); // Risk mitigation effectiveness
            $table->timestamp('period_start');
            $table->timestamp('period_end');
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        // Regulatory Compliance Tables
        Schema::create('aml_kyc_reports', function (Blueprint $table) {
            $table->id();
            $table->json('verification_status')->nullable(); // User verification completion rates
            $table->json('suspicious_transactions')->nullable(); // Transactions requiring regulatory reporting
            $table->json('customer_due_diligence')->nullable(); // Enhanced due diligence activities
            $table->json('pep_screening')->nullable(); // Politically Exposed Person identification
            $table->json('sanctions_screening')->nullable(); // OFAC and other sanctions list compliance
            $table->timestamp('period_start');
            $table->timestamp('period_end');
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('tax_reports', function (Blueprint $table) {
            $table->id();
            $table->json('user_tax_forms')->nullable(); // 1099 Forms for US users
            $table->json('taxable_events')->nullable(); // All taxable events by user
            $table->json('jurisdictional_taxes')->nullable(); // Tax obligations by country
            $table->json('withholding_taxes')->nullable(); // Tax withheld and remitted
            $table->json('audit_trails')->nullable(); // Complete transaction audit trails
            $table->timestamp('period_start');
            $table->timestamp('period_end');
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        // Operational Reports Tables
        Schema::create('system_performance_reports', function (Blueprint $table) {
            $table->id();
            $table->json('uptime_data')->nullable(); // System availability and downtime analysis
            $table->json('response_times')->nullable(); // API and interface performance metrics
            $table->json('error_rates')->nullable(); // System errors and resolution times
            $table->json('load_balancing')->nullable(); // Server load distribution
            $table->json('backup_recovery')->nullable(); // Data backup status and recovery tests
            $table->decimal('uptime_percentage', 8, 4)->default(0);
            $table->timestamp('period_start');
            $table->timestamp('period_end');
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('customer_service_reports', function (Blueprint $table) {
            $table->id();
            $table->json('support_tickets')->nullable(); // Volume, resolution time, satisfaction
            $table->json('user_complaints')->nullable(); // Types and frequency of complaints
            $table->json('feature_requests')->nullable(); // User suggestions and priority analysis
            $table->json('churn_analysis')->nullable(); // User departure reasons and patterns
            $table->json('support_agent_performance')->nullable(); // Efficiency and user satisfaction metrics
            $table->timestamp('period_start');
            $table->timestamp('period_end');
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        // Accounting Reports Tables
        Schema::create('general_ledger_reports', function (Blueprint $table) {
            $table->id();
            $table->json('chart_of_accounts')->nullable(); // Complete account structure and balances
            $table->json('trial_balance')->nullable(); // Period-end account balances verification
            $table->json('journal_entries')->nullable(); // All accounting transactions with details
            $table->json('account_reconciliations')->nullable(); // Bank, exchange, and wallet reconciliations
            $table->json('accrued_items')->nullable(); // Accrued revenue/expenses requiring adjustment
            $table->timestamp('period_start');
            $table->timestamp('period_end');
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('revenue_recognition_reports', function (Blueprint $table) {
            $table->id();
            $table->json('trading_fee_revenue')->nullable(); // Daily revenue recognition by service type
            $table->json('unearned_revenue')->nullable(); // Advance payments and service obligations
            $table->json('bad_debt')->nullable(); // Uncollectible amounts and allowance analysis
            $table->json('revenue_by_geographic')->nullable(); // Income by operating region
            $table->json('deferred_revenue')->nullable(); // Services to be provided in future periods
            $table->timestamp('period_start');
            $table->timestamp('period_end');
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        // Advanced Analytics Tables
        Schema::create('predictive_analytics_reports', function (Blueprint $table) {
            $table->id();
            $table->json('market_trends')->nullable(); // Predicted market movements and volume
            $table->json('user_growth_projections')->nullable(); // Forecasted user acquisition and retention
            $table->json('revenue_forecasts')->nullable(); // Predicted future revenue streams
            $table->json('risk_assessments')->nullable(); // Predicted market and operational risks
            $table->json('seasonal_patterns')->nullable(); // Cyclical behavior and seasonal adjustments
            $table->timestamp('forecast_date');
            $table->timestamp('period_start');
            $table->timestamp('period_end');
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('performance_metrics_reports', function (Blueprint $table) {
            $table->id();
            $table->json('kpis')->nullable(); // Key performance indicators
            $table->json('roi_analysis')->nullable(); // Return on investment for different business segments
            $table->json('efficiency_ratios')->nullable(); // Operational efficiency metrics
            $table->json('benchmark_comparisons')->nullable(); // Performance against industry standards
            $table->json('trend_analysis')->nullable(); // Long-term performance trend identification
            $table->timestamp('period_start');
            $table->timestamp('period_end');
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        // Real-time Monitoring Tables
        Schema::create('live_dashboard_reports', function (Blueprint $table) {
            $table->id();
            $table->json('market_data')->nullable(); // Real-time prices, volumes, and trends
            $table->json('active_sessions')->nullable(); // Current users and their activities
            $table->json('pending_transactions')->nullable(); // All unsettled trades and settlements
            $table->json('system_health')->nullable(); // Real-time infrastructure and system status
            $table->json('alerts')->nullable(); // Active system alerts and notifications
            $table->timestamp('last_updated');
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('automated_alert_reports', function (Blueprint $table) {
            $table->id();
            $table->string('alert_type'); // threshold_breach, security_incident, compliance_violation, financial_anomaly, system_performance
            $table->string('severity'); // low, medium, high, critical
            $table->text('description');
            $table->json('data')->nullable(); // The data that triggered the alert
            $table->boolean('is_resolved')->default(false);
            $table->timestamp('triggered_at');
            $table->timestamp('resolved_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('balance_sheet_reports');
        Schema::dropIfExists('income_statement_reports');
        Schema::dropIfExists('cash_flow_reports');
        Schema::dropIfExists('daily_trading_reports');
        Schema::dropIfExists('user_activity_reports');
        Schema::dropIfExists('user_trade_history_reports');
        Schema::dropIfExists('user_segmentation_reports');
        Schema::dropIfExists('security_reports');
        Schema::dropIfExists('market_risk_reports');
        Schema::dropIfExists('aml_kyc_reports');
        Schema::dropIfExists('tax_reports');
        Schema::dropIfExists('system_performance_reports');
        Schema::dropIfExists('customer_service_reports');
        Schema::dropIfExists('general_ledger_reports');
        Schema::dropIfExists('revenue_recognition_reports');
        Schema::dropIfExists('predictive_analytics_reports');
        Schema::dropIfExists('performance_metrics_reports');
        Schema::dropIfExists('live_dashboard_reports');
        Schema::dropIfExists('automated_alert_reports');
    }
};