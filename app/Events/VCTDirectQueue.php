<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\DirectQueue as DirectQueueModel;
use Auth;

class VCTDirectQueue implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private $directQueues = '';
    private $branch_id = null;


    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($directQueue, $branch_id = null)
    {
        $directQueues = $directQueue;
        $this->branch_id = $branch_id;
        $this->directQueues = [
            'success' => true,
            'message' => 'realtime direct queue',
            'data' => $directQueues
        ];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('event_direct_queue.'.($this->branch_id ?? Auth::user()->branch_id));
    }
}
