<?php

namespace App\Http\Controllers\AdminBranch;

use App\Http\Controllers\Controller;
use App\Service;
use App\Log;
use App\Department;
use Illuminate\Http\Request;
use App\Http\Requests\AdminBranch\StoreService;
use App\Http\Requests\AdminBranch\UpdateService;
use Illuminate\Support\Facades\Auth;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $services = Service::whereBranchId(Auth::user()->branch_id)->get();
        
        return view('adminBranch.service.index', [
            'services' => $services
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $departments = Department::whereBranchId(Auth::user()->branch_id)->get();
        return view('adminBranch.service.create', [
            'departments' => $departments
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreService $request)
    {
        $total_services = Service::where('branch_id', Auth::user()->branch_id)->count();

        if (!Auth::user()->Branch->is_premium && $total_services >= 5) {
            $request->session()->flash('error', __('Batas maksimal 5 layanan telah terlampaui untuk lisensi gratis'));
            return redirect(route('admin-branch.branch-configuration.service.create'));
        }

        $input = $request->all();
        $input['branch_id'] = Auth::user()->branch_id;

        Service::create($input);
    
        Log::create([
            'user_id' => Auth::id(),
            'description' => 'Insert Service'
        ]);

        $request->session()->flash('success', __('module.created', ['module' => __('Service'), 'name' => $request->name]));
        return redirect(route('admin-branch.branch-configuration.department.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function show(Service $service)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function edit(Service $service)
    {
        // gate
        if ($service->branch_id != Auth::user()->branch_id) {
            return redirect(route('unauthorized'));
        }
        $departments = Department::whereBranchId(Auth::user()->branch_id)->get();
        return view('adminBranch.service.edit', [
            'service' => $service,
            'departments' => $departments
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateService $request, Service $service)
    {
        // gate
        if ($service->branch_id != Auth::user()->branch_id) {
            return redirect(route('unauthorized'));
        }

        $service->update($request->all());
        Log::create([
            'user_id' => Auth::id(),
            'description' => 'Update Service'
        ]);
        $request->session()->flash('warning', __('module.updated', ['module' => __('Service'), 'name' => $request->name]));
        return redirect(route('admin-branch.branch-configuration.department.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Service $service)
    {
        try {
            if ($service->branch_id != Auth::user()->branch_id) {
                return redirect(route('unauthorized'));
            }
            
            $service->delete();
            
            Log::create([
                'user_id' => Auth::id(),
                'description' => 'Remove Service'
            ]);
            
            $request->session()->flash('error', __('module.removed', ['module' => __('Service'), 'name' => $service->name]));

            return redirect(route('admin-branch.branch-configuration.department.index'));
        } catch (\Throwable $e) {
            if (str_contains($e->getMessage(), 'SQLSTATE[23503]')) {
                return back()->with('error', 'Hapus layanan gagal. Data masih direferensikan dari tabel lain');
            }

            return back()->with('error', $e->getMessage());
        }
    }
}
