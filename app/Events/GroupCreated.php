<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GroupCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public $group;

    public function __construct($group)
    {
        \Log::info('GroupCreated event triggered: ' . json_encode($group));
        $this->group = $group;
    }

    public function broadcastOn()
    {
        $channelName = 'group.' . $this->group->id;
        \Log::info('Broadcasting on channel: ' . $channelName);
    
        return new Channel('group.' . $this->group->id);
    }
}
