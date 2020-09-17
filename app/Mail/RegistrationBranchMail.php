<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\RegistrationBranch;
class RegistrationBranchMail extends Mailable
{
    use Queueable, SerializesModels;
    public $branch;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(RegistrationBranch $branch)
    {
        $this->branch = $branch;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('noreply@kyoo.id', 'KYOO')->markdown('emails.registrationBranch', [
            'branch' => $this->branch
        ]);
    }
}
