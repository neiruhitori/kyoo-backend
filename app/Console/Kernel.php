<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        $schedule->command('notification:hourly')->hourly();
        $schedule->command('notification:daily')->dailyAt('07:00')->timezone('Asia/Jakarta');
        $schedule->command('appointment:update')->dailyAt('23:55')->timezone('Asia/Jakarta');
        $schedule->command('directQueue:update')->dailyAt('23:55')->timezone('Asia/Jakarta');
        $schedule->command('branchToken:expriy')->dailyAt('01:00')->timezone('Asia/Jakarta');
        $schedule->command('report:generate')->monthly()->timezone('Asia/Jakarta');
        $schedule->command('report:feed')->dailyAt('02:00')->timezone('Asia/Jakarta');
        $schedule->call(function () {
            Log::info('Cron test is running');
        })->everyThirtyMinutes();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
