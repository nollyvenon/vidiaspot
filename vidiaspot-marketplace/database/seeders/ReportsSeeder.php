<?php

namespace Database\Seeders;

use App\Models\BalanceSheetReport;
use App\Models\IncomeStatementReport;
use App\Models\DailyTradingReport;
use App\Models\UserActivityReport;
use App\Models\UserTradeHistoryReport;
use App\Models\UserSegmentationReport;
use App\Models\SecurityReport;
use App\Models\MarketRiskReport;
use App\Models\AmlKycReport;
use App\Models\TaxReport;
use App\Models\SystemPerformanceReport;
use App\Models\CustomerServiceReport;
use App\Models\GeneralLedgerReport;
use App\Models\RevenueRecognitionReport;
use App\Models\PredictiveAnalyticsReport;
use App\Models\PerformanceMetricsReport;
use App\Models\LiveDashboardReport;
use App\Models\AutomatedAlertReport;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ReportsSeeder extends Seeder
{
    public function run()
    {
        // Create sample users for user-specific reports
        $users = User::factory()->count(10)->create();

        // Create sample Balance Sheet Reports
        BalanceSheetReport::create([
            'report_type' => 'balance_sheet',
            'assets' => [
                'crypto_holdings' => ['BTC' => 15.5, 'ETH' => 85.2, 'USDT' => 150000],
                'fiat_balances' => ['USD' => 75000, 'EUR' => 15000],
                'escrow_funds' => 35000,
                'cash_equivalents' => 25000,
                'total' => 305000
            ],
            'liabilities' => [
                'user_deposits' => 220000,
                'pending_settlements' => 8000,
                'obligations_to_users' => 3500,
                'total' => 231500
            ],
            'equity' => [
                'owners_equity' => 55000,
                'retained_earnings' => 8500,
                'accumulated_profits_losses' => 10000,
                'total' => 73500
            ],
            'working_capital' => [
                'current_assets' => 100000,
                'current_liabilities' => 223500,
                'working_capital' => -123500
            ],
            'fixed_assets' => [
                'servers' => 30000,
                'equipment' => 18000,
                'software_licenses' => 6000,
                'total' => 54000
            ],
            'total_assets' => 305000,
            'total_liabilities' => 231500,
            'total_equity' => 73500,
            'period_start' => Carbon::now()->subMonth(),
            'period_end' => Carbon::now(),
            'metadata' => [
                'generated_at' => now(),
                'currency' => 'USD',
                'reporting_period' => 'Monthly'
            ]
        ]);

        // Create sample Income Statement Reports
        IncomeStatementReport::create([
            'revenue' => [
                'trading_fees' => 12500,
                'withdrawal_fees' => 850,
                'deposit_fees' => 400,
                'premium_service_revenue' => 2000,
                'total' => 15750
            ],
            'cost_of_goods_sold' => [
                'payment_processing_fees' => 1250,
                'blockchain_fees' => 650,
                'exchange_rate_spreads' => 300,
                'total' => 2200
            ],
            'operating_expenses' => [
                'personnel_costs' => 6500,
                'infrastructure_costs' => 1200,
                'marketing_costs' => 950,
                'compliance_costs' => 650,
                'total' => 9300
            ],
            'other_income_expenses' => [],
            'total_revenue' => 15750,
            'total_cost_of_goods_sold' => 2200,
            'total_operating_expenses' => 9300,
            'gross_profit' => 13550,
            'operating_income' => 4250,
            'net_income' => 4250,
            'period_start' => Carbon::now()->subMonth(),
            'period_end' => Carbon::now(),
            'revenue_by_currency' => [
                'BTC' => 6500,
                'ETH' => 4200,
                'USDT' => 3000,
                'USD' => 2050,
            ],
            'metadata' => [
                'generated_at' => now(),
                'currency' => 'USD',
                'reporting_period' => 'Monthly'
            ]
        ]);

        // Create sample Daily Trading Reports
        DailyTradingReport::create([
            'volume_by_pair' => [
                'BTC_USD' => 200000,
                'ETH_USD' => 120000,
                'BTC_ETH' => 65000,
                'ADA_USD' => 45000,
            ],
            'volume_by_currency' => [
                'BTC' => 14.5,
                'ETH' => 75.8,
                'USDT' => 135000,
                'ADA' => 250000,
                'total' => 320000
            ],
            'transaction_counts' => [
                'completed_trades' => 1250,
                'unique_users' => 450,
                'new_registrations' => 15,
                'total' => 1250
            ],
            'fee_revenue' => [
                'trading_fees' => 2100,
                'by_pair' => [
                    'BTC_USD' => 1100,
                    'ETH_USD' => 650,
                    'BTC_ETH' => 350,
                ],
                'by_user_tier' => [
                    'basic' => 650,
                    'verified' => 1000,
                    'premium' => 450,
                ],
                'total' => 2100
            ],
            'settlement_status' => [
                'successful' => 1245,
                'pending' => 3,
                'failed' => 2,
                'total' => 1250
            ],
            'average_order_size' => [
                'by_pair' => [
                    'BTC_USD' => 1750,
                    'ETH_USD' => 1200,
                    'BTC_ETH' => 2200,
                ],
                'by_user_segment' => [
                    'new_users' => 650,
                    'regular_users' => 1400,
                    'vip_users' => 3200,
                ],
                'overall_average' => 1450
            ],
            'total_volume' => 320000,
            'total_transactions' => 1250,
            'total_fees' => 2100,
            'date' => Carbon::now()->subDay(),
            'metadata' => [
                'generated_at' => now(),
                'currency' => 'USD',
                'reporting_period' => 'Daily'
            ]
        ]);

        // Create sample User Activity Reports
        UserActivityReport::create([
            'dau_mau_data' => [
                'dau' => 450,
                'mau' => 1850,
                'ratio' => 24.3
            ],
            'retention_data' => [
                'day_1' => 78.5,
                'day_7' => 48.2,
                'day_30' => 28.8,
                'churn_rate' => 12.3,
                'retention_rate' => 87.7
            ],
            'conversion_data' => [
                'registration_to_first_trade' => 68.2,
                'deposit_to_trade' => 85.7,
                'trial_to_paid' => 15.3
            ],
            'geographic_data' => [
                'US' => 42,
                'EU' => 28,
                'Asia' => 18,
                'Africa' => 8,
                'Other' => 4
            ],
            'usage_patterns' => [
                'peak_hours' => [
                    '09:00-12:00' => 22,
                    '14:00-17:00' => 28,
                    '20:00-23:00' => 25,
                ],
                'peak_days' => [
                    'Monday' => 16,
                    'Wednesday' => 19,
                    'Friday' => 18,
                    'Sunday' => 22,
                ],
                'avg_session_duration' => 18
            ],
            'dau' => 450,
            'mau' => 1850,
            'retention_rate' => 87.7,
            'period_start' => Carbon::now()->subMonth(),
            'period_end' => Carbon::now(),
            'metadata' => [
                'generated_at' => now(),
                'reporting_period' => 'Monthly'
            ]
        ]);

        // Create sample User Trade History Reports for 3 different users
        $sampleUsers = $users->take(3);
        foreach ($sampleUsers as $user) {
            UserTradeHistoryReport::create([
                'user_id' => $user->id,
                'trade_history' => [
                    [
                        'id' => 'trade_' . rand(1000, 9999),
                        'pair' => ['BTC', 'USDT'],
                        'type' => rand(0, 1) ? 'buy' : 'sell',
                        'amount' => rand(1000, 5000) / 1000,
                        'price' => rand(30000, 70000),
                        'fee' => rand(10, 50) / 100,
                        'timestamp' => Carbon::now()->subDays(rand(1, 30))->toISOString()
                    ],
                    [
                        'id' => 'trade_' . rand(1000, 9999),
                        'pair' => ['ETH', 'USDT'],
                        'type' => rand(0, 1) ? 'buy' : 'sell',
                        'amount' => rand(1000, 10000) / 1000,
                        'price' => rand(2000, 4000),
                        'fee' => rand(5, 30) / 100,
                        'timestamp' => Carbon::now()->subDays(rand(1, 30))->toISOString()
                    ]
                ],
                'deposit_withdrawal_history' => [
                    'deposits' => [
                        [
                            'type' => 'crypto',
                            'currency' => 'BTC',
                            'amount' => rand(100, 500) / 100,
                            'timestamp' => Carbon::now()->subDays(rand(1, 30))->toISOString()
                        ]
                    ],
                    'withdrawals' => [
                        [
                            'type' => 'fiat',
                            'currency' => 'USD',
                            'amount' => rand(500, 2000),
                            'timestamp' => Carbon::now()->subDays(rand(1, 20))->toISOString()
                        ]
                    ]
                ],
                'fee_breakdown' => [
                    'trading_fees' => rand(50, 300) / 10,
                    'withdrawal_fees' => rand(10, 50) / 10,
                    'other_fees' => rand(5, 20) / 10,
                    'total' => rand(100, 400) / 10
                ],
                'balance_snapshots' => [
                    'start_balance' => rand(1000, 5000),
                    'end_balance' => rand(2000, 8000),
                    'min_balance' => rand(800, 4500),
                    'max_balance' => rand(2500, 9000),
                ],
                'tax_report' => [
                    'capital_gains' => rand(200, 1000),
                    'income' => 0,
                    'expenses' => rand(10, 80),
                    'tax_owed' => rand(30, 150)
                ],
                'report_date' => now(),
                'period_start' => Carbon::now()->subMonth(),
                'period_end' => Carbon::now(),
                'metadata' => [
                    'generated_at' => now(),
                    'user_name' => $user->name,
                ]
            ]);
        }

        // Create sample User Segmentation Reports
        UserSegmentationReport::create([
            'tier_based_data' => [
                'basic' => [
                    'count' => 12500,
                    'activity' => 25,
                    'revenue_contribution' => 15
                ],
                'verified' => [
                    'count' => 4500,
                    'activity' => 55,
                    'revenue_contribution' => 45
                ],
                'premium' => [
                    'count' => 850,
                    'activity' => 20,
                    'revenue_contribution' => 40
                ]
            ],
            'vip_user_data' => [
                'top_1_percent_activity' => 35,
                'top_1_percent_revenue' => 52,
                'average_trade_value' => 4500,
                'average_daily_trades' => 12
            ],
            'regional_user_data' => [
                'US' => [
                    'count' => 8500,
                    'avg_trade_size' => 1800,
                    'activity_level' => 35
                ],
                'EU' => [
                    'count' => 4200,
                    'avg_trade_size' => 1600,
                    'activity_level' => 28
                ]
            ],
            'new_returning_data' => [
                'new_users' => [
                    'count' => 450,
                    'retention_7d' => 45.2,
                    'avg_first_trade_value' => 850
                ],
                'returning_users' => [
                    'count' => 16800,
                    'retention_7d' => 78.5,
                    'avg_trade_value' => 2200
                ]
            ],
            'power_user_data' => [
                'top_1_percent_count' => 179,
                'top_1_percent_volume' => 45,
                'avg_daily_trades' => 25,
                'avg_monthly_revenue' => 12500
            ],
            'period_start' => Carbon::now()->subMonth(),
            'period_end' => Carbon::now(),
            'metadata' => [
                'generated_at' => now(),
                'reporting_period' => 'Monthly'
            ]
        ]);

        // Create sample Security Reports
        SecurityReport::create([
            'suspicious_activities' => [
                'count' => 12,
                'types' => [
                    'unusual_volume' => 5,
                    'rapid_account_opening' => 3,
                    'anomalous_behavior' => 4
                ]
            ],
            'fraud_detection' => [
                'flagged_transactions' => 8,
                'blocked_accounts' => 2,
                'resolved_cases' => 6,
                'accuracy_rate' => 85.5
            ],
            'kyc_aml_compliance' => [
                'verification_completion_rate' => 89.7,
                'compliance_issues' => 4,
                'document_review_backlog' => 15
            ],
            'system_security' => [
                'breach_attempts' => 25,
                'security_incidents' => 0,
                'patch_compliance' => 98.5
            ],
            'dispute_resolutions' => [
                'total_disputes' => 18,
                'resolved' => 16,
                'unresolved' => 2,
                'avg_resolution_time' => 48 // hours
            ],
            'suspicious_activity_count' => 12,
            'fraud_detection_count' => 8,
            'dispute_count' => 18,
            'period_start' => Carbon::now()->subMonth(),
            'period_end' => Carbon::now(),
            'metadata' => [
                'generated_at' => now(),
                'reporting_period' => 'Monthly'
            ]
        ]);

        $this->command->info('Reports seeded successfully!');
    }
}