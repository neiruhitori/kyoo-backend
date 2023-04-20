<?php

namespace App\Events;

use App\DirectQueue;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OnsiteQueueCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public DirectQueue $directQueue;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(DirectQueue $directQueue)
    {
        $this->directQueue = $directQueue;
    }
}
