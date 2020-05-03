<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreScheduleTemplate;
use App\Http\Requests\Admin\UpdateScheduleTemplate;
use App\Imports\ScheduleTemplateDetailImport;
use App\ScheduleTemplate;
use App\Log;

use Storage;
use Excel;
use Auth;
class ScheduleTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $schedules = ScheduleTemplate::all();
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
        Log::create([
            'user_id' => Auth::id(),
            'description' => 'Import Schedule Template'
        ]);
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
        return view('admin.scheduleTemplate.show')->withSchedule($scheduleTemplate);
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
    public function update(UpdateScheduleTemplate $request, ScheduleTemplate $scheduleTemplate)
    {
        // remove all schedule template detail
        foreach ($scheduleTemplate->ScheduleTemplateDetail as $detail) {
            $detail->delete();
        }
        Storage::disk('public')->delete($scheduleTemplate->file);
        $scheduleTemplate->file = Storage::disk('public')->put('schedule_templates', $request->file);
        $scheduleTemplate->save();
        Excel::import(new ScheduleTemplateDetailImport($scheduleTemplate->id), "storage/$scheduleTemplate->file");
        Log::create([
            'user_id' => Auth::id(),
            'description' => 'Update Schedule Template'
        ]);
        $request->session()->flash('warning', 'Schedule Template '.$scheduleTemplate->name.' has been updated!');
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ScheduleTemplate  $scheduleTemplate
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, ScheduleTemplate $scheduleTemplate)
    {
        Storage::disk('public')->delete($scheduleTemplate->file);
        $scheduleTemplate->delete();
        Log::create([
            'user_id' => Auth::id(),
            'description' => 'Remove Schedule Template'
        ]);
        $request->session()->flash('error', 'Schedule Template '.$scheduleTemplate->name.' has been removed!');
        return redirect(route('admin.scheduleTemplate.index'));
    }
}
