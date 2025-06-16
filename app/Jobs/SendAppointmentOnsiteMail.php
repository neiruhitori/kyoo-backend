<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Mail\CS\AppointmentOnsiteCreatedMail;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class SendAppointmentOnsiteMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    public $tries = 2;
    public $backoff = 10;

    protected $appointmentOnsite;


    public function __construct($appointmentOnsite)
    {
        $this->appointmentOnsite = $appointmentOnsite;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
          Mail::to($this->appointmentOnsite->email)
            ->send(new AppointmentOnsiteCreatedMail($this->appointmentOnsite));
    }

     public function failed(\Throwable $exception)
    {
        Log::warning("Gagal kirim email appointment onsite: {$exception->getMessage()}");
    }
}
