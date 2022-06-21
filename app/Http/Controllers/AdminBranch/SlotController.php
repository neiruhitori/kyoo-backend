<?php

namespace App\Http\Controllers\AdminBranch;

use App\Http\Controllers\Controller;
use App\Slot;
use App\Service;
use App\Log;
use Illuminate\Http\Request;
use App\Http\Requests\AdminBranch\StoreSlot;
use App\Http\Requests\AdminBranch\UpdateSlot;
use Auth;
class SlotController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Service $service)
    {
        return view('adminBranch.service.slot.index')->withService($service);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Service $service)
    {
        return view('adminBranch.service.slot.create')->withService($service);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreSlot $request, Service $service)
    {
        $input = $request->all();
        $input['service_id'] = $service->id;
        Slot::create($input);
        Log::create([
            'user_id' => Auth::id(),
            'description' => 'Insert Slot'
        ]);
        $request->session()->flash('success', __('module.generated', ['module' => __('Slot'), 'name' => $service->name]));
        return redirect(route('admin-branch.branch-configuration.service.slot.index', $service->id));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Slot  $slot
     * @return \Illuminate\Http\Response
     */
    public function show(Slot $slot)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Slot  $slot
     * @return \Illuminate\Http\Response
     */
    public function edit(Slot $slot)
    {
        // gate
        if ($slot->Service->branch_id != Auth::user()->branch_id) {
            return redirect(route('unauthorized'));
        }
        
        return view('adminBranch.service.slot.edit')->withSlot($slot);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Slot  $slot
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateSlot $request, Slot $slot)
    {
        // gate
        if ($slot->Service->branch_id != Auth::user()->branch_id) {
            return redirect(route('unauthorized'));
        }
        
        $slot->update($request->all());
        Log::create([
            'user_id' => Auth::id(),
            'description' => 'Update Slot'
        ]);
        $request->session()->flash('warning', __('Slot has been updated'));
        return redirect(route('admin-branch.branch-configuration.service.slot.index', $slot->service_id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Slot  $slot
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Slot $slot)
    {
        // gate
        if ($slot->Service->branch_id != Auth::user()->branch_id) {
            return redirect(route('unauthorized'));
        }
        
        $slot->delete();
        Log::create([
            'user_id' => Auth::id(),
            'description' => 'Remove Slot'
        ]);
        $request->session()->flash('error', __('Slot has been removed'));
        return redirect(route('admin-branch.branch-configuration.service.slot.index', $slot->service_id));
    }
}
