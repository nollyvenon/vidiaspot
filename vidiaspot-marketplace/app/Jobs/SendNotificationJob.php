<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Notification as NotificationFacade;
use App\Notifications\GenericNotification;

class SendNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function handle()
    {
        try {
            $userId = $this->data['user_id'] ?? null;
            $message = $this->data['message'] ?? '';
            $type = $this->data['type'] ?? 'generic';
            $additionalData = $this->data['data'] ?? [];

            if (!$userId) {
                throw new \Exception('User ID is required for sending notification');
            }

            // Create and send notification
            $notification = new GenericNotification($message, $type, $additionalData);
            
            $user = \App\Models\User::find($userId);
            if ($user) {
                NotificationFacade::send($user, $notification);
                
                Log::info("Notification sent to user ID: {$userId}");
            } else {
                Log::warning("User with ID {$userId} not found for notification");
            }
        } catch (\Exception $e) {
            Log::error("Failed to send notification: " . $e->getMessage());
            $this->fail($e);
        }
    }
}