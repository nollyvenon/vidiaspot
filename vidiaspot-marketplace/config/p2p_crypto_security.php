<?php

return [
    /*
    |--------------------------------------------------------------------------
    | P2P Crypto Marketplace Security Configuration
    |--------------------------------------------------------------------------
    |
    | This configuration file defines the security parameters for the P2P
    | crypto marketplace, including verification thresholds, risk management,
    | and security features.
    |
    */

    'verification' => [
        'enabled' => true,
        'required_for_trading' => true,
        'verification_thresholds' => [
            'level_1' => [  // Basic verification
                'daily_limit' => 1000,      // USD equivalent
                'requirements' => ['email', 'phone'],
            ],
            'level_2' => [  // Enhanced verification
                'daily_limit' => 10000,     // USD equivalent
                'requirements' => ['email', 'phone', 'id_document'],
            ],
            'level_3' => [  // Full verification
                'daily_limit' => 50000,     // USD equivalent
                'requirements' => ['email', 'phone', 'id_document', 'proof_of_address'],
            ],
        ],
    ],

    'kyc_aml' => [
        'provider' => env('KYC_PROVIDER', 'default'),  // Options: default, onfido, jumio, etc.
        'enabled' => true,
        'auto_approve_under' => env('KYC_AUTO_APPROVE_UNDER', 500), // USD equivalent
        'document_types' => [
            'passport',
            'driver_license',
            'national_id',
            'proof_of_address',
        ],
    ],

    'biometric' => [
        'enabled' => true,
        'providers' => [
            'face' => env('FACE_RECOGNITION_PROVIDER', 'aws_rekognition'),
            'fingerprint' => env('FINGERPRINT_PROVIDER', 'local'),
        ],
    ],

    'video_verification' => [
        'enabled' => env('VIDEO_VERIFICATION_ENABLED', true),
        'threshold_amount' => env('VIDEO_VERIFICATION_THRESHOLD', 25000), // USD equivalent
        'provider' => env('VIDEO_VERIFICATION_PROVIDER', 'twilio'),
    ],

    'reputation' => [
        'calculate_automatically' => true,
        'min_trades_for_trusted' => 10,
        'feedback_retention_days' => 365,
        'scoring_algorithm' => 'weighted_average',
    ],

    'escrow' => [
        'enabled' => true,
        'provider' => env('ESCROW_PROVIDER', 'smart_contract'), // Options: smart_contract, multi_sig, centralized
        'min_amount' => env('ESCROW_MIN_AMOUNT', 100), // USD equivalent
        'auto_release_hours' => env('ESCROW_AUTO_RELEASE_HOURS', 24),
    ],

    'dispute_resolution' => [
        'auto_resolve_days' => env('DISPUTE_AUTO_RESOLVE_DAYS', 7),
        'max_amount_for_direct_resolution' => env('DISPUTE_MAX_DIRECT_RESOLUTION', 5000),
        'resolution_methods' => [
            'mediation',
            'arbitration',
            'smart_contract',
        ],
    ],

    'insurance' => [
        'enabled' => env('CRYPTO_INSURANCE_ENABLED', true),
        'provider' => env('INSURANCE_PROVIDER', 'default'),
        'coverage_percentage' => env('INSURANCE_COVERAGE_PERCENTAGE', 90), // Percentage of funds covered
        'max_coverage_amount' => env('INSURANCE_MAX_COVERAGE', 100000), // USD equivalent per user
    ],

    'payment_methods' => [
        'enabled' => [
            'bank_transfer',
            'mobile_money',
            'cryptocurrency',
            'gift_card',
            'paypal',
            'venmo',
            'cash_app',
            'debit_credit_card',
            'cash_deposit',
            'peer_to_peer_cash',
        ],
        'mobile_money_providers' => [
            'm_pesa',
            'mtn',
            'airtel_money',
            'tigo_pesa',
            'orange_money',
        ],
        'bank_transfer_countries' => [
            'ng',  // Nigeria
            'ke',  // Kenya
            'za',  // South Africa
            'gh',  // Ghana
            'ug',  // Uganda
            'tz',  // Tanzania
            'us',  // USA
            'gb',  // UK
            'eu',  // European Union
        ],
    ],

    'security' => [
        'cold_wallet_percentage' => 95, // Percentage of funds in cold storage
        'multi_signature_threshold' => 2, // Out of 3 keys required
        'hsm_enabled' => true,
        'encryption_algorithm' => 'AES-256-GCM',
        'fraud_detection' => [
            'enabled' => true,
            'ai_provider' => env('FRAUD_AI_PROVIDER', 'ml_kit'),
            'suspicious_patterns' => [
                'unusual_volume',
                'rapid_deposits_withdrawals',
                'multiple_failed_attempts',
                'geographic_anomalies',
                'device_fingerprinting',
            ],
        ],
        'session_management' => [
            'max_concurrent_sessions' => 3,
            'session_timeout_minutes' => 120,
            'device_fingerprinting' => true,
        ],
        'two_factor_auth' => [
            'enabled' => true,
            'methods' => [
                'totp',      // Google Authenticator
                'sms',       // SMS codes
                'email',     // Email codes
                'backup_codes', // Backup recovery codes
            ],
            'required_for' => [
                'login',
                'withdrawals',
                'profile_changes',
                'large_transactions',
            ],
        ],
    ],

    'risk_management' => [
        'automatic_liquidation_enabled' => true,
        'position_sizing_enabled' => true,
        'negative_balance_protection' => true,
        'max_leverage' => 10, // 10x leverage
        'liquidation_threshold' => 5, // Percentage before liquidation
        'insurance_coverage' => true,
    ],

    'limits' => [
        'max_trading_pairs' => env('MAX_TRADING_PAIRS', 50),
        'max_open_orders' => env('MAX_OPEN_ORDERS', 100),
        'max_oco_orders' => env('MAX_OCO_ORDERS', 10),
        'max_grid_levels' => env('MAX_GRID_LEVELS', 50),
    ],
];