<?php

namespace App\Http\Controllers\AdminBranch;

use PDF;
use App\Branch;
use Carbon\Carbon;
use App\BranchType;
use App\Models\Invoice;
use App\Models\ItemPrices;
use App\BranchConfiguration;
use App\Models\Subscription;
use Illuminate\Http\Request;
use App\Models\AdditionalFeature;
use App\Models\BillingPricesModel;
use Illuminate\Support\Facades\DB;
use App\Models\FeatureSubscription;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class BillingController extends Controller
{

    private $paypalBaseUrl;

    public function __construct(){
        $this->paypalBaseUrl = env('PAYPAL_MODE') === 'live'
        ? 'https://api-m.paypal.com'
        : 'https://api-m.sandbox.paypal.com';
    }

    public function index(Request $request)
    {
        if(Auth::user()->Branch->BranchType->is_exhibition){
            abort(404);
        }
        $branch_id = Auth::user()->branch->id;
        $features = FeatureSubscription::with('AdditionalFeature')
        ->where('branch_id',$branch_id)->get();
        $invoices = Invoice::where('branch_id',$branch_id)->orderBy('created_at','desc')->get();
        if ($request->query('success')) {
            session()->flash('success', 'Billing processed, please wait several minutes and refresh the page');
        } 
        return view('adminBranch.billing.index',compact('features','invoices'));
    }

    public function paypalAccessToken(){
        // dd(config('app.paypal_client_id'),config('app.paypal_secret_key'));
        $response = Http::asForm()->withBasicAuth(config('app.paypal_client_id'),config('app.paypal_secret_key'))
                    ->post("{$this->paypalBaseUrl}/v1/oauth2/token",[
                        'grant_type' => 'client_credentials',
                    ]);
        if ($response->failed()) {
             throw new \Exception('Failed to retrieve PayPal access token');
        }
        return $response->json()['access_token'];
    }

    public function no_transaksi()
    {
        $kd =  mt_rand(1000, 9999);
        return 'KYOO_INV' . date('dmy') . $kd;
    }

    public function generateDesc($dataDesc,$branch){
        if(!$dataDesc && !$branch){
            return false;
            // dd('tidak ada data diberikan');
        }
        $branchModel = Branch::where('id',$branch)->first();
        $branchType = null;

        if(!$branchModel){
            return false;
        }
        //cek tipe branch
        if($branchModel->BranchType->is_appointment){
            $branchType = "Appointment";
        }elseif($branchModel->BranchType->is_direct_queue){
            $branchType = "Onsite";
        }

        $duration = $dataDesc['subs_duration'];
        $license = $dataDesc['packageSelection'];
        $queue = $dataDesc['queue'];
        $services = $dataDesc['services'];
        $table = $dataDesc['table'];
        $kiosk = $dataDesc['kiosk'] ?? 0;
        $signage = $dataDesc['signage'] ?? 0;
        $startDate = Carbon::now();
        $endDate = $startDate->copy()->addMonths($duration);

        $descIndo = sprintf(
            "Berlangganan Antrian %s - Lisensi %s Selama %d Bulan (%s - %s). Jenis Antrian %s, %d Bulan Langganan, Max %d Antrian, Max Petugas Layanan %d User, %d Meja",
            $branchType,
            $license,
            $duration,
            $startDate->format('d/m/Y'),
            $endDate->format('d/m/Y'),
            $branchType,
            $duration,
            $queue,
            $services,
            $table,
        );

        if ($signage > 0) {
            $descIndo .= sprintf(
                ", Web Signage %d Perangkat",
                $signage,
            );
        }
        if ($kiosk > 0) {
            $descIndo .= sprintf(
                ", Web Kiosk %d Perangkat",
                $kiosk
            );
        }

        $descEn = sprintf(
            "%s Queue Subscription - %s license for %d Months (%s - %s). %s Queue Type, %d Months Subscription, Max %d Queues, Max Service Staff %d User, %d Workstation",
            $branchType,
            $license,
            $duration,
            $startDate->format('d/m/Y'),
            $endDate->format('d/m/Y'),
            $branchType,
            $duration,
            $queue,
            $services,
            $table,
        );

        if ($signage > 0) {
            $descEn .= sprintf(
                ", Web Signage %d Device",
                $signage,
            );
        }
        if ($kiosk > 0) {
            $descEn .= sprintf(
                ", Web Kiosk %d Device",
                $kiosk
            );
        }
        

        return [
            'desc_indo' => $descIndo,
            'desc_en' => $descEn,
        ];
 
    }

    public function storeInvoice(Request $request)
    {
        $credentials = base64_encode(config('app.xendit_api_key'));
        $client = new \GuzzleHttp\Client();

        if(Auth::user()->Branch->BranchType->is_exhibition){
            abort(404);
        }

        $user= Auth::user()->id;
        $branch= Auth::user()->Branch->id;
        $invoice_number = $this->no_transaksi();
        $invoice_duration = Carbon::now()->addDays(3)->diffInSeconds() + 1; //tepat 3 hari
        // $invoice_duration = 5*60;
        $description = $this->generateDesc($request->all(),$branch);
        $amount = (int) $request->amount; //terkadang value nya desimal, jadi dibulatkan kebawah
       if (Auth::user()->Branch->country == 'Indonesia') {
            try{
                $response = $client->post('https://api.xendit.co/v2/invoices',
                [
                    'headers' => [
                        'Authorization' => 'Basic ' . $credentials,
                    ],
                    'json' =>[
                        'external_id' => $invoice_number, 
                        'amount' => $amount,
                        'invoice_duration' => $invoice_duration,
                        'success_redirect_url' => url('/admin-branch/billing')
                    ],
                ]);
                $jsonResponse = json_decode($response->getBody(), true);
                $data = $jsonResponse;
        
                $invoice_data = [
                    'id_invoice' => $data['id'],
                    'invoice_url' => $data['invoice_url'],
                    'expiry_date' =>Carbon::parse($data['expiry_date'])->setTimezone('Asia/Jakarta'),
                    'status' => $data['status'],
                    'description' => $description['desc_indo'],
                    'description_en' => $description['desc_en'],
                    'invoice_number' => $invoice_number,
                    'user_id' => $user,
                    'branch_id' => $branch,
                    'created_at' => Carbon::now(),
                    'amount' => $amount,
                    'payment_gateway' => 'XENDIT',
                    'currency' => 'IDR',
                ];
                \DB::transaction(function () use ($invoice_data,$user,$branch,$invoice_number,$request){

                    $invoice = Invoice::insert($invoice_data);
                    $subscription = Subscription::insert([
                        'user_id' => $user,
                        'branch_id' => $branch,
                        'invoice' => $invoice_number,
                        'package' => $request->packageSelection ,
                        'license_type' => $request->license_type ,
                        'subs_duration' => $request->subs_duration ,
                        'queue' => $request->queue ,
                        'max_table' => $request->table ,
                        'max_service' => $request->services ,
                        'kiosk' => $request->kiosk ,
                        'created_at' => Carbon::now()->setTimezone('Asia/Jakarta'),
                        'status' => 'pending'
                    ]);
                });
                
                $request->session()->flash('success', 'Invoice berhasil dibuat');

                return redirect()->back();

            }catch(\Guzzle\Http\Exception\BadResponseException $e){
                $response = $e->getResponse();
                $response = json_decode($response->getBody()->getContents(), true);
                return response()->json([
                    'status' => 'error',
                    'message' => $response
                ], 403);
            }
        }else{
            try {
                $response = $client->post("{$this->paypalBaseUrl}/v2/checkout/orders",
                [
                    'headers' => [
                        'Authorization' => "Bearer {$this->paypalAccessToken()}",
                        'Content-Type'  => 'application/json',
                    ],
                    'json' => [
                        'intent' => 'CAPTURE',
                        'purchase_units' => [
                            [
                                'reference_id' => $invoice_number,
                                'amount' => [
                                    'currency_code' => 'USD',
                                    'value' => $amount,
                                ],
                            ],
                        ],
                        'application_context' => [
                            'return_url' => url('/admin-branch/billing').'?success=1',
                            'cancel_url' => url('/admin-branch/billing'),
                            'shipping_preference' => 'NO_SHIPPING',
                            'payment_method' => [
                                'payee_preferred' => 'IMMEDIATE_PAYMENT_REQUIRED',
                                'allowed_payment_method' => 'INSTANT_FUNDING_SOURCE'
                            ]
                        ],
                    ],
                ]);
                $jsonResponse = json_decode($response->getBody(), true);
                $data = $jsonResponse;
                $approveLink = null;

                foreach ($data['links'] as $link) {
                    if ($link['rel'] === 'approve') {
                        $approveLink = $link['href'];
                        break;
                    }
                }
                $countryTimezones = [
                    'Indonesia' => 'Asia/Jakarta',
                    'Vietnam' => 'Asia/Ho_Chi_Minh',
                    'Singapore' => 'Asia/Singapore',
                ];
                $timezone = $countryTimezones[Auth::user()->Branch->country];
                $invoice_data = [
                    // 'id_invoice' => '1A867992C6617241Y',
                    // 'invoice_url' => 'https://www.sandbox.paypal.com/checkoutnow?token=1A867992C6617241Y',
                    // 'status' => 'CREATED',
                    'id_invoice' => $data['id'],
                    'invoice_url' => $approveLink,
                    'status' => 'PENDING',
                    // 'expiry_date' =>Carbon::now()->addDays(3)->setTimezone($timezone),
                    'expiry_date' =>Carbon::now()->addMinutes(5)->setTimezone($timezone),
                    'description' => $description['desc_indo'],
                    'description_en' => $description['desc_en'],
                    'invoice_number' => $invoice_number,
                    'user_id' => $user,
                    'branch_id' => $branch,
                    'created_at' => Carbon::now()->setTimezone($timezone),
                    'amount' => $amount,
                    'payment_gateway' => 'PAYPAL',
                    'currency' => 'USD',
                ];
                \DB::transaction(function () use ($invoice_data,$user,$branch,$invoice_number,$request,$timezone){

                    $invoice = Invoice::insert($invoice_data);
                    $subscription = Subscription::insert([
                        'user_id' => $user,
                        'branch_id' => $branch,
                        'invoice' => $invoice_number,
                        'package' => $request->packageSelection ,
                        'license_type' => $request->license_type ,
                        'subs_duration' => $request->subs_duration ,
                        'queue' => $request->queue ,
                        'max_table' => $request->table ,
                        'max_service' => $request->services ,
                        'kiosk' => $request->kiosk ,
                        'created_at' => Carbon::now()->setTimezone($timezone),
                        'status' => 'pending'
                    ]);
                });

                $request->session()->flash('success', 'Invoice berhasil dibuat');

                return redirect()->back();
                // dd($invoice_data);
            } catch (\Guzzle\Http\Exception\BadResponseException $e) {
                $response = $e->getResponse();
                $response = json_decode($response->getBody()->getContents(), true);
                return response()->json([
                    'status' => 'error',
                    'message' => $response
                ], 403);
            }
        }
        
    }
    public function invoiceForm()
    {
        if(Auth::user()->Branch->is_premium){
            return redirect(route('admin-branch.billing'));
        }
        if(Auth::user()->Branch->BranchType->is_exhibition){
            return back();
        }

           $type=Auth::user()->Branch->branch_type_id;
           $branchType = BranchType::where('id',$type)->first();
           $isDirect = $branchType->is_direct_queue;

            $unpaidInvoice = Invoice::where('branch_id', Auth::user()->Branch->id)
                            ->whereIn('status', ['PENDING', 'APPROVED'])
                            ->first();

            $subscription = $unpaidInvoice ? Subscription::where('invoice', $unpaidInvoice->invoice_number)
                                      ->where('status', 'pending')
                                      ->first() : null;
            return view('adminBranch.billing.invoiceForm',compact('isDirect','unpaidInvoice','subscription'));
    }

    public function callbackInvoice(Request $request)
    {
                $callbackToken = config('app.xendit_callback_token');
   
                if ($request->header('x-callback-token') != $callbackToken) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Request Ditolak'
                    ], 403);
                }
        try{
    
          // aman sedikit dunia
          DB::transaction(function () use ($request) {
              // if ($invoice) {
                if ($request->status == "PAID") {
                     $invoice = Invoice::where('invoice_number', $request->external_id)->first();
                  
                    if($invoice){
                        Invoice::where('id_invoice', $request->id)
                        ->where('status', 'PENDING')->update([
                            'status' => $request->status,
                        ]);
                        
                        $subscription = Subscription::where('invoice', $request->external_id)
                        ->where('status', 'pending')->update([
                            'status' => 'active',
                        ]);
               
                   if($subscription){
                          //ambil data membership di subscription
                          $data = Subscription::where('invoice', $request->external_id)
                          ->where('status', 'active')
                          ->first();
                    if($data){
                           
                          //setup
                          $branch_id = $data->branch_id;
                          $features = AdditionalFeature::all();
                          $license = null;
                          
                          if($data->license_type == "onsite"){
                              $license = 7; //PDQ
                          }else{
                              $license = 6; //PA
                          }
                          //reset fitur branch
                          FeatureSubscription::where('branch_id', $branch_id)->delete();

                          // cek paket pilihan
                            if ($data->package === "premium") {
                                // 1 dan 2 khusus premium
                                $featuresData = $features->filter(function($feature) {
                                    return in_array($feature->id, [1, 2]);
                                })->map(function($feature) use($branch_id) {
                                    return [
                                        'branch_id'  => $branch_id,
                                        'feature_id' => $feature->id,
                                        'created_at' => date('Y-m-d H:i:s')
                                    ];
                                });
                            } elseif ($data->package === "custom") {
                                $featuresData = $features->map(function($feature) use($branch_id) {
                                    return [
                                        'branch_id'  => $branch_id,
                                        'feature_id' => $feature->id,
                                        'created_at' => date('Y-m-d H:i:s')
                                    ];
                                });
                            } elseif ($data->package === "lite") {
                                $featuresData = collect(); // kosongkan 
                            }

                          
                            if ($featuresData->isNotEmpty()) {
                                FeatureSubscription::insert($featuresData->toArray());
                            }
                          //set ke branch
                          Branch::where('id', $branch_id)->update([
                              'branch_type_id' => $license,
                              'max_counter' => $data->max_table,
                              'max_queue' => $data->queue,
                              'license_expiration_date' => Carbon::now()->addMonths($data->subs_duration)->format('Y-m-d H:i:s'),
                          ]);
                            BranchConfiguration::where('branch_id',$branch_id)->update([
                                'max_services' => $data->max_service,
                            ]);
                          }
                         
                       }
                            else{
                                        return response()->json([
                                            'status' => 'error',
                                            'message' => 'Data Subscription Not Found'
                                        ], 404);
                                    }
                    }else{
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Invoice Not Found'
                        ], 404);
                    }
                   


                } elseif ($request->status == "EXPIRED") {
                  
                    Invoice::where('id_invoice', $request->id)
                        ->where('status', 'PENDING')->update([
                            'status' => $request->status,
                        ]);

                    Subscription::where('invoice', $request->external_id)
                        ->where('status', 'pending')->update([
                            'status' => 'expired',
                        ]);
                }
        });
        return response()->json([
            'status' => 'Success',
            'message' => 'Transaksi Berhasil'
        ]);

        }catch(\Exception $e){

            return response()->json([
                'status' => 'error',
                'message' => $e
            ], 400);
        }
        
    }

    public function callbackPaypal(Request $request)
    {
        $client = new \GuzzleHttp\Client();
        //in progress
        try{
            $event = $request->all();

            \Log::info('Data:', ['data' => $event]);
            switch ($event['event_type']) {
                case 'CHECKOUT.ORDER.APPROVED':
                    try{
                        $orderID = $event['resource']['id'];
                        $response = $client->post("{$this->paypalBaseUrl}/v2/checkout/orders/{$orderID}/capture", [
                            'headers' => [
                                'Authorization' => "Bearer {$this->paypalAccessToken()}",
                                'Content-Type'  => 'application/json'
                            ]
                        ]);
                        Invoice::where('id_invoice', $orderID)
                            ->where('status', 'PENDING')
                            ->where('payment_gateway', 'PAYPAL')
                            ->update([
                                'status' => 'APPROVED',
                            ]);
                    }catch(\GuzzleHttp\Exception\RequestException $e){
                        if ($e->hasResponse()) {
                            $errorResponse = json_decode($e->getResponse()->getBody(), true);
                            \Log::info("Error Occurred:", ['response' => $errorResponse]);
                        } else {
                            \Log::info("Error Occurred: No response from PayPal.");
                        }
                    }
                    break;
                case 'PAYMENT.CAPTURE.COMPLETED':

                    DB::transaction(function () use ($event) {
                        $orderID = $event['resource']['supplementary_data']['related_ids']['order_id']; //invoice_id
                        $invoice =  Invoice::where('id_invoice', $orderID)
                                            ->where('status', 'APPROVED')
                                            ->where('payment_gateway', 'PAYPAL')->first();
                        
                        if($invoice){
                            Invoice::where('id_invoice', $orderID)
                                    ->where('status', 'APPROVED')
                                    ->where('payment_gateway', 'PAYPAL')
                                    ->update(['status' => 'PAID']);
                            $subs = Subscription::where('invoice', $invoice->invoice_number)
                                                ->where('status', 'pending');
                            if ($subs) {
                                $subs->update(['status' => 'active']);
                                $data = Subscription::where('invoice', $invoice->invoice_number)
                                        ->where('status', 'active')
                                        ->first();

                                $branchID = $data->branch_id;
                                $features = AdditionalFeature::all();
                                $license = ($data->license_type == "onsite") ? 7 :6 ;
                                FeatureSubscription::where('branch_id', $branchID)->delete();

                                 // cek paket pilihan
                                    if ($data->package === "premium") {
                                        // 1 dan 2 khusus premium
                                        $featuresData = $features->filter(function($feature) {
                                            return in_array($feature->id, [1, 2]);
                                        })->map(function($feature) use($branchID) {
                                            return [
                                                'branch_id'  => $branchID,
                                                'feature_id' => $feature->id,
                                                'created_at' => date('Y-m-d H:i:s')
                                            ];
                                        });
                                    } elseif ($data->package === "custom") {
                                        $featuresData = $features->map(function($feature) use($branchID) {
                                            return [
                                                'branch_id'  => $branchID,
                                                'feature_id' => $feature->id,
                                                'created_at' => date('Y-m-d H:i:s')
                                            ];
                                        });
                                    } elseif ($data->package === "lite") {
                                        $featuresData = collect(); // kosongkan 
                                    }

                                
                                    if ($featuresData->isNotEmpty()) {
                                        FeatureSubscription::insert($featuresData->toArray());
                                    }
                                     //set ke branch
                                    Branch::where('id', $branchID)->update([
                                        'branch_type_id' => $license,
                                        'max_counter' => $data->max_table,
                                        'max_queue' => $data->queue,
                                        'license_expiration_date' => Carbon::now()->addMonths($data->subs_duration)->format('Y-m-d H:i:s'),
                                    ]);
                                    BranchConfiguration::where('branch_id',$branchID)->update([
                                        'max_services' => $data->max_service,
                                    ]);

                            }else{
                                return response()->json([
                                    'success' => false,
                                    'mes' => 'No subscription found'
                                ]);
                            }
                        }else{
                            return response()->json([
                                'success' => false,
                                'mes' => 'No invoice found'
                            ]);
                        }
                    });
                    break;
                }
        }catch(\Exception $e){
            \Log::info("Error Occurred:", ['response' => $e]);
            return response()->json([
                'status' => 'error',
                'message' => $e
            ], 400);
        }
    }
    public function getBilling(Request $request)
    {
           $type=Auth::user()->Branch->getQueueTypeAttribute();
        //    $branchType = BranchType::where('id',$type)->first();
        //    $isDirect = $branchType->is_direct_queue;
           $branch_type_id = $type == 'onsite' ? 7 : 6;
           $duration = $request->input('duration');
            $license = $request->input('license');
        //    $duration = 3;
        //    $license = 'lite';
        // dd(Auth::user()->Branch);

        $totalItems = 0;
        $totalKiosk = 0;
        $totalTable=0;
        $totalSignage=0;

        $tableQty = $request->input('table_qty');
        $kioskQty = $request->input('kiosk_qty');
        $signageQty = $request->input('signage_qty');
        $signagePrices =  ItemPrices::find(4); //harga signage
        $kioskPrices =  ItemPrices::find(5); //harga kiosk

        $dataBilling = BillingPricesModel::where('branch_type_id', $branch_type_id)
                    ->where('subscription_duration', $duration)
                    ->where('billing_types', $license)
                    ->first(['prices','en_prices' ,'billing_types', 'subscription_duration']);
            
                            if (!$dataBilling) {
                                return response()->json([
                                    'status' => 404,
                                    'message' => 'Lisensi Tidak Tersedia'
                                ]);
                            }
        
           if(Auth::user()->Branch->country != 'Indonesia'){
               // ${harga_item selama 1 bulan} * ${durasi_langganan} * ${jumlah_meja}
                 $totalSignage = $signagePrices->prices * $duration * $signageQty;
                 $totalKiosk = $kioskPrices->prices * $duration * $kioskQty;
   
               // ${harga_meja selama 1 bulan} * ${durasi_langganan} * ${jumlah_meja}
                 $totalTable = $dataBilling->en_prices * $duration * $tableQty;
                 $totalItems = $dataBilling->en_prices * $duration * $tableQty;
           }else{
                    if($license == 'custom'){
                        // ${harga_item selama 1 bulan} * ${durasi_langganan} * ${jumlah_meja}
                        $totalSignage = $signagePrices->prices * $duration * $signageQty;
                        $totalKiosk = $kioskPrices->prices * $duration * $kioskQty;
            
                        // ${harga_meja selama 1 bulan} * ${durasi_langganan} * ${jumlah_meja}
                        $totalTable = $dataBilling->prices * $duration * $tableQty;
                        
            
                        $totalItems = $totalTable + $totalKiosk + $totalSignage;
            
                    }else{
                        $totalItems = $dataBilling->prices;
                    }
           }
            return response()->json([
                'status' => 200,
                'data' => [
                    'total_kiosk_prices' => $totalKiosk,
                    'total_table_prices' =>  $totalTable,
                    'signage_prices' => $totalSignage,
                    'license_prices' => $totalItems ,
                    'billing_type' => $dataBilling->billing_types,
                    'subscription_duration' => $dataBilling->subscription_duration,
                    'country' => Auth::user()->Branch->country,
                ]
            ]);
        
    }
   
    public function print($id)
    {
        $print = Invoice::with('branch')->where('id_invoice',$id)->first();
        $subs = Subscription::where('invoice',$print->invoice_number)->first();
        $total = $print->amount;
        $subTotal = $total;
        $ppn = 0;
        $country = $print->branch->country;

         if ($country == 'Indonesia') {
             $subTotal = $total / 1.11;
             $ppn = $total - $subTotal;
            }
   
        
         return view('adminBranch.billing.print', compact('print', 'subs', 'total', 'subTotal', 'ppn','country')); 
    }


    
}
