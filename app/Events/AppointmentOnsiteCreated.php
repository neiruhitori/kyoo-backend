<?php

namespace App\Events;

use App\Models\AppointmentOnsite;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AppointmentOnsiteCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public AppointmentOnsite $appointmentOnsite;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(AppointmentOnsite $appointmentOnsite)
    {
        $this->appointmentOnsite = $appointmentOnsite;
    }

    public function broadcastOn()
    {
        return new Channel('branches.' . $this->appointmentOnsite->branch_id . '.appointment-onsite');
    }
}
