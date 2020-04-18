<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreScheduleTemplateDetail;
use App\ScheduleTemplateDetail;
use Illuminate\Http\Request;

class ScheduleTemplateDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\ScheduleTemplateDetail  $scheduleTemplateDetail
     * @return \Illuminate\Http\Response
     */
    public function show(ScheduleTemplateDetail $scheduleTemplateDetail)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ScheduleTemplateDetail  $scheduleTemplateDetail
     * @return \Illuminate\Http\Response
     */
    public function edit(ScheduleTemplateDetail $scheduleTemplateDetail)
    {
        return view('admin.scheduleTemplate.scheduleTemplateDetail.edit')->withSchedule($scheduleTemplateDetail);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ScheduleTemplateDetail  $scheduleTemplateDetail
     * @return \Illuminate\Http\Response
     */
    public function update(StoreScheduleTemplateDetail $request, ScheduleTemplateDetail $scheduleTemplateDetail)
    {
        $scheduleTemplateDetail->update($request->all());
        $request->session()->flash('warning', 'Schedule Template Detail '.$request->description.' has been updated!');
        return redirect(route('admin.scheduleTemplate.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ScheduleTemplateDetail  $scheduleTemplateDetail
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, ScheduleTemplateDetail $scheduleTemplateDetail)
    {
        $scheduleTemplateDetail->delete();
        $request->session()->flash('error', 'Schedule Template Detail '.$scheduleTemplateDetail->description.' has been removed!');
        return redirect(route('admin.scheduleTemplate.index'));
    }
}
