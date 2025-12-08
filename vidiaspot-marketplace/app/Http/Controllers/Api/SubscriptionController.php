<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\SubscriptionService;
use App\Models\Subscription;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    protected $subscriptionService;

    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }

    /**
     * Get all available subscription plans
     */
    public function index(): JsonResponse
    {
        try {
            $plans = $this->subscriptionService->getAvailablePlans();

            return response()->json([
                'success' => true,
                'data' => $plans,
                'message' => 'Available subscription plans retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve subscription plans',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Subscribe user to a plan
     */
    public function subscribe(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'subscription_id' => 'required|exists:subscriptions,id',
            'payment_gateway' => 'required|in:paystack,flutterwave',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::user();
        $subscription = Subscription::findOrFail($request->subscription_id);

        try {
            $result = $this->subscriptionService->subscribeUser(
                $user,
                $subscription,
                $request->payment_gateway
            );

            return response()->json([
                'success' => true,
                'data' => $result,
                'message' => 'Subscription initiated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to initiate subscription',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user's current subscription
     */
    public function getCurrentSubscription(): JsonResponse
    {
        $user = Auth::user();

        try {
            $subscription = $this->subscriptionService->getUserSubscription($user);

            if (!$subscription) {
                return response()->json([
                    'success' => false,
                    'message' => 'No active subscription found',
                    'data' => null
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => $subscription,
                'message' => 'Current subscription retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve subscription',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if user's subscription is active
     */
    public function checkStatus(): JsonResponse
    {
        $user = Auth::user();

        try {
            $isActive = $this->subscriptionService->isUserSubscriptionActive($user);

            return response()->json([
                'success' => true,
                'data' => [
                    'is_active' => $isActive,
                    'expires_at' => $user->subscription_end_date ? $user->subscription_end_date->toISOString() : null
                ],
                'message' => 'Subscription status checked successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to check subscription status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel user's subscription
     */
    public function cancel(): JsonResponse
    {
        $user = Auth::user();

        try {
            $result = $this->subscriptionService->cancelUserSubscription($user);

            return response()->json([
                'success' => true,
                'data' => $result,
                'message' => 'Subscription cancelled successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel subscription',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Renew user's subscription
     */
    public function renew(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'subscription_id' => 'required|exists:subscriptions,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::user();
        $subscription = Subscription::findOrFail($request->subscription_id);

        try {
            $result = $this->subscriptionService->renewSubscription($user, $subscription);

            return response()->json([
                'success' => true,
                'data' => $result,
                'message' => 'Subscription renewed successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to renew subscription',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process successful payment and activate subscription
     */
    public function processSuccessfulPayment(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'transaction_id' => 'required|string',
            'payment_gateway' => 'required|in:paystack,flutterwave'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // First verify the payment with the payment gateway
            $paymentService = app('App\Services\PaymentService');
            $result = null;

            if ($request->payment_gateway === 'paystack') {
                $result = $paymentService->verifyPaystackTransaction($request->transaction_id);
            } else {
                $result = $paymentService->verifyFlutterwaveTransaction($request->transaction_id);
            }

            if ($result && $result['success']) {
                // Get the transaction
                $transaction = $result['transaction'];

                // Process successful subscription payment
                if ($transaction && $transaction->type === 'subscription') {
                    $this->subscriptionService->processSuccessfulSubscription($transaction);

                    return response()->json([
                        'success' => true,
                        'message' => 'Subscription activated successfully',
                        'transaction' => $transaction
                    ]);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Payment verified successfully',
                    'transaction' => $transaction
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $result['message'] ?? 'Payment verification failed',
                'transaction' => $result['transaction'] ?? null
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to process payment',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user's subscription benefits
     */
    public function getBenefits(): JsonResponse
    {
        $user = Auth::user();

        try {
            $isActive = $this->subscriptionService->isUserSubscriptionActive($user);
            $subscription = $this->subscriptionService->getUserSubscription($user);

            $benefits = [
                'is_active' => $isActive,
                'ad_limit' => $user->ad_limit ?? 0,
                'featured_ads_limit' => $user->featured_ads_limit ?? 0,
                'has_priority_support' => $user->has_priority_support ?? false,
                'remaining_ads' => max(0, ($user->ad_limit ?? 0) - count($user->ads)), // Assuming user has ads relation
                'remaining_featured_ads' => max(0, ($user->featured_ads_limit ?? 0) - count($user->featuredAds)),
                'expires_at' => $user->subscription_end_date ? $user->subscription_end_date->toISOString() : null,
                'plan_name' => $subscription ? $subscription->name : 'Free'
            ];

            return response()->json([
                'success' => true,
                'data' => $benefits,
                'message' => 'Subscription benefits retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve subscription benefits',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
