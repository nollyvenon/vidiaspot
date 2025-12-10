<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ApiDocumentationController extends Controller
{
    public function index()
    {
        return view('api.documentation');
    }

    public function endpoints()
    {
        $endpoints = [
            // Authentication endpoints
            [
                'method' => 'POST',
                'uri' => '/api/login',
                'description' => 'Authenticate user and get API token',
                'parameters' => [
                    'email' => 'string, required - User email address',
                    'password' => 'string, required - User password',
                    'remember' => 'boolean, optional - Remember user session'
                ],
                'response' => [
                    'success' => 'boolean',
                    'data' => [
                        'user' => 'object - User information',
                        'token' => 'string - API token for authentication'
                    ],
                    'message' => 'string - Status message'
                ]
            ],
            [
                'method' => 'POST',
                'uri' => '/api/register',
                'description' => 'Register a new user account',
                'parameters' => [
                    'name' => 'string, required - User full name',
                    'email' => 'string, required - User email address',
                    'password' => 'string, required - User password (min 8 chars)',
                    'password_confirmation' => 'string, required - Password confirmation'
                ],
                'response' => [
                    'success' => 'boolean',
                    'data' => [
                        'user' => 'object - New user information',
                        'token' => 'string - API token for authentication'
                    ],
                    'message' => 'string - Status message'
                ]
            ],
            
            // Ad endpoints
            [
                'method' => 'GET',
                'uri' => '/api/ads',
                'description' => 'Get all ads with optional filters',
                'parameters' => [
                    'category_id' => 'integer, optional - Filter by category',
                    'location' => 'string, optional - Filter by location',
                    'min_price' => 'numeric, optional - Minimum price',
                    'max_price' => 'numeric, optional - Maximum price',
                    'search' => 'string, optional - Search term',
                    'page' => 'integer, optional - Page number for pagination'
                ],
                'response' => [
                    'success' => 'boolean',
                    'data' => [
                        'ads' => 'array of ad objects',
                        'pagination' => 'pagination metadata'
                    ]
                ]
            ],
            [
                'method' => 'POST',
                'uri' => '/api/ads',
                'description' => 'Create a new ad',
                'requires_auth' => true,
                'parameters' => [
                    'title' => 'string, required - Ad title',
                    'description' => 'text, required - Ad description',
                    'price' => 'numeric, required - Price of item',
                    'category_id' => 'integer, required - Category ID',
                    'location' => 'string, required - Location of item',
                    'condition' => 'string, optional - Condition of item (new, used, etc.)',
                    'negotiable' => 'boolean, optional - Whether price is negotiable'
                ],
                'response' => [
                    'success' => 'boolean',
                    'data' => [
                        'ad' => 'object - Created ad information'
                    ]
                ]
            ],
            
            // Advanced Payment Solutions
            [
                'method' => 'GET',
                'uri' => '/api/advanced-payments/methods',
                'description' => 'Get user\'s payment methods',
                'requires_auth' => true,
                'response' => [
                    'success' => 'boolean',
                    'data' => [
                        'payment_methods' => 'array of payment method objects'
                    ]
                ]
            ],
            [
                'method' => 'POST',
                'uri' => '/api/advanced-payments/methods',
                'description' => 'Add a new payment method',
                'requires_auth' => true,
                'parameters' => [
                    'type' => 'string, required - Payment method type (credit_card, crypto, etc.)',
                    'name' => 'string, required - Name for this payment method',
                    'provider' => 'string, required - Provider name',
                    'identifier' => 'string, required - Payment identifier',
                    'is_default' => 'boolean, optional - Set as default payment method'
                ],
                'response' => [
                    'success' => 'boolean',
                    'data' => [
                        'payment_method' => 'object - Created payment method'
                    ]
                ]
            ],
            [
                'method' => 'POST',
                'uri' => '/api/advanced-payments/cryptocurrency',
                'description' => 'Process cryptocurrency payment',
                'requires_auth' => true,
                'parameters' => [
                    'transaction_id' => 'integer, required - Payment transaction ID',
                    'currency' => 'string, required - Cryptocurrency (BTC, ETH, etc.)',
                    'wallet_address' => 'string, required - Wallet address for payment',
                    'amount_crypto' => 'numeric, required - Amount in cryptocurrency',
                    'amount_ngn' => 'numeric, required - Equivalent amount in naira',
                    'exchange_rate' => 'numeric, required - Exchange rate used'
                ],
                'response' => [
                    'success' => 'boolean',
                    'data' => [
                        'crypto_payment' => 'object - Created crypto payment object'
                    ]
                ]
            ],
            [
                'method' => 'GET',
                'uri' => '/api/advanced-payments/cryptocurrency/supported',
                'description' => 'Get supported cryptocurrencies',
                'requires_auth' => true,
                'response' => [
                    'success' => 'boolean',
                    'data' => [
                        'currencies' => 'array of supported cryptocurrency codes'
                    ]
                ]
            ],
            [
                'method' => 'POST',
                'uri' => '/api/advanced-payments/bnpl',
                'description' => 'Process Buy Now Pay Later payment',
                'requires_auth' => true,
                'parameters' => [
                    'ad_id' => 'integer, required - Ad ID for this payment',
                    'transaction_id' => 'integer, required - Payment transaction ID',
                    'provider' => 'string, required - BNPL provider (klarna, afterpay, etc.)',
                    'total_amount' => 'numeric, required - Total purchase amount',
                    'down_payment' => 'numeric, optional - Down payment amount',
                    'installment_count' => 'integer, required - Number of installments',
                    'installment_amount' => 'numeric, required - Amount per installment',
                    'frequency' => 'string, required - Payment frequency (week, month)',
                    'first_payment_date' => 'date, required - Date of first installment',
                    'apr_rate' => 'numeric, optional - Annual percentage rate'
                ],
                'response' => [
                    'success' => 'boolean',
                    'data' => [
                        'bnpl' => 'object - Created BNPL object'
                    ]
                ]
            ],
            [
                'method' => 'POST',
                'uri' => '/api/advanced-payments/split',
                'description' => 'Process split payment for group',
                'requires_auth' => true,
                'parameters' => [
                    'ad_id' => 'integer, required - Ad ID for this payment',
                    'transaction_id' => 'integer, required - Payment transaction ID',
                    'total_amount' => 'numeric, required - Total amount to be split',
                    'title' => 'string, required - Title for the split payment',
                    'participant_count' => 'integer, required - Number of participants',
                    'expires_in_days' => 'integer, optional - Days until split payment expires'
                ],
                'response' => [
                    'success' => 'boolean',
                    'data' => [
                        'split_payment' => 'object - Created split payment object'
                    ]
                ]
            ],
            [
                'method' => 'POST',
                'uri' => '/api/advanced-payments/split/{id}/join',
                'description' => 'Join a split payment',
                'requires_auth' => true,
                'parameters' => [
                    'amount' => 'numeric, required - Amount to contribute'
                ],
                'response' => [
                    'success' => 'boolean',
                    'data' => [
                        'split_payment' => 'object - Updated split payment object'
                    ]
                ]
            ],
            [
                'method' => 'POST',
                'uri' => '/api/advanced-payments/insurance',
                'description' => 'Process insurance for an item',
                'requires_auth' => true,
                'parameters' => [
                    'ad_id' => 'integer, required - Ad ID for this insurance',
                    'transaction_id' => 'integer, required - Payment transaction ID',
                    'type' => 'string, required - Insurance type (device_protection, product_insurance, etc.)',
                    'provider' => 'string, required - Insurance provider',
                    'premium_amount' => 'numeric, required - Insurance premium amount',
                    'coverage_amount' => 'numeric, required - Maximum coverage amount',
                    'risk_level' => 'string, required - Risk level (low, medium, high)',
                    'effective_from' => 'date, required - Coverage start date',
                    'effective_until' => 'date, required - Coverage end date'
                ],
                'response' => [
                    'success' => 'boolean',
                    'data' => [
                        'insurance' => 'object - Created insurance object'
                    ]
                ]
            ],
            [
                'method' => 'POST',
                'uri' => '/api/advanced-payments/tax/calculate',
                'description' => 'Calculate tax based on location',
                'requires_auth' => true,
                'parameters' => [
                    'amount' => 'numeric, required - Base amount for tax calculation',
                    'location' => 'string, required - Location to calculate tax for'
                ],
                'response' => [
                    'success' => 'boolean',
                    'data' => [
                        'tax_calculation' => 'object - Tax calculation results'
                    ]
                ]
            ],
            [
                'method' => 'POST',
                'uri' => '/api/advanced-payments/mobile-money',
                'description' => 'Process mobile money payment',
                'requires_auth' => true,
                'parameters' => [
                    'provider' => 'string, required - Mobile money provider (mpesa, mtn, etc.)',
                    'amount' => 'numeric, required - Amount to pay',
                    'receiver_phone' => 'string, required - Receiver phone number',
                    'reference' => 'string, required - Transaction reference'
                ],
                'response' => [
                    'success' => 'boolean',
                    'data' => [
                        'result' => 'object - Payment result information'
                    ]
                ]
            ],
            
            // Personalization endpoints
            [
                'method' => 'GET',
                'uri' => '/api/personalization/feed',
                'description' => 'Get personalized home feed',
                'requires_auth' => true,
                'parameters' => [
                    'mood' => 'string, optional - Current mood (affects recommendations)'
                ],
                'response' => [
                    'success' => 'boolean',
                    'data' => [
                        'recommendations' => 'array of personalized ad recommendations',
                        'mood' => 'string - Current mood setting',
                        'preferences' => 'object - User preferences'
                    ]
                ]
            ],
            [
                'method' => 'PUT',
                'uri' => '/api/personalization/preferences',
                'description' => 'Update user preferences',
                'requires_auth' => true,
                'parameters' => [
                    'theme' => 'string, optional - UI theme (light, dark, auto)',
                    'layout' => 'string, optional - Layout preference (default, compact, card)',
                    'preferred_categories' => 'array, optional - Preferred ad categories',
                    'preferred_locations' => 'array, optional - Preferred locations',
                    'price_range' => 'array, optional - Preferred price range',
                    'mood_state' => 'string, optional - Mood state',
                    'notification_preferences' => 'object, optional - Notification settings'
                ],
                'response' => [
                    'success' => 'boolean',
                    'message' => 'string - Status message'
                ]
            ],
            [
                'method' => 'POST',
                'uri' => '/api/personalization/behavior',
                'description' => 'Track user behavior for personalization',
                'requires_auth' => true,
                'parameters' => [
                    'behavior_type' => 'string, required - Type of behavior (view, click, search)',
                    'target_type' => 'string, required - Type of target (ad, category, user)',
                    'target_id' => 'integer, required - ID of the target',
                    'metadata' => 'object, optional - Additional behavior metadata'
                ],
                'response' => [
                    'success' => 'boolean',
                    'message' => 'string - Status message'
                ]
            ],
            [
                'method' => 'GET',
                'uri' => '/api/personalization/preferences',
                'description' => 'Get user preferences',
                'requires_auth' => true,
                'response' => [
                    'success' => 'boolean',
                    'data' => [
                        'preferences' => 'object - User preferences'
                    ]
                ]
            ],
        ];

        return response()->json($endpoints);
    }
}