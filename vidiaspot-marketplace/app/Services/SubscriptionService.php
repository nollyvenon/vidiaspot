<?php

namespace App\Services;

use App\Models\Subscription;
use App\Models\User;
use App\Models\PaymentTransaction;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Services\PaymentService;

class SubscriptionService
{
    public function __construct()
    {
        // PaymentService is used directly when needed
    }

    /**
     * Subscribe a user to a plan
     */
    public function subscribeUser(User $user, Subscription $subscription, $paymentGateway = 'paystack')
    {
        // Create a payment transaction record for the subscription
        $reference = 'SUB_' . strtoupper(Str::random(20));
        
        $transaction = PaymentTransaction::create([
            'transaction_id' => Str::uuid(),
            'payment_gateway' => $paymentGateway,
            'transaction_reference' => $reference,
            'user_id' => $user->id,
            'subscription_id' => $subscription->id,
            'type' => 'subscription',
            'amount' => $subscription->price,
            'currency' => $subscription->currency_code,
            'status' => 'pending',
            'metadata' => [
                'subscription_plan' => $subscription->name,
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
            ]
        ]);

        // Prepare metadata for payment gateway
        $metadata = [
            'user_id' => $user->id,
            'subscription_id' => $subscription->id,
            'plan_name' => $subscription->name,
            'custom_fields' => [
                'subscription_name' => $subscription->name,
                'billing_cycle' => $subscription->billing_cycle
            ]
        ];

        // Initialize payment with the selected gateway
        $paymentService = app(PaymentService::class);
        if ($paymentGateway === 'paystack') {
            return $paymentService->initializePaystackPayment(
                $subscription->price,
                $user->email,
                $reference,
                url('/api/payment/verify'),
                $metadata
            );
        } else { // flutterwave
            return $paymentService->initializeFlutterwavePayment(
                $subscription->price,
                $user->email,
                $reference,
                url('/api/payment/verify'),
                $metadata
            );
        }
    }

    /**
     * Process successful subscription payment
     */
    public function processSuccessfulSubscription($transaction)
    {
        if (!$transaction || $transaction->type !== 'subscription') {
            return false;
        }

        $user = $transaction->user;
        $subscription = $transaction->subscription;

        // Update user with subscription details
        $user->update([
            'subscription_id' => $subscription->id,
            'subscription_start_date' => Carbon::now(),
            'subscription_end_date' => Carbon::now()->addDays($subscription->duration_days),
            'subscription_status' => 'active',
            'ad_limit' => $subscription->ad_limit,
            'featured_ads_limit' => $subscription->featured_ads_limit,
            'has_priority_support' => $subscription->has_priority_support,
        ]);

        // Update transaction status
        $transaction->update([
            'status' => 'success',
            'paid_at' => Carbon::now()
        ]);

        return true;
    }

    /**
     * Get user's current subscription
     */
    public function getUserSubscription(User $user)
    {
        if (!$user->subscription_id) {
            return null;
        }

        return $user->subscription;
    }

    /**
     * Check if user's subscription is active
     */
    public function isUserSubscriptionActive(User $user)
    {
        if (!$user->subscription_end_date) {
            return false;
        }

        return $user->subscription_end_date->isFuture();
    }

    /**
     * Cancel user's subscription
     */
    public function cancelUserSubscription(User $user)
    {
        $user->update([
            'subscription_status' => 'cancelled',
            'subscription_end_date' => Carbon::now()
        ]);

        return true;
    }

    /**
     * Renew user's subscription
     */
    public function renewSubscription(User $user, Subscription $newSubscription)
    {
        $currentEndDate = $user->subscription_end_date ?? Carbon::now();

        $user->update([
            'subscription_id' => $newSubscription->id,
            'subscription_start_date' => $currentEndDate,
            'subscription_end_date' => $currentEndDate->addDays($newSubscription->duration_days),
            'subscription_status' => 'active',
            'ad_limit' => $newSubscription->ad_limit,
            'featured_ads_limit' => $newSubscription->featured_ads_limit,
            'has_priority_support' => $newSubscription->has_priority_support,
        ]);

        return true;
    }

    /**
     * Get available subscription plans
     */
    public function getAvailablePlans($activeOnly = true)
    {
        $query = Subscription::query();

        if ($activeOnly) {
            $query = $query->where('is_active', true);
        }

        return $query->orderBy('price', 'asc')->get();
    }

    /**
     * Process recurring billing for a user (scheduled job)
     */
    public function processRecurringBilling(User $user)
    {
        if (!$user->subscription || $user->subscription->billing_cycle === null) {
            return false;
        }

        $subscription = $user->subscription;
        $cycleInterval = $this->getCycleInterval($subscription->billing_cycle);

        // Check if it's time to bill again
        if ($user->subscription_end_date->diffInDays(Carbon::now()) >= 0) {
            // Create new payment for renewal
            $reference = 'RENEW_' . strtoupper(Str::random(20));
            
            $transaction = PaymentTransaction::create([
                'transaction_id' => Str::uuid(),
                'payment_gateway' => 'paystack', // Default gateway for renewals
                'transaction_reference' => $reference,
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
                'type' => 'subscription_renewal',
                'amount' => $subscription->price,
                'currency' => $subscription->currency_code,
                'status' => 'pending',
                'metadata' => [
                    'subscription_plan' => $subscription->name,
                    'billing_cycle' => $subscription->billing_cycle,
                    'renewal' => true
                ]
            ]);

            // Return transaction for further processing
            return $transaction;
        }

        return false;
    }

    /**
     * Renew user's subscription
     */
    public function renewSubscription(User $user, Subscription $newSubscription)
    {
        $currentEndDate = $user->subscription_end_date ?? now();

        $user->update([
            'subscription_id' => $newSubscription->id,
            'subscription_start_date' => $currentEndDate,
            'subscription_end_date' => $currentEndDate->addDays($newSubscription->duration_days),
            'subscription_status' => 'active',
            'ad_limit' => $newSubscription->ad_limit,
            'featured_ads_limit' => $newSubscription->featured_ads_limit,
            'has_priority_support' => $newSubscription->has_priority_support,
        ]);

        return true;
    }

    /**
     * Helper to get cycle interval
     */
    private function getCycleInterval($billingCycle)
    {
        switch ($billingCycle) {
            case 'daily':
                return 1;
            case 'weekly':
                return 7;
            case 'monthly':
                return 30;
            case 'quarterly':
                return 90;
            case 'yearly':
                return 365;
            default:
                return 30; // Default to monthly
        }
    }
}