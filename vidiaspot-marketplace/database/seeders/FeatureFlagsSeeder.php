<?php

namespace Database\Seeders;

use App\Models\FeatureFlag;
use Illuminate\Database\Seeder;

class FeatureFlagsSeeder extends Seeder
{
    public function run()
    {
        // Social & Community Features
        FeatureFlag::updateOrCreate(
            ['feature_key' => 'social_trading'],
            [
                'name' => 'Social Trading',
                'description' => 'Enable copy trading functionality, social leaderboards and rankings, follow successful traders',
                'is_enabled' => true,
            ]
        );

        FeatureFlag::updateOrCreate(
            ['feature_key' => 'signal_sharing'],
            [
                'name' => 'Signal Sharing System',
                'description' => 'Enable trading signal sharing between users',
                'is_enabled' => true,
            ]
        );

        FeatureFlag::updateOrCreate(
            ['feature_key' => 'trading_groups'],
            [
                'name' => 'Community Trading Groups',
                'description' => 'Enable creation of trading groups and communities',
                'is_enabled' => true,
            ]
        );

        FeatureFlag::updateOrCreate(
            ['feature_key' => 'educational_content'],
            [
                'name' => 'Educational Content',
                'description' => 'Enable expert educational content for trading',
                'is_enabled' => true,
            ]
        );

        FeatureFlag::updateOrCreate(
            ['feature_key' => 'live_trading_sessions'],
            [
                'name' => 'Live Trading Sessions',
                'description' => 'Enable live trading sessions and webinars',
                'is_enabled' => true,
            ]
        );

        FeatureFlag::updateOrCreate(
            ['feature_key' => 'trading_journal'],
            [
                'name' => 'Trading Journal',
                'description' => 'Enable trading journal and performance tracking',
                'is_enabled' => true,
            ]
        );

        FeatureFlag::updateOrCreate(
            ['feature_key' => 'encrypted_messaging'],
            [
                'name' => 'Encrypted Messaging',
                'description' => 'Enable encrypted messaging system',
                'is_enabled' => true,
            ]
        );

        FeatureFlag::updateOrCreate(
            ['feature_key' => 'voice_video_calls'],
            [
                'name' => 'Voice and Video Calls',
                'description' => 'Enable voice and video call functionality',
                'is_enabled' => true,
            ]
        );

        FeatureFlag::updateOrCreate(
            ['feature_key' => 'group_chats'],
            [
                'name' => 'Group Chats',
                'description' => 'Enable group chats for trading communities',
                'is_enabled' => true,
            ]
        );

        FeatureFlag::updateOrCreate(
            ['feature_key' => 'market_sentiment_analysis'],
            [
                'name' => 'Market Sentiment Analysis',
                'description' => 'Enable real-time market sentiment analysis',
                'is_enabled' => true,
            ]
        );

        FeatureFlag::updateOrCreate(
            ['feature_key' => 'news_announcements'],
            [
                'name' => 'News and Announcements',
                'description' => 'Enable news and announcement feeds',
                'is_enabled' => true,
            ]
        );

        FeatureFlag::updateOrCreate(
            ['feature_key' => 'social_media_integration'],
            [
                'name' => 'Social Media Integration',
                'description' => 'Enable social media integration features',
                'is_enabled' => true,
            ]
        );

        FeatureFlag::updateOrCreate(
            ['feature_key' => 'trading_alerts'],
            [
                'name' => 'Trading Alerts',
                'description' => 'Enable trading alerts and notifications',
                'is_enabled' => true,
            ]
        );

        // Mobile & Accessibility Features
        FeatureFlag::updateOrCreate(
            ['feature_key' => 'mobile_apps'],
            [
                'name' => 'Native Mobile Apps',
                'description' => 'Enable native iOS and Android applications',
                'is_enabled' => true,
            ]
        );

        FeatureFlag::updateOrCreate(
            ['feature_key' => 'push_notifications'],
            [
                'name' => 'Push Notifications',
                'description' => 'Enable push notifications for price alerts',
                'is_enabled' => true,
            ]
        );

        FeatureFlag::updateOrCreate(
            ['feature_key' => 'biometric_login'],
            [
                'name' => 'Biometric Login',
                'description' => 'Enable biometric login (Face ID, Touch ID)',
                'is_enabled' => true,
            ]
        );

        FeatureFlag::updateOrCreate(
            ['feature_key' => 'mobile_only_features'],
            [
                'name' => 'Mobile-Only Trading Features',
                'description' => 'Enable trading features exclusive to mobile',
                'is_enabled' => true,
            ]
        );

        FeatureFlag::updateOrCreate(
            ['feature_key' => 'qr_scanning'],
            [
                'name' => 'QR Code Scanning',
                'description' => 'Enable QR code scanning for transactions',
                'is_enabled' => true,
            ]
        );

        FeatureFlag::updateOrCreate(
            ['feature_key' => 'mobile_payment_integration'],
            [
                'name' => 'Mobile Payment Integration',
                'description' => 'Enable mobile payment integration',
                'is_enabled' => true,
            ]
        );

        FeatureFlag::updateOrCreate(
            ['feature_key' => 'offline_mode'],
            [
                'name' => 'Offline Mode',
                'description' => 'Enable offline mode with sync capability',
                'is_enabled' => true,
            ]
        );

        // Advanced Analytics & Tools
        FeatureFlag::updateOrCreate(
            ['feature_key' => 'real_time_market_data'],
            [
                'name' => 'Real-Time Market Data',
                'description' => 'Enable real-time market data and news',
                'is_enabled' => true,
            ]
        );

        FeatureFlag::updateOrCreate(
            ['feature_key' => 'technical_analysis_tools'],
            [
                'name' => 'Technical Analysis Tools',
                'description' => 'Enable technical analysis tools and indicators',
                'is_enabled' => true,
            ]
        );

        FeatureFlag::updateOrCreate(
            ['feature_key' => 'price_prediction'],
            [
                'name' => 'Price Prediction Algorithms',
                'description' => 'Enable price prediction algorithms',
                'is_enabled' => true,
            ]
        );

        FeatureFlag::updateOrCreate(
            ['feature_key' => 'portfolio_tracking'],
            [
                'name' => 'Portfolio Tracking',
                'description' => 'Enable portfolio tracking and analytics',
                'is_enabled' => true,
            ]
        );

        FeatureFlag::updateOrCreate(
            ['feature_key' => 'tax_reporting'],
            [
                'name' => 'Tax Reporting Tools',
                'description' => 'Enable tax reporting tools',
                'is_enabled' => true,
            ]
        );

        FeatureFlag::updateOrCreate(
            ['feature_key' => 'historical_data'],
            [
                'name' => 'Historical Data',
                'description' => 'Enable historical data and backtesting',
                'is_enabled' => true,
            ]
        );

        FeatureFlag::updateOrCreate(
            ['feature_key' => 'correlation_analysis'],
            [
                'name' => 'Correlation Analysis',
                'description' => 'Enable correlation analysis between pairs',
                'is_enabled' => true,
            ]
        );

        FeatureFlag::updateOrCreate(
            ['feature_key' => 'portfolio_risk_calculator'],
            [
                'name' => 'Portfolio Risk Calculator',
                'description' => 'Enable portfolio risk calculator',
                'is_enabled' => true,
            ]
        );

        FeatureFlag::updateOrCreate(
            ['feature_key' => 'diversification_analyzer'],
            [
                'name' => 'Diversification Analyzer',
                'description' => 'Enable diversification analyzer',
                'is_enabled' => true,
            ]
        );

        FeatureFlag::updateOrCreate(
            ['feature_key' => 'volatility_indicators'],
            [
                'name' => 'Volatility Indicators',
                'description' => 'Enable volatility indicators',
                'is_enabled' => true,
            ]
        );

        FeatureFlag::updateOrCreate(
            ['feature_key' => 'risk_reward_calculator'],
            [
                'name' => 'Risk/Reward Ratio Calculator',
                'description' => 'Enable risk/reward ratio calculator',
                'is_enabled' => true,
            ]
        );

        FeatureFlag::updateOrCreate(
            ['feature_key' => 'position_sizing'],
            [
                'name' => 'Position Sizing Calculator',
                'description' => 'Enable position sizing calculator',
                'is_enabled' => true,
            ]
        );

        FeatureFlag::updateOrCreate(
            ['feature_key' => 'drawdown_analysis'],
            [
                'name' => 'Drawdown Analysis',
                'description' => 'Enable drawdown analysis',
                'is_enabled' => true,
            ]
        );

        FeatureFlag::updateOrCreate(
            ['feature_key' => 'performance_attribution'],
            [
                'name' => 'Performance Attribution',
                'description' => 'Enable performance attribution',
                'is_enabled' => true,
            ]
        );

        // Payment & Settlement Features
        FeatureFlag::updateOrCreate(
            ['feature_key' => 'instant_crypto_transfers'],
            [
                'name' => 'Instant Cryptocurrency Transfers',
                'description' => 'Enable real-time cryptocurrency transfers',
                'is_enabled' => true,
            ]
        );

        FeatureFlag::updateOrCreate(
            ['feature_key' => 'atomic_swaps'],
            [
                'name' => 'Atomic Swap Technology',
                'description' => 'Enable atomic swap technology',
                'is_enabled' => true,
            ]
        );

        FeatureFlag::updateOrCreate(
            ['feature_key' => 'cross_chain_bridges'],
            [
                'name' => 'Cross-Chain Bridge Integration',
                'description' => 'Enable cross-chain bridge integration',
                'is_enabled' => true,
            ]
        );

        FeatureFlag::updateOrCreate(
            ['feature_key' => 'lightning_network'],
            [
                'name' => 'Lightning Network Support',
                'description' => 'Enable Lightning Network support for Bitcoin',
                'is_enabled' => true,
            ]
        );

        FeatureFlag::updateOrCreate(
            ['feature_key' => 'ethereum_layer2'],
            [
                'name' => 'Ethereum Layer 2 Solutions',
                'description' => 'Enable Layer 2 solutions for Ethereum',
                'is_enabled' => true,
            ]
        );

        FeatureFlag::updateOrCreate(
            ['feature_key' => 'instant_fiat_settlements'],
            [
                'name' => 'Instant Fiat Settlements',
                'description' => 'Enable instant fiat settlements',
                'is_enabled' => true,
            ]
        );

        FeatureFlag::updateOrCreate(
            ['feature_key' => 'multi_currency_wallets'],
            [
                'name' => 'Multi-Currency Wallets',
                'description' => 'Enable multi-currency wallets',
                'is_enabled' => true,
            ]
        );

        FeatureFlag::updateOrCreate(
            ['feature_key' => 'automatic_rebalancing'],
            [
                'name' => 'Automatic Rebalancing',
                'description' => 'Enable automatic rebalancing',
                'is_enabled' => true,
            ]
        );

        // Compliance & Regulatory Features
        FeatureFlag::updateOrCreate(
            ['feature_key' => 'auto_tax_reporting'],
            [
                'name' => 'Automatic Tax Reporting',
                'description' => 'Enable automatic tax reporting (1099, etc.)',
                'is_enabled' => true,
            ]
        );

        FeatureFlag::updateOrCreate(
            ['feature_key' => 'transaction_monitoring'],
            [
                'name' => 'Transaction Monitoring',
                'description' => 'Enable transaction monitoring for compliance',
                'is_enabled' => true,
            ]
        );

        FeatureFlag::updateOrCreate(
            ['feature_key' => 'sar_reporting'],
            [
                'name' => 'Suspicious Activity Reporting',
                'description' => 'Enable suspicious activity reporting (SAR)',
                'is_enabled' => true,
            ]
        );

        FeatureFlag::updateOrCreate(
            ['feature_key' => 'geo_restrictions'],
            [
                'name' => 'Geographic Restrictions',
                'description' => 'Enable geographic restriction enforcement',
                'is_enabled' => true,
            ]
        );

        FeatureFlag::updateOrCreate(
            ['feature_key' => 'age_verification'],
            [
                'name' => 'Age Verification Systems',
                'description' => 'Enable age verification systems',
                'is_enabled' => true,
            ]
        );

        FeatureFlag::updateOrCreate(
            ['feature_key' => 'transaction_limits'],
            [
                'name' => 'Transaction Limits',
                'description' => 'Enable transaction limits based on verification',
                'is_enabled' => true,
            ]
        );

        FeatureFlag::updateOrCreate(
            ['feature_key' => 'aml_tools'],
            [
                'name' => 'Anti-Money Laundering Tools',
                'description' => 'Enable anti-money laundering (AML) tools',
                'is_enabled' => true,
            ]
        );

        FeatureFlag::updateOrCreate(
            ['feature_key' => 'regulatory_automation'],
            [
                'name' => 'Regulatory Reporting Automation',
                'description' => 'Enable regulatory reporting automation',
                'is_enabled' => true,
            ]
        );

        // Advanced Trading Features
        FeatureFlag::updateOrCreate(
            ['feature_key' => 'algorithmic_trading'],
            [
                'name' => 'Algorithmic Trading',
                'description' => 'Enable custom trading bot creation and algorithmic trading',
                'is_enabled' => true,
            ]
        );

        FeatureFlag::updateOrCreate(
            ['feature_key' => 'trading_api'],
            [
                'name' => 'Trading API Access',
                'description' => 'Enable API access for developers',
                'is_enabled' => true,
            ]
        );

        FeatureFlag::updateOrCreate(
            ['feature_key' => 'webhook_notifications'],
            [
                'name' => 'Webhook Notifications',
                'description' => 'Enable webhook notifications',
                'is_enabled' => true,
            ]
        );

        FeatureFlag::updateOrCreate(
            ['feature_key' => 'strategy_backtesting'],
            [
                'name' => 'Strategy Backtesting',
                'description' => 'Enable automated strategy backtesting',
                'is_enabled' => true,
            ]
        );

        FeatureFlag::updateOrCreate(
            ['feature_key' => 'strategy_marketplace'],
            [
                'name' => 'Strategy Sharing Marketplace',
                'description' => 'Enable strategy sharing marketplace',
                'is_enabled' => true,
            ]
        );

        FeatureFlag::updateOrCreate(
            ['feature_key' => 'indicator_builder'],
            [
                'name' => 'Technical Indicator Builders',
                'description' => 'Enable technical indicator builders',
                'is_enabled' => true,
            ]
        );

        FeatureFlag::updateOrCreate(
            ['feature_key' => 'custom_alerts'],
            [
                'name' => 'Custom Alert Systems',
                'description' => 'Enable custom alert systems',
                'is_enabled' => true,
            ]
        );

        FeatureFlag::updateOrCreate(
            ['feature_key' => 'market_making'],
            [
                'name' => 'Market Making Tools',
                'description' => 'Enable market making tools',
                'is_enabled' => true,
            ]
        );

        // Customer Support & Education Features
        FeatureFlag::updateOrCreate(
            ['feature_key' => 'interactive_courses'],
            [
                'name' => 'Interactive Trading Courses',
                'description' => 'Enable interactive trading courses',
                'is_enabled' => true,
            ]
        );

        FeatureFlag::updateOrCreate(
            ['feature_key' => 'market_tutorials'],
            [
                'name' => 'Market Analysis Tutorials',
                'description' => 'Enable market analysis tutorials',
                'is_enabled' => true,
            ]
        );

        FeatureFlag::updateOrCreate(
            ['feature_key' => 'crypto_education'],
            [
                'name' => 'Cryptocurrency Education',
                'description' => 'Enable cryptocurrency education center',
                'is_enabled' => true,
            ]
        );

        FeatureFlag::updateOrCreate(
            ['feature_key' => 'risk_management_guides'],
            [
                'name' => 'Risk Management Guides',
                'description' => 'Enable risk management guides',
                'is_enabled' => true,
            ]
        );

        FeatureFlag::updateOrCreate(
            ['feature_key' => 'webinar_series'],
            [
                'name' => 'Webinar Series',
                'description' => 'Enable webinar series',
                'is_enabled' => true,
            ]
        );

        FeatureFlag::updateOrCreate(
            ['feature_key' => 'community_forums'],
            [
                'name' => 'Community Forums',
                'description' => 'Enable community forums',
                'is_enabled' => true,
            ]
        );

        FeatureFlag::updateOrCreate(
            ['feature_key' => 'glossary_faq'],
            [
                'name' => 'Glossary and FAQ',
                'description' => 'Enable glossary and FAQ section',
                'is_enabled' => true,
            ]
        );

        FeatureFlag::updateOrCreate(
            ['feature_key' => 'video_tutorials'],
            [
                'name' => 'Video Tutorials',
                'description' => 'Enable video tutorials and walkthroughs',
                'is_enabled' => true,
            ]
        );

        FeatureFlag::updateOrCreate(
            ['feature_key' => 'live_chat_support'],
            [
                'name' => '24/7 Live Chat Support',
                'description' => 'Enable 24/7 live chat support',
                'is_enabled' => true,
            ]
        );

        FeatureFlag::updateOrCreate(
            ['feature_key' => 'multi_language_support'],
            [
                'name' => 'Multi-Language Customer Service',
                'description' => 'Enable multi-language customer service',
                'is_enabled' => true,
            ]
        );

        FeatureFlag::updateOrCreate(
            ['feature_key' => 'ticket_system'],
            [
                'name' => 'Ticket System',
                'description' => 'Enable ticket system with priority levels',
                'is_enabled' => true,
            ]
        );

        FeatureFlag::updateOrCreate(
            ['feature_key' => 'video_call_support'],
            [
                'name' => 'Video Call Support',
                'description' => 'Enable video call support for complex issues',
                'is_enabled' => true,
            ]
        );

        FeatureFlag::updateOrCreate(
            ['feature_key' => 'self_service_help'],
            [
                'name' => 'Self-Service Help Center',
                'description' => 'Enable self-service help center',
                'is_enabled' => true,
            ]
        );

        // Innovative Features
        FeatureFlag::updateOrCreate(
            ['feature_key' => 'defi_staking'],
            [
                'name' => 'Staking and Yield Farming',
                'description' => 'Enable staking and yield farming',
                'is_enabled' => true,
            ]
        );

        FeatureFlag::updateOrCreate(
            ['feature_key' => 'liquidity_pools'],
            [
                'name' => 'Liquidity Pool Participation',
                'description' => 'Enable liquidity pool participation',
                'is_enabled' => true,
            ]
        );

        FeatureFlag::updateOrCreate(
            ['feature_key' => 'defi_protocols'],
            [
                'name' => 'Decentralized Finance Protocols',
                'description' => 'Enable decentralized finance protocols',
                'is_enabled' => true,
            ]
        );

        FeatureFlag::updateOrCreate(
            ['feature_key' => 'nft_integration'],
            [
                'name' => 'NFT Marketplace Integration',
                'description' => 'Enable NFT marketplace integration',
                'is_enabled' => true,
            ]
        );

        FeatureFlag::updateOrCreate(
            ['feature_key' => 'prediction_markets'],
            [
                'name' => 'Prediction Markets',
                'description' => 'Enable prediction markets',
                'is_enabled' => true,
            ]
        );

        FeatureFlag::updateOrCreate(
            ['feature_key' => 'synthetic_assets'],
            [
                'name' => 'Synthetic Assets',
                'description' => 'Enable synthetic assets',
                'is_enabled' => true,
            ]
        );

        FeatureFlag::updateOrCreate(
            ['feature_key' => 'cross_chain_interoperability'],
            [
                'name' => 'Cross-Chain Interoperability',
                'description' => 'Enable cross-chain interoperability',
                'is_enabled' => true,
            ]
        );

        FeatureFlag::updateOrCreate(
            ['feature_key' => 'dao_governance'],
            [
                'name' => 'DAO Governance Participation',
                'description' => 'Enable DAO governance participation',
                'is_enabled' => true,
            ]
        );

        FeatureFlag::updateOrCreate(
            ['feature_key' => 'social_sentiment_analysis'],
            [
                'name' => 'Social Sentiment Analysis',
                'description' => 'Enable social sentiment analysis',
                'is_enabled' => true,
            ]
        );

        FeatureFlag::updateOrCreate(
            ['feature_key' => 'whale_tracking'],
            [
                'name' => 'Whale Transaction Tracking',
                'description' => 'Enable whale transaction tracking',
                'is_enabled' => true,
            ]
        );

        FeatureFlag::updateOrCreate(
            ['feature_key' => 'manipulation_detection'],
            [
                'name' => 'Market Manipulation Detection',
                'description' => 'Enable market manipulation detection',
                'is_enabled' => true,
            ]
        );

        FeatureFlag::updateOrCreate(
            ['feature_key' => 'arbitrage_tools'],
            [
                'name' => 'Cross-Exchange Arbitrage Tools',
                'description' => 'Enable cross-exchange arbitrage tools',
                'is_enabled' => true,
            ]
        );

        FeatureFlag::updateOrCreate(
            ['feature_key' => 'portfolio_rebalancing'],
            [
                'name' => 'Portfolio Rebalancing Automation',
                'description' => 'Enable portfolio rebalancing automation',
                'is_enabled' => true,
            ]
        );

        FeatureFlag::updateOrCreate(
            ['feature_key' => 'tax_loss_harvesting'],
            [
                'name' => 'Tax-Loss Harvesting',
                'description' => 'Enable tax-loss harvesting',
                'is_enabled' => true,
            ]
        );

        FeatureFlag::updateOrCreate(
            ['feature_key' => 'dca_tools'],
            [
                'name' => 'Dollar-Cost Averaging Tools',
                'description' => 'Enable dollar-cost averaging (DCA) tools',
                'is_enabled' => true,
            ]
        );

        FeatureFlag::updateOrCreate(
            ['feature_key' => 'recurring_orders'],
            [
                'name' => 'Recurring Buy/Sell Orders',
                'description' => 'Enable recurring buy/sell orders',
                'is_enabled' => true,
            ]
        );
    }
}