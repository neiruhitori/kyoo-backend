<?php

namespace App\Http\Controllers\AdminBranch;

use App\Http\Controllers\Controller;
use App\Slot;
use App\Service;
use Illuminate\Http\Request;
use App\Http\Requests\AdminBranch\StoreSlot;
use App\Http\Requests\AdminBranch\UpdateSlot;

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
        $request->session()->flash('success', 'Slot for service '.$service->name.' has been inserted!');
        return redirect(route('adminBranch.service.slot.index', $service->id));
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
        $slot->update($request->all());
        $request->session()->flash('warning', 'Slot has been updated!');
        return redirect(route('adminBranch.service.slot.index', $slot->service_id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Slot  $slot
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Slot $slot)
    {
        $slot->delete();
        $request->session()->flash('error', 'Slot has been removed!');
        return redirect(route('adminBranch.service.slot.index', $slot->service_id));
    }
}
