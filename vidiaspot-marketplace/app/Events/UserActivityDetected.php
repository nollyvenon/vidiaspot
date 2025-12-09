<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserActivityDetected
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public User $user;
    public string $activityType;
    public array $context;

    /**
     * Create a new event instance.
     */
    public function __construct(User $user, string $activityType, array $context = [])
    {
        $this->user = $user;
        $this->activityType = $activityType;
        $this->context = $context;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
