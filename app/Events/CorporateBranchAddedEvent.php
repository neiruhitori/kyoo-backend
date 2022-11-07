<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Branch;
use App\User;
use App\Models\Corporate;

class CorporateBranchAddedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $branch, $user, $corporate;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Branch $branch, User $user, Corporate $corporate)
    {
        $this->branch = $branch;
        $this->user = $user;
        $this->corporate = $corporate;
    }
}
