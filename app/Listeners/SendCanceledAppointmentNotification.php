<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use App\Events\AppointmentCanceledEvent;
use App\Mail\AppointmentCanceledMail;

class SendCanceledAppointmentNotification implements ShouldQueue
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
    public function handle(AppointmentCanceledEvent $event)
    {
        Mail::to($event->appointment->email)->queue(
            (new AppointmentCanceledMail($event->appointment))->onQueue('mails')
        );
    }
}
