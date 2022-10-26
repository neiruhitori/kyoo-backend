<?php

namespace App\Listeners;

use App\Events\CorporateCreatedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Services\UserService;

class CreateCorporateUser
{
    protected $userService;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(CorporateCreatedEvent $event)
    {
        $this->userService->createCorporate([
            'corporate_id' => $event->corporate->user['corporate_id'],
            'name' => $event->corporate->user['name'],
            'email' => $event->corporate->user['email'],
            'phone' => $event->corporate->user['phone'],
        ]);
    }
}
