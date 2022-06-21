<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\DirectQueue;

class OnsiteQueueUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    protected $onsite;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(DirectQueue $onsite)
    {
        $this->onsite = $onsite;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('onsite_queues.' . $this->onsite->client_id);
    }

    public function broadcastWith()
    {
        return $this->onsite->toArray();
    }
}
