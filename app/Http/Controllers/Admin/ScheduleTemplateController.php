<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreScheduleTemplate;
use App\Imports\ScheduleTemplateDetailImport;
use App\ScheduleTemplate;
use App\ScheduleTemplateDetail;

use Storage;
use Excel;

class ScheduleTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $schedules = ScheduleTemplateDetail::all();
        return view('admin.scheduleTemplate.index')->withSchedules($schedules);
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
    public function store(StoreScheduleTemplate $request)
    {
        $input = $request->all();
        $input['file'] = Storage::disk('public')->put('schedule_templates', $request->file);
        $scheduleTemplate = ScheduleTemplate::create($input);

        Excel::import(new ScheduleTemplateDetailImport($scheduleTemplate->id), "storage/$scheduleTemplate->file");

        $request->session()->flash('success', 'Schedule Template '.$request->name.' has been added!');
        return redirect(route('admin.scheduleTemplate.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\ScheduleTemplate  $scheduleTemplate
     * @return \Illuminate\Http\Response
     */
    public function show(ScheduleTemplate $scheduleTemplate)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ScheduleTemplate  $scheduleTemplate
     * @return \Illuminate\Http\Response
     */
    public function edit(ScheduleTemplate $scheduleTemplate)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ScheduleTemplate  $scheduleTemplate
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ScheduleTemplate $scheduleTemplate)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ScheduleTemplate  $scheduleTemplate
     * @return \Illuminate\Http\Response
     */
    public function destroy(ScheduleTemplate $scheduleTemplate)
    {
        //
    }
}
