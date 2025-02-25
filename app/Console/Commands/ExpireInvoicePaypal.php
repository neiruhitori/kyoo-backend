<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Invoice;
use App\Models\Subscription;
use Illuminate\Console\Command;

class ExpireInvoicePaypal extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoice:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Paypal Invoice Expiry';

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
        $expiredInvoiceNumbers = Invoice::whereIn('status', ['PENDING', 'APPROVED'])
            ->where('expiry_date', '<=', Carbon::now())
            ->where('payment_gateway', 'PAYPAL')
            ->pluck('invoice_number'); 

        // Update invoice
        Invoice::whereIn('invoice_number', $expiredInvoiceNumbers)
            ->update(['status' => 'EXPIRED']);

        // Update subscription
        Subscription::whereIn('invoice', $expiredInvoiceNumbers)
            ->update(['status' => 'expired']);
    }
}
