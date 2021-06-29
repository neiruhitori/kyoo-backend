<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\BranchToken;

class SetExpiryBranchToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'branchToken:expiry';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set branch token into expiry';

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
        $date = date('Y-m-d', strtotime('-1 year'));
        BranchToken::where('created_at', $date)->delete();
    }
}
