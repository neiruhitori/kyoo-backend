<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Notification;

class NotificationTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notification:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Testing notification with cron minutes';

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
        Notification::create([
            'user_id' => 1,
            'text' => 'from test notification'
        ]);
    }
}
