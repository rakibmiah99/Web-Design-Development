<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class MakeCallEvent implements ShouldBroadcastNow
{
    public $peer_id;
    /**
     * Create a new event instance.
    */
    public function __construct($peer_id)
    {
        $this->peer_id = $peer_id;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('web-rtc-chanel'),
        ];
    }


    public function broadcastWith(){
        return [
            'peer_id' => $this->peer_id,
        ];
    }


    public function broadcastAs()
    {
        return 'make-call';
    }
}
