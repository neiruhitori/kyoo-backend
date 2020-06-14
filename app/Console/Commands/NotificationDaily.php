<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Notification;
use App\Appointment;
use App\FcmToken;

class NotificationDaily extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notification:daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reminder for customer who have appointment today';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $date = date('Y-m-d');
        $hourStart = date('H:00:00', strtotime('1 hour'));
        $hourEnd = date('H:00:00', strtotime('2 hour'));
        
        $appointments = Appointment::where('date', $date)->where('status', 'book')->get();
        
        foreach ($appointments as $appointment) {
            $recipients = FcmToken::whereUserId($appointment->user_id)->pluck('token');
            fcm()
                ->to($recipients) // $recipients must an array
                ->priority('high')
                ->timeToLive(0)
                ->notification([
                    'title' => 'KYOO Daily Reminder',
                    'body' => "Hi, reminder for your appointment today {$appointment->Slot->Service->name} - {$appointment->Slot->Service->Branch->name} at {$appointment->Slot->start_time}"
                ])
                ->send();
        }
    }
}
