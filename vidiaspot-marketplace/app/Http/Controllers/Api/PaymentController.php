<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Initialize a payment (Paystack or Flutterwave)
     */
    public function initializePayment(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1',
            'email' => 'required|email',
            'payment_gateway' => 'required|in:paystack,flutterwave',
            'type' => 'required|in:ad_payment,featured_ad,premium_subscription,subscription,in_app_purchase',
            'user_id' => 'required|exists:users,id',
            'ad_id' => 'nullable|exists:ads,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $amount = $request->amount;
        $email = $request->email;
        $gateway = $request->payment_gateway;
        $reference = Str::random(20); // Generate unique reference
        $callbackUrl = $request->callback_url ?? url('/api/payment/verify');

        // Prepare metadata
        $metadata = [
            'user_id' => $request->user_id,
            'ad_id' => $request->ad_id,
            'type' => $request->type,
            'custom_fields' => $request->custom_fields ?? []
        ];

        if ($gateway === 'paystack') {
            $result = $this->paymentService->initializePaystackPayment(
                $amount,
                $email,
                $reference,
                $callbackUrl,
                $metadata
            );
        } else { // flutterwave
            $result = $this->paymentService->initializeFlutterwavePayment(
                $amount,
                $email,
                $reference,
                $callbackUrl,
                $metadata
            );
        }

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => 'Payment initialized successfully',
                'data' => [
                    'authorization_url' => $result['authorization_url'],
                    'reference' => $result['reference'],
                    'payment_gateway' => $gateway
                ]
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => $result['message'] ?? 'Payment initialization failed'
            ], 400);
        }
    }

    /**
     * Verify a payment transaction
     */
    public function verifyPayment(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'reference' => 'required|string',
            'payment_gateway' => 'required|in:paystack,flutterwave'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $reference = $request->reference;
        $gateway = $request->payment_gateway;

        if ($gateway === 'paystack') {
            $result = $this->paymentService->verifyPaystackTransaction($reference);
        } else { // flutterwave
            $result = $this->paymentService->verifyFlutterwaveTransaction($reference);
        }

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => 'Payment verified successfully',
                'data' => $result['data'] ?? null,
                'transaction' => $result['transaction']
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => $result['message'] ?? 'Payment verification failed',
                'transaction' => $result['transaction'] ?? null
            ], 400);
        }
    }

    /**
     * Handle Paystack webhook
     */
    public function handlePaystackWebhook(Request $request)
    {
        $payload = $request->getContent();
        $result = $this->paymentService->handlePaystackWebhook($payload);

        if ($result['success']) {
            return response('OK', 200);
        } else {
            return response($result['message'], 400);
        }
    }

    /**
     * Handle Flutterwave webhook
     */
    public function handleFlutterwaveWebhook(Request $request)
    {
        $payload = $request->getContent();
        $result = $this->paymentService->handleFlutterwaveWebhook($payload);

        if ($result['success']) {
            return response('OK', 200);
        } else {
            return response($result['message'], 400);
        }
    }

    /**
     * Get transaction details by reference
     */
    public function getTransaction(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'reference' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $transaction = $this->paymentService->getTransactionByReference($request->reference);

        if ($transaction) {
            return response()->json([
                'success' => true,
                'data' => $transaction
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found'
            ], 404);
        }
    }
}
