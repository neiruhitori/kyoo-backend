<?php

namespace App\Http\Controllers\AdminBranch;

use App\Http\Controllers\Controller;
use App\Service;
use App\Log;
use Illuminate\Http\Request;
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
        return view('adminBranch.service.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();
        $input['branch_id'] = Auth::user()->branch_id;
        Service::create($input);
        Log::create([
            'user_id' => Auth::id(),
            'description' => 'Insert Service'
        ]);
        $request->session()->flash('success', 'Service '.$request->name.' has been inserted!');
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
        return view('adminBranch.service.edit')->withService($service);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Service $service)
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
        $request->session()->flash('warning', 'Service '.$request->name.' has been updated!');
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
        $request->session()->flash('error', 'Service '.$service->name.' has been removed!');
        return redirect(route('adminBranch.service.index'));
    }
}
