<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

use App\Events\AppointmentCreated;
use App\Notifications\AppointmentCreatedNotification;

class SendAppointmentCreatedNotification implements ShouldQueue
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
     * @param  object  $event
     * @return void
     */
    public function handle(AppointmentCreated $event)
    {
        Notification::send($event->appointment, new AppointmentCreatedNotification());
    }
}
