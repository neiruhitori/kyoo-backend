<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use App\Models\RegistrationUser;
use Illuminate\Support\Facades\URL;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class RegistrationUserMail extends Mailable
{
    use Queueable, SerializesModels;
    public $user;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(RegistrationUser $user)
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
        $user = $this->user;
        $full = URL::temporarySignedRoute(
                            'userMobile.verif',
                            now()->addHours(24),
                            [
                                'registrationUser' => $user->id,
                            ]
                        );
        $link = parse_url($full, PHP_URL_PATH) . '?' . parse_url($full, PHP_URL_QUERY);

        return $this->from('noreply@kyoo.id', 'KYOO')
        ->subject(__('User Registration Verification'))
        ->markdown('emails.registrationUser', [
            'user' => $this->user,
            'verif_link' => $link,
        ]);
    }
}
