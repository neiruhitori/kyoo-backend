<?php

namespace App\Http\Controllers\AdminBranch;

use App\Http\Controllers\Controller;
use App\Schedule;
use Illuminate\Http\Request;
use App\Http\Requests\AdminBranch\StoreSchedule;
use App\Http\Requests\AdminBranch\UpdateSchedule;
use Auth;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $schedules = Schedule::whereBranchId(Auth::user()->branch_id)->get();
        return view('adminBranch.schedule.index')->withSchedules($schedules);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('adminBranch.schedule.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreSchedule $request)
    {
        $input = $request->all();
        $input['branch_id'] = Auth::user()->branch_id;
        Schedule::create($input);
        $request->session()->flash('success', 'Schedule has been inserted!');
        return redirect(route('adminBranch.schedule.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Schedule  $schedule
     * @return \Illuminate\Http\Response
     */
    public function show(Schedule $schedule)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Schedule  $schedule
     * @return \Illuminate\Http\Response
     */
    public function edit(Schedule $schedule)
    {
        // gate
        if ($schedule->branch_id != Auth::user()->branch_id) {
            return redirect(route('unauthorized'));
        }

        return view('adminBranch.schedule.edit')->withSchedule($schedule);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Schedule  $schedule
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateSchedule $request, Schedule $schedule)
    {
        // gate
        if ($schedule->branch_id != Auth::user()->branch_id) {
            return redirect(route('unauthorized'));
        }

        $schedule->update($request->all());
        $request->session()->flash('warning', 'Schedule has been updated!');
        return redirect(route('adminBranch.schedule.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Schedule  $schedule
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Schedule $schedule)
    {
        // gate
        if ($schedule->branch_id != Auth::user()->branch_id) {
            return redirect(route('unauthorized'));
        }

        $schedule->delete();
        $request->session()->flash('error', 'Schedule has been removed!');
        return redirect(route('adminBranch.schedule.index'));
    }
}
