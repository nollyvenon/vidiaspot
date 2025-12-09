<?php

namespace App\Listeners;

use App\Events\UserActivityDetected;
use App\Services\AnomalyDetectionService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class ProcessAnomalyDetection
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(UserActivityDetected $event): void
    {
        // Only process anomaly detection for certain activities that warrant it
        $activitiesToMonitor = [
            'login',
            'login_failed',
            'transaction',
            'profile_update',
            'payment_method_added',
            'password_change',
            'email_change',
            'phone_change'
        ];

        if (in_array($event->activityType, $activitiesToMonitor)) {
            try {
                $anomalyService = new AnomalyDetectionService();
                $anomalyService->processAnomalies($event->user);
            } catch (\Exception $e) {
                Log::error('Error processing anomaly detection', [
                    'user_id' => $event->user->id,
                    'activity_type' => $event->activityType,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }
}
