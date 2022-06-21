<?php

namespace App\Events;

use Auth;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\DirectQueue;

class OnsiteQueueCalled implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    protected $onsite_queue;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(DirectQueue $directQueue)
    {
        $this->onsite_queue = $directQueue;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('onsite_queues.' . $this->onsite_queue->client_id);
    }

    public function broadcastWith()
    {
        return [
            'data' => $this->onsite_queue->toArray(),
            'message' => "Antrian " . $this->onsite_queue->queue_no . " sedang dipanggil. Mohon ke " . Auth::user()->WorkstationVct->Workstation->label
        ];
    }
}
