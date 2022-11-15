<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\CorporateCreatedEvent;
use App\Services\CorporateBranchService;

class CreateCorporateBranch
{
    protected CorporateBranchService $corporateBranchService;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(CorporateBranchService $corporateBranchService)
    {
        $this->corporateBranchService = $corporateBranchService;
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(CorporateCreatedEvent $event)
    {
        $this->corporateBranchService->addFromBranch(
            $event->branch->id,
            $event->corporate->id
        );
    }
}
