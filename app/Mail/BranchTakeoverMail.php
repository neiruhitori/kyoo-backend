<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Branch;
use App\Models\Corporate;
use App\User;

class BranchTakeoverMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $branch, $corporate, $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Branch $branch, User $user, Corporate $corporate)
    {
        $this->branch = $branch;
        $this->corporate = $corporate;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('noreply@kyoo.id', 'KYOO')
            ->subject("Pembaruan Status Cabang {$this->branch->name}")
            ->markdown('corporate.emails.branchTakenOver', [
                'branch' => $this->branch,
                'corporate' => $this->corporate,
                'user' => $this->user,
                'adminKyoo' => 'admin@kyoo.id',
            ]);
    }
}
