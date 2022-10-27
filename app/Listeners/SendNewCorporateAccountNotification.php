<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\CorporateUserCreatedEvent;
use Illuminate\Support\Facades\Mail;
use App\Mail\CorporateUserCreatedMail;

class SendNewCorporateAccountNotification implements ShouldQueue
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
    public function handle(CorporateUserCreatedEvent $event)
    {
        Mail::to($event->user->email)->queue(
            (new CorporateUserCreatedMail($event->user))->onQueue('mails')
        );
    }
}
