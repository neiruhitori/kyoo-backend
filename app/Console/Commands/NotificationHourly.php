<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Notification;
use App\Appointment;

class NotificationHourly extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notification:hourly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reminder for customer about one hour before appointments';

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
        
        $appointments = Appointment::whereHas('Slot', function($query) use ($hourStart, $hourEnd){
            $query->whereBetween('start_time', [$hourStart, $hourEnd]);
        })->where('date', $date)->where('status', 'book')->get();
        
        foreach ($appointments as $appointment) {
            Notification::create([
                'user_id' => $appointment->user_id,
                'text' => "Hi, reminder for your appointment {$appointment->Slot->Service->name} - {$appointment->Slot->Service->Branch->name} at {$appointment->Slot->start_time}"
            ]);
        }
    }
}
