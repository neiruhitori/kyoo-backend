<?php

namespace App\Mail\CS;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\User;
use Crypt;

class ResetPassword extends Mailable
{
    use Queueable, SerializesModels;
    private $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $user_id = Crypt::encrypt($this->user->id);
        return $this->from('noreply@kyoo.id', 'KYOO')->subject(__('VCT Reset Password'))->markdown('emails.cs.resetPassword', [
            'user' => $this->user,
            'user_id' => $user_id,
        ]);
    }
}
