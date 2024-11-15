<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class AppointmentQueue implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private $appointmentQueues = '';
    private $branch_id = null;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($appointmentQueue, $branch_id)
    {
        $appointmentQueues = $appointmentQueue;
        $this->branch_id = $branch_id;
        $this->appointmentQueues = [
            'success' => true,
            'message' => 'realtime appointment queue',
            'data' => $appointmentQueues
        ];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('event_appointment_queue_general.' . ($this->branch_id ?? Auth::user()->branch_id));
    }
}
