<?php

namespace App\Mail\CS;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Exhibition;
use Crypt;

class StoreExhibitionMail extends Mailable
{
    use Queueable, SerializesModels;
    public $exhibition;

    public function __construct(Exhibition $exhibition)
    {
        $this->exhibition = $exhibition;
    }

    public function build()
    {
        $exhibition_id = $this->exhibition->id;
        $branch = $this->exhibition->Slot->Service->Branch;

        return $this->from('noreply@kyoo.id', 'KYOO')
            ->subject(__('Branch Exhibition'))
            ->markdown('emails.cs.storeExhibition', [
                'exhibition' => $this->exhibition,
                'exhibition_id' => $exhibition_id,
                'branch_id' => $branch->id
            ]);
    }
}
