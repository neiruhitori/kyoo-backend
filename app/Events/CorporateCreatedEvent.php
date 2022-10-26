<?php

namespace App\Events;

use App\Models\Corporate;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CorporateCreatedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $corporate;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Corporate $corporate)
    {
        $this->corporate = $corporate;
    }
}
