<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Auth\Passwords\PasswordBroker;

class CorporateUserCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $user;

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
        $corporate = $this->user->Corporate;
        $token = app(PasswordBroker::class)->createToken($this->user);

        return $this
            ->from('noreply@kyoo.id', 'KYOO')
            ->subject('Akun Corporate ' . $corporate->name . ' Telah Terdaftar di Kyoo')
            ->markdown('users.emails.corporateUserCreated', [
                'corporate' => $corporate,
                'resetPasswordUrl' => url(route('password.reset', [
                    'token' => $token,
                    'email' => $this->user->email
                ]))
            ]);
    }
}
