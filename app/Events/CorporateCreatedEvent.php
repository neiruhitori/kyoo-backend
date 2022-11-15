<?php

namespace App\Events;

use App\Models\Corporate;
use App\Branch;
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

    public $corporate, $user, $branch;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Corporate $corporate, $user, Branch $branch)
    {
        $this->corporate = $corporate;
        $this->user = $user;
        $this->branch = $branch;
    }
}
