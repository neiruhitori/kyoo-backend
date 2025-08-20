<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use App\Mail\CS\OnsiteFeedbackMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\CS\DirectQueueCalledMail;
use Illuminate\Queue\SerializesModels;
use App\Mail\CS\AppointmentFeedbackMail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class SendFeedbackMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    public $tries = 2;
    public $backoff = 10;

    protected $queueType;
    protected $queueData;


    public function __construct(string $queueType, $queueData)
    {
        $this->queueType = $queueType;
        $this->queueData = $queueData;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $locale = $this->queueData->Branch->country === 'Indonesia' ? 'id' : 'en';
            if ($this->queueType == 'onsite') {
                if ($this->queueData->email) {
                    Mail::to($this->queueData->email)
                        ->locale($locale)
                        ->send(new OnsiteFeedbackMail($this->queueData));
                }
            } 
            elseif ($this->queueType === 'appointment') {
                if ($this->queueData->email) {
                    Mail::to($this->queueData->email)
                        ->locale($locale)
                        ->send(new AppointmentFeedbackMail($this->queueData));
                }
            }
        } catch (\Throwable $e) {
            Log::warning("Gagal kirim email survey [{$this->queueType}]: {$e->getMessage()}");
            throw $e;
        }
    }

     public function failed(\Throwable $exception)
    {
        Log::warning("Gagal kirim email survey: {$exception->getMessage()}");
    }
}
