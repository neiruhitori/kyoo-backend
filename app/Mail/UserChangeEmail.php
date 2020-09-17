<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\ChangeEmail;
use Crypt;

class UserChangeEmail extends Mailable
{
    use Queueable, SerializesModels;
    public $changeEmail;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(ChangeEmail $changeEmail)
    {
        $this->changeEmail = $changeEmail;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('noreply@kyoo.id', 'KYOO')->markdown('emails.users.changeEmail', [
            'changeEmail' => $this->changeEmail,
            'id' => Crypt::encrypt($this->changeEmail->id)
        ]);
    }
}
