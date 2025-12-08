<?php

namespace App\Services;

use App\Models\PaymentMethod;
use App\Models\CryptocurrencyPayment;
use App\Models\BuyNowPayLater;
use App\Models\SplitPayment;
use App\Models\Insurance;
use App\Models\PaymentTransaction;
use App\Models\Ad;
use App\Models\User;
use App\Models\PaymentSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaymentService
{
    private $supportedCryptocurrencies = ['BTC', 'ETH', 'USDT', 'USDC', 'BNB', 'ADA', 'SOL', 'DOT', 'DOGE', 'LTC'];

    /**
     * Add a new payment method for a user
     */
    public function addPaymentMethod($userId, $methodData)
    {
        $paymentMethod = PaymentMethod::create([
            'user_id' => $userId,
            'method_type' => $methodData['type'],
            'method_name' => $methodData['name'],
            'provider' => $methodData['provider'] ?? null,
            'identifier' => $this->encryptPaymentIdentifier($methodData['identifier']),
            'details' => $methodData['details'] ?? [],
            'is_default' => $methodData['is_default'] ?? false,
            'is_active' => true,
        ]);

        // If this is set as default, reset others for this user
        if ($methodData['is_default']) {
            $this->setDefaultPaymentMethod($userId, $paymentMethod->id);
        }

        return $paymentMethod;
    }

    /**
     * Set a payment method as default
     */
    public function setDefaultPaymentMethod($userId, $paymentMethodId)
    {
        PaymentMethod::where('user_id', $userId)
            ->update(['is_default' => false]);

        PaymentMethod::where('id', $paymentMethodId)
            ->where('user_id', $userId)
            ->update(['is_default' => true]);
    }

    /**
     * Get user's payment methods
     */
    public function getUserPaymentMethods($userId)
    {
        return PaymentMethod::where('user_id', $userId)
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Process cryptocurrency payment
     */
    public function processCryptocurrencyPayment($userId, $transactionId, $cryptoData)
    {
        $cryptoPayment = CryptocurrencyPayment::create([
            'user_id' => $userId,
            'payment_transaction_id' => $transactionId,
            'crypto_currency' => strtoupper($cryptoData['currency']),
            'wallet_address' => $cryptoData['wallet_address'],
            'amount_crypto' => $cryptoData['amount_crypto'],
            'amount_ngn' => $cryptoData['amount_ngn'],
            'exchange_rate' => $cryptoData['exchange_rate'],
            'status' => 'pending',
            'expires_at' => now()->addHours(24), // Payment expires in 24 hours
        ]);

        // Generate QR code data for payment
        $qrData = $this->generateCryptoPaymentQR(
            $cryptoData['wallet_address'],
            $cryptoData['amount_crypto'],
            $cryptoData['currency']
        );

        $cryptoPayment->update([
            'raw_data' => [
                'qr_data' => $qrData,
                'payment_url' => $this->generateCryptoPaymentUrl($cryptoPayment)
            ]
        ]);

        return $cryptoPayment;
    }

    /**
     * Generate cryptocurrency payment QR code
     */
    private function generateCryptoPaymentQR($walletAddress, $amount, $currency)
    {
        $cryptoCode = strtoupper($currency);
        return "$cryptoCode:$walletAddress?amount=$amount"; // Format varies by crypto
    }

    /**
     * Generate cryptocurrency payment URL
     */
    private function generateCryptoPaymentUrl($cryptoPayment)
    {
        // In a real implementation, this would generate a payment URL
        return url("/payment/crypto/{$cryptoPayment->id}/payment");
    }

    /**
     * Process Buy Now Pay Later payment
     */
    public function processBuyNowPayLater($userId, $adId, $paymentData)
    {
        $bnpl = BuyNowPayLater::create([
            'user_id' => $userId,
            'ad_id' => $adId,
            'payment_transaction_id' => $paymentData['transaction_id'],
            'provider' => $paymentData['provider'],
            'total_amount' => $paymentData['total_amount'],
            'down_payment' => $paymentData['down_payment'] ?? 0,
            'installment_count' => $paymentData['installment_count'],
            'installment_amount' => $paymentData['installment_amount'],
            'frequency' => $paymentData['frequency'] ?? 'month',
            'status' => 'pending_approval',
            'first_payment_date' => $paymentData['first_payment_date'],
            'provider_details' => $paymentData['provider_details'] ?? [],
            'apr_rate' => $paymentData['apr_rate'] ?? 0,
        ]);

        // Generate payment schedule
        $schedule = $this->generatePaymentSchedule(
            $bnpl->first_payment_date,
            $bnpl->installment_amount,
            $bnpl->installment_count,
            $bnpl->frequency
        );

        $bnpl->update([
            'payment_schedule' => $schedule
        ]);

        return $bnpl;
    }

    /**
     * Generate installment payment schedule
     */
    private function generatePaymentSchedule($startDate, $installmentAmount, $installmentCount, $frequency)
    {
        $schedule = [];
        $currentDate = clone $startDate;

        for ($i = 1; $i <= $installmentCount; $i++) {
            $schedule[] = [
                'installment_number' => $i,
                'amount' => $installmentAmount,
                'due_date' => $currentDate->format('Y-m-d'),
                'status' => 'pending',
                'paid_at' => null,
                'payment_reference' => null,
            ];

            // Add interval based on frequency
            $currentInterval = $frequency === 'week' ? '7 days' : '1 month';
            $currentDate = date_create($currentDate->format('Y-m-d'))->modify("+$currentInterval");
        }

        return $schedule;
    }

    /**
     * Process split payment
     */
    public function processSplitPayment($userId, $adId, $splitData)
    {
        $splitPayment = SplitPayment::create([
            'user_id' => $userId,
            'ad_id' => $adId,
            'payment_transaction_id' => $splitData['transaction_id'],
            'total_amount' => $splitData['total_amount'],
            'amount_paid' => 0,
            'amount_remaining' => $splitData['total_amount'],
            'status' => 'active',
            'title' => $splitData['title'],
            'description' => $splitData['description'] ?? null,
            'participant_count' => $splitData['participant_count'],
            'expires_at' => now()->addDays($splitData['expires_in_days'] ?? 30),
            'participants' => [],
        ]);

        // Generate payment links for participants
        $this->generateParticipantPaymentLinks($splitPayment);

        return $splitPayment;
    }

    /**
     * Generate payment links for split payment participants
     */
    private function generateParticipantPaymentLinks($splitPayment)
    {
        $perPersonAmount = $splitPayment->total_amount / $splitPayment->participant_count;
        $links = [];

        for ($i = 1; $i <= $splitPayment->participant_count; $i++) {
            $links[] = [
                'participant_number' => $i,
                'amount' => $perPersonAmount,
                'payment_link' => url("/payment/split/{$splitPayment->id}/join/" . Str::random(32)),
                'status' => 'pending',
                'joined_at' => null,
            ];
        }

        $splitPayment->update([
            'payment_details' => ['links' => $links]
        ]);
    }

    /**
     * Join a split payment
     */
    public function joinSplitPayment($splitPaymentId, $userId, $amount)
    {
        $splitPayment = SplitPayment::findOrFail($splitPaymentId);

        // Check if there's still room for participants
        $participants = $splitPayment->participants ?: [];
        if (count($participants) >= $splitPayment->participant_count) {
            throw new \Exception('Split payment is full');
        }

        // Update participant list
        $participants[] = [
            'user_id' => $userId,
            'amount' => $amount,
            'status' => 'joined',
            'joined_at' => now(),
            'paid_at' => null,
        ];

        // Update amounts
        $amountPaid = $splitPayment->amount_paid + $amount;
        $amountRemaining = $splitPayment->amount_remaining - $amount;

        $splitPayment->update([
            'participants' => $participants,
            'amount_paid' => $amountPaid,
            'amount_remaining' => $amountRemaining,
            'status' => $amountRemaining <= 0 ? 'completed' : 'active',
        ]);

        return $splitPayment;
    }

    /**
     * Process insurance for an ad
     */
    public function processInsurance($userId, $adId, $paymentTransactionId, $insuranceData)
    {
        $insurance = Insurance::create([
            'user_id' => $userId,
            'ad_id' => $adId,
            'payment_transaction_id' => $paymentTransactionId,
            'insurance_type' => $insuranceData['type'],
            'provider' => $insuranceData['provider'],
            'policy_number' => $this->generatePolicyNumber(),
            'premium_amount' => $insuranceData['premium_amount'],
            'coverage_amount' => $insuranceData['coverage_amount'],
            'status' => 'active',
            'risk_level' => $insuranceData['risk_level'],
            'effective_from' => $insuranceData['effective_from'],
            'effective_until' => $insuranceData['effective_until'],
            'exclusions' => $insuranceData['exclusions'] ?? null,
            'beneficiaries' => $insuranceData['beneficiaries'] ?? [],
            'claim_process' => $insuranceData['claim_process'] ?? [],
            'documents' => ['policy_document' => $insuranceData['policy_url'] ?? null],
            'terms_and_conditions' => $insuranceData['terms'] ?? null,
        ]);

        return $insurance;
    }

    /**
     * Generate a policy number for insurance
     */
    private function generatePolicyNumber()
    {
        return 'VIDI' . date('Y') . strtoupper(Str::random(8));
    }

    /**
     * Calculate taxes based on location
     */
    public function calculateTax($amount, $location)
    {
        // Tax rates by Nigerian states (example)
        $taxRates = [
            'Lagos' => 0.075, // 7.5%
            'Abuja' => 0.05,  // 5%
            'Kano' => 0.05,   // 5%
            'Rivers' => 0.075, // 7.5%
            'Ogun' => 0.05,    // 5%
            'default' => 0.075, // Default 7.5% VAT
        ];

        $taxRate = $taxRates[$location] ?? $taxRates['default'];
        $taxAmount = $amount * $taxRate;

        return [
            'tax_rate' => $taxRate,
            'tax_amount' => $taxAmount,
            'total_with_tax' => $amount + $taxAmount,
            'breakdown' => [
                'original_amount' => $amount,
                'tax_rate' => $taxRate,
                'tax_percentage' => round($taxRate * 100, 2) . '%',
                'tax_amount' => $taxAmount,
                'total_with_tax' => $amount + $taxAmount,
            ]
        ];
    }

    /**
     * Process mobile money payment (M-Pesa, MTN, etc.)
     */
    public function processMobileMoneyPayment($userId, $paymentData)
    {
        $mobileMoneyProviders = [
            'mpesa' => [
                'endpoint' => env('MPESA_PAYMENT_URL'),
                'credentials' => [
                    'consumer_key' => env('MPESA_CONSUMER_KEY'),
                    'consumer_secret' => env('MPESA_CONSUMER_SECRET'),
                ]
            ],
            'mtn' => [
                'endpoint' => env('MTN_MOBILE_MONEY_URL'),
                'credentials' => [
                    'client_id' => env('MTN_CLIENT_ID'),
                    'client_secret' => env('MTN_CLIENT_SECRET'),
                ]
            ],
        ];

        $provider = $paymentData['provider'];
        if (!isset($mobileMoneyProviders[$provider])) {
            throw new \Exception("Unsupported mobile money provider: $provider");
        }

        // In a real implementation, this would call the provider's API
        // For now, we'll simulate the process
        $transactionResult = [
            'initiated' => true,
            'reference' => Str::random(16),
            'status' => 'pending',
            'message' => 'Payment initiated, awaiting confirmation',
        ];

        return $transactionResult;
    }

    /**
     * Get mood-based recommendations
     */
    public function getMoodBasedRecommendations($user, $mood, $limit = 10)
    {
        // This would use the user's behavior and preferences to generate recommendations
        // based on their current mood state
        $behaviorQuery = PaymentTransaction::where('user_id', $user->id)
            ->with(['ad', 'ad.category'])
            ->orderBy('created_at', 'desc')
            ->limit($limit);

        $transactions = $behaviorQuery->get();

        // Apply mood-based filtering
        $moodFilters = $this->getMoodFilters($mood);
        $filteredTransactions = $transactions->filter(function ($transaction) use ($moodFilters) {
            if (empty($moodFilters)) return true;
            
            return collect($moodFilters)->contains($transaction->ad->category->name ?? '');
        });

        return $filteredTransactions->take($limit);
    }

    /**
     * Get mood-based filters
     */
    private function getMoodFilters($mood)
    {
        $moodFilters = [
            'excited' => ['electronics', 'cars', 'luxury', 'entertainment'],
            'home' => ['furniture', 'appliances', 'home', 'garden'],
            'luxury' => ['luxury', 'cars', 'jewelry', 'fashion'],
            'practical' => ['tools', 'books', 'services', 'office'],
        ];

        return $moodFilters[$mood] ?? [];
    }

    /**
     * Track user behavior for personalization
     */
    public function trackBehavior($userId, $behaviorType, $targetType, $targetId, $metadata = [])
    {
        \App\Models\UserBehavior::create([
            'user_id' => $userId,
            'behavior_type' => $behaviorType,
            'target_type' => $targetType,
            'target_id' => $targetId,
            'metadata' => $metadata,
        ]);
    }

    /**
     * Encrypt payment identifier (simplified for demo)
     */
    private function encryptPaymentIdentifier($identifier)
    {
        // In a real implementation, use proper encryption
        return base64_encode($identifier . '|' . config('app.key')); // This is for demo only
    }

    /**
     * Get supported cryptocurrencies
     */
    public function getSupportedCryptocurrencies()
    {
        return $this->supportedCryptocurrencies;
    }

    /**
     * Get payment settings for a specific feature
     */
    public function getFeatureStatus($featureKey)
    {
        return PaymentSetting::getFeatureStatus($featureKey);
    }

    /**
     * Check if feature is available in a specific country
     */
    public function isFeatureAvailableInCountry($featureKey, $countryCode)
    {
        return PaymentSetting::isAvailableInCountry($featureKey, $countryCode);
    }

    /**
     * Get enabled features for a specific country
     */
    public function getEnabledFeaturesForCountry($countryCode)
    {
        return PaymentSetting::getEnabledFeaturesForCountry($countryCode);
    }
}