<?php

namespace App\Http\Controllers\AdminBranch;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BranchScheduleTemplateDetail;
use Illuminate\Support\Facades\Auth;
use App\ScheduleTemplate;
use App\ScheduleTemplateDetail;

class HolidayController extends Controller
{
    public function create()
    {
        return view('adminBranch.holiday.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'name' => 'required' 
        ]);

        BranchScheduleTemplateDetail::create([
            'branch_id' => Auth::user()->branch_id,
            'date' => $request->date,
            'name' => $request->name
        ]);

        return redirect()
            ->route('admin-branch.branch-configuration.schedule.index')
            ->with('success', __('Holiday has been added'));
    }

    public function template()
    {
        $scheduleTemplates = ScheduleTemplate::all();

        return view('adminBranch.holiday.template', [
            'scheduleTemplates' => $scheduleTemplates
        ]);
    }

    public function storeAll(Request $request)
    {
        $request->validate([
            'schedule_template_id' => 'required|integer'
        ]);

        $scheduleTemplateId = $request->schedule_template_id;

        $branchScheduleTemplateDetailIds = BranchScheduleTemplateDetail::where([
            'branch_id' => Auth::user()->branch_id,
            'schedule_template_id' => $scheduleTemplateId
        ])
            ->get()
            ->map(function ($branchScheduleTemplateDetail) {
                return $branchScheduleTemplateDetail->schedule_template_detail_id;
            })
            ->toArray();

        $scheduleTemplateDetails = ScheduleTemplateDetail::where('schedule_template_id', $scheduleTemplateId)
            ->whereNotIn('id', $branchScheduleTemplateDetailIds)
            ->get()
            ->map(function ($scheduleTemplateDetail) use ($scheduleTemplateId) {
                return [
                    'branch_id' => Auth::user()->branch_id,
                    'schedule_template_id' => $scheduleTemplateId,
                    'schedule_template_detail_id' => $scheduleTemplateDetail->id,
                    'name' => $scheduleTemplateDetail->description,
                    'date' => $scheduleTemplateDetail->date
                ];
            })
            ->toArray();

        BranchScheduleTemplateDetail::insert($scheduleTemplateDetails);

        return redirect()
            ->route('admin-branch.branch-configuration.schedule.index')
            ->with('success', __('Holiday template has been added'));
    }

    public function destroy($id)
    {
        BranchScheduleTemplateDetail::destroy($id);

        return redirect()
            ->route('admin-branch.branch-configuration.schedule.index')
            ->with('success', __('Holiday has been removed'));
    }
}
