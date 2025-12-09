@extends('layouts.app')

@section('title', 'API Documentation')
@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h1 class="mb-0">Vidiaspot Marketplace API Documentation</h1>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        This documentation provides details about the APIs available for integrating with the Vidiaspot Marketplace platform.
                    </div>
                    
                    <h2>Base URL</h2>
                    <div class="mb-4">
                        <code>https://api.vidiaspot.marketplace/v1</code>
                        <p class="text-muted mt-2">All API endpoints are prefixed with this base URL.</p>
                    </div>
                    
                    <h2>Authentication</h2>
                    <div class="mb-4">
                        <p>Most endpoints require authentication using a Bearer token:</p>
                        <pre><code>Authorization: Bearer {your-api-token}</code></pre>
                        <p>You can obtain your API token by registering your application or using user credentials to generate a personal access token.</p>
                    </div>
                    
                    <h2>Response Format</h2>
                    <div class="mb-4">
                        <p>All API responses follow this format:</p>
                        <pre><code>{
    "success": true,
    "data": {
        // Response data
    },
    "message": "Status message",
    "errors": [] // Present only on failure
}</code></pre>
                    </div>
                    
                    <h2>Endpoints</h2>
                    
                    <div id="authentication-section">
                        <h3>Authentication</h3>
                        
                        <div class="api-endpoint mb-4">
                            <div class="method-get" style="background-color: #e3f2fd; padding: 15px; border-radius: 5px;">
                                <div class="d-flex">
                                    <span class="badge bg-primary me-2">POST</span>
                                    <strong>/api/login</strong>
                                </div>
                                <p class="mt-2">Authenticate user and get API token</p>
                                <div class="params">
                                    <h6>Parameters:</h6>
                                    <ul>
                                        <li><code>email</code> (string, required) - User email address</li>
                                        <li><code>password</code> (string, required) - User password</li>
                                        <li><code>remember</code> (boolean, optional) - Remember user session</li>
                                    </ul>
                                </div>
                                <div class="response">
                                    <h6>Response:</h6>
                                    <pre><code>{
    "success": true,
    "data": {
        "user": { /* user object */ },
        "token": "api-token-string"
    },
    "message": "Login successful"
}</code></pre>
                                </div>
                            </div>
                        </div>
                        
                        <div class="api-endpoint mb-4">
                            <div class="method-post" style="background-color: #e8f5e9; padding: 15px; border-radius: 5px;">
                                <div class="d-flex">
                                    <span class="badge bg-success me-2">POST</span>
                                    <strong>/api/register</strong>
                                </div>
                                <p class="mt-2">Register a new user account</p>
                                <div class="params">
                                    <h6>Parameters:</h6>
                                    <ul>
                                        <li><code>name</code> (string, required) - User full name</li>
                                        <li><code>email</code> (string, required) - User email address</li>
                                        <li><code>password</code> (string, required) - User password (min 8 chars)</li>
                                        <li><code>password_confirmation</code> (string, required) - Password confirmation</li>
                                    </ul>
                                </div>
                                <div class="response">
                                    <h6>Response:</h6>
                                    <pre><code>{
    "success": true,
    "data": {
        "user": { /* user object */ },
        "token": "api-token-string"
    },
    "message": "Registration successful"
}</code></pre>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div id="ads-section">
                        <h3>Ads Management</h3>
                        
                        <div class="api-endpoint mb-4">
                            <div class="method-get" style="background-color: #e3f2fd; padding: 15px; border-radius: 5px;">
                                <div class="d-flex">
                                    <span class="badge bg-primary me-2">GET</span>
                                    <strong>/api/ads</strong>
                                </div>
                                <p class="mt-2">Get all ads with optional filters</p>
                                <div class="params">
                                    <h6>Query Parameters:</h6>
                                    <ul>
                                        <li><code>category_id</code> (integer, optional) - Filter by category</li>
                                        <li><code>location</code> (string, optional) - Filter by location</li>
                                        <li><code>min_price</code> (numeric, optional) - Minimum price</li>
                                        <li><code>max_price</code> (numeric, optional) - Maximum price</li>
                                        <li><code>search</code> (string, optional) - Search term</li>
                                        <li><code>page</code> (integer, optional) - Page number for pagination</li>
                                    </ul>
                                </div>
                                <div class="response">
                                    <h6>Response:</h6>
                                    <pre><code>{
    "success": true,
    "data": {
        "ads": [ /* array of ad objects */ ],
        "pagination": { /* pagination metadata */ }
    }
}</code></pre>
                                </div>
                            </div>
                        </div>
                        
                        <div class="api-endpoint mb-4">
                            <div class="method-post" style="background-color: #e8f5e9; padding: 15px; border-radius: 5px;">
                                <div class="d-flex">
                                    <span class="badge bg-success me-2">POST</span>
                                    <strong>/api/ads</strong>
                                </div>
                                <p class="mt-2">Create a new ad</p>
                                <div class="params">
                                    <h6>Parameters:</h6>
                                    <ul>
                                        <li><code>title</code> (string, required) - Ad title</li>
                                        <li><code>description</code> (text, required) - Ad description</li>
                                        <li><code>price</code> (numeric, required) - Price of item</li>
                                        <li><code>category_id</code> (integer, required) - Category ID</li>
                                        <li><code>location</code> (string, required) - Location of item</li>
                                        <li><code>condition</code> (string, optional) - Condition of item (new, used, etc.)</li>
                                        <li><code>negotiable</code> (boolean, optional) - Whether price is negotiable</li>
                                    </ul>
                                </div>
                                <div class="response">
                                    <h6>Response:</h6>
                                    <pre><code>{
    "success": true,
    "data": {
        "ad": { /* created ad object */ }
    }
}</code></pre>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div id="advanced-payments-section">
                        <h3>Advanced Payment Solutions</h3>
                        
                        <div class="api-endpoint mb-4">
                            <div class="method-get" style="background-color: #e3f2fd; padding: 15px; border-radius: 5px;">
                                <div class="d-flex">
                                    <span class="badge bg-primary me-2">GET</span>
                                    <strong>/api/advanced-payments/methods</strong>
                                </div>
                                <p class="mt-2">Get user's payment methods</p>
                                <div class="response">
                                    <h6>Requires Authentication</h6>
                                    <pre><code>{
    "success": true,
    "data": {
        "payment_methods": [ /* array of payment method objects */ ]
    }
}</code></pre>
                                </div>
                            </div>
                        </div>
                        
                        <div class="api-endpoint mb-4">
                            <div class="method-post" style="background-color: #e8f5e9; padding: 15px; border-radius: 5px;">
                                <div class="d-flex">
                                    <span class="badge bg-success me-2">POST</span>
                                    <strong>/api/advanced-payments/methods</strong>
                                </div>
                                <p class="mt-2">Add a new payment method</p>
                                <div class="params">
                                    <h6>Parameters:</h6>
                                    <ul>
                                        <li><code>type</code> (string, required) - Payment method type (credit_card, crypto, etc.)</li>
                                        <li><code>name</code> (string, required) - Name for this payment method</li>
                                        <li><code>provider</code> (string, required) - Provider name</li>
                                        <li><code>identifier</code> (string, required) - Payment identifier</li>
                                        <li><code>is_default</code> (boolean, optional) - Set as default payment method</li>
                                    </ul>
                                </div>
                                <div class="response">
                                    <h6>Response:</h6>
                                    <pre><code>{
    "success": true,
    "data": {
        "payment_method": { /* created payment method object */ }
    }
}</code></pre>
                                </div>
                            </div>
                        </div>
                        
                        <div class="api-endpoint mb-4">
                            <div class="method-post" style="background-color: #fff3e0; padding: 15px; border-radius: 5px;">
                                <div class="d-flex">
                                    <span class="badge bg-warning me-2">POST</span>
                                    <strong>/api/advanced-payments/cryptocurrency</strong>
                                </div>
                                <p class="mt-2">Process cryptocurrency payment</p>
                                <div class="params">
                                    <h6>Parameters:</h6>
                                    <ul>
                                        <li><code>transaction_id</code> (integer, required) - Payment transaction ID</li>
                                        <li><code>currency</code> (string, required) - Cryptocurrency (BTC, ETH, etc.)</li>
                                        <li><code>wallet_address</code> (string, required) - Wallet address for payment</li>
                                        <li><code>amount_crypto</code> (numeric, required) - Amount in cryptocurrency</li>
                                        <li><code>amount_ngn</code> (numeric, required) - Equivalent amount in naira</li>
                                        <li><code>exchange_rate</code> (numeric, required) - Exchange rate used</li>
                                    </ul>
                                </div>
                                <div class="response">
                                    <h6>Response:</h6>
                                    <pre><code>{
    "success": true,
    "data": {
        "crypto_payment": { /* created crypto payment object */ }
    }
}</code></pre>
                                </div>
                            </div>
                        </div>
                        
                        <div class="api-endpoint mb-4">
                            <div class="method-get" style="background-color: #e3f2fd; padding: 15px; border-radius: 5px;">
                                <div class="d-flex">
                                    <span class="badge bg-primary me-2">GET</span>
                                    <strong>/api/advanced-payments/cryptocurrency/supported</strong>
                                </div>
                                <p class="mt-2">Get supported cryptocurrencies</p>
                                <div class="response">
                                    <h6>Response:</h6>
                                    <pre><code>{
    "success": true,
    "data": {
        "currencies": [ /* array of supported cryptocurrency codes */ ]
    }
}</code></pre>
                                </div>
                            </div>
                        </div>
                        
                        <div class="api-endpoint mb-4">
                            <div class="method-post" style="background-color: #f3e5f5; padding: 15px; border-radius: 5px;">
                                <div class="d-flex">
                                    <span class="badge bg-purple me-2">POST</span>
                                    <strong>/api/advanced-payments/bnpl</strong>
                                </div>
                                <p class="mt-2">Process Buy Now Pay Later payment</p>
                                <div class="params">
                                    <h6>Parameters:</h6>
                                    <ul>
                                        <li><code>ad_id</code> (integer, required) - Ad ID for this payment</li>
                                        <li><code>transaction_id</code> (integer, required) - Payment transaction ID</li>
                                        <li><code>provider</code> (string, required) - BNPL provider (klarna, afterpay, etc.)</li>
                                        <li><code>total_amount</code> (numeric, required) - Total purchase amount</li>
                                        <li><code>down_payment</code> (numeric, optional) - Down payment amount</li>
                                        <li><code>installment_count</code> (integer, required) - Number of installments</li>
                                        <li><code>installment_amount</code> (numeric, required) - Amount per installment</li>
                                        <li><code>frequency</code> (string, required) - Payment frequency (week, month)</li>
                                        <li><code>first_payment_date</code> (date, required) - Date of first installment</li>
                                        <li><code>apr_rate</code> (numeric, optional) - Annual percentage rate</li>
                                    </ul>
                                </div>
                                <div class="response">
                                    <h6>Response:</h6>
                                    <pre><code>{
    "success": true,
    "data": {
        "bnpl": { /* created BNPL object */ }
    }
}</code></pre>
                                </div>
                            </div>
                        </div>
                        
                        <div class="api-endpoint mb-4">
                            <div class="method-post" style="background-color: #e1f5fe; padding: 15px; border-radius: 5px;">
                                <div class="d-flex">
                                    <span class="badge bg-info me-2">POST</span>
                                    <strong>/api/advanced-payments/split</strong>
                                </div>
                                <p class="mt-2">Process split payment for group</p>
                                <div class="params">
                                    <h6>Parameters:</h6>
                                    <ul>
                                        <li><code>ad_id</code> (integer, required) - Ad ID for this payment</li>
                                        <li><code>transaction_id</code> (integer, required) - Payment transaction ID</li>
                                        <li><code>total_amount</code> (numeric, required) - Total amount to be split</li>
                                        <li><code>title</code> (string, required) - Title for the split payment</li>
                                        <li><code>participant_count</code> (integer, required) - Number of participants</li>
                                        <li><code>expires_in_days</code> (integer, optional) - Days until split payment expires</li>
                                    </ul>
                                </div>
                                <div class="response">
                                    <h6>Response:</h6>
                                    <pre><code>{
    "success": true,
    "data": {
        "split_payment": { /* created split payment object */ }
    }
}</code></pre>
                                </div>
                            </div>
                        </div>
                        
                        <div class="api-endpoint mb-4">
                            <div class="method-post" style="background-color: #e8f5e9; padding: 15px; border-radius: 5px;">
                                <div class="d-flex">
                                    <span class="badge bg-success me-2">POST</span>
                                    <strong>/api/advanced-payments/split/{id}/join</strong>
                                </div>
                                <p class="mt-2">Join a split payment</p>
                                <div class="params">
                                    <h6>Parameters:</h6>
                                    <ul>
                                        <li><code>amount</code> (numeric, required) - Amount to contribute</li>
                                    </ul>
                                </div>
                                <div class="response">
                                    <h6>Response:</h6>
                                    <pre><code>{
    "success": true,
    "data": {
        "split_payment": { /* updated split payment object */ }
    }
}</code></pre>
                                </div>
                            </div>
                        </div>
                        
                        <div class="api-endpoint mb-4">
                            <div class="method-post" style="background-color: #fff3e0; padding: 15px; border-radius: 5px;">
                                <div class="d-flex">
                                    <span class="badge bg-warning me-2">POST</span>
                                    <strong>/api/advanced-payments/insurance</strong>
                                </div>
                                <p class="mt-2">Process insurance for an item</p>
                                <div class="params">
                                    <h6>Parameters:</h6>
                                    <ul>
                                        <li><code>ad_id</code> (integer, required) - Ad ID for this insurance</li>
                                        <li><code>transaction_id</code> (integer, required) - Payment transaction ID</li>
                                        <li><code>type</code> (string, required) - Insurance type (device_protection, product_insurance, etc.)</li>
                                        <li><code>provider</code> (string, required) - Insurance provider</li>
                                        <li><code>premium_amount</code> (numeric, required) - Insurance premium amount</li>
                                        <li><code>coverage_amount</code> (numeric, required) - Maximum coverage amount</li>
                                        <li><code>risk_level</code> (string, required) - Risk level (low, medium, high)</li>
                                        <li><code>effective_from</code> (date, required) - Coverage start date</li>
                                        <li><code>effective_until</code> (date, required) - Coverage end date</li>
                                    </ul>
                                </div>
                                <div class="response">
                                    <h6>Response:</h6>
                                    <pre><code>{
    "success": true,
    "data": {
        "insurance": { /* created insurance object */ }
    }
}</code></pre>
                                </div>
                            </div>
                        </div>
                        
                        <div class="api-endpoint mb-4">
                            <div class="method-post" style="background-color: #f3e5f5; padding: 15px; border-radius: 5px;">
                                <div class="d-flex">
                                    <span class="badge bg-purple me-2">POST</span>
                                    <strong>/api/advanced-payments/tax/calculate</strong>
                                </div>
                                <p class="mt-2">Calculate tax based on location</p>
                                <div class="params">
                                    <h6>Parameters:</h6>
                                    <ul>
                                        <li><code>amount</code> (numeric, required) - Base amount for tax calculation</li>
                                        <li><code>location</code> (string, required) - Location to calculate tax for</li>
                                    </ul>
                                </div>
                                <div class="response">
                                    <h6>Response:</h6>
                                    <pre><code>{
    "success": true,
    "data": {
        "tax_calculation": { /* tax calculation results */ }
    }
}</code></pre>
                                </div>
                            </div>
                        </div>
                        
                        <div class="api-endpoint mb-4">
                            <div class="method-post" style="background-color: #e1f5fe; padding: 15px; border-radius: 5px;">
                                <div class="d-flex">
                                    <span class="badge bg-info me-2">POST</span>
                                    <strong>/api/advanced-payments/mobile-money</strong>
                                </div>
                                <p class="mt-2">Process mobile money payment</p>
                                <div class="params">
                                    <h6>Parameters:</h6>
                                    <ul>
                                        <li><code>provider</code> (string, required) - Mobile money provider (mpesa, mtn, etc.)</li>
                                        <li><code>amount</code> (numeric, required) - Amount to pay</li>
                                        <li><code>receiver_phone</code> (string, required) - Receiver phone number</li>
                                        <li><code>reference</code> (string, required) - Transaction reference</li>
                                    </ul>
                                </div>
                                <div class="response">
                                    <h6>Response:</h6>
                                    <pre><code>{
    "success": true,
    "data": {
        "result": { /* payment result information */ }
    }
}</code></pre>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div id="personalization-section">
                        <h3>Personalization</h3>
                        
                        <div class="api-endpoint mb-4">
                            <div class="method-get" style="background-color: #e3f2fd; padding: 15px; border-radius: 5px;">
                                <div class="d-flex">
                                    <span class="badge bg-primary me-2">GET</span>
                                    <strong>/api/personalization/feed</strong>
                                </div>
                                <p class="mt-2">Get personalized home feed</p>
                                <div class="params">
                                    <h6>Query Parameters:</h6>
                                    <ul>
                                        <li><code>mood</code> (string, optional) - Current mood (affects recommendations)</li>
                                    </ul>
                                </div>
                                <div class="response">
                                    <h6>Response:</h6>
                                    <pre><code>{
    "success": true,
    "data": {
        "recommendations": [ /* array of personalized ad recommendations */ ],
        "mood": "string - Current mood setting",
        "preferences": { /* user preferences object */ }
    }
}</code></pre>
                                </div>
                            </div>
                        </div>
                        
                        <div class="api-endpoint mb-4">
                            <div class="method-put" style="background-color: #e8f5e9; padding: 15px; border-radius: 5px;">
                                <div class="d-flex">
                                    <span class="badge bg-success me-2">PUT</span>
                                    <strong>/api/personalization/preferences</strong>
                                </div>
                                <p class="mt-2">Update user preferences</p>
                                <div class="params">
                                    <h6>Parameters:</h6>
                                    <ul>
                                        <li><code>theme</code> (string, optional) - UI theme (light, dark, auto)</li>
                                        <li><code>layout</code> (string, optional) - Layout preference (default, compact, card)</li>
                                        <li><code>preferred_categories</code> (array, optional) - Preferred ad categories</li>
                                        <li><code>preferred_locations</code> (array, optional) - Preferred locations</li>
                                        <li><code>price_range</code> (array, optional) - Preferred price range</li>
                                        <li><code>mood_state</code> (string, optional) - Mood state</li>
                                        <li><code>notification_preferences</code> (object, optional) - Notification settings</li>
                                    </ul>
                                </div>
                                <div class="response">
                                    <h6>Response:</h6>
                                    <pre><code>{
    "success": true,
    "message": "string - Status message"
}</code></pre>
                                </div>
                            </div>
                        </div>
                        
                        <div class="api-endpoint mb-4">
                            <div class="method-post" style="background-color: #fff3e0; padding: 15px; border-radius: 5px;">
                                <div class="d-flex">
                                    <span class="badge bg-warning me-2">POST</span>
                                    <strong>/api/personalization/behavior</strong>
                                </div>
                                <p class="mt-2">Track user behavior for personalization</p>
                                <div class="params">
                                    <h6>Parameters:</h6>
                                    <ul>
                                        <li><code>behavior_type</code> (string, required) - Type of behavior (view, click, search)</li>
                                        <li><code>target_type</code> (string, required) - Type of target (ad, category, user)</li>
                                        <li><code>target_id</code> (integer, required) - ID of the target</li>
                                        <li><code>metadata</code> (object, optional) - Additional behavior metadata</li>
                                    </ul>
                                </div>
                                <div class="response">
                                    <h6>Response:</h6>
                                    <pre><code>{
    "success": true,
    "message": "string - Status message"
}</code></pre>
                                </div>
                            </div>
                        </div>
                        
                        <div class="api-endpoint mb-4">
                            <div class="method-get" style="background-color: #e3f2fd; padding: 15px; border-radius: 5px;">
                                <div class="d-flex">
                                    <span class="badge bg-primary me-2">GET</span>
                                    <strong>/api/personalization/preferences</strong>
                                </div>
                                <p class="mt-2">Get user preferences</p>
                                <div class="response">
                                    <h6>Response:</h6>
                                    <pre><code>{
    "success": true,
    "data": {
        "preferences": { /* user preferences object */ }
    }
}</code></pre>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-5">
                        <h3>Error Handling</h3>
                        <p>API errors follow this format:</p>
                        <pre><code>{
    "success": false,
    "message": "Error message",
    "errors": [
        "Field-specific error messages"
    ]
}</code></pre>
                    </div>
                    
                    <div class="mt-5">
                        <h3>Rate Limiting</h3>
                        <p>API requests are rate limited to prevent abuse. Default limits are:</p>
                        <ul>
                            <li>100 requests per minute per IP</li>
                            <li>1000 requests per hour per authenticated user</li>
                        </ul>
                        <p>You can check your rate limit status via response headers:</p>
                        <pre><code>X-RateLimit-Limit: 100
X-RateLimit-Remaining: 99
X-RateLimit-Reset: 1634567890</code></pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.method-get { background-color: #e3f2fd; }
.method-post { background-color: #e8f5e9; }
.method-put { background-color: #fff3e0; }
.method-delete { background-color: #ffebee; }
.method-patch { background-color: #f3e5f5; }
</style>
@endsection