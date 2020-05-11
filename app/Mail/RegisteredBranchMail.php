<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Branch;

class RegisteredBranchMail extends Mailable
{
    use Queueable, SerializesModels;
    public $branch, $password;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Branch $branch, $password)
    {
        $this->branch = $branch;
        $this->password = $password;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('noreply@kyoo.id', 'Kyoo')->markdown('emails.registeredBranch', [
            'branch' => $this->branch,
            'password' => $this->password
        ]);
    }
}
