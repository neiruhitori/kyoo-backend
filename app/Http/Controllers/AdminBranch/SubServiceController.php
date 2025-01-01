<?php

namespace App\Http\Controllers\AdminBranch;

use App\Service;
use App\Models\SubService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class SubServiceController extends Controller
{
    public function index(Type $args): void
    {
        # code...
    }
   
    public function edit($id)
    {
        $hasAccess = Auth::user()->Branch->FeatureSubscription->contains('feature_id', 9);
        if(!$hasAccess){
          return back();
        }
        $subService = SubService::find($id);
        return view('adminBranch.service.sub_service.edit', ['sub_service' => $subService]);
    }
    public function create()
    {
        $hasAccess = Auth::user()->Branch->FeatureSubscription->contains('feature_id', 9);
        if(!$hasAccess){
          return back();
        }
        return view('adminBranch.service.sub_service.create');
    }
    public function store(Request $request)
    {
       
       $validate =  $request->validate([
            'name' => 'required'
        ]);

        if($validate){
            $query = SubService::create([
                'branch_id' => Auth::user()->Branch->id,
                'name' => $request->name
            ]);
            if($query){
                $request->session()->flash('success', __('module.created', ['module' => __('Sub Layanan'), 'name' => $request->name]));
                return redirect(route('admin-branch.branch-configuration.department.index'));
            }
            $request->session()->flash('warning', 'Sub layanan gagal ditambahkan');
        }

    }
    public function update(Request $request, $id)
    {
        $validate =  $request->validate([
            'name' => 'required'
        ]);
        if($validate){
            $subService = SubService::find($id);
            if($subService){
                $query = $subService->update($request->all());
                if($query){
                    $request->session()->flash('success', __('module.updated', ['module' => __('Sub Layanan'), 'name' => $request->name]));
                    return redirect(route('admin-branch.branch-configuration.department.index'));
                }
                $request->session()->flash('warning', 'Sub layanan gagal diperbaharui');
            }
           
        }
    }
    public function destroy($id)
    {
        $sub_service = SubService::where('id', $id)
                        ->where('branch_id',Auth::user()->Branch->id)
                        ->firstOrFail();

        $sub_service->delete();
        session()->flash('success', 'Sub Layanan berhasil dihapus!');
        return redirect()->back();
    }



    public function assign(Request $request, $id)
    {
        $hasAccess = Auth::user()->Branch->FeatureSubscription->contains('feature_id', 9);
        if(!$hasAccess){
            return back();
        }
        $service = Service::where('id',$id)->where('branch_id', Auth::user()->Branch->id)->first();
        if(!$service){
            return redirect(route('unauthorized'));
        }
        $pool = SubService::where('branch_id', Auth::user()->Branch->id)->get();

        return view('adminBranch.service.sub_service.assign', [
            'service' => $service
        ]);
    }
    public function add($id)
    {
        $hasAccess = Auth::user()->Branch->FeatureSubscription->contains('feature_id', 9);
        if(!$hasAccess){
            return back();
        }
        $service = Service::where('id',$id)->where('branch_id', Auth::user()->Branch->id)->first();
        if(!$service){
            return redirect(route('unauthorized'));
        }
        $pool = SubService::where('branch_id', Auth::user()->Branch->id)->get();

        return view('adminBranch.service.sub_service.addSubService', [
            'pool' => $pool,
            'service' => $service
        ]);
    }
    public function submitAdd(Request $request, $id)
    {
        $service = Service::where('id', $id)
        ->where('branch_id', Auth::user()->Branch->id)
        ->firstOrFail();

        $exist = DB::table('service_sub_service')
        ->where('service_id', $service->id)
        ->where('sub_service_id', $request->sub_service)
        ->exists();

        if($exist){
            $request->session()->flash('warning', 'Sub layanan sudah ada di Layanan ini!');
            return redirect()->back();
        }
        $sub_service = SubService::where('id', $request->sub_service)
                        ->where('branch_id', Auth::user()->Branch->id)->first();
        
        $service->subServices()->attach($request->sub_service);

        $request->session()->flash('success', "{$sub_service->name} telah ditambahkan");
        return redirect(route('admin-branch.branch-configuration.service.assign', $service->id));
    }
    public function removeSubService(Request $request, $id){

            $service = Service::where('branch_id', Auth::user()->Branch->id)
            ->whereHas('subServices', function ($query) use ($id) {
                $query->where('service_sub_service.id', $id);
            })
            ->with(['subServices' => function ($query) use ($id) {
                $query->where('service_sub_service.id', $id);
            }])->first();

        if (!$service) {
            $request->session()->flash('warning', 'Layanan atau sub layanan tidak ditemukan.');
            return redirect()->back();
        }

        $subService = $service->subServices->first();
        $service->subServices()->detach($subService->id);

        $request->session()->flash('success',
            "Sub layanan '{$subService->name}' berhasil dihapus dari layanan '{$service->name}'."
        );

        return redirect()->back();
    }
    public function editSubService(Request $request, $id)
    {
        $hasAccess = Auth::user()->Branch->FeatureSubscription->contains('feature_id', 9);
        if(!$hasAccess){
            return back();
        }
        $service = Service::where('branch_id', Auth::user()->Branch->id)
        ->whereHas('subServices', function ($query) use ($id) {
            $query->where('service_sub_service.id', $id);
        })
        ->with(['subServices' => function ($query) use ($id) {
            $query->where('service_sub_service.id', $id);
        }])->first();

        if (!$service) {
            $request->session()->flash('warning', 'Layanan atau sub layanan tidak ditemukan.');
            return redirect()->back();
        }
        $pool = SubService::where('branch_id', Auth::user()->Branch->id)->get();
        $subService = $service->subServices->first();
        
        return view('adminBranch.service.sub_service.editSubService', [
            'service' => $service,
            'pool' => $pool,
            'subService' => $subService
        ]);

    }
    public function syncSubService(Request $request)
    {
        $service = Service::where('branch_id', Auth::user()->Branch->id)
        ->whereHas('subServices', function ($query) use ($request) {
            $query->where('service_sub_service.id', $request->pivot_id);
        })
        ->with(['subServices' => function ($query) use ($request) {
            $query->where('service_sub_service.id', $request->pivot_id);
        }])->first();

        $pivot  = $service->subServices->first();
        $exists = $service->subServices()->wherePivot('sub_service_id', $request->sub_service)->exists();

        if ($exists) {
            $request->session()->flash('warning', 'Sub Layanan sudah ada di Layanan ini!');
            return redirect()->back();
        }
        
        $service->subServices()->updateExistingPivot($pivot->id,[
           'sub_service_id' => $request->sub_service
        ]);

        $request->session()->flash('success', "Sub Layanan telah diperbaharui");
        return redirect(route('admin-branch.branch-configuration.service.assign', $service->id));
    }
}
