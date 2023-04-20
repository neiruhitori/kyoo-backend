<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;

use App\Events\OnsiteQueueCreated;
use App\Notifications\OnsiteQueueCreatedNotification;

class SendDirectQueueCreatedNotification implements ShouldQueue
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
    public function handle(OnsiteQueueCreated $event)
    {
        Notification::send($event->directQueue, new OnsiteQueueCreatedNotification());
    }
}
