<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\Store;
use App\Models\PaymentMethod;
use App\Models\Order;
use App\Models\InsurancePolicy;
use App\Models\SplitPayment;
use App\Models\CryptoPayment;
use App\Models\BnplApplication;
use App\Models\MobileMoneyPayment;

/**
 * Payment Methods API Controller
 * Handles cryptocurrency, BNPL, mobile money, QR code, split payments, and insurance
 */
class PaymentMethodsController extends Controller
{
    /**
     * Get supported cryptocurrencies
     */
    public function getSupportedCryptocurrencies()
    {
        $currencies = [
            ['id' => 'bitcoin', 'name' => 'Bitcoin', 'symbol' => 'BTC', 'enabled' => true],
            ['id' => 'ethereum', 'name' => 'Ethereum', 'symbol' => 'ETH', 'enabled' => true],
            ['id' => 'usd-coin', 'name' => 'USD Coin', 'symbol' => 'USDC', 'enabled' => true],
            ['id' => 'binance-coin', 'name' => 'Binance Coin', 'symbol' => 'BNB', 'enabled' => true],
            ['id' => 'solana', 'name' => 'Solana', 'symbol' => 'SOL', 'enabled' => true],
            ['id' => 'ripple', 'name' => 'Ripple', 'symbol' => 'XRP', 'enabled' => true],
            ['id' => 'cardano', 'name' => 'Cardano', 'symbol' => 'ADA', 'enabled' => true],
            ['id' => 'dogecoin', 'name' => 'Dogecoin', 'symbol' => 'DOGE', 'enabled' => true],
        ];

        return response()->json(['data' => $currencies], 200);
    }

    /**
     * Create cryptocurrency payment
     */
    public function createCryptoPayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'required|string|in:btc,eth,usdc,usdt,ada,sol,xrp,bnb,doge',
            'recipient' => 'required|string',
            'note' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();
        $walletAddress = $this->generateWalletAddress($request->currency);

        $cryptoPayment = CryptoPayment::create([
            'user_id' => $user->id,
            'amount' => $request->amount,
            'currency' => $request->currency,
            'recipient_address' => $request->recipient,
            'note' => $request->note ?? null,
            'wallet_address' => $walletAddress,
            'status' => 'pending',
            'reference' => uniqid('crypto_'),
        ]);

        // Generate QR code for payment
        $qrCode = $this->generateQRCode($cryptoPayment->wallet_address . '?amount=' . $request->amount);

