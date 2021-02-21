<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\DirectQueue;

class UpdateDirectQueueStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'directQueue:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to update direct queue status in case VCT forget to change';

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
     * @return int
     */
    public function handle()
    {
        DirectQueue::whereStatus('waiting')->whereDate('created_at', '<', date('Y-m-d'))->update([
            'status' => 'no show'
        ]);
    }
}
