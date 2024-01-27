<?php

namespace App\Listeners;

use App\Events\AppointmentOnsiteCreated;
use App\Notifications\AppointmentOnsiteCreatedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class SendAppointmentOnsiteCreatedNotification implements ShouldQueue
{
    public $queue = 'listeners';

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\AppointmentOnsiteCreated  $event
     * @return void
     */
    public function handle(AppointmentOnsiteCreated $event)
    {
        Notification::send($event->appointmentOnsite, new AppointmentOnsiteCreatedNotification());
    }
}
