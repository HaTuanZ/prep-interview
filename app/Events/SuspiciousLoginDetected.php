<?php

namespace App\Events;

use App\Models\User;
use App\Notifications\SuspiciousLoginNotification;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SuspiciousLoginDetected
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $userId;
    public $ip;
    public $deviceId;
    public $location;

    /**
     * Create a new event instance.
     */
    public function __construct($userId, $ip, $deviceId, $location)
    {
        $this->userId = $userId;
        $this->ip = $ip;
        $this->deviceId = $deviceId;
        $this->location = $location;
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
