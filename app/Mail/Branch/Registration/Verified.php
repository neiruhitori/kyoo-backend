<?php

namespace App\Mail\Branch\Registration;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Branch;

class Verified extends Mailable
{
    use Queueable, SerializesModels;
    public $branch;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Branch $branch)
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
        return $this->from('noreply@kyoo.id', 'KYOO')->subject(__('Your Branch Has Been Verified'))->markdown('emails.branch.registration.verified', [
            'branch' => $this->branch
        ]);
    }
}