        return response()->json([
            'data' => [
                'payment' => $cryptoPayment,
                'qr_code' => $qrCode,
                'instructions' => 'Send the specified amount to the wallet address above. Payment will be verified automatically.',
            ]
        ], 201);
    }

    /**
     * Verify cryptocurrency payment
     */
    public function verifyCryptoPayment($id)
    {
        $payment = CryptoPayment::findOrFail($id);

        // Verify payment on blockchain (simplified for example)
        $isPaid = $this->verifyOnBlockchain($payment->wallet_address, $payment->amount, $payment->currency);

        if ($isPaid) {
            $payment->update(['status' => 'completed']);
            return response()->json(['data' => $payment, 'verified' => true], 200);
        }

        return response()->json(['data' => $payment, 'verified' => false, 'message' => 'Payment not yet confirmed'], 200);
    }

    /**
     * Get buy-now-pay-later providers
     */
    public function getBnplProviders()
    {
        $providers = [
            [
                'id' => 'klarna',
                'name' => 'Klarna',
                'description' => 'Buy now, pay later with flexible payment plans',
                'countries' => ['US', 'UK', 'DE', 'FR', 'AU'],
                'interest_free_period' => '30 days',
                'max_amount' => 1000,
                'fees' => '1.99%',
            ],
            [
                'id' => 'afterpay',
                'name' => 'Afterpay',
                'description' => 'Pay in 4 interest-free installments',
                'countries' => ['US', 'AU', 'CA', 'UK'],
                'interest_free_period' => 'Bi-weekly payments',
                'max_amount' => 1500,
                'fees' => 'No interest if paid on time',
            ],
            [
                'id' => 'affirm',
                'name' => 'Affirm',
                'description' => 'Flexible payment solutions with transparent terms',
                'countries' => ['US', 'CA'],
                'interest_free_period' => 'Varies by plan',
                'max_amount' => 17500,
                'fees' => '0-30% APR depending on credit',
            ],
            [
                'id' => 'sezzle',
                'name' => 'Sezzle',
                'description' => 'Buy now, pay later with interest-free installments',
                'countries' => ['US', 'CA'],
                'interest_free_period' => 'Interest-free',
                'max_amount' => 2500,
                'fees' => 'Installment fees apply',
            ],
            [
                'id' => 'zip',
                'name' => 'Zip Co',
                'description' => 'Pay later at your own pace',
                'countries' => ['US', 'NZ', 'AU'],
                'interest_free_period' => '28 days interest-free',
                'max_amount' => 1000,
                'fees' => '2.99% fee',
            ],
        ];

        return response()->json(['data' => $providers], 200);
    }

    /**
     * Apply for buy-now-pay-later
     */
    public function applyForBnpl(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'provider' => 'required|in:klarna,afterpay,affirm,sezzle,zip',
            'income' => 'required|numeric|min:0',
            'employment_type' => 'required|string|in:full_time,part_time,self_employed,government,retired,student',
            'credit_score' => 'required|integer|min:300|max:850',
            'country' => 'required|string|size:2',
            'supporting_documents' => 'nullable|array',
            'supporting_documents.*' => 'file|mimes:pdf,jpg,png|max:5000',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Verify credit score and eligibility
        $isEligible = $this->checkBnplEligibility(
            $request->provider, 
            $request->credit_score, 
            $request->income,
            $request->country
        );

        if (!$isEligible) {
            return response()->json([
                'error' => 'Not eligible for ' . ucfirst($request->provider),
                'message' => 'Based on our assessment, you do not meet the eligibility criteria for this provider.'
            ], 400);
        }

        $application = BnplApplication::create([
            'user_id' => Auth::user()->id,
            'provider' => $request->provider,
            'income' => $request->income,
            'employment_type' => $request->employment_type,
            'credit_score' => $request->credit_score,
            'country' => $request->country,
            'status' => 'pending_review',
            'documents' => $request->supporting_documents ?? [],
        ]);

        // Simulate immediate approval for demo purposes
        $this->processBnplApplication($application);

        return response()->json(['data' => $application], 201);
    }

    /**
     * Get mobile money providers
     */
    public function getMobileMoneyProviders()
    {
        $providers = [
            [
                'id' => 'mpesa',
                'name' => 'M-Pesa',
                'region' => 'East Africa',
                'countries' => ['KE', 'TZ', 'UG', 'GH'],
                'default_number' => '+2547XXXXXXXX',
                'fees' => 'Varies by transaction amount',
                'max_transaction' => 150000,
            ],
            [
                'id' => 'mtn',
                'name' => 'MTN Mobile Money',
                'region' => 'West & Central Africa',
                'countries' => ['NG', 'GH', 'CM', 'CI'],
                'default_number' => '+234XXXXXXXXXX',
                'fees' => '0-2% depending on amount',
                'max_transaction' => 200000,
            ],
            [
                'id' => 'airtel',
                'name' => 'Airtel Money',
                'region' => 'Africa',
                'countries' => ['UG', 'ZW', 'CD', 'RW'],
                'default_number' => '+256XXXXXXXX',
                'fees' => '0.5-3% depending on amount',
                'max_transaction' => 100000,
            ],
            [
                'id' => 'orange',
                'name' => 'Orange Money',
                'region' => 'West Africa',
                'countries' => ['CI', 'SN', 'CM', 'ML'],
                'default_number' => '+225XXXXXXXX',
                'fees' => '1-2.5% depending on amount',
                'max_transaction' => 120000,
            ],
            [
                'id' => 'tigo',
                'name' => 'Tigo Pesa',
                'region' => 'East Africa',
                'countries' => ['TZ', 'CD'],
                'default_number' => '+255XXXXXXXX',
                'fees' => 'Varies by amount',
                'max_transaction' => 50000,
            ],
        ];

        return response()->json(['data' => $providers], 200);
    }

    /**
     * Create mobile money payment
     */
    public function createMobileMoneyPayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:0.01',
            'provider' => 'required|string|in:mpesa,mtn,airtel,orange,tigo',
            'mobile_number' => 'required|string|max:15',
            'reason' => 'nullable|string|max:200',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();

        $mobileMoneyPayment = MobileMoneyPayment::create([
            'user_id' => $user->id,
            'amount' => $request->amount,
            'provider' => $request->provider,
            'mobile_number' => $request->mobile_number,
            'reason' => $request->reason ?? null,
            'status' => 'pending',
            'reference' => uniqid('mm_'),
        ]);

        // Initiate payment with provider (simulated)
        $this->initiateMobileMoneyPayment($mobileMoneyPayment);

        return response()->json([
            'data' => $mobileMoneyPayment,
            'message' => 'Mobile money payment initiated. Please confirm the payment on your mobile device.'
        ], 201);
    }

    /**
     * Generate QR code payment
     */
    public function generateQRCodePayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:0.01',
            'recipient' => 'required|string',
            'currency' => 'required|string|max:3',
            'note' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();

        $qrCodePayment = new \stdClass(); // In a real app, this would be a model
        $qrCodePayment->id = uniqid('qrc_');
        $qrCodePayment->user_id = $user->id;
        $qrCodePayment->amount = $request->amount;
        $qrCodePayment->recipient = $request->recipient;
        $qrCodePayment->currency = $request->currency;
        $qrCodePayment->note = $request->note ?? null;
        $qrCodePayment->status = 'generated';
        $qrCodePayment->expires_at = now()->addMinutes(30);
        $qrCodePayment->reference = uniqid('qr_');

        // Generate QR code
        $qrData = json_encode([
            'reference' => $qrCodePayment->reference,
            'amount' => $qrCodePayment->amount,
            'currency' => $qrCodePayment->currency,
            'recipient' => $qrCodePayment->recipient,
            'user_id' => $qrCodePayment->user_id
        ]);

        $qrCode = $this->generateQRCode($qrData);

        return response()->json([
            'data' => $qrCodePayment,
            'qr_code' => $qrCode,
            'instructions' => 'Scan this QR code with your mobile payment app to complete the payment.'
        ], 201);
    }

    /**
     * Create split payment
     */
    public function createSplitPayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'total_amount' => 'required|numeric|min:0.01',
            'participants' => 'required|array|min:2',
            'participants.*.email' => 'required|email',
            'participants.*.amount' => 'required|numeric|min:0.01',
            'note' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();

        $splitPayment = SplitPayment::create([
            'initiator_id' => $user->id,
            'total_amount' => $request->total_amount,
            'participants' => $request->participants,
            'note' => $request->note ?? null,
            'status' => 'awaiting_payment',
            'reference' => uniqid('sp_'),
        ]);

        // Send notifications to participants
        foreach ($request->participants as $participant) {
            $this->sendSplitPaymentNotification($participant['email'], $splitPayment);
        }

        return response()->json([
            'data' => $splitPayment,
            'message' => 'Split payment created successfully. Participants have been notified.'
        ], 201);
    }

    /**
     * Get insurance options
     */
    public function getInsuranceOptions(Request $request)
    {
        $options = [
            [
                'id' => 'basic',
                'name' => 'Basic Protection',
                'description' => 'Covers loss, damage, and theft up to $1000',
                'cost' => '1.5% of item value',
                'coverage_points' => [
                    'Theft and loss protection',
                    'Damage during shipping',
                    '30-day coverage period',
                    'Online claim filing'
                ]
            ],
            [
                'id' => 'premium',
                'name' => 'Premium Protection',
                'description' => 'Extended coverage up to $5000 with faster claims',
                'cost' => '2.5% of item value',
                'coverage_points' => [
                    'Covers up to $5000',
                    'Damage, theft, and loss',
                    'Extended 60-day coverage',
                    'Priority claim processing',
                    'Partial coverage options'
                ]
            ],
            [
                'id' => 'comprehensive',
                'name' => 'Comprehensive Protection',
                'description' => 'Full coverage with extended protection and premium support',
                'cost' => '3.5% of item value',
                'coverage_points' => [
                    'Covers up to $10000',
                    'Full coverage for all risks',
                    '180-day protection',
                    'Dedicated support',
                    'Replacement guarantee',
                    'No deductibles'
                ]
            ]
        ];

        return response()->json(['data' => $options], 200);
    }

    /**
     * Purchase insurance
     */
    public function purchaseInsurance(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,id',
            'item_description' => 'required|string|max:500',
            'estimated_value' => 'required|numeric|min:1',
            'insurance_type' => 'required|in:basic,premium,comprehensive',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();

        $insurancePolicy = InsurancePolicy::create([
            'user_id' => $user->id,
            'order_id' => $request->order_id,
            'item_description' => $request->item_description,
            'coverage_amount' => $request->estimated_value,
            'premium' => $this->calculatePremium($request->estimated_value, $request->insurance_type),
            'type' => $request->insurance_type,
            'status' => 'active',
            'policy_number' => 'INS-' . strtoupper(substr(md5(uniqid()), 0, 8)),
            'start_date' => now(),
            'end_date' => now()->addDays($this->getCoverageDays($request->insurance_type)),
        ]);

        return response()->json([
            'data' => $insurancePolicy,
            'message' => 'Insurance purchased successfully. Your item is now protected.'
        ], 201);
    }

    /**
     * Helper method: Generate wallet address for crypto
     */
    private function generateWalletAddress($currency)
    {
        // In a real implementation, this would interface with a crypto wallet service
        $prefixes = [
            'btc' => 'bc1q',
            'eth' => '0x',
            'usdc' => '0x',
            'usdt' => '1',
            'ada' => 'addr1',
            'sol' => 'So111',
            'xrp' => 'r',
            'bnb' => 'bnb',
            'doge' => 'D'
        ];

        $prefix = $prefixes[$currency] ?? '1';
        $randomPart = bin2hex(random_bytes(20));

        return $prefix . substr($randomPart, 0, 32);
    }

    /**
     * Helper method: Generate QR code
     */
    private function generateQRCode($data)
    {
        // In a real implementation, this would generate an actual QR code
        return base64_encode($data); // Simulated QR code
    }

    /**
     * Helper method: Verify payment on blockchain
     */
    private function verifyOnBlockchain($address, $amount, $currency)
    {
        // In a real implementation, this would check the blockchain
        // Simulated for demo purposes
        return rand(0, 1) === 1; // Randomly return true/false for demo
    }

    /**
     * Helper method: Check BNPL eligibility
     */
    private function checkBnplEligibility($provider, $creditScore, $income, $country)
    {
        // Simulated eligibility check
        $requirements = [
            'klarna' => ['min_credit' => 600, 'min_income' => 1500, 'countries' => ['US', 'UK', 'DE', 'FR', 'AU']],
            'afterpay' => ['min_credit' => 620, 'min_income' => 1200, 'countries' => ['US', 'AU', 'CA', 'UK']],
            'affirm' => ['min_credit' => 550, 'min_income' => 1800, 'countries' => ['US', 'CA']],
            'sezzle' => ['min_credit' => 580, 'min_income' => 1600, 'countries' => ['US', 'CA']],
            'zip' => ['min_credit' => 600, 'min_income' => 1400, 'countries' => ['US', 'NZ', 'AU']]
        ];

        $req = $requirements[$provider] ?? $requirements['klarna'];

        return (
            $creditScore >= $req['min_credit'] &&
            $income >= $req['min_income'] &&
            in_array($country, $req['countries'])
        );
    }

    /**
     * Helper method: Process BNPL application
     */
    private function processBnplApplication($application)
    {
        // Simulate credit check and approval process
        $approvalProbability = 0.8; // 80% approval rate for demo
        
        if (rand(1, 100) <= ($approvalProbability * 100)) {
            $application->update([
                'status' => 'approved',
                'credit_limit' => $this->calculateCreditLimit($application->income),
                'approved_at' => now()
            ]);
        } else {
            $application->update(['status' => 'declined', 'declined_reason' => 'Credit risk assessment']);
        }
    }

    /**
     * Helper method: Calculate credit limit based on income
     */
    private function calculateCreditLimit($income)
    {
        // Simple calculation: 3-5x monthly income depending on credit score
        $multiplier = 3 + (min(850, max(300, $income)) - 300) / 550 * 2; // 3-5 multiplier based on credit score
        return min(10000, $income * $multiplier); // Max $10,000 limit
    }

    /**
     * Helper method: Initiate mobile money payment
     */
    private function initiateMobileMoneyPayment($payment)
    {
        // Simulate initiating payment with mobile money provider
        sleep(2); // Simulate API call delay

        // In a real implementation, this would make an API call to the mobile money provider
        $payment->update(['status' => 'initiated']);
    }

    /**
     * Helper method: Send split payment notification
     */
    private function sendSplitPaymentNotification($email, $splitPayment)
    {
        // In a real implementation, this would send an email/SMS notification
        // Simulated for demo
        \Log::info("Split payment notification sent to {$email} for payment {$splitPayment->reference}");
    }

    /**
     * Helper method: Calculate insurance premium
     */
    private function calculatePremium($value, $type)
    {
        $rates = [
            'basic' => 0.015,    // 1.5%
            'premium' => 0.025,  // 2.5%
            'comprehensive' => 0.035 // 3.5%
        ];
        
        return $value * ($rates[$type] ?? $rates['basic']);
    }

    /**
     * Helper method: Get coverage days based on type
     */
    private function getCoverageDays($type)
    {
        $days = [
            'basic' => 30,
            'premium' => 60,
            'comprehensive' => 180
        ];
        
        return $days[$type] ?? $days['basic'];
    }
}