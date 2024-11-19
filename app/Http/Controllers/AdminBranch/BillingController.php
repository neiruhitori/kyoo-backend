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

class BillingController extends Controller
{
    public function index()
    {
        $branch_id = Auth::user()->branch->id;
        $features = FeatureSubscription::with('AdditionalFeature')
        ->where('branch_id',$branch_id)->get();
        $invoice = Invoice::where('branch_id',$branch_id)->orderBy('created_at','desc')->get();

        return view('adminBranch.billing.index',compact('features','invoice'));
    }

    public function no_transaksi()
    {
        $kd =  mt_rand(1000, 9999);
        return 'KYOO_INV' . date('dmy') . $kd;
    }

    public function generateDesc($dataDesc,$branch){
        if(!$dataDesc && !$branch){
            return false;
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

        $desc = sprintf(
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
            $desc .= sprintf(
                ", Web Signage %d Perangkat",
                $signage,
            );
        }
        if ($kiosk > 0) {
            $desc .= sprintf(
                ", Web Kiosk %d Perangkat",
                $kiosk
            );
        }
        

        return $desc;
 
    }

    public function storeInvoice(Request $request)
    {
        $credentials = base64_encode(config('app.xendit_api_key'));
        $client = new \GuzzleHttp\Client();

        try{

            $user= Auth::user()->id;
            $branch= Auth::user()->Branch->id;
            $invoice_number = $this->no_transaksi();
            $invoice_duration = Carbon::now()->addDays(3)->diffInSeconds() + 1; //tepat 14 hari
            // $invoice_duration = 5*60;
            $description = $this->generateDesc($request->all(),$branch);
            $amount = (int) $request->amount; //terkadang value nya desimal, jadi dibulatkan kebawah
    
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
                'description' => $description,
                'invoice_number' => $invoice_number,
                'user_id' => $user,
                'branch_id' => $branch,
                'created_at' => Carbon::now(),
                'amount' => $amount
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

        
    }
    public function invoiceForm()
    {
        if(Auth::user()->Branch->is_premium){
            return redirect(route('admin-branch.billing'));
        }

           $type=Auth::user()->Branch->branch_type_id;
           $branchType = BranchType::where('id',$type)->first();
           $isDirect = $branchType->is_direct_queue;

            $unpaidInvoice = Invoice::where('branch_id', Auth::user()->Branch->id)
                        ->where('status','PENDING')->first();

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
    public function getBilling(Request $request)
    {
           $type=Auth::user()->Branch->branch_type_id;
           $branchType = BranchType::where('id',$type)->first();
           $isDirect = $branchType->is_direct_queue;
           $branch_type_id = $isDirect ? 7 : 6;

           $duration = $request->input('duration');
           $license = $request->input('license');

           $dataBilling = BillingPricesModel::where('branch_type_id', $branch_type_id)
           ->where('subscription_duration', $duration)
           ->where('billing_types', $license)
           ->first(['prices', 'billing_types', 'subscription_duration']);

           if (!$dataBilling) {
            return response()->json([
                'status' => 404,
                'message' => 'Lisensi Tidak Tersedia'
            ]);
        }
           $totalItems = 0;
           $totalKiosk = 0;
           $totalTable=0;
           $totalSignage=0;
           

           if($license == 'custom'){
            $tableQty = $request->input('table_qty');
            $kioskQty = $request->input('kiosk_qty');
            $signageQty = $request->input('signage_qty');

            $signagePrices =  ItemPrices::find(4); //harga signage
            $kioskPrices =  ItemPrices::find(5); //harga kiosk
            
            // ${harga_item selama 1 bulan} * ${durasi_langganan} * ${jumlah_meja}
            $totalSignage = $signagePrices->prices * $duration * $signageQty;
            $totalKiosk = $kioskPrices->prices * $duration * $kioskQty;

            // ${harga_meja selama 1 bulan} * ${durasi_langganan} * ${jumlah_meja}
            $totalTable = $dataBilling->prices * $duration * $tableQty;
            

            $totalItems = $totalTable + $totalKiosk + $totalSignage;

        }else{

            $totalItems = $dataBilling->prices;
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
                ]
            ]);
        
    }
   
    public function print($id)
    {
        $print = Invoice::with('branch')->where('id_invoice',$id)->first();
        $subs = Subscription::where('invoice',$print->invoice_number)->first();
         $total = $print->amount;
         $subTotal = $total / 1.11;
         $ppn = $total - $subTotal;
   
        
         return view('adminBranch.billing.print', compact('print', 'subs', 'total', 'subTotal', 'ppn')); 
    }


    
}
