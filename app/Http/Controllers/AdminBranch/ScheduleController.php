<?php

namespace App\Http\Controllers\AdminBranch;

use App\Http\Controllers\Controller;
use App\Schedule;
use App\ScheduleTemplate;
use App\Branch;
use App\Log;
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
        $schedules = Schedule::whereBranchId(Auth::user()->branch_id)->orderByRaw(
                        "CASE WHEN Day = 'sunday' THEN 1
                            WHEN Day = 'monday' THEN 2
                            WHEN Day = 'tuesday' THEN 3
                            WHEN Day = 'wednesday' THEN 4
                            WHEN Day = 'thursday' THEN 5
                            WHEN Day = 'friday' THEN 6
                            WHEN Day = 'saturday' THEN 7 END ASC"
                      )->get();
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
        $sameDay = Schedule::where('branch_id', $input['branch_id'])->where('day', $input['day'])->first();
        if (isset($sameDay)) {
            $request->session()->flash('error', "Schedule on {$input['day']} already inserted!");
            return redirect()->back()->withInput();
        }
        Schedule::create($input);
        Log::create([
            'user_id' => Auth::id(),
            'description' => 'Insert Schedule'
        ]);
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
        $input = $request->all();
        $sameDay = Schedule::where('branch_id', $schedule['branch_id'])->where('day', $input['day'])->where('id', '!=', $schedule->id)->first();
        if (isset($sameDay)) {
            $request->session()->flash('error', "Schedule on {$input['day']} already inserted!");
            return redirect()->back()->withInput();
        }
        $schedule->update($input);
        Log::create([
            'user_id' => Auth::id(),
            'description' => 'Update Schedule'
        ]);
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
        Log::create([
            'user_id' => Auth::id(),
            'description' => 'Remove Schedule'
        ]);
        $request->session()->flash('error', 'Schedule has been removed!');
        return redirect(route('adminBranch.schedule.index'));
    }

    public function templateIndex()
    {
        $schedules = ScheduleTemplate::all();
        $branch = Branch::find(Auth::user()->branch_id);
        return view('adminBranch.schedule.template.index', [
            'schedules' => $schedules,
            'branch' => $branch,
        ]);
    }

    public function templateUpdate(Request $request)
    {
        Branch::find(Auth::user()->branch_id)->update($request->all());
        Log::create([
            'user_id' => Auth::id(),
            'description' => 'Update Template Schedule Branch'
        ]);
        $request->session()->flash('warning', 'Schedule Template has been updated!');
        return redirect(route('adminBranch.schedule.index'));
    }
}
