<?php

namespace App\Http\Controllers\AdminBranch;

use App\Http\Controllers\Controller;
use App\Service;
use App\Log;
use App\Department;
use Illuminate\Http\Request;
use App\Http\Requests\AdminBranch\StoreService;
use App\Http\Requests\AdminBranch\UpdateService;
use Auth;

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
        return view('adminBranch.service.index')->withServices($services);
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
        $input = $request->all();
        $input['branch_id'] = Auth::user()->branch_id;
        Service::create($input);
        Log::create([
            'user_id' => Auth::id(),
            'description' => 'Insert Service'
        ]);
        $request->session()->flash('success', __('module.created', ['module' => __('Service'), 'name' => $request->name]));
        return redirect(route('adminBranch.service.index'));
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
        return redirect(route('adminBranch.service.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Service $service)
    {
        // gate
        if ($service->branch_id != Auth::user()->branch_id) {
            return redirect(route('unauthorized'));
        }
        
        $service->delete();
        Log::create([
            'user_id' => Auth::id(),
            'description' => 'Remove Service'
        ]);
        $request->session()->flash('error', __('module.removed', ['module' => __('Service'), 'name' => $service->name]));
        return redirect(route('adminBranch.service.index'));
    }
}
