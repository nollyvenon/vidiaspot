<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Services\SubscriptionService;
use Carbon\Carbon;

class ProcessSubscriptionRenewals extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscription:renewals';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process subscription renewals and billing';

    /**
     * Execute the console command.
     */
    public function handle(SubscriptionService $subscriptionService)
    {
        $this->info('Processing subscription renewals...');

        // Get users whose subscriptions are expiring today or have expired
        $expiringUsers = User::whereNotNull('subscription_end_date')
            ->where('subscription_status', 'active')
            ->where('subscription_end_date', '<=', Carbon::now()->addDays(1))
            ->get();

        $processedCount = 0;
        $renewalCount = 0;

        foreach ($expiringUsers as $user) {
            $this->info("Processing renewal for user: {$user->email}");

            try {
                // Process recurring billing for this user
                $transaction = $subscriptionService->processRecurringBilling($user);

                if ($transaction) {
                    $this->info("Created renewal transaction for user: {$user->email}");
                    $renewalCount++;
                } else {
                    $this->info("No renewal needed for user: {$user->email}");
                }

                $processedCount++;
            } catch (\Exception $e) {
                $this->error("Error processing user {$user->email}: " . $e->getMessage());
            }
        }

        $this->info("Processed {$processedCount} users, created {$renewalCount} renewal transactions.");
        return 0;
    }
}
