<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Appointment;

class UpdateAppointmentStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'appointment:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to update appointment status in case VCT forget to change';

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
        $date = date('Y-m-d', strtotime('-1 day'));
        $appointments = Appointment::where('date', $date)->whereNotIn('status', ['no show', 'end served'])->get();

        foreach ($appointments as $appointment) {
            switch ($appointment->status) {
                case 'book':
                case 'check in':
                    $appointment->status = 'no show';
                    break;
                case 'served':
                    $appointment->status = 'end served';
                    $appointment->end_served_time = date('Y-m-d H:i:s');
                    break;
            }
            $appointment->save();
        }
    }
}
