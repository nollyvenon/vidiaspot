<?php

namespace App\Services;

use App\Models\PaymentTransaction;
use Illuminate\Support\Str;
use Yabacon\Paystack;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;

class PaymentService
{
    protected $paystack;
    protected $flutterwave;

    public function __construct()
    {
        // Initialize Paystack
        $this->paystack = new Paystack(config('payment.paystack.secret_key'));

        // Initialize Flutterwave (we'll use their REST API)
        $this->flutterwave = new Client([
            'base_uri' => config('payment.flutterwave.payment_url') . '/',
            'headers' => [
                'Authorization' => 'Bearer ' . config('payment.flutterwave.secret_key'),
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    /**
     * Initialize a payment with Paystack
     */
    public function initializePaystackPayment($amount, $email, $reference, $callbackUrl, $metadata = [])
    {
        try {
            $response = $this->paystack->transaction->initialize([
                'amount' => $amount * 100, // Paystack uses kobo (100 kobo = 1 naira)
                'email' => $email,
                'reference' => $reference,
                'callback_url' => $callbackUrl,
                'metadata' => $metadata
            ]);

            if ($response->status) {
                // Create payment transaction record
                $transaction = PaymentTransaction::create([
                    'transaction_id' => Str::uuid(),
                    'payment_gateway' => 'paystack',
                    'transaction_reference' => $reference,
                    'user_id' => $metadata['user_id'] ?? null,
                    'ad_id' => $metadata['ad_id'] ?? null,
                    'type' => $metadata['type'] ?? 'ad_payment',
                    'amount' => $amount,
                    'currency' => 'NGN',
                    'status' => 'pending',
                    'gateway_response' => json_decode(json_encode($response), true),
                    'metadata' => $metadata
                ]);

                return [
                    'success' => true,
                    'authorization_url' => $response->data->authorization_url,
                    'reference' => $response->data->reference,
                    'transaction' => $transaction
                ];
            }

            return ['success' => false, 'message' => $response->message];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Initialize a payment with Flutterwave
     */
    public function initializeFlutterwavePayment($amount, $email, $tx_ref, $callbackUrl, $metadata = [])
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('FLUTTERWAVE_SECRET_KEY'),
            ])->post('https://api.flutterwave.com/v3/payments', [
                'amount' => $amount,
                'customer' => [
                    'email' => $email,
                ],
                'currency' => 'NGN',
                'tx_ref' => $tx_ref,
                'redirect_url' => $callbackUrl,
                'meta' => $metadata
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                // Create payment transaction record
                $transaction = PaymentTransaction::create([
                    'transaction_id' => Str::uuid(),
                    'payment_gateway' => 'flutterwave',
                    'transaction_reference' => $tx_ref,
                    'user_id' => $metadata['user_id'] ?? null,
                    'ad_id' => $metadata['ad_id'] ?? null,
                    'type' => $metadata['type'] ?? 'ad_payment',
                    'amount' => $amount,
                    'currency' => 'NGN',
                    'status' => 'pending',
                    'gateway_response' => $data,
                    'metadata' => $metadata
                ]);

                return [
                    'success' => true,
                    'authorization_url' => $data['data']['link'] ?? null,
                    'reference' => $tx_ref,
                    'transaction' => $transaction
                ];
            }

            return ['success' => false, 'message' => $response->json()['message'] ?? 'Payment initialization failed'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Verify a Paystack transaction
     */
    public function verifyPaystackTransaction($reference)
    {
        try {
            $response = $this->paystack->transaction->verify([
                'reference' => $reference,
            ]);

            if ($response->status && $response->data->status === 'success') {
                $transaction = PaymentTransaction::where('transaction_reference', $reference)
                    ->where('payment_gateway', 'paystack')
                    ->first();

                if ($transaction) {
                    $transaction->update([
                        'status' => 'success',
                        'paid_at' => now(),
                        'gateway_response' => array_merge(
                            $transaction->gateway_response ?? [], 
                            json_decode(json_encode($response), true)
                        )
                    ]);
                }

                return [
                    'success' => true,
                    'data' => $response->data,
                    'transaction' => $transaction
                ];
            } else {
                $transaction = PaymentTransaction::where('transaction_reference', $reference)
                    ->where('payment_gateway', 'paystack')
                    ->first();
                
                if ($transaction) {
                    $transaction->update(['status' => 'failed']);
                }

                return [
                    'success' => false,
                    'message' => $response->message ?? 'Payment verification failed',
                    'transaction' => $transaction
                ];
            }
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Verify a Flutterwave transaction using reference
     */
    public function verifyFlutterwaveTransaction($tx_ref)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('FLUTTERWAVE_SECRET_KEY'),
            ])->get("https://api.flutterwave.com/v3/transactions/{$tx_ref}/verify");

            if ($response->successful()) {
                $data = $response->json();
                
                if ($data['status'] === 'success' && $data['data']['status'] === 'successful') {
                    $transaction = PaymentTransaction::where('transaction_reference', $tx_ref)
                        ->where('payment_gateway', 'flutterwave')
                        ->first();

                    if ($transaction) {
                        $transaction->update([
                            'status' => 'success',
                            'paid_at' => now(),
                            'gateway_response' => $data
                        ]);
                    }

                    return [
                        'success' => true,
                        'data' => $data['data'],
                        'transaction' => $transaction
                    ];
                } else {
                    $transaction = PaymentTransaction::where('transaction_reference', $tx_ref)
                        ->where('payment_gateway', 'flutterwave')
                        ->first();
                    
                    if ($transaction) {
                        $transaction->update(['status' => 'failed']);
                    }

                    return [
                        'success' => false,
                        'message' => $data['message'] ?? 'Payment verification failed',
                        'transaction' => $transaction
                    ];
                }
            }

            return [
                'success' => false, 
                'message' => $response->json()['message'] ?? 'Payment verification failed'
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Handle Paystack webhook verification
     */
    public function handlePaystackWebhook($payload)
    {
        // Verify webhook signature
        $secret = config('payment.paystack.secret_key');
        $signature = $_SERVER['HTTP_X_PAYSTACK_SIGNATURE'] ?? '';

        $computedSignature = hash_hmac('sha512', $payload, $secret);

        if ($signature !== $computedSignature) {
            return ['success' => false, 'message' => 'Invalid signature'];
        }

        $event = json_decode($payload, true);

        if ($event['event'] === 'charge.success') {
            $reference = $event['data']['reference'];
            $transaction = PaymentTransaction::where('transaction_reference', $reference)
                ->where('payment_gateway', 'paystack')
                ->first();

            if ($transaction) {
                $transaction->update([
                    'status' => 'success',
                    'paid_at' => now(),
                    'gateway_response' => $event
                ]);

                // Process successful payment (e.g., activate ad, subscription, etc.)
                $this->processSuccessfulPayment($transaction);
            }
        }

        return ['success' => true];
    }

    /**
     * Handle Flutterwave webhook verification
     */
    public function handleFlutterwaveWebhook($payload)
    {
        // Note: For Flutterwave, you would typically use a secret hash
        // This is set in your Flutterwave dashboard and should match
        $secret_hash = config('payment.flutterwave.secret_hash');
        $signature = $_SERVER['HTTP_VERIF_HASH'] ?? '';

        if (!$secret_hash || !$signature || ($signature !== $secret_hash)) {
            return ['success' => false, 'message' => 'Invalid signature'];
        }

        $event = json_decode($payload, true);

        if (isset($event['event']) && $event['event'] === 'charge.completed') {
            $tx_ref = $event['data']['tx_ref'];
            $transaction = PaymentTransaction::where('transaction_reference', $tx_ref)
                ->where('payment_gateway', 'flutterwave')
                ->first();

            if ($transaction) {
                $transaction->update([
                    'status' => 'success',
                    'paid_at' => now(),
                    'gateway_response' => $event
                ]);

                // Process successful payment
                $this->processSuccessfulPayment($transaction);
            }
        }

        return ['success' => true];
    }

    /**
     * Process successful payment (activate ad, subscription, etc.)
     */
    protected function processSuccessfulPayment($transaction)
    {
        // This is where you'd handle the business logic after successful payment
        // For example:
        // - Activate a featured ad
        // - Extend a subscription
        // - Unlock premium features
        // - Update user's wallet balance
        
        switch ($transaction->type) {
            case 'ad_payment':
                // Handle ad payment logic
                break;
            case 'featured_ad':
                // Handle featured ad logic
                break;
            case 'premium_subscription':
                // Handle subscription logic
                break;
            default:
                // Handle other payment types
                break;
        }
    }

    /**
     * Get transaction by reference
     */
    public function getTransactionByReference($reference)
    {
        return PaymentTransaction::where('transaction_reference', $reference)->first();
    }
}