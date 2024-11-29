<?php

namespace App\Http\Controllers\Admin;

use PDF;
use Carbon\Carbon;
use App\BranchType;
use App\Models\Invoice;
use App\Models\ItemPrices;
use App\Models\Subscription;
use Illuminate\Http\Request;
use App\Models\BillingPricesModel;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class BillingController extends Controller
{
    public function index()
    {
        $invoice = Invoice::with('branch')->orderBy('created_at', 'desc')->get();

        return view('admin.billing.index',compact('invoice'));
    }
    public function show($id)
    {
        //di daftar branch utk menampilkan history per branch
        $invoice = Invoice::where('branch_id',$id)->get();

        return view('admin.branch.billing',compact('invoice'));
    }
    public function create()
    {
        $prices = BillingPricesModel::with('Branches_type')->get();
        // dd($prices);
        return view('admin.billing.config.create',compact('prices'));
    }
    public function print($id)
    {
        $print = Invoice::with('branch')->where('id_invoice',$id)->first();
        $subs = Subscription::where('invoice',$print->invoice_number)->first();
         $total = $print->amount;
         $subTotal = $total / 1.11;
         $ppn = $total - $subTotal;
        
         return view('admin.billing.print', compact('print', 'subs', 'total', 'subTotal', 'ppn')); 
    }
    public function itemList()
    {
        $items = ItemPrices::all();

        return view('admin.billing.items.index',compact('items'));
    }
    public function itemEdit($id)
    {
        $items = ItemPrices::find($id);

        return view('admin.billing.items.edit',compact('items'));
    }
    
    public function itemUpdate(Request $request, $id)
    {
            if (strpos($request->prices, '.') !== false || strpos($request->prices, ',') !== false) {
                $request->session()->flash('error', 'Angka mengandung karakter yang tidak diizinkan');
                return redirect()->back();
            }

            $item = ItemPrices::find($id);
            if($item){
                $item->update([
                    'item_name' => $request->item_name,
                    'prices' => $request->prices,
                ]);
                $request->session()->flash('warning', 'Data Berhasil Diubah');
                return redirect(route('admin.billing.item'));
            }
    }
    public function list()
    {
        $prices = BillingPricesModel::with('Branches_type')->get();

        return view('admin.billing.config.index',compact('prices'));
    }
   
    public function priceEdit($id)
    {
        $prices = BillingPricesModel::with('Branches_type')
                ->where('id',$id)
                ->first();

        return view('admin.billing.config.edit',compact('prices'));
    }
    public function priceUpdate(Request $request, $id)
    {
        $request->validate([
            'billing_types' => 'required|string',
            'prices' => 'required|string',
            'subscription_duration' => 'required|integer',
        ]);
        if (strpos($request->prices, '.') !== false || strpos($request->prices, ',') !== false) {
            $request->session()->flash('error', 'Angka mengandung karakter yang tidak diizinkan');
            return redirect()->back();
        }

        $price = BillingPricesModel::where('id',$id)->update([
            "billing_types" => $request->billing_types,
            "prices" => $request->prices,
            "subscription_duration" => $request->subscription_duration,
        ]);
        if(!$price){
            $request->session()->flash('error', 'Data Gagal Diubah');
            return redirect()->back();
        }
        $request->session()->flash('warning', 'Data Berhasil Diubah');
        return redirect(route('admin.billing.config'));
    }
    public function priceStore(Request $request)
    {
        
        $validator = $request->validate([
            'queue_types' => 'required',
            'branches_types' => 'required',
            'billing_types' => 'required',
            'prices' => 'required|numeric',
            'subscription_duration' => 'required|numeric',
        ]);
        if (strpos($request->prices, '.') !== false || strpos($request->prices, ',') !== false) {
            $request->session()->flash('error', 'Angka mengandung karakter yang tidak diizinkan');
            return redirect()->back();
        }
        $branches_number = ($request->queue_types == "onsite") ? 7 : 6;

        DB::beginTransaction();
        try{

            $query = BillingPricesModel::updateOrCreate(
                [
                'branch_type_id' => $branches_number,
                'billing_types' => $request->billing_types,
                'subscription_duration' => $request->subscription_duration,
                ],
                [
                    'prices' => $request->prices
                ]);

                DB::commit();
                if ($query->wasRecentlyCreated) {
                    $request->session()->flash('success', 'Data Berhasil Dibuat');
                } else {
                    $request->session()->flash('warning', 'Data Berhasil Diubah');
                }
        
                return redirect(route('admin.billing.config'));

        }catch(\Exception $e){
            DB::rollBack();
            $request->session()->flash('error', 'Error :' .$e->getMessage());
            return redirect()->back();
        }

    }
}
