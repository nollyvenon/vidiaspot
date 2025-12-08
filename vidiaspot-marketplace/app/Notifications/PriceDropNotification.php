<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PriceDropNotification extends Notification
{
    use Queueable;

    public $ad;
    public $alert;

    /**
     * Create a new notification instance.
     */
    public function __construct($ad, $alert)
    {
        $this->ad = $ad;
        $this->alert = $alert;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    /**
     * Get the database representation of the notification.
     *
     * @return array
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'price_drop',
            'ad_id' => $this->ad->id,
            'ad_title' => $this->ad->title,
            'old_price' => $this->alert->current_price,
            'new_price' => $this->ad->price,
            'target_price' => $this->alert->target_price,
            'message' => "The price for \"{$this->ad->title}\" has dropped to {$this->ad->price}!",
            'action_url' => "/ads/{$this->ad->id}",
            'created_at' => now()
        ];
    }

    /**
     * Get the broadcast representation of the notification.
     */
    public function toBroadcast(object $notifiable): array
    {
        return [
            'type' => 'price_drop',
            'ad_id' => $this->ad->id,
            'ad_title' => $this->ad->title,
            'old_price' => $this->alert->current_price,
            'new_price' => $this->ad->price,
            'target_price' => $this->alert->target_price,
            'message' => "The price for \"{$this->ad->title}\" has dropped to {$this->ad->price}!",
            'action_url' => "/ads/{$this->ad->id}",
            'created_at' => now()->toISOString()
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'price_drop',
            'ad_id' => $this->ad->id,
            'ad_title' => $this->ad->title,
            'old_price' => $this->alert->current_price,
            'new_price' => $this->ad->price,
            'target_price' => $this->alert->target_price,
            'message' => "The price for \"{$this->ad->title}\" has dropped to {$this->ad->price}!",
            'action_url' => "/ads/{$this->ad->id}",
            'created_at' => now()->toISOString()
        ];
    }
}
