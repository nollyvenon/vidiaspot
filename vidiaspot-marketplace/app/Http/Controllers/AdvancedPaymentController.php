<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdvancedPaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Add a new payment method
     */
    public function addPaymentMethod(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'type' => 'required|in:credit_card,paypal,bitcoin,ethereum,mpesa,mobile_money,qr_code,klarna,afterpay',
            'name' => 'required|string|max:255',
            'provider' => 'required|string|max:50',
            'identifier' => 'required|string',
            'details' => 'array',
            'is_default' => 'boolean',
        ]);

        try {
            $paymentMethod = $this->paymentService->addPaymentMethod($user->id, $request->all());
            return response()->json([
                'success' => true,
                'payment_method' => $paymentMethod
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add payment method: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get user's payment methods
     */
    public function getUserPaymentMethods()
    {
        $user = Auth::user();
        try {
            $paymentMethods = $this->paymentService->getUserPaymentMethods($user->id);
            return response()->json([
                'success' => true,
                'payment_methods' => $paymentMethods
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve payment methods: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Set a payment method as default
     */
    public function setDefaultPaymentMethod(Request $request, $paymentMethodId)
    {
        $user = Auth::user();
        try {
            $this->paymentService->setDefaultPaymentMethod($user->id, $paymentMethodId);
            return response()->json([
                'success' => true,
                'message' => 'Default payment method updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update default payment method: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Process cryptocurrency payment
     */
    public function processCryptocurrencyPayment(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'transaction_id' => 'required|integer',
            'currency' => 'required|in:BTC,ETH,USDT,USDC,BNB,ADA,SOL,DOT,DOGE,LTC',
            'wallet_address' => 'required|string',
            'amount_crypto' => 'required|numeric',
            'amount_ngn' => 'required|numeric',
            'exchange_rate' => 'required|numeric',
        ]);

        try {
            $cryptoData = [
                'currency' => $request->currency,
                'wallet_address' => $request->wallet_address,
                'amount_crypto' => $request->amount_crypto,
                'amount_ngn' => $request->amount_ngn,
                'exchange_rate' => $request->exchange_rate,
            ];

            $cryptoPayment = $this->paymentService->processCryptocurrencyPayment(
                $user->id,
                $request->transaction_id,
                $cryptoData
            );

            return response()->json([
                'success' => true,
                'payment' => $cryptoPayment
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to process cryptocurrency payment: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Process Buy Now Pay Later payment
     */
    public function processBuyNowPayLater(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'ad_id' => 'required|integer|exists:ads,id',
            'transaction_id' => 'required|integer',
            'provider' => 'required|in:klarna,afterpay,paypal_credit',
            'total_amount' => 'required|numeric|min:0',
            'down_payment' => 'nullable|numeric|min:0',
            'installment_count' => 'required|integer|min:2|max:24',
            'installment_amount' => 'required|numeric|min:0',
            'frequency' => 'required|in:week,month',
            'first_payment_date' => 'required|date',
            'apr_rate' => 'nullable|numeric|min:0|max:100',
        ]);

        try {
            $bnplData = $request->only([
                'transaction_id',
                'provider',
                'total_amount',
                'down_payment',
                'installment_count',
                'installment_amount',
                'frequency',
                'first_payment_date',
                'apr_rate',
                'provider_details'
            ]);

            $bnpl = $this->paymentService->processBuyNowPayLater(
                $user->id,
                $request->ad_id,
                $bnplData
            );

            return response()->json([
                'success' => true,
                'bnpl' => $bnpl
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to process BNPL payment: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Process split payment
     */
    public function processSplitPayment(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'ad_id' => 'required|integer|exists:ads,id',
            'transaction_id' => 'required|integer',
            'total_amount' => 'required|numeric|min:0',
            'title' => 'required|string|max:255',
            'participant_count' => 'required|integer|min:2',
            'expires_in_days' => 'nullable|integer|min:1|max:365',
        ]);

        try {
            $splitData = $request->only([
                'transaction_id',
                'total_amount',
                'title',
                'description',
                'participant_count',
                'expires_in_days'
            ]);

            $splitPayment = $this->paymentService->processSplitPayment(
                $user->id,
                $request->ad_id,
                $splitData
            );

            return response()->json([
                'success' => true,
                'split_payment' => $splitPayment
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to process split payment: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Join a split payment
     */
    public function joinSplitPayment(Request $request, $splitPaymentId)
    {
        $user = Auth::user();
        $request->validate([
            'amount' => 'required|numeric|min:0',
        ]);

        try {
            $splitPayment = $this->paymentService->joinSplitPayment(
                $splitPaymentId,
                $user->id,
                $request->amount
            );

            return response()->json([
                'success' => true,
                'split_payment' => $splitPayment
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to join split payment: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Process insurance for an ad
     */
    public function processInsurance(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'ad_id' => 'required|integer|exists:ads,id',
            'transaction_id' => 'required|integer',
            'type' => 'required|in:device_protection,product_insurance,delivery_insurance,high_value_item',
            'provider' => 'required|string|max:100',
            'premium_amount' => 'required|numeric|min:0',
            'coverage_amount' => 'required|numeric|min:0',
            'risk_level' => 'required|in:low,medium,high',
            'effective_from' => 'required|date',
            'effective_until' => 'required|date|after:effective_from',
        ]);

        try {
            $insuranceData = $request->only([
                'type',
                'provider',
                'premium_amount',
                'coverage_amount',
                'risk_level',
                'effective_from',
                'effective_until',
                'policy_url',
                'terms'
            ]);

            $insurance = $this->paymentService->processInsurance(
                $user->id,
                $request->ad_id,
                $request->transaction_id,
                $insuranceData
            );

            return response()->json([
                'success' => true,
                'insurance' => $insurance
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to process insurance: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Calculate tax based on location
     */
    public function calculateTax(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'location' => 'required|string|max:100',
        ]);

        try {
            $taxCalculation = $this->paymentService->calculateTax(
                $request->amount,
                $request->location
            );

            return response()->json([
                'success' => true,
                'tax_calculation' => $taxCalculation
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to calculate tax: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Process mobile money payment
     */
    public function processMobileMoneyPayment(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'provider' => 'required|in:mpesa,mtn_mobile_money,airtel_money,glo_money',
            'amount' => 'required|numeric|min:0',
            'receiver_phone' => 'required|string',
            'reference' => 'required|string',
        ]);

        try {
            $paymentData = $request->only([
                'provider',
                'amount',
                'receiver_phone',
                'reference'
            ]);

            $result = $this->paymentService->processMobileMoneyPayment(
                $user->id,
                $paymentData
            );

            return response()->json([
                'success' => true,
                'result' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to process mobile money payment: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get supported cryptocurrencies
     */
    public function getSupportedCryptocurrencies()
    {
        try {
            $currencies = $this->paymentService->getSupportedCryptocurrencies();
            return response()->json([
                'success' => true,
                'currencies' => $currencies
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve cryptocurrencies: ' . $e->getMessage()
            ], 400);
        }
    }
}