<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */



     public $message;
     public $conversationId;
 
     public function __construct($message, $conversationId)
     {
         $this->message = $message;
         $this->conversationId = $conversationId;
     }
 
     public function broadcastOn()
     {
         return new Channel('conversation.' . $this->conversationId);
     }
 
}
