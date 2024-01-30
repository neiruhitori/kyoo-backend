<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\AppointmentOnsite;

class AppointmentOnsiteCreated
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
}
