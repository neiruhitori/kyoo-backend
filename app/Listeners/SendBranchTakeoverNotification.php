<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\CorporateBranchAddedEvent;
use Illuminate\Support\Facades\Mail;
use App\Mail\BranchTakeoverMail;

class SendBranchTakeoverNotification
{
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
    public function handle(CorporateBranchAddedEvent $event)
    {
        Mail::to($event->user->email)->queue(
            (new BranchTakeoverMail($event->branch, $event->user, $event->corporate))->onQueue('mails')
        );
    }
}
